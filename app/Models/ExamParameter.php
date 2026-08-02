<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'exam_id',
    'name',
    'unit',
    'normal_range',
    'is_archive',
])]
/**
 * A named measurable parameter of an exam with its normal range and unit.
 */
class ExamParameter extends Model
{
    protected function casts(): array
    {
        return [
            'is_archive' => 'boolean',
        ];
    }

    /** The exam this parameter belongs to. */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}
