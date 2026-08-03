<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'exam_id',
    'name',
    'unit',
    'normal_range',
    'critical_low',
    'critical_high',
    'is_archive',
])]
/**
 * A named measurable parameter of an exam with its normal range and unit.
 */
class ExamParameter extends Model
{
    protected function casts(): array
    {
        return [
            'is_archive' => 'boolean',
            'critical_low' => 'decimal:3',
            'critical_high' => 'decimal:3',
        ];
    }

    /** The exam this parameter belongs to. */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    /** True when both critical thresholds are defined. */
    public function hasCriticalThresholds(): bool
    {
        return $this->critical_low !== null && $this->critical_high !== null;
    }

    /** True when the given numeric value falls outside the critical thresholds. */
    public function isCriticalValue(float $value): bool
    {
        if (! $this->hasCriticalThresholds()) {
            return false;
        }

        return $value < (float) $this->critical_low || $value > (float) $this->critical_high;
    }
}
