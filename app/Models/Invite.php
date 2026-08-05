<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A single-use invitation emailed to a new account (admin-driven onboarding).
 */
class Invite extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_ACCEPTED = 'accepted';

    public const STATUS_REVOKED = 'revoked';

    protected $fillable = [
        'email',
        'token',
        'group_id',
        'laboratory_id',
        'invited_by',
        'first_name',
        'last_name',
        'status',
        'expires_at',
        'accepted_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'accepted_at' => 'datetime',
        ];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function laboratory(): BelongsTo
    {
        return $this->belongsTo(Labo::class, 'laboratory_id');
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    /** Whether the invite is still pending (not accepted or revoked). */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /** Whether the invite link is expired. */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /** Whether the invite can still be used to create an account. */
    public function isUsable(): bool
    {
        return $this->isPending() && ! $this->isExpired();
    }

    /** The role label shown to the invited person (from the group code). */
    public function roleLabel(): string
    {
        return match ($this->group->code ?? '') {
            'admin' => 'administrateur',
            'doctor' => 'médecin',
            'patient' => 'patient',
            'center' => 'centre médical',
            default => 'utilisateur',
        };
    }
}
