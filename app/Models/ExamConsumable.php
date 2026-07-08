<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['exam_id', 'consumable_id', 'quantity_needed'])]
class ExamConsumable extends Model
{
    protected $table = 'exam_consumables';

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function consumable()
    {
        return $this->belongsTo(Consumable::class);
    }
}
