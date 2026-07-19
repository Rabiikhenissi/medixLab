<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use App\Models\Traits\ActiveScoped;

#[Fillable(['labo_id', 'name', 'unit', 'quantity', 'min_quantity', 'is_archive'])]
class Consumable extends Model
{
    use ActiveScoped;
    protected function casts(): array
    {
        return [
            'is_archive' => 'boolean',
        ];
    }
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
