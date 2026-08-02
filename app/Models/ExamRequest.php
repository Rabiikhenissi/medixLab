<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use App\Models\Traits\ActiveScoped;

#[Fillable(['doctor_id', 'patient_id', 'labo_id', 'status', 'clinical_notes', 'doctor_interpretation', 'approved_by_doctor', 'is_archive'])]
class ExamRequest extends Model
{
    use ActiveScoped;
    protected function casts(): array
    {
        return [
            'is_archive' => 'boolean',
            'approved_by_doctor' => 'boolean',
        ];
    }
    protected $table = 'exam_requests';

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function laboratory()
    {
        return $this->belongsTo(Labo::class, 'labo_id');
    }

    public function items()
    {
        return $this->hasMany(ExamRequestItem::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}