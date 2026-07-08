<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['doctor_id', 'name', 'description', 'is_archive'])]
class ExamGroup extends Model
{
    protected function casts(): array
    {
        return [
            'is_archive' => 'boolean',
        ];
    }
    protected $table = 'exam_groups';

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function items()
    {
        return $this->hasMany(ExamGroupItem::class);
    }

    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'exam_group_items');
    }
}
