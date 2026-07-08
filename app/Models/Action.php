<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['feature_id', 'code', 'name'])]
class Action extends Model
{
    public function feature()
    {
        return $this->belongsTo(Feature::class);
    }

    public function groupPermissions()
    {
        return $this->hasMany(GroupPermission::class);
    }
}
