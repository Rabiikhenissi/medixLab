<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['doctor_id', 'patient_id', 'labo_id', 'status', 'clinical_notes'])]
class ExamRequest extends Model
{
    protected $table = 'exam_requests';

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function labo()
    {
        return $this->belongsTo(Labo::class);
    }

    public function items()
    {
        return $this->hasMany(ExamRequestItem::class);
    }
}
