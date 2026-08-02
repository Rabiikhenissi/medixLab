<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'is_archive'])]
/**
 * Platform administrator profile linked to a user account.
 */
class Admin extends Model
{
    protected function casts(): array
    {
        return [
            'is_archive' => 'boolean',
        ];
    }

    /** The user account behind this admin profile. */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
