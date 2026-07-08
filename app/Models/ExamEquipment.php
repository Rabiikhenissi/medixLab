<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['exam_id', 'equipment_id', 'is_archive'])]
class ExamEquipment extends Model
{
    protected function casts(): array
    {
        return [
            'is_archive' => 'boolean',
        ];
    }
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
