<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['user_id', 'title', 'message', 'is_read', 'is_archive'])]
class Notification extends Model
{
    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'is_archive' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
