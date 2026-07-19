<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use App\Models\Traits\ActiveScoped;

#[Fillable(['code', 'name', 'category', 'description', 'default_normal_range', 'preparation_instructions', 'is_archive'])]
class Exam extends Model
{
    use ActiveScoped;

protected function casts(): array
{
    return [
        'is_archive'=>'boolean',
    ];
}


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


public function parameters()
{
    return $this->hasMany(ExamParameter::class);
}

}