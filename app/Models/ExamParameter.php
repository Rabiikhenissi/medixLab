<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;


#[Fillable([
    'exam_id',
    'name',
    'unit',
    'normal_range',
    'is_archive'
])]
class ExamParameter extends Model
{

    protected function casts(): array
    {
        return [
            'is_archive' => 'boolean',
        ];
    }


    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

}