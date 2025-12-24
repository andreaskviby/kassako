<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FortnoxInvoice extends Model
{
    protected $fillable = [
        'team_id',
        'fortnox_id',
        'document_number',
        'customer_name',
        'customer_number',
        'total',
        'total_vat',
        'currency',
        'invoice_date',
        'due_date',
        'paid_date',
        'status',
        'days_overdue',
        'is_credit',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'total_vat' => 'decimal:2',
        'invoice_date' => 'date',
        'due_date' => 'date',
        'paid_date' => 'date',
        'is_credit' => 'boolean',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
