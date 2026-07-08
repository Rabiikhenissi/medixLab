<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['user_id', 'patient_code'])]
class Patient extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function doctorAccesses()
    {
        return $this->hasMany(DoctorPatientAccess::class);
    }

    public function doctors()
    {
        return $this->belongsToMany(Doctor::class, 'doctor_patient_access');
    }

    public function examRequests()
    {
        return $this->hasMany(ExamRequest::class);
    }
}
