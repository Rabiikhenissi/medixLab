<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['labo_id', 'name', 'unit', 'quantity', 'min_quantity'])]
class Consumable extends Model
{
    public function labo()
    {
        return $this->belongsTo(Labo::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function examConsumables()
    {
        return $this->hasMany(ExamConsumable::class);
    }

    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'exam_consumables');
    }
}
