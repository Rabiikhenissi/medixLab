<?php

namespace App\Models;

use App\Models\Traits\ActiveScoped;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['sender_id', 'receiver_id', 'message', 'is_read', 'is_archive'])]
/**
 * One-to-one message between a doctor and a patient.
 */
class ChatMessage extends Model
{
    use ActiveScoped;

    protected $table = 'chat_messages';

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'is_archive' => 'boolean',
        ];
    }

    /** The user who sent the message. */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /** The user the message was sent to. */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
