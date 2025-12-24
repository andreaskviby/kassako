<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashSnapshot extends Model
{
    protected $fillable = [
        'team_id',
        'snapshot_date',
        'cash_balance',
        'accounts_receivable',
        'accounts_payable',
        'runway_days',
        'avg_daily_burn',
        'avg_daily_income',
        'monthly_forecast',
        'insights',
    ];

    protected $casts = [
        'snapshot_date' => 'date',
        'cash_balance' => 'decimal:2',
        'accounts_receivable' => 'decimal:2',
        'accounts_payable' => 'decimal:2',
        'avg_daily_burn' => 'decimal:2',
        'avg_daily_income' => 'decimal:2',
        'monthly_forecast' => 'array',
        'insights' => 'array',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function getRunwayStatusAttribute(): string
    {
        return match (true) {
            $this->runway_days >= 90 => 'safe',
            $this->runway_days >= 60 => 'good',
            $this->runway_days >= 30 => 'warning',
            default => 'danger',
        };
    }

    public function getRunwayColorAttribute(): string
    {
        return match ($this->runway_status) {
            'safe' => '#2D7A4F',
            'good' => '#3B82F6',
            'warning' => '#D97706',
            'danger' => '#DC2626',
        };
    }
}
