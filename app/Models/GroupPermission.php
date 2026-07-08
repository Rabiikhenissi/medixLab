<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['group_id', 'action_id'])]
class GroupPermission extends Model
{
    protected $table = 'group_permissions';

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function action()
    {
        return $this->belongsTo(Action::class);
    }
}
