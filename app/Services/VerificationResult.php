<?php

namespace App\Services;

use App\Models\OtpCode;

class VerificationResult
{
    private function __construct(
        private bool $success,
        private string $message,
        private ?OtpCode $otpRecord = null,
        private ?int $remainingAttempts = null,
        private bool $rateLimited = false
    ) {}

    public static function success(OtpCode $record): self
    {
        return new self(true, 'Verifiering lyckades', $record);
    }

    public static function invalidCode(int $remainingAttempts): self
    {
        return new self(
            false,
            "Ogiltig kod. {$remainingAttempts} försök kvar.",
            remainingAttempts: $remainingAttempts
        );
    }

    public static function invalidOrExpired(): self
    {
        return new self(false, 'Koden är ogiltig eller har gått ut. Begär en ny kod.');
    }

    public static function maxAttemptsExceeded(): self
    {
        return new self(false, 'Maximalt antal försök uppnått. Begär en ny kod.');
    }

    public static function rateLimited(): self
    {
        return new self(
            false,
            'För många försök. Vänta en stund innan du försöker igen.',
            rateLimited: true
        );
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getOtpRecord(): ?OtpCode
    {
        return $this->otpRecord;
    }

    public function getRemainingAttempts(): ?int
    {
        return $this->remainingAttempts;
    }

    public function isRateLimited(): bool
    {
        return $this->rateLimited;
    }
}
