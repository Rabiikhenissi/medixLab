<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['user_id', 'speciality', 'doctor_code'])]
class Doctor extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function patientAccesses()
    {
        return $this->hasMany(DoctorPatientAccess::class);
    }

    public function patients()
    {
        return $this->belongsToMany(Patient::class, 'doctor_patient_access');
    }

    public function examGroups()
    {
        return $this->hasMany(ExamGroup::class);
    }

    public function examRequests()
    {
        return $this->hasMany(ExamRequest::class);
    }
}
