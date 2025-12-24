<?php

namespace App\Services;

use App\Models\OtpCode;
use App\Models\OtpRateLimit;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class OtpService
{
    private const MAX_ATTEMPTS = 5;
    private const EXPIRY_MINUTES = 10;
    private const RATE_LIMIT_REQUESTS = 3;
    private const RATE_LIMIT_MINUTES = 1;
    private const LOCKOUT_MINUTES = 30;

    public function generateCode(): string
    {
        return (string) random_int(100000, 999999);
    }

    public function createOtp(string $email, string $purpose = 'login', ?int $userId = null): string
    {
        $this->invalidateExistingCodes($email, $purpose);

        $plaintextCode = $this->generateCode();

        OtpCode::create([
            'user_id' => $userId,
            'email' => strtolower(trim($email)),
            'code_hash' => Hash::make($plaintextCode),
            'purpose' => $purpose,
            'expires_at' => now()->addMinutes(self::EXPIRY_MINUTES),
            'ip_hash' => $this->hashIdentifier(request()->ip()),
        ]);

        return $plaintextCode;
    }

    public function verifyOtp(string $email, string $code, string $purpose = 'login'): VerificationResult
    {
        $email = strtolower(trim($email));

        if ($this->isRateLimited($email, 'verify')) {
            return VerificationResult::rateLimited();
        }

        $otpRecord = OtpCode::where('email', $email)
            ->where('purpose', $purpose)
            ->where('expires_at', '>', now())
            ->whereNull('verified_at')
            ->where('attempts', '<', self::MAX_ATTEMPTS)
            ->latest()
            ->first();

        $dummyHash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
        $hashToCheck = $otpRecord?->code_hash ?? $dummyHash;

        $isValid = Hash::check($code, $hashToCheck);

        if (! $otpRecord) {
            $this->incrementRateLimit($email, 'verify');
            return VerificationResult::invalidOrExpired();
        }

        $otpRecord->incrementAttempts();

        if (! $isValid) {
            $this->incrementRateLimit($email, 'verify');

            if ($otpRecord->hasExceededMaxAttempts(self::MAX_ATTEMPTS)) {
                $this->logSecurityEvent('otp_max_attempts_exceeded', $email);
                return VerificationResult::maxAttemptsExceeded();
            }

            return VerificationResult::invalidCode(self::MAX_ATTEMPTS - $otpRecord->attempts);
        }

        $otpRecord->markAsVerified();
        $this->clearRateLimit($email, 'verify');
        $this->logSecurityEvent('otp_verified', $email);

        return VerificationResult::success($otpRecord);
    }

    public function isRequestRateLimited(string $email): bool
    {
        return $this->isRateLimited($email, 'request');
    }

    public function incrementRequestRateLimit(string $email): void
    {
        $this->incrementRateLimit($email, 'request');
    }

    private function hashIdentifier(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return hash_hmac('sha256', $value, config('app.key'));
    }

    private function invalidateExistingCodes(string $email, string $purpose): void
    {
        OtpCode::where('email', strtolower($email))
            ->where('purpose', $purpose)
            ->whereNull('verified_at')
            ->delete();
    }

    private function isRateLimited(string $identifier, string $type): bool
    {
        $key = "otp_ratelimit:{$type}:" . hash('sha256', $identifier);

        if (Cache::has($key . ':blocked')) {
            return true;
        }

        $record = OtpRateLimit::where('identifier', hash('sha256', $identifier))
            ->where('type', $type)
            ->first();

        if (! $record) {
            return false;
        }

        if ($record->blocked_until && $record->blocked_until->isFuture()) {
            Cache::put($key . ':blocked', true, $record->blocked_until);
            return true;
        }

        $windowMinutes = $type === 'request' ? self::RATE_LIMIT_MINUTES : 5;

        if ($record->window_start->diffInMinutes(now()) < $windowMinutes) {
            $maxAttempts = $type === 'request' ? self::RATE_LIMIT_REQUESTS : self::MAX_ATTEMPTS;
            return $record->attempts >= $maxAttempts;
        }

        return false;
    }

    private function incrementRateLimit(string $identifier, string $type): void
    {
        $hashedIdentifier = hash('sha256', $identifier);

        $record = OtpRateLimit::firstOrNew([
            'identifier' => $hashedIdentifier,
            'type' => $type,
        ]);

        $windowMinutes = $type === 'request' ? 1 : 5;

        if (! $record->exists || $record->window_start->diffInMinutes(now()) >= $windowMinutes) {
            $record->attempts = 1;
            $record->window_start = now();
            $record->blocked_until = null;
        } else {
            $record->attempts++;
        }

        $threshold = $type === 'request' ? 5 : 10;
        if ($record->attempts >= $threshold) {
            $record->blocked_until = now()->addMinutes(self::LOCKOUT_MINUTES);
        }

        $record->save();
    }

    private function clearRateLimit(string $identifier, string $type): void
    {
        $hashedIdentifier = hash('sha256', $identifier);

        OtpRateLimit::where('identifier', $hashedIdentifier)
            ->where('type', $type)
            ->delete();

        $key = "otp_ratelimit:{$type}:{$hashedIdentifier}";
        Cache::forget($key . ':blocked');
    }

    private function logSecurityEvent(string $event, string $identifier): void
    {
        Log::channel('single')->info("OTP Security Event: {$event}", [
            'identifier_hash' => hash('sha256', $identifier),
            'ip_hash' => hash('sha256', request()->ip()),
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
