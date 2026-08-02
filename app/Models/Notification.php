<?php

namespace App\Models;

use App\Models\Traits\ActiveScoped;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'title', 'message', 'is_read', 'is_archive', 'notification_type', 'reference_id'])]
/**
 * In-app notification delivered to a user's account.
 */
class Notification extends Model
{
    use ActiveScoped;

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'is_archive' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /** The user the notification is addressed to. */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get user notifications
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId)
            ->where('is_archive', false);
    }

    /**
     * Scope to get unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
}
