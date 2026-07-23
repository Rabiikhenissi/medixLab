<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['sender_id', 'receiver_id', 'message', 'is_read', 'is_archive'])]
class ChatMessage extends Model
{
    use \App\Models\Traits\ActiveScoped;

    protected $table = 'chat_messages';

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'is_archive' => 'boolean',
        ];
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
