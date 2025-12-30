<?php

namespace App\Livewire;

use App\Models\CashSnapshot;
use App\Models\FortnoxInvoice;
use App\Services\AI\InsightsGenerator;
use App\Services\CashFlow\CashFlowCalculator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Dashboard extends Component
{
    public ?CashSnapshot $snapshot = null;
    public bool $isLoading = true;
    public bool $hasConnection = false;
    public ?string $lastUpdated = null;
    public string $chartPeriod = '12';
    public string $syncStatus = 'idle';
    public ?string $sessionExpiresAt = null;
    public bool $hasEncryption = false;

    public function mount(): void
    {
        $team = Auth::user()->currentTeam;
        $this->hasConnection = $team->hasFortnoxConnected();
        $this->hasEncryption = $team->hasEncryptionInitialized();

        // Get session expiration time
        $expiresAt = session('encryption_expires_at');
        if ($expiresAt) {
            $this->sessionExpiresAt = $expiresAt->toIso8601String();
        }

        // Get sync status from cache
        $this->syncStatus = Cache::get("sync_status_{$team->id}", 'idle');

        if ($this->hasConnection) {
            $this->snapshot = $team->latestSnapshot;
            $this->lastUpdated = $this->snapshot?->updated_at?->locale('sv')->diffForHumans();
        }

        $this->isLoading = false;
    }

    public function checkSyncStatus(): void
    {
        $team = Auth::user()->currentTeam;
        $this->syncStatus = Cache::get("sync_status_{$team->id}", 'idle');

        if ($this->syncStatus === 'completed') {
            // Reload snapshot after sync completed
            $this->snapshot = $team->fresh()->latestSnapshot;
            $this->lastUpdated = $this->snapshot?->updated_at?->locale('sv')->diffForHumans();
            $this->dispatch('charts-updated');
        }
    }

    public function setChartPeriod(string $period): void
    {
        $this->chartPeriod = $period;
    }

    public function refreshData(): void
    {
        $this->isLoading = true;

        $team = Auth::user()->currentTeam;

        try {
            $calculator = app(CashFlowCalculator::class);
            $this->snapshot = $calculator->calculateForTeam($team);

            $generator = app(InsightsGenerator::class);
            $insights = $generator->generateForSnapshot($team, $this->snapshot);

            $this->snapshot->update(['insights' => $insights]);
            $this->snapshot->refresh();

            $this->lastUpdated = 'just nu';

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Data uppdaterad!',
            ]);

            $this->dispatch('charts-updated');
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Kunde inte uppdatera data. Försök igen.',
            ]);
        }

        $this->isLoading = false;
    }

    public function getRunwayPercentageProperty(): int
    {
        if (!$this->snapshot) {
            return 0;
        }

        return min(100, (int) (($this->snapshot->runway_days / 120) * 100));
    }

    public function getRunwayTrendProperty(): array
    {
        // Generate sample trend data - in production this would come from historical snapshots
        return [
            'change' => $this->snapshot ? rand(5, 15) : 0,
            'direction' => 'up',
        ];
    }

    public function getCashFlowChartDataProperty(): array
    {
        if (!$this->snapshot || empty($this->snapshot->monthly_forecast)) {
            return $this->getDefaultChartData();
        }

        $forecast = $this->snapshot->monthly_forecast;
        $months = [];
        $actual = [];
        $projected = [];
        $minValues = [];
        $maxValues = [];

        $currentMonth = now()->month;

        foreach ($forecast as $index => $month) {
            $months[] = $month['month_name'];

            // First 6 months are "actual", rest are projections
            if ($index < 6) {
                $actual[] = $month['projected_balance'];
                $projected[] = null;
                $minValues[] = null;
                $maxValues[] = null;
            } else {
                $actual[] = null;
                $projected[] = $month['projected_balance'];
                // Calculate min/max as ±15% variance
                $minValues[] = (int) ($month['projected_balance'] * 0.85);
                $maxValues[] = (int) ($month['projected_balance'] * 1.15);
            }
        }

        // Connect actual to projected at transition point
        if (isset($actual[5]) && $actual[5] !== null) {
            $projected[5] = $actual[5];
        }

        return [
            'months' => $months,
            'actual' => $actual,
            'projected' => $projected,
            'min' => $minValues,
            'max' => $maxValues,
        ];
    }

    public function getPaymentPatternsDataProperty(): array
    {
        $team = Auth::user()->currentTeam;
        $patterns = $team->customerPaymentPatterns()
            ->orderByDesc('total_revenue')
            ->limit(6)
            ->get();

        if ($patterns->isEmpty()) {
            return [
                ['name' => 'Ingen data', 'days' => 0],
            ];
        }

        return $patterns->map(fn ($p) => [
            'name' => $p->customer_name === '[ENCRYPTED]' ? 'Kund ' . $p->customer_number : $p->customer_name,
            'days' => max(0, $p->avg_days_to_pay ?? 0),
        ])->toArray();
    }

    public function getOutstandingInvoicesProperty(): array
    {
        $team = Auth::user()->currentTeam;

        $unpaid = $team->fortnoxInvoices()
            ->where('status', 'unpaid')
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(total), 0) as total')
            ->first();

        $overdue = $team->fortnoxInvoices()
            ->where('status', 'overdue')
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(total), 0) as total')
            ->first();

        return [
            'unpaid_count' => $unpaid->count ?? 0,
            'unpaid_total' => $unpaid->total ?? 0,
            'overdue_count' => $overdue->count ?? 0,
            'overdue_total' => $overdue->total ?? 0,
            'total_count' => ($unpaid->count ?? 0) + ($overdue->count ?? 0),
            'total_amount' => ($unpaid->total ?? 0) + ($overdue->total ?? 0),
        ];
    }

    public function getAgingAnalysisProperty(): array
    {
        $team = Auth::user()->currentTeam;

        $invoices = $team->fortnoxInvoices()
            ->whereIn('status', ['unpaid', 'overdue'])
            ->get();

        $aging = [
            '0-30' => ['count' => 0, 'total' => 0],
            '31-60' => ['count' => 0, 'total' => 0],
            '61-90' => ['count' => 0, 'total' => 0],
            '90+' => ['count' => 0, 'total' => 0],
        ];

        $totalAmount = 0;

        foreach ($invoices as $invoice) {
            $daysOld = $invoice->invoice_date->diffInDays(now());
            $amount = $invoice->total;
            $totalAmount += $amount;

            if ($daysOld <= 30) {
                $aging['0-30']['count']++;
                $aging['0-30']['total'] += $amount;
            } elseif ($daysOld <= 60) {
                $aging['31-60']['count']++;
                $aging['31-60']['total'] += $amount;
            } elseif ($daysOld <= 90) {
                $aging['61-90']['count']++;
                $aging['61-90']['total'] += $amount;
            } else {
                $aging['90+']['count']++;
                $aging['90+']['total'] += $amount;
            }
        }

        foreach ($aging as $key => &$data) {
            $data['percentage'] = $totalAmount > 0
                ? round(($data['total'] / $totalAmount) * 100)
                : 0;
        }

        return $aging;
    }

    public function getOverdueInvoicesListProperty(): array
    {
        $team = Auth::user()->currentTeam;

        return $team->fortnoxInvoices()
            ->where('status', 'overdue')
            ->orderBy('due_date')
            ->limit(5)
            ->get()
            ->map(fn ($inv) => [
                'document_number' => $inv->document_number,
                'customer_name' => $inv->customer_name === '[ENCRYPTED]' ? 'Kund ' . $inv->customer_number : $inv->customer_name,
                'total' => $inv->total,
                'due_date' => $inv->due_date->format('Y-m-d'),
                'days_overdue' => $inv->due_date->diffInDays(now()),
            ])
            ->toArray();
    }

    private function getDefaultChartData(): array
    {
        return [
            'months' => ['Jan', 'Feb', 'Mar', 'Apr', 'Maj', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dec'],
            'actual' => [0, 0, 0, 0, 0, 0, null, null, null, null, null, null],
            'projected' => [null, null, null, null, null, 0, 0, 0, 0, 0, 0, 0],
            'min' => [null, null, null, null, null, null, 0, 0, 0, 0, 0, 0],
            'max' => [null, null, null, null, null, null, 0, 0, 0, 0, 0, 0],
        ];
    }

    public function render()
    {
        return view('livewire.dashboard')->layout('layouts.app');
    }
}
