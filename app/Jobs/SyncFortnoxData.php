<?php

namespace App\Jobs;

use App\Models\Team;
use App\Services\AI\InsightsGenerator;
use App\Services\CashFlow\CashFlowCalculator;
use App\Services\Fortnox\FortnoxSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncFortnoxData implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public Team $team
    ) {}

    public function handle(
        FortnoxSyncService $syncService,
        CashFlowCalculator $calculator,
        InsightsGenerator $insights
    ): void {
        try {
            $syncService->syncTeam($this->team);

            $snapshot = $calculator->calculateForTeam($this->team);

            $generatedInsights = $insights->generateForSnapshot($this->team, $snapshot);
            $snapshot->update(['insights' => $generatedInsights]);

            Log::info('Fortnox sync completed', [
                'team_id' => $this->team->id,
                'runway_days' => $snapshot->runway_days,
            ]);
        } catch (\Exception $e) {
            Log::error('Fortnox sync job failed', [
                'team_id' => $this->team->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        $this->team->fortnoxConnection?->markAsFailed($exception->getMessage());
    }
}
