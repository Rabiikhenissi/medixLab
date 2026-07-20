<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use App\Models\Traits\ActiveScoped;

#[Fillable(['user_id', 'title', 'message', 'is_read', 'is_archive', 'notification_type', 'reference_id'])]
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
