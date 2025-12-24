<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerPaymentPattern extends Model
{
    protected $fillable = [
        'team_id',
        'customer_number',
        'customer_name',
        'total_invoices',
        'paid_invoices',
        'avg_days_to_pay',
        'median_days_to_pay',
        'total_revenue',
        'revenue_percentage',
        'payment_reliability',
    ];

    protected $casts = [
        'total_revenue' => 'decimal:2',
        'revenue_percentage' => 'decimal:2',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
