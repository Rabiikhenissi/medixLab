<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for creating a new exam request from a doctor.
 */
class CreateExamRequestFormRequest extends FormRequest
{
    /** Only authenticated doctors may create exam requests. */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->doctor !== null;
    }

    public function rules(): array
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'exam_ids' => 'required|array|min:1',
            'exam_ids.*' => 'exists:exams,id',
            'clinical_notes' => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'patient_id.required' => 'Un patient doit être sélectionné.',
            'exam_ids.required' => 'Veuillez sélectionner au moins un examen.',
            'exam_ids.min' => 'Veuillez sélectionner au moins un examen.',
        ];
    }
}
