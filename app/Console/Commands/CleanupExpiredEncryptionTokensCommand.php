<?php

namespace App\Console\Commands;

use App\Services\Encryption\TeamEncryptionService;
use Illuminate\Console\Command;

/**
 * Cleanup Expired Encryption Tokens
 *
 * This command should run periodically (e.g., every hour) to clean up
 * expired session tokens used for background job processing.
 *
 * Security importance:
 * - Minimizes window of exposure for session tokens
 * - Reduces attack surface if database is compromised
 * - Maintains principle of least privilege
 */
class CleanupExpiredEncryptionTokensCommand extends Command
{
    protected $signature = 'encryption:cleanup-tokens';

    protected $description = 'Clean up expired encryption session tokens';

    public function handle(TeamEncryptionService $encryptionService): int
    {
        $this->info('Cleaning up expired encryption session tokens...');

        $deleted = $encryptionService->cleanupExpiredTokens();

        $this->info("Deleted {$deleted} expired tokens.");

        return Command::SUCCESS;
    }
}
