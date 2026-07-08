<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['exam_request_item_id', 'staff_id', 'interpretation'])]
class ResultLabo extends Model
{
    protected $table = 'result_labos';

    public function examRequestItem()
    {
        return $this->belongsTo(ExamRequestItem::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function details()
    {
        return $this->hasMany(ResultLaboDetail::class);
    }
}
