<?php

namespace App\Models;

use App\Models\Traits\ActiveScoped;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'patient_code', 'blood_group', 'date_of_birth', 'gender', 'country', 'state_code', 'latitude', 'longitude', 'is_archive'])]
class Patient extends Model
{
    use ActiveScoped;

    protected function casts(): array
    {
        return [
            'is_archive' => 'boolean',
            'date_of_birth' => 'date',
        ];
    }

    /** The user account behind this patient profile. */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Access grants issued by doctors for this patient. */
    public function doctorAccesses()
    {
        return $this->hasMany(DoctorPatientAccess::class);
    }

    /** Doctors granted access to this patient. */
    public function doctors()
    {
        return $this->belongsToMany(Doctor::class, 'doctor_patient_access');
    }

    /** Exam requests prescribed for this patient. */
    public function examRequests()
    {
        return $this->hasMany(ExamRequest::class);
    }

    /** The currently active CNAM insurance affiliation for this patient. */
    public function cnamAffiliation()
    {
        return $this->hasOne(CnamAffiliation::class)->where('is_active', true);
    }

    /** Invoices billed to this patient. */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /** Biological samples collected from this patient. */
    public function samples()
    {
        return $this->hasMany(Sample::class);
    }
}
