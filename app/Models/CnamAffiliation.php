<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A patient's insurance affiliation with CNAM and its reimbursement rate.
 */
class CnamAffiliation extends Model
{
    protected $fillable = ['patient_id', 'cnam_number', 'affiliation_number', 'cnam_rate_id', 'valid_until', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'valid_until' => 'date'];
    }

    /** The affiliated patient. */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /** The reimbursement rate applied to this affiliation. */
    public function rate()
    {
        return $this->belongsTo(CnamRate::class, 'cnam_rate_id');
    }
}
