<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['doctor_id', 'name', 'description'])]
class ExamGroup extends Model
{
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
