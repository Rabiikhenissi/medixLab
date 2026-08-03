<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['result_labo_id', 'parameter', 'value', 'status', 'reference_range', 'unit', 'is_archive'])]
/**
 * One measured parameter value within a lab result.
 */
class ResultLaboDetail extends Model
{
    protected function casts(): array
    {
        return [
            'is_archive' => 'boolean',
        ];
    }

    protected $table = 'result_labo_details';

    /** The lab result this parameter belongs to. */
    public function resultLabo()
    {
        return $this->belongsTo(ResultLabo::class);
    }

    /** True when this value is flagged as a critical value. */
    public function isCritical(): bool
    {
        return $this->status === 'critical';
    }

    /** True when the value is not within its normal range. */
    public function isAbnormal(): bool
    {
        return in_array($this->status, ['high', 'low', 'critical'], true);
    }
}
