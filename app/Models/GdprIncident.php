<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Register of security incidents and personal-data breaches (RGPD art. 33/34).
 */
class GdprIncident extends Model
{
    protected $fillable = [
        'incident_type',
        'severity',
        'description',
        'affected_users_count',
        'detected_at',
        'notified_authority_at',
        'notified_affected_at',
        'status',
        'resolution',
    ];

    protected function casts(): array
    {
        return [
            'affected_users_count' => 'integer',
            'detected_at' => 'datetime',
            'notified_authority_at' => 'datetime',
            'notified_affected_at' => 'datetime',
        ];
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }
}
