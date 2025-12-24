<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Encryption Session Token Model
 *
 * Stores short-lived tokens that allow background jobs to decrypt data.
 * These tokens contain the MEK encrypted with a server-side session key.
 *
 * Security properties:
 * - Short expiration (max 1 hour)
 * - Limited purpose (sync, export, report)
 * - Can be invalidated immediately
 * - Automatically cleaned up when expired
 */
class EncryptionSessionToken extends Model
{
    protected $fillable = [
        'team_id',
        'user_id',
        'encrypted_mek',
        'session_iv',
        'session_auth_tag',
        'expires_at',
        'purpose',
        'token_id',
    ];

    protected $hidden = [
        'encrypted_mek',
        'session_iv',
        'session_auth_tag',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isValid(): bool
    {
        return !$this->isExpired();
    }
}
