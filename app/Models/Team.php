<?php

namespace App\Models;

use App\Services\Encryption\TeamEncryptionService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Cashier\Billable;
use Laravel\Jetstream\Events\TeamCreated;
use Laravel\Jetstream\Events\TeamDeleted;
use Laravel\Jetstream\Events\TeamUpdated;
use Laravel\Jetstream\Team as JetstreamTeam;

class Team extends JetstreamTeam
{
    use Billable;
    use HasFactory;

    protected $fillable = [
        'name',
        'personal_team',
    ];

    protected $dispatchesEvents = [
        'created' => TeamCreated::class,
        'updated' => TeamUpdated::class,
        'deleted' => TeamDeleted::class,
    ];

    protected $appends = [
        'is_subscribed',
        'is_on_trial',
    ];

    protected function casts(): array
    {
        return [
            'personal_team' => 'boolean',
            'trial_ends_at' => 'datetime',
            'subscription_ends_at' => 'datetime',
        ];
    }

    public function fortnoxConnection(): HasOne
    {
        return $this->hasOne(FortnoxConnection::class);
    }

    public function cashSnapshots(): HasMany
    {
        return $this->hasMany(CashSnapshot::class);
    }

    public function latestSnapshot(): HasOne
    {
        return $this->hasOne(CashSnapshot::class)->latestOfMany('snapshot_date');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(FortnoxInvoice::class);
    }

    public function supplierInvoices(): HasMany
    {
        return $this->hasMany(FortnoxSupplierInvoice::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(FortnoxOrder::class);
    }

    public function customerPaymentPatterns(): HasMany
    {
        return $this->hasMany(CustomerPaymentPattern::class);
    }

    public function encryptionKey(): HasOne
    {
        return $this->hasOne(TeamEncryptionKey::class);
    }

    public function encryptionSessionTokens(): HasMany
    {
        return $this->hasMany(EncryptionSessionToken::class);
    }

    public function hasEncryptionInitialized(): bool
    {
        return $this->encryptionKey()->exists();
    }

    public function isEncryptionUnlocked(string $sessionId): bool
    {
        return app(TeamEncryptionService::class)->isUnlocked($this, $sessionId);
    }

    public function hasFortnoxConnected(): bool
    {
        return $this->fortnoxConnection?->is_active ?? false;
    }

    public function getIsSubscribedAttribute(): bool
    {
        return $this->subscribed('default') || $this->onTrial();
    }

    public function getIsOnTrialAttribute(): bool
    {
        return $this->onTrial() && !$this->subscribed('default');
    }

    public function canAccessDashboard(): bool
    {
        return $this->is_subscribed && $this->hasFortnoxConnected();
    }
}
