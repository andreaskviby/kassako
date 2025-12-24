<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FortnoxSupplierInvoice extends Model
{
    protected $fillable = [
        'team_id',
        'fortnox_id',
        'document_number',
        'supplier_name',
        'supplier_number',
        'total',
        'currency',
        'invoice_date',
        'due_date',
        'paid_date',
        'status',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'invoice_date' => 'date',
        'due_date' => 'date',
        'paid_date' => 'date',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
