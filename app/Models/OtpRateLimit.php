<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpRateLimit extends Model
{
    protected $fillable = [
        'identifier',
        'type',
        'attempts',
        'blocked_until',
        'window_start',
    ];

    protected $casts = [
        'blocked_until' => 'datetime',
        'window_start' => 'datetime',
    ];
}
