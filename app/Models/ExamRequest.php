<?php

namespace App\Models;

use App\Models\Traits\ActiveScoped;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['doctor_id', 'patient_id', 'labo_id', 'status', 'clinical_notes', 'doctor_interpretation', 'approved_by_doctor', 'is_archive'])]
/**
 * A prescription from a doctor to a laboratory for one or more exams for a patient.
 */
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

    /** The doctor who prescribed this request. */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    /** The patient this request concerns. */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /** The laboratory assigned to run this request. */
    public function laboratory()
    {
        return $this->belongsTo(Labo::class, 'labo_id');
    }

    /** Individual exams ordered within this request. */
    public function items()
    {
        return $this->hasMany(ExamRequestItem::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
