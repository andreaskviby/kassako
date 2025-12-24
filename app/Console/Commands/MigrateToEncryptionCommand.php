<?php

namespace App\Console\Commands;

use App\Models\Team;
use App\Services\Encryption\DataEncryptorService;
use App\Services\Encryption\TeamEncryptionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Migrate Existing Data to Encryption
 *
 * This command helps migrate existing unencrypted data to the
 * new zero-knowledge encryption system.
 *
 * Migration Strategy:
 * 1. Identify teams without encryption initialized
 * 2. For each team, the owner must set up encryption
 * 3. Once encryption is set up, existing data is encrypted
 *
 * This command generates a report of migration status.
 */
class MigrateToEncryptionCommand extends Command
{
    protected $signature = 'encryption:migration-status
                            {--detailed : Show detailed record counts}';

    protected $description = 'Show encryption migration status for all teams';

    public function handle(): int
    {
        $this->info('Encryption Migration Status Report');
        $this->info('==================================');
        $this->newLine();

        $teams = Team::with('encryptionKey')->get();

        $stats = [
            'total_teams' => $teams->count(),
            'encrypted_teams' => 0,
            'pending_teams' => 0,
            'records_encrypted' => 0,
            'records_pending' => 0,
        ];

        $tableData = [];

        foreach ($teams as $team) {
            $hasEncryption = $team->encryptionKey !== null;

            if ($hasEncryption) {
                $stats['encrypted_teams']++;
            } else {
                $stats['pending_teams']++;
            }

            $recordCounts = $this->getRecordCounts($team);

            $tableData[] = [
                'id' => $team->id,
                'name' => $team->name,
                'encryption' => $hasEncryption ? 'Yes' : 'No',
                'key_version' => $team->encryptionKey?->key_version ?? '-',
                'last_accessed' => $team->encryptionKey?->last_accessed_at?->diffForHumans() ?? '-',
                'records' => $recordCounts['total'],
                'encrypted_records' => $recordCounts['encrypted'],
            ];

            $stats['records_encrypted'] += $recordCounts['encrypted'];
            $stats['records_pending'] += ($recordCounts['total'] - $recordCounts['encrypted']);
        }

        // Summary
        $this->info("Total Teams: {$stats['total_teams']}");
        $this->info("Encrypted: {$stats['encrypted_teams']}");
        $this->info("Pending Setup: {$stats['pending_teams']}");
        $this->newLine();
        $this->info("Total Records: " . ($stats['records_encrypted'] + $stats['records_pending']));
        $this->info("Encrypted Records: {$stats['records_encrypted']}");
        $this->info("Pending Encryption: {$stats['records_pending']}");
        $this->newLine();

        // Detailed table
        $this->table(
            ['Team ID', 'Name', 'Encryption', 'Key Version', 'Last Access', 'Records', 'Encrypted'],
            $tableData
        );

        if ($this->option('detailed')) {
            $this->showDetailedBreakdown($teams);
        }

        // Migration instructions
        if ($stats['pending_teams'] > 0) {
            $this->newLine();
            $this->warn('Migration Required:');
            $this->line('Teams without encryption must set up their passphrase.');
            $this->line('Send them to: /encryption/setup');
            $this->newLine();
            $this->line('Once they set up encryption, their existing data');
            $this->line('will be automatically encrypted.');
        }

        return Command::SUCCESS;
    }

    /**
     * Get record counts for a team.
     */
    private function getRecordCounts(Team $team): array
    {
        $counts = [
            'total' => 0,
            'encrypted' => 0,
            'by_table' => [],
        ];

        $tables = [
            'fortnox_connections' => $team->fortnoxConnection()->count(),
            'cash_snapshots' => $team->cashSnapshots()->count(),
            'fortnox_invoices' => $team->fortnoxInvoices()->count(),
            'fortnox_supplier_invoices' => $team->supplierInvoices()->count(),
            'fortnox_orders' => $team->orders()->count(),
            'customer_payment_patterns' => $team->customerPaymentPatterns()->count(),
        ];

        $encryptedCounts = [
            'fortnox_connections' => $team->fortnoxConnection()->where('is_encrypted', true)->count(),
            'cash_snapshots' => $team->cashSnapshots()->where('is_encrypted', true)->count(),
            'fortnox_invoices' => $team->fortnoxInvoices()->where('is_encrypted', true)->count(),
            'fortnox_supplier_invoices' => $team->supplierInvoices()->where('is_encrypted', true)->count(),
            'fortnox_orders' => $team->orders()->where('is_encrypted', true)->count(),
            'customer_payment_patterns' => $team->customerPaymentPatterns()->where('is_encrypted', true)->count(),
        ];

        foreach ($tables as $table => $count) {
            $counts['total'] += $count;
            $counts['encrypted'] += $encryptedCounts[$table];
            $counts['by_table'][$table] = [
                'total' => $count,
                'encrypted' => $encryptedCounts[$table],
            ];
        }

        return $counts;
    }

    /**
     * Show detailed breakdown by table.
     */
    private function showDetailedBreakdown($teams): void
    {
        $this->newLine();
        $this->info('Detailed Breakdown by Table:');
        $this->info('============================');

        foreach ($teams as $team) {
            $counts = $this->getRecordCounts($team);

            $this->newLine();
            $this->line("Team: {$team->name} (ID: {$team->id})");

            $tableData = [];
            foreach ($counts['by_table'] as $table => $data) {
                $tableData[] = [
                    'table' => $table,
                    'total' => $data['total'],
                    'encrypted' => $data['encrypted'],
                    'pending' => $data['total'] - $data['encrypted'],
                ];
            }

            $this->table(
                ['Table', 'Total', 'Encrypted', 'Pending'],
                $tableData
            );
        }
    }
}
