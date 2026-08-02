<?php

namespace App\Models;

use App\Models\Traits\ActiveScoped;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['labo_id', 'name', 'unit', 'quantity', 'min_quantity', 'is_archive'])]
/**
 * A stocked item a laboratory consumes when running exams.
 */
class Consumable extends Model
{
    use ActiveScoped;

    protected function casts(): array
    {
        return [
            'is_archive' => 'boolean',
        ];
    }

    /** The laboratory that owns this stock item. */
    public function labo()
    {
        return $this->belongsTo(Labo::class);
    }

    /** Quantity in/out movements for this consumable. */
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    /** Links between this consumable and exams requiring it. */
    public function examConsumables()
    {
        return $this->hasMany(ExamConsumable::class);
    }

    /** Exams that require this consumable. */
    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'exam_consumables');
    }
}
