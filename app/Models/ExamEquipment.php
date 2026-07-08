<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['exam_id', 'equipment_id'])]
class ExamEquipment extends Model
{
    protected $table = 'exam_equipment';

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
}
