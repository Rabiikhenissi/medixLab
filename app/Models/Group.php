<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['name', 'code', 'is_archive'])]
class Group extends Model
{
    protected function casts(): array
    {
        return [
            'is_archive' => 'boolean',
        ];
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function permissions()
    {
        return $this->hasMany(GroupPermission::class);
    }

    public function actions()
    {
        return $this->belongsToMany(Action::class, 'group_permissions', 'group_id', 'action_id')->withTimestamps();
    }
}
