<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Immutable record of a user accepting a legal policy version (RGPD consent log).
 */
class Consent extends Model
{
    public const TYPE_TERMS = 'terms';

    public const TYPE_PRIVACY = 'privacy';

    protected $fillable = [
        'user_id',
        'consent_type',
        'version',
        'ip_address',
        'user_agent',
        'accepted_at',
    ];

    protected function casts(): array
    {
        return [
            'accepted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
