<?php

namespace App\Services\CashFlow;

use App\Models\CashSnapshot;
use App\Models\Team;
use App\Services\Fortnox\FortnoxService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;

class CashFlowCalculator
{
    protected Team $team;
    protected FortnoxService $fortnox;

    public function __construct(FortnoxService $fortnox)
    {
        $this->fortnox = $fortnox;
    }

    public function calculateForTeam(Team $team): CashSnapshot
    {
        $this->team = $team;

        $cashBalance = $this->getCurrentCashBalance();
        $receivables = $this->getAccountsReceivable();
        $payables = $this->getAccountsPayable();

        $avgDailyBurn = $this->calculateAverageDailyBurn();
        $avgDailyIncome = $this->calculateAverageDailyIncome();

        $netDailyChange = $avgDailyIncome - $avgDailyBurn;
        $runwayDays = $this->calculateRunwayDays($cashBalance, $receivables, $payables, $netDailyChange);

        $forecast = $this->generateMonthlyForecast($cashBalance, $receivables, $payables);

        return CashSnapshot::updateOrCreate(
            [
                'team_id' => $team->id,
                'snapshot_date' => now()->toDateString(),
            ],
            [
                'cash_balance' => $cashBalance,
                'accounts_receivable' => $receivables,
                'accounts_payable' => $payables,
                'runway_days' => $runwayDays,
                'avg_daily_burn' => $avgDailyBurn,
                'avg_daily_income' => $avgDailyIncome,
                'monthly_forecast' => $forecast,
                'insights' => [],
            ]
        );
    }

    protected function getCurrentCashBalance(): float
    {
        try {
            $this->fortnox->forTeam($this->team);

            return $this->fortnox->getCashBalance();
        } catch (Exception $e) {
            return $this->estimateCashFromTransactions();
        }
    }

    protected function estimateCashFromTransactions(): float
    {
        $received = $this->team->invoices()
            ->where('status', 'paid')
            ->where('paid_date', '>=', now()->subMonths(12))
            ->sum('total');

        $paid = $this->team->supplierInvoices()
            ->where('status', 'paid')
            ->where('paid_date', '>=', now()->subMonths(12))
            ->sum('total');

        return $received - $paid;
    }

    protected function getAccountsReceivable(): float
    {
        return $this->team->invoices()
            ->whereIn('status', ['unpaid', 'overdue'])
            ->sum('total');
    }

    protected function getAccountsPayable(): float
    {
        return $this->team->supplierInvoices()
            ->whereIn('status', ['unpaid', 'overdue'])
            ->sum('total');
    }

    protected function calculateAverageDailyBurn(): float
    {
        $totalPaid = $this->team->supplierInvoices()
            ->where('status', 'paid')
            ->where('paid_date', '>=', now()->subMonths(6))
            ->sum('total');

        $days = min(180, now()->diffInDays($this->team->created_at));

        return $days > 0 ? $totalPaid / $days : 0;
    }

    protected function calculateAverageDailyIncome(): float
    {
        $totalReceived = $this->team->invoices()
            ->where('status', 'paid')
            ->where('paid_date', '>=', now()->subMonths(6))
            ->sum('total');

        $days = min(180, now()->diffInDays($this->team->created_at));

        return $days > 0 ? $totalReceived / $days : 0;
    }

    protected function calculateRunwayDays(
        float $cashBalance,
        float $receivables,
        float $payables,
        float $netDailyChange
    ): int {
        $expectedReceivables = $receivables * 0.7;
        $totalAvailable = $cashBalance + $expectedReceivables - $payables;

        if ($netDailyChange >= 0) {
            return min(365, max(0, (int) ($totalAvailable / max(1, $this->calculateAverageDailyBurn()))));
        }

        $runwayDays = (int) abs($totalAvailable / $netDailyChange);

        return max(0, min(365, $runwayDays));
    }

