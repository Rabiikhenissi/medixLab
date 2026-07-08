<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['name', 'code'])]
class Feature extends Model
{
    public function actions()
    {
        return $this->hasMany(Action::class);
    }
}
