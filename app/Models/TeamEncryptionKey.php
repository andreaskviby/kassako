<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Team Encryption Key Model
 *
 * Stores the wrapped (encrypted) Data Encryption Key for each team.
 * The DEK is wrapped using the team's Master Encryption Key (MEK),
 * which is derived from the user's passphrase and NEVER stored.
 *
 * Security note: While this model stores encryption-related data,
 * all sensitive cryptographic material is encrypted. The passphrase
 * and derived keys are never persisted to the database.
 */
class TeamEncryptionKey extends Model
{
    protected $fillable = [
        'team_id',
        'wrapped_dek',
        'key_salt',
        'verification_hash',
        'verification_salt',
        'wrap_iv',
        'wrap_auth_tag',
        'key_version',
        'last_accessed_at',
        'rotated_at',
    ];

    protected $hidden = [
        'wrapped_dek',
        'key_salt',
        'verification_hash',
        'verification_salt',
        'wrap_iv',
        'wrap_auth_tag',
    ];

    protected function casts(): array
    {
        return [
            'key_version' => 'integer',
            'last_accessed_at' => 'datetime',
            'rotated_at' => 'datetime',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
