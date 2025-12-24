<?php

namespace App\Services\Fortnox;

use App\Models\CustomerPaymentPattern;
use App\Models\FortnoxInvoice;
use App\Models\FortnoxOrder;
use App\Models\FortnoxSupplierInvoice;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class FortnoxSyncService
{
    protected FortnoxService $fortnox;
    protected Team $team;

    public function __construct(FortnoxService $fortnox)
    {
        $this->fortnox = $fortnox;
    }

    public function syncTeam(Team $team): void
    {
        $this->team = $team;
        $connection = $team->fortnoxConnection;

        if (!$connection || !$connection->is_active) {
            return;
        }

        try {
            $connection->markAsSyncing();

            $this->fortnox->forTeam($team);

            $this->syncCompanyInfo();
            $this->syncInvoices();
            $this->syncSupplierInvoices();
            $this->syncOrders();
            $this->calculatePaymentPatterns();

            $connection->markAsSynced();
        } catch (\Exception $e) {
            Log::error('Fortnox sync failed', [
                'team_id' => $team->id,
                'error' => $e->getMessage(),
            ]);
            $connection->markAsFailed($e->getMessage());
            throw $e;
        }
    }

    protected function syncCompanyInfo(): void
    {
        $info = $this->fortnox->getCompanyInfo();

        $this->team->fortnoxConnection->update([
            'company_name' => $info['CompanyName'] ?? null,
            'organization_number' => $info['OrganizationNumber'] ?? null,
        ]);
    }

    protected function syncInvoices(): void
    {
        $invoices = $this->fortnox->getInvoices([
            'fromdate' => now()->subMonths(24)->format('Y-m-d'),
        ]);

        foreach ($invoices as $invoice) {
            $dueDate = Carbon::parse($invoice['DueDate']);
            $paidDate = !empty($invoice['FinalPayDate']) ? Carbon::parse($invoice['FinalPayDate']) : null;

            $status = match (true) {
                $invoice['Cancelled'] ?? false => 'cancelled',
                $invoice['Booked'] && $paidDate !== null => 'paid',
                $invoice['Booked'] && $dueDate->isPast() => 'overdue',
                default => 'unpaid',
            };

            $daysOverdue = 0;
            if ($status === 'overdue') {
                $daysOverdue = $dueDate->diffInDays(now());
            } elseif ($status === 'paid' && $paidDate && $paidDate->isAfter($dueDate)) {
                $daysOverdue = $dueDate->diffInDays($paidDate);
            }

            FortnoxInvoice::updateOrCreate(
                [
                    'team_id' => $this->team->id,
                    'fortnox_id' => $invoice['DocumentNumber'],
                ],
                [
                    'document_number' => $invoice['DocumentNumber'],
                    'customer_name' => $invoice['CustomerName'] ?? null,
                    'customer_number' => $invoice['CustomerNumber'] ?? null,
                    'total' => floatval($invoice['Total'] ?? 0),
                    'total_vat' => floatval($invoice['TotalVAT'] ?? 0),
                    'currency' => $invoice['Currency'] ?? 'SEK',
                    'invoice_date' => Carbon::parse($invoice['InvoiceDate']),
                    'due_date' => $dueDate,
                    'paid_date' => $paidDate,
                    'status' => $status,
                    'days_overdue' => $daysOverdue,
                    'is_credit' => ($invoice['Total'] ?? 0) < 0,
                ]
            );
        }
    }

    protected function syncSupplierInvoices(): void
    {
        $invoices = $this->fortnox->getSupplierInvoices([
            'fromdate' => now()->subMonths(12)->format('Y-m-d'),
        ]);

        foreach ($invoices as $invoice) {
            $dueDate = Carbon::parse($invoice['DueDate']);
            $paidDate = !empty($invoice['FinalPayDate']) ? Carbon::parse($invoice['FinalPayDate']) : null;

            $status = match (true) {
                $paidDate !== null => 'paid',
                $dueDate->isPast() => 'overdue',
                default => 'unpaid',
            };

            FortnoxSupplierInvoice::updateOrCreate(
                [
                    'team_id' => $this->team->id,
                    'fortnox_id' => $invoice['GivenNumber'] ?? $invoice['InvoiceNumber'],
                ],
                [
                    'document_number' => $invoice['InvoiceNumber'] ?? null,
                    'supplier_name' => $invoice['SupplierName'] ?? null,
                    'supplier_number' => $invoice['SupplierNumber'] ?? null,
                    'total' => floatval($invoice['Total'] ?? 0),
                    'currency' => $invoice['Currency'] ?? 'SEK',
                    'invoice_date' => Carbon::parse($invoice['InvoiceDate']),
                    'due_date' => $dueDate,
                    'paid_date' => $paidDate,
                    'status' => $status,
                ]
            );
        }
    }

    protected function syncOrders(): void
    {
        $orders = $this->fortnox->getOrders([
            'fromdate' => now()->subMonths(12)->format('Y-m-d'),
        ]);

        foreach ($orders as $order) {
            FortnoxOrder::updateOrCreate(
                [
                    'team_id' => $this->team->id,
                    'fortnox_id' => $order['DocumentNumber'],
                ],
                [
                    'document_number' => $order['DocumentNumber'],
                    'customer_name' => $order['CustomerName'] ?? null,
                    'customer_number' => $order['CustomerNumber'] ?? null,
                    'total' => floatval($order['Total'] ?? 0),
                    'currency' => $order['Currency'] ?? 'SEK',
                    'order_date' => Carbon::parse($order['OrderDate']),
                    'delivery_date' => !empty($order['DeliveryDate'])
                        ? Carbon::parse($order['DeliveryDate'])
                        : null,
                    'status' => strtolower($order['OrderStatus'] ?? 'registered'),
                ]
            );
        }
    }

    protected function calculatePaymentPatterns(): void
    {
        $totalRevenue = $this->team->invoices()
            ->where('status', '!=', 'cancelled')
            ->where('is_credit', false)
            ->sum('total');

        $patterns = $this->team->invoices()
            ->where('status', '!=', 'cancelled')
            ->whereNotNull('customer_number')
            ->select('customer_number', 'customer_name')
            ->selectRaw('COUNT(*) as total_invoices')
            ->selectRaw('SUM(CASE WHEN status = "paid" THEN 1 ELSE 0 END) as paid_invoices')
            ->selectRaw('AVG(CASE WHEN paid_date IS NOT NULL THEN DATEDIFF(paid_date, due_date) ELSE NULL END) as avg_days_late')
            ->selectRaw('SUM(CASE WHEN is_credit = 0 THEN total ELSE 0 END) as total_revenue')
            ->groupBy('customer_number', 'customer_name')
            ->get();

        foreach ($patterns as $pattern) {
            $avgDaysLate = round($pattern->avg_days_late ?? 0);
            $revenuePercentage = $totalRevenue > 0
                ? round(($pattern->total_revenue / $totalRevenue) * 100, 2)
                : 0;

            $reliability = match (true) {
                $avgDaysLate <= 0 => 'excellent',
                $avgDaysLate <= 7 => 'good',
                $avgDaysLate <= 14 => 'average',
                $avgDaysLate <= 30 => 'poor',
                default => 'risky',
            };

            CustomerPaymentPattern::updateOrCreate(
                [
                    'team_id' => $this->team->id,
                    'customer_number' => $pattern->customer_number,
                ],
                [
                    'customer_name' => $pattern->customer_name,
                    'total_invoices' => $pattern->total_invoices,
                    'paid_invoices' => $pattern->paid_invoices,
                    'avg_days_to_pay' => $avgDaysLate,
                    'total_revenue' => $pattern->total_revenue,
                    'revenue_percentage' => $revenuePercentage,
                    'payment_reliability' => $reliability,
                ]
            );
        }
    }
}
