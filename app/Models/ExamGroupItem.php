<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['exam_group_id', 'exam_id', 'is_archive'])]
/**
 * A single exam line within an exam group.
 */
class ExamGroupItem extends Model
{
    protected function casts(): array
    {
        return [
            'is_archive' => 'boolean',
        ];
    }

    protected $table = 'exam_group_items';

    /** The exam group this line belongs to. */
    public function examGroup()
    {
        return $this->belongsTo(ExamGroup::class);
    }

    /** The exam included in the group. */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}
