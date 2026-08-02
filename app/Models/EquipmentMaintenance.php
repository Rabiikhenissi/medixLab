<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['equipment_id', 'staff_id', 'issue_type', 'description', 'start_date', 'end_date', 'status', 'is_archive'])]
/**
 * A maintenance or repair operation recorded for a piece of equipment.
 */
class EquipmentMaintenance extends Model
{
    protected $table = 'equipment_maintenance';

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'is_archive' => 'boolean',
        ];
    }

    /** The equipment being maintained. */
    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    /** The staff member who handled the maintenance. */
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
