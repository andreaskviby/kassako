<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FortnoxOrder extends Model
{
    protected $fillable = [
        'team_id',
        'fortnox_id',
        'document_number',
        'customer_name',
        'customer_number',
        'total',
        'currency',
        'order_date',
        'delivery_date',
        'status',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'order_date' => 'date',
        'delivery_date' => 'date',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
