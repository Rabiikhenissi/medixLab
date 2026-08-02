<?php

namespace App\Models;

use App\Models\Traits\ActiveScoped;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'speciality', 'doctor_code', 'latitude', 'longitude', 'is_archive'])]
/**
 * Doctor profile linked to a user account, with its own exams and patient access grants.
 */
class Doctor extends Model
{
    use ActiveScoped;

    protected function casts(): array
    {
        return [
            'is_archive' => 'boolean',
        ];
    }

    /** The user account behind this doctor profile. */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Access grants issued by this doctor to patients. */
    public function patientAccesses()
    {
        return $this->hasMany(DoctorPatientAccess::class);
    }

    /** Patients this doctor has been granted access to. */
    public function patients()
    {
        return $this->belongsToMany(Patient::class, 'doctor_patient_access');
    }

    /** Reusable exam groups created by this doctor. */
    public function examGroups()
    {
        return $this->hasMany(ExamGroup::class);
    }

    /** Exam requests prescribed by this doctor. */
    public function examRequests()
    {
        return $this->hasMany(ExamRequest::class);
    }
}
