<?php

namespace App\Models;

use App\Models\Traits\ActiveScoped;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['code', 'name', 'category', 'description', 'default_normal_range', 'preparation_instructions', 'is_archive'])]
/**
 * A catalog test/analysis a laboratory can run for a patient.
 */
class Exam extends Model
{
    use ActiveScoped;

    protected function casts(): array
    {
        return [
            'is_archive' => 'boolean',
        ];
    }

    /** Pricings per laboratory offering this exam. */
    public function availableExams()
    {
        return $this->hasMany(AvailableExam::class);
    }

    /** Consumables required to run this exam. */
    public function examConsumables()
    {
        return $this->hasMany(ExamConsumable::class);
    }

    /** Equipment required to run this exam. */
    public function examEquipment()
    {
        return $this->hasMany(ExamEquipment::class);
    }

    /** Line items of exam groups containing this exam. */
    public function examGroupItems()
    {
        return $this->hasMany(ExamGroupItem::class);
    }

    /** Line items of exam requests containing this exam. */
    public function examRequestItems()
    {
        return $this->hasMany(ExamRequestItem::class);
    }

    /** Measurable parameters defined for this exam. */
    public function parameters()
    {
        return $this->hasMany(ExamParameter::class);
    }
}
