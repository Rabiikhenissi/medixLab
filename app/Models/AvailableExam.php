<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['labo_id', 'exam_id', 'price', 'is_active', 'is_archive'])]
/**
 * Pivot linking an exam to a laboratory with the price the lab charges.
 */
class AvailableExam extends Model
{
    protected $table = 'available_exams';

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
            'is_archive' => 'boolean',
        ];
    }

    /** The laboratory offering the exam. */
    public function labo()
    {
        return $this->belongsTo(Labo::class);
    }

    /** The exam being offered. */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}
