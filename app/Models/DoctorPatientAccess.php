<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['doctor_id', 'patient_id', 'access_status', 'is_archive'])]
/**
 * Access grant linking a doctor to a patient.
 *
 * Access is permanent: once granted it only ends when the patient blocks
 * the doctor (access_status = 'blocked') or revokes it ('revoked').
 */
class DoctorPatientAccess extends Model
{
    protected function casts(): array
    {
        return [
            'is_archive' => 'boolean',
        ];
    }

    protected $table = 'doctor_patient_access';

    /**
     * Scope: only currently granted accesses (the patient block is the only
     * way a doctor loses access).
     */
    public function scopeActive($query)
    {
        return $query->where('access_status', 'granted');
    }

    /** Scope: only accesses that are not blocked. */
    public function scopeNotBlocked($query)
    {
        return $query->where('access_status', '!=', 'blocked');
    }

    /** The doctor granted access. */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    /** The patient whose data is shared. */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
