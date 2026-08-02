<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'code', 'is_archive'])]
/**
 * A permission group that bundles users and grants them a set of actions.
 */
class Group extends Model
{
    protected function casts(): array
    {
        return [
            'is_archive' => 'boolean',
        ];
    }

    /** Users assigned to this group. */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /** Permission links between this group and actions. */
    public function permissions()
    {
        return $this->hasMany(GroupPermission::class);
    }

    /** Actions granted to this group through the permissions pivot. */
    public function actions()
    {
        return $this->belongsToMany(Action::class, 'group_permissions', 'group_id', 'action_id')->withTimestamps();
    }
}
