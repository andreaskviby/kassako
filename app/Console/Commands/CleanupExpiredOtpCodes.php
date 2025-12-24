<?php

namespace App\Console\Commands;

use App\Models\OtpCode;
use App\Models\OtpRateLimit;
use Illuminate\Console\Command;

class CleanupExpiredOtpCodes extends Command
{
    protected $signature = 'otp:cleanup {--dry-run : Show what would be deleted without deleting}';

    protected $description = 'Clean up expired OTP codes and rate limit records';

    public function handle(): int
    {
        $this->info('Starting OTP cleanup...');

        $expiredCodes = OtpCode::where('expires_at', '<', now()->subHours(24));
        $codeCount = $expiredCodes->count();

        if (! $this->option('dry-run')) {
            $expiredCodes->delete();
        }

        $this->comment("Expired OTP codes: {$codeCount}");

        $verifiedCodes = OtpCode::whereNotNull('verified_at')
            ->where('verified_at', '<', now()->subHours(24));
        $verifiedCount = $verifiedCodes->count();

        if (! $this->option('dry-run')) {
            $verifiedCodes->delete();
        }

        $this->comment("Old verified codes: {$verifiedCount}");

        $expiredLimits = OtpRateLimit::where('blocked_until', '<', now())
            ->orWhere('updated_at', '<', now()->subHours(2));
        $limitCount = $expiredLimits->count();

        if (! $this->option('dry-run')) {
            $expiredLimits->delete();
        }

        $this->comment("Expired rate limits: {$limitCount}");

        $total = $codeCount + $verifiedCount + $limitCount;
        $action = $this->option('dry-run') ? 'Would delete' : 'Deleted';
        $this->info("{$action} {$total} records total.");

        return Command::SUCCESS;
    }
}
