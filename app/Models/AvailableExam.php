<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['labo_id', 'exam_id', 'price', 'is_active'])]
class AvailableExam extends Model
{
    protected $table = 'available_exams';

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function labo()
    {
        return $this->belongsTo(Labo::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}
