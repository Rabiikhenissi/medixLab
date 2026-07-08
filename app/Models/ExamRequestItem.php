<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['exam_request_id', 'exam_id', 'is_archive'])]
class ExamRequestItem extends Model
{
    protected function casts(): array
    {
        return [
            'is_archive' => 'boolean',
        ];
    }
    protected $table = 'exam_request_items';

    public function examRequest()
    {
        return $this->belongsTo(ExamRequest::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function resultLabo()
    {
        return $this->hasOne(ResultLabo::class, 'exam_request_item_id');
    }
}
