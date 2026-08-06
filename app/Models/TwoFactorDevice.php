<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'device_token', 'expires_at'])]
/**
 * A remembered device that skips the two-factor challenge until its expiry.
 */
class TwoFactorDevice extends Model
{
    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    /** The account this remembered device belongs to. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
