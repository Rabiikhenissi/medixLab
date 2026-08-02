<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'code', 'route_name', 'icon', 'is_sidebar', 'order', 'view_permission', 'is_archive'])]
/**
 * A UI feature (sidebar entry) that groups a set of permission actions.
 */
class Feature extends Model
{
    protected function casts(): array
    {
        return [
            'is_archive' => 'boolean',
        ];
    }

    /** Permission actions available under this feature. */
    public function actions()
    {
        return $this->hasMany(Action::class);
    }
}
