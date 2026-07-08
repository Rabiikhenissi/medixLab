<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['labo_id', 'name', 'type', 'serial_number', 'status', 'is_archive'])]
class Equipment extends Model
{
    protected function casts(): array
    {
        return [
            'is_archive' => 'boolean',
        ];
    }
    protected $table = 'equipment';

    public function labo()
    {
        return $this->belongsTo(Labo::class);
    }

    public function maintenances()
    {
        return $this->hasMany(EquipmentMaintenance::class);
    }

    public function examEquipment()
    {
        return $this->hasMany(ExamEquipment::class);
    }

    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'exam_equipment');
    }
}
