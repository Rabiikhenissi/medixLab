<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['equipment_id', 'staff_id', 'issue_type', 'description', 'start_date', 'end_date', 'status'])]
class EquipmentMaintenance extends Model
{
    protected $table = 'equipment_maintenance';

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
        ];
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