    protected function generateMonthlyForecast(
        float $cashBalance,
        float $receivables,
        float $payables
    ): array {
        $forecast = [];
        $currentCash = $cashBalance;

        $monthlyPatterns = $this->analyzeMonthlyPatterns();
        $upcomingReceivables = $this->getUpcomingReceivables();
        $upcomingPayables = $this->getUpcomingPayables();
        $taxMonths = $this->getSwedishTaxCalendar();

        $period = CarbonPeriod::create(
            now()->startOfMonth(),
            '1 month',
            now()->addMonths(11)->endOfMonth()
        );

        foreach ($period as $month) {
            $monthKey = $month->format('Y-m');
            $monthNum = (int) $month->format('n');

            $expectedIncome = $upcomingReceivables[$monthKey] ?? 0;
            if ($expectedIncome === 0) {
                $seasonalFactor = $monthlyPatterns['income'][$monthNum] ?? 1.0;
                $expectedIncome = $this->calculateAverageDailyIncome() * 30 * $seasonalFactor;
            }

            $expectedExpenses = $upcomingPayables[$monthKey] ?? 0;
            if ($expectedExpenses === 0) {
                $seasonalFactor = $monthlyPatterns['expenses'][$monthNum] ?? 1.0;
                $expectedExpenses = $this->calculateAverageDailyBurn() * 30 * $seasonalFactor;
            }

            $taxAmount = $taxMonths[$monthNum] ?? 0;
            $expectedExpenses += $taxAmount;

            $currentCash = $currentCash + $expectedIncome - $expectedExpenses;

            $forecast[] = [
                'month' => $month->format('Y-m'),
                'month_name' => $month->locale('sv')->translatedFormat('M'),
                'expected_income' => round($expectedIncome, 2),
                'expected_expenses' => round($expectedExpenses, 2),
                'projected_balance' => round($currentCash, 2),
                'has_tax_payment' => isset($taxMonths[$monthNum]),
                'warning' => $currentCash < 0,
            ];
        }

        return $forecast;
    }

    protected function analyzeMonthlyPatterns(): array
    {
        $incomePatterns = [];
        $expensePatterns = [];

        $invoices = $this->team->invoices()
            ->where('status', 'paid')
            ->where('paid_date', '>=', now()->subYears(2))
            ->get();

        $supplierInvoices = $this->team->supplierInvoices()
            ->where('status', 'paid')
            ->where('paid_date', '>=', now()->subYears(2))
            ->get();

        $monthlyIncome = $invoices->groupBy(fn ($i) => $i->paid_date->format('n'))
            ->map(fn ($g) => $g->avg('total'));

        $monthlyExpenses = $supplierInvoices->groupBy(fn ($i) => $i->paid_date->format('n'))
            ->map(fn ($g) => $g->avg('total'));

        $avgIncome = $monthlyIncome->avg() ?: 1;
        $avgExpense = $monthlyExpenses->avg() ?: 1;

        for ($m = 1; $m <= 12; $m++) {
            $incomePatterns[$m] = ($monthlyIncome[$m] ?? $avgIncome) / $avgIncome;
            $expensePatterns[$m] = ($monthlyExpenses[$m] ?? $avgExpense) / $avgExpense;
        }

        return [
            'income' => $incomePatterns,
            'expenses' => $expensePatterns,
        ];
    }

    protected function getUpcomingReceivables(): array
    {
        return $this->team->invoices()
            ->whereIn('status', ['unpaid', 'overdue'])
            ->get()
            ->groupBy(fn ($i) => $i->due_date->format('Y-m'))
            ->map(fn ($g) => $g->sum('total'))
            ->toArray();
    }

    protected function getUpcomingPayables(): array
    {
        return $this->team->supplierInvoices()
            ->whereIn('status', ['unpaid', 'overdue'])
            ->get()
            ->groupBy(fn ($i) => $i->due_date->format('Y-m'))
            ->map(fn ($g) => $g->sum('total'))
            ->toArray();
    }

    protected function getSwedishTaxCalendar(): array
    {
        $avgMonthlyRevenue = $this->calculateAverageDailyIncome() * 30;
        $estimatedVAT = $avgMonthlyRevenue * 0.20;
        $estimatedEmployerTax = $avgMonthlyRevenue * 0.10;

        return [
            1 => $estimatedVAT + $estimatedEmployerTax,
            2 => $estimatedEmployerTax,
            3 => $estimatedEmployerTax,
            4 => $estimatedVAT + $estimatedEmployerTax,
            5 => $estimatedEmployerTax,
            6 => $estimatedEmployerTax,
            7 => $estimatedVAT + $estimatedEmployerTax,
            8 => $estimatedEmployerTax,
            9 => $estimatedEmployerTax,
            10 => $estimatedVAT + $estimatedEmployerTax,
            11 => $estimatedEmployerTax,
            12 => $estimatedEmployerTax,
        ];
    }
}
