<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['exam_request_item_id', 'staff_id', 'interpretation', 'is_archive'])]
/**
 * Laboratory result for a single exam request item, with the staff who validated it.
 */
class ResultLabo extends Model
{
    protected function casts(): array
    {
        return [
            'is_archive' => 'boolean',
        ];
    }

    protected $table = 'result_labos';

    /** The exam request item this result belongs to. */
    public function examRequestItem()
    {
        return $this->belongsTo(ExamRequestItem::class);
    }

    /** The staff member who entered the result. */
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    /** Per-parameter measured values for this result. */
    public function details()
    {
        return $this->hasMany(ResultLaboDetail::class);
    }

    /** Consumables consumed while producing this result, with quantities. */
    public function consumables()
    {
        return $this->belongsToMany(Consumable::class, 'result_consumables')
            ->withPivot('quantity_used')
            ->withTimestamps();
    }

    /** Equipment used to produce this result. */
    public function equipment()
    {
        return $this->belongsToMany(Equipment::class, 'result_equipment')
            ->withTimestamps();
    }
}
