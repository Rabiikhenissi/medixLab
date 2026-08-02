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
}
