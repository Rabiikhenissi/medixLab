<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['feature_id', 'code', 'name', 'is_archive'])]
/**
 * A granular permission action (e.g. create-exams) grouped under a feature.
 */
class Action extends Model
{
    protected function casts(): array
    {
        return [
            'is_archive' => 'boolean',
        ];
    }

    /** The feature this action belongs to. */
    public function feature()
    {
        return $this->belongsTo(Feature::class);
    }

    /** Permission links granting this action to groups. */
    public function groupPermissions()
    {
        return $this->hasMany(GroupPermission::class);
    }

    /** Groups granted this action through the permissions pivot. */
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_permissions', 'action_id', 'group_id')->withTimestamps();
    }
}
