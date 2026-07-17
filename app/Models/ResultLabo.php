<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['exam_request_item_id', 'staff_id', 'interpretation', 'is_archive'])]
class ResultLabo extends Model
{
    protected function casts(): array
    {
        return [
            'is_archive' => 'boolean',
        ];
    }
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

    public function consumables()
    {
        return $this->belongsToMany(Consumable::class, 'result_consumables')
            ->withPivot('quantity_used')
            ->withTimestamps();
    }

    public function equipment()
    {
        return $this->belongsToMany(Equipment::class, 'result_equipment')
            ->withTimestamps();
    }
}
