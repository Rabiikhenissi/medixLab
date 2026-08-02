<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'laboratory_id', 'staff_code', 'is_archive'])]
/**
 * Center staff profile linked to a user account and a laboratory.
 */
class Staff extends Model
{
    protected function casts(): array
    {
        return [
            'is_archive' => 'boolean',
        ];
    }

    protected $table = 'staff';

    /** The user account behind this staff profile. */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** The laboratory this staff member belongs to. */
    public function laboratory()
    {
        return $this->belongsTo(Labo::class, 'laboratory_id');
    }

    /** Maintenance actions recorded by this staff member. */
    public function equipmentMaintenances()
    {
        return $this->hasMany(EquipmentMaintenance::class);
    }

    /** Lab results recorded by this staff member. */
    public function resultLabos()
    {
        return $this->hasMany(ResultLabo::class);
    }
}
