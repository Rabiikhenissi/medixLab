<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['code', 'name', 'category', 'description', 'default_normal_range', 'preparation_instructions'])]
class Exam extends Model
{
    public function availableExams()
    {
        return $this->hasMany(AvailableExam::class);
    }

    public function examConsumables()
    {
        return $this->hasMany(ExamConsumable::class);
    }

    public function examEquipment()
    {
        return $this->hasMany(ExamEquipment::class);
    }

    public function examGroupItems()
    {
        return $this->hasMany(ExamGroupItem::class);
    }

    public function examRequestItems()
    {
        return $this->hasMany(ExamRequestItem::class);
    }
}
