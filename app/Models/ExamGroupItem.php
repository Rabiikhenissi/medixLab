<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['exam_group_id', 'exam_id', 'is_archive'])]
class ExamGroupItem extends Model
{
    protected function casts(): array
    {
        return [
            'is_archive' => 'boolean',
        ];
    }
    protected $table = 'exam_group_items';

    public function examGroup()
    {
        return $this->belongsTo(ExamGroup::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}
