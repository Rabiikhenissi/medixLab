<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'user_id', 'role', 'action', 'entity_type', 'entity_id',
    'description', 'changes', 'ip_address', 'user_agent',
])]
/**
 * Append-only record of a sensitive action performed by a user (medico-legal trace).
 */
class AuditLog extends Model
{
    protected function casts(): array
    {
        return [
            'changes' => 'array',
        ];
    }

    /** The user who performed the action, if any (system actions have none). */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
