<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['doctor_id', 'name', 'description', 'is_archive'])]
/**
 * A reusable set of exams a doctor saves to speed up future prescriptions.
 */
class ExamGroup extends Model
{
    protected function casts(): array
    {
        return [
            'is_archive' => 'boolean',
        ];
    }

    protected $table = 'exam_groups';

    /** The doctor who owns this group. */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    /** The exams contained in this group. */
    public function items()
    {
        return $this->hasMany(ExamGroupItem::class);
    }

    /** Exams linked through the group items pivot. */
    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'exam_group_items');
    }
}
