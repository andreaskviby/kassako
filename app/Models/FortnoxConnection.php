<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class FortnoxConnection extends Model
{
    protected $fillable = [
        'team_id',
        'company_name',
        'organization_number',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'last_synced_at',
        'sync_status',
        'sync_error',
        'is_active',
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
        'last_synced_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'access_token',
        'refresh_token',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function setAccessTokenAttribute($value): void
    {
        $this->attributes['access_token'] = $value ? Crypt::encryptString($value) : null;
    }

    public function setRefreshTokenAttribute($value): void
    {
        $this->attributes['refresh_token'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getAccessTokenAttribute($value): ?string
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    public function getRefreshTokenAttribute($value): ?string
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    public function isTokenExpired(): bool
    {
        return $this->token_expires_at?->isPast() ?? true;
    }

    public function needsRefresh(): bool
    {
        return $this->token_expires_at?->subMinutes(5)->isPast() ?? true;
    }

    public function markAsSyncing(): void
    {
        $this->update([
            'sync_status' => 'syncing',
            'sync_error' => null,
        ]);
    }

    public function markAsSynced(): void
    {
        $this->update([
            'sync_status' => 'completed',
            'last_synced_at' => now(),
            'sync_error' => null,
        ]);
    }

    public function markAsFailed(string $error): void
    {
        $this->update([
            'sync_status' => 'failed',
            'sync_error' => $error,
        ]);
    }
}
