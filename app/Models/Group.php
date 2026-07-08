<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['name', 'code'])]
class Group extends Model
{
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function permissions()
    {
        return $this->hasMany(GroupPermission::class);
    }
}
