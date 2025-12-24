<?php

namespace App\Livewire;

use App\Models\CashSnapshot;
use App\Services\AI\InsightsGenerator;
use App\Services\CashFlow\CashFlowCalculator;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public ?CashSnapshot $snapshot = null;
    public bool $isLoading = true;
    public bool $hasConnection = false;
    public ?string $lastUpdated = null;
    public string $chartPeriod = '12';

    public function mount(): void
    {
        $team = Auth::user()->currentTeam;
        $this->hasConnection = $team->hasFortnoxConnected();

        if ($this->hasConnection) {
            $this->snapshot = $team->latestSnapshot;
            $this->lastUpdated = $this->snapshot?->updated_at?->locale('sv')->diffForHumans();
        }

        $this->isLoading = false;
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
        // In production, this would analyze actual customer payment patterns
        // For now, return sample data
        return [
            ['name' => 'Snabb AB', 'days' => 3],
            ['name' => 'Normal Konsult', 'days' => 8],
            ['name' => 'Byggteamet', 'days' => 12],
            ['name' => 'Design & Co', 'days' => 5],
            ['name' => 'Sen Betalare AB', 'days' => 15],
            ['name' => 'Medel Service', 'days' => 7],
        ];
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
