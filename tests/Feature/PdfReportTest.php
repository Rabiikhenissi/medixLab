<?php

namespace Tests\Feature;

use App\Models\ExamRequest;
use App\Models\ExamRequestItem;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesUsers;
use Tests\TestCase;

class PdfReportTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    /** Build a completed, doctor-approved prescription for a given doctor/patient. */
    protected function makeRequestFor(int $doctorId, int $patientId): ExamRequest
    {
        $labo = $this->makeLabo();
        $exam = $this->makeExam();

        $examRequest = ExamRequest::create([
            'doctor_id' => $doctorId,
            'patient_id' => $patientId,
            'labo_id' => $labo->id,
            'status' => 'completed',
            'approved_by_doctor' => true,
            'clinical_notes' => 'Bilan de routine',
        ]);

        ExamRequestItem::create([
            'exam_request_id' => $examRequest->id,
            'exam_id' => $exam->id,
        ]);

        return $examRequest;
    }

    /** Build a completed, doctor-approved prescription with a result. */
    protected function makeCompletedRequest(): ExamRequest
    {
        $doctor = $this->makeDoctor();
        $patient = $this->makePatient();

        return $this->makeRequestFor($doctor['doctor']->id, $patient['patient']->id);
    }

    public function test_doctor_can_download_server_side_pdf(): void
    {
        $doctor = $this->makeDoctor();
        $patient = $this->makePatient();
        $examRequest = $this->makeRequestFor($doctor['doctor']->id, $patient['patient']->id);

        $this->actingAs($doctor['user'])
            ->get(route('doctor.print-exam-request', $examRequest).'?pdf=1')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf')
            ->assertHeader('Content-Disposition', 'attachment; filename=rapport-medix-'.$examRequest->id.'.pdf');
    }

    public function test_owning_patient_can_download_server_side_pdf(): void
    {
        $examRequest = $this->makeCompletedRequest();
        $patient = Patient::find($examRequest->patient_id);

        $this->actingAs($patient->user)
            ->get(route('patient.print-exam-request', $examRequest).'?pdf=1')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_patient_cannot_download_pdf_before_completion(): void
    {
        $doctor = $this->makeDoctor();
        $patient = $this->makePatient();
        $labo = $this->makeLabo();
        $exam = $this->makeExam();

        $examRequest = ExamRequest::create([
            'doctor_id' => $doctor['doctor']->id,
            'patient_id' => $patient['patient']->id,
            'labo_id' => $labo->id,
            'status' => 'pending',
            'approved_by_doctor' => false,
        ]);

        $this->actingAs($patient['user'])
            ->get(route('patient.print-exam-request', $examRequest).'?pdf=1')
            ->assertRedirect();
    }

    public function test_other_doctor_cannot_download_pdf(): void
    {
        $owner = $this->makeDoctor();
        $other = $this->makeDoctor();
        $patient = $this->makePatient();
        $labo = $this->makeLabo();
        $exam = $this->makeExam();

        $examRequest = ExamRequest::create([
            'doctor_id' => $owner['doctor']->id,
            'patient_id' => $patient['patient']->id,
            'labo_id' => $labo->id,
            'status' => 'completed',
            'approved_by_doctor' => true,
        ]);

        $this->actingAs($other['user'])
            ->get(route('doctor.print-exam-request', $examRequest).'?pdf=1')
            ->assertForbidden();
    }
}
