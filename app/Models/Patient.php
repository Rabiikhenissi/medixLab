<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use App\Models\Traits\ActiveScoped;

#[Fillable(['user_id', 'patient_code', 'blood_group', 'date_of_birth', 'gender', 'country', 'state_code', 'is_archive'])]
class Patient extends Model
{
    use ActiveScoped;

    protected function casts(): array
    {
        return [
            'is_archive'    => 'boolean',
            'date_of_birth' => 'date',
        ];
    }

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
