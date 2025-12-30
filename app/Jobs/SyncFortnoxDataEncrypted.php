<?php

namespace App\Jobs;

use App\Models\Team;
use App\Services\AI\InsightsGenerator;
use App\Services\CashFlow\CashFlowCalculator;
use App\Services\Encryption\DataEncryptorService;
use App\Services\Encryption\TeamEncryptionService;
use App\Services\Fortnox\FortnoxSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SyncFortnoxDataEncrypted implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public Team $team,
        public string $tokenId
    ) {}

    public function handle(
        FortnoxSyncService $syncService,
        CashFlowCalculator $calculator,
        InsightsGenerator $insights,
        TeamEncryptionService $encryptionService,
        DataEncryptorService $dataEncryptor
    ): void {
        // Update sync status
        Cache::put("sync_status_{$this->team->id}", 'syncing', 300);

        try {
            // Get DEK using the session token
            $dek = $encryptionService->getDekFromToken($this->team, $this->tokenId);
            $keyVersion = $this->team->encryptionKey->key_version;

            // Sync data from Fortnox (stored in plaintext temporarily)
            $syncService->syncTeam($this->team);

            // Encrypt all the synced data
            $this->encryptSyncedData($dataEncryptor, $dek, $keyVersion);

            // Calculate cash flow snapshot
            $snapshot = $calculator->calculateForTeam($this->team);

            // Generate AI insights
            $generatedInsights = $insights->generateForSnapshot($this->team, $snapshot);
            $snapshot->update(['insights' => $generatedInsights]);

            // Update sync status to completed
            Cache::put("sync_status_{$this->team->id}", 'completed', 300);
            Cache::put("sync_completed_at_{$this->team->id}", now()->toIso8601String(), 300);

            Log::info('Encrypted Fortnox sync completed', [
                'team_id' => $this->team->id,
                'runway_days' => $snapshot->runway_days,
            ]);
        } catch (\Exception $e) {
            Cache::put("sync_status_{$this->team->id}", 'failed', 300);
            Cache::put("sync_error_{$this->team->id}", $e->getMessage(), 300);

            Log::error('Encrypted Fortnox sync job failed', [
                'team_id' => $this->team->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    protected function encryptSyncedData(DataEncryptorService $dataEncryptor, string $dek, int $keyVersion): void
    {
        // Encrypt invoices
        $this->team->fortnoxInvoices()
            ->where('is_encrypted', false)
            ->each(function ($invoice) use ($dataEncryptor, $dek, $keyVersion) {
                $dataEncryptor->encryptInvoice($invoice, $dek, $keyVersion);
            });

        // Encrypt orders
        $this->team->fortnoxOrders()
            ->where('is_encrypted', false)
            ->each(function ($order) use ($dataEncryptor, $dek, $keyVersion) {
                $dataEncryptor->encryptOrder($order, $dek, $keyVersion);
            });

        // Encrypt supplier invoices
        $this->team->fortnoxSupplierInvoices()
            ->where('is_encrypted', false)
            ->each(function ($supplierInvoice) use ($dataEncryptor, $dek, $keyVersion) {
                $dataEncryptor->encryptSupplierInvoice($supplierInvoice, $dek, $keyVersion);
            });

        // Encrypt customer payment patterns
        $this->team->customerPaymentPatterns()
            ->where('is_encrypted', false)
            ->each(function ($pattern) use ($dataEncryptor, $dek, $keyVersion) {
                $dataEncryptor->encryptCustomerPaymentPattern($pattern, $dek, $keyVersion);
            });

        // Encrypt connection data
        $connection = $this->team->fortnoxConnection;
        if ($connection && !$connection->is_encrypted) {
            $dataEncryptor->encryptFortnoxConnection($connection, $dek, $keyVersion);
        }
    }

    public function failed(\Throwable $exception): void
    {
        Cache::put("sync_status_{$this->team->id}", 'failed', 300);
        $this->team->fortnoxConnection?->markAsFailed($exception->getMessage());
    }
}
