<?php

namespace App\Models;

use App\Models\Traits\ActiveScoped;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['labo_id', 'name', 'type', 'serial_number', 'status', 'is_archive'])]
/**
 * A piece of laboratory equipment, tracked with its operational status.
 */
class Equipment extends Model
{
    use ActiveScoped;

    protected function casts(): array
    {
        return [
            'is_archive' => 'boolean',
        ];
    }

    protected $table = 'equipment';

    /** The laboratory that owns this equipment. */
    public function labo()
    {
        return $this->belongsTo(Labo::class);
    }

    /** Maintenance records for this equipment. */
    public function maintenances()
    {
        return $this->hasMany(EquipmentMaintenance::class);
    }

    /** Links between this equipment and exams requiring it. */
    public function examEquipment()
    {
        return $this->hasMany(ExamEquipment::class);
    }

    /** Exams that require this equipment. */
    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'exam_equipment');
    }
}
