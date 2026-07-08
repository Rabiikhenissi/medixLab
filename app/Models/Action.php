<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['feature_id', 'code', 'name', 'is_archive'])]
class Action extends Model
{
    protected function casts(): array
    {
        return [
            'is_archive' => 'boolean',
        ];
    }
    public function feature()
    {
        return $this->belongsTo(Feature::class);
    }

    public function groupPermissions()
    {
        return $this->hasMany(GroupPermission::class);
    }
}
