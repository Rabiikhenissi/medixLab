<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['user_id', 'laboratory_id', 'staff_code'])]
class Staff extends Model
{
    protected $table = 'staff';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function laboratory()
    {
        return $this->belongsTo(Labo::class, 'laboratory_id');
    }

    public function equipmentMaintenances()
    {
        return $this->hasMany(EquipmentMaintenance::class);
    }

    public function resultLabos()
    {
        return $this->hasMany(ResultLabo::class);
    }
}
