<?php

namespace Tests\Feature;

use App\Models\Consumable;
use App\Models\DoctorPatientAccess;
use App\Models\ExamRequest;
use App\Models\Invoice;
use App\Models\ResultLabo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Tests\CreatesUsers;
use Tests\TestCase;

class EndToEndWorkflowTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    /** Grant the doctor active access to the patient. */
    protected function grantAccess(int $doctorId, int $patientId): void
    {
        DoctorPatientAccess::create([
            'doctor_id' => $doctorId,
            'patient_id' => $patientId,
            'access_status' => 'granted',
        ]);
    }

    public function test_full_prescription_to_approval_workflow(): void
    {
        // 1. Setup: doctor, patient (with access), lab, exam, staff and consumable
        $doctor = $this->makeDoctor();
        $patient = $this->makePatient();
        $this->grantAccess($doctor['doctor']->id, $patient['patient']->id);

        $labo = $this->makeLabo();
        $staff = $this->makeStaff($labo);
        $exam = $this->makeExam();

        $consumable = Consumable::create([
            'labo_id' => $labo->id,
            'name' => 'Tube EDTA',
            'unit' => 'unité',
            'quantity' => 10,
        ]);

        // 2. Doctor prescribes an exam
        $response = $this->actingAs($doctor['user'])->post(route('doctor.create-exam-request'), [
            'patient_id' => $patient['patient']->id,
            'exam_ids' => [$exam->id],
            'clinical_notes' => 'Contrôle annuel de glycémie.',
        ]);
        $response->assertOk()->assertJson(['success' => true]);

        $examRequest = ExamRequest::query()->firstOrFail();
        $this->assertSame('pending', $examRequest->status);
        $item = $examRequest->items->firstOrFail();

        // 3. Patient picks a laboratory
        $this->actingAs($patient['user'])->post(route('patient.assign-laboratory', $examRequest), [
            'labo_id' => $labo->id,
        ])->assertRedirect();

        $examRequest->refresh();
        $this->assertSame('assigned', $examRequest->status);
        $this->assertSame($labo->id, $examRequest->labo_id);

        // 4. Center staff claims the request and records the sample as collected
        $this->actingAs($staff['user'])->post(route('center.exam-requests.claim', $examRequest))
            ->assertRedirect();
        $examRequest->refresh();
        $this->assertSame('processing', $examRequest->status);

        // collect only applies to fresh assigned requests, not already processing ones
        $this->actingAs($staff['user'])->post(route('center.exam-requests.collect', $examRequest))
            ->assertRedirect()->assertSessionHas('error');
        $examRequest->refresh();
        $this->assertSame('processing', $examRequest->status);

        // 5. Center staff records a critical result (stock is deducted)
        $this->actingAs($staff['user'])->post(route('center.results.store', $item), [
            'interpretation' => 'Glycémie très élevée.',
            'parameters' => [[
                'name' => 'Glycémie',
                'value' => '2.4',
                'status' => 'critical',
                'range' => '0.7-1.1',
                'unit' => 'g/L',
            ]],
            'consumables' => [[
                'id' => $consumable->id,
                'quantity' => 2,
            ]],
        ])->assertRedirect(route('center.exam-requests'));

        $examRequest->refresh();
        $this->assertSame('completed', $examRequest->status);
        $this->assertSame(1, ResultLabo::where('exam_request_item_id', $item->id)->count());
        $this->assertSame(8, $consumable->fresh()->quantity);
        $this->assertSame(1, Invoice::where('exam_request_id', $examRequest->id)->count());

        // 6. Patient cannot see results before doctor approval
        $this->actingAs($patient['user'])->get(route('patient.exam-request-detail', $examRequest))
            ->assertForbidden();

        // 7. Doctor interprets and approves the results
        $this->actingAs($doctor['user'])->post(route('doctor.submit-interpretation', $examRequest), [
            'doctor_interpretation' => 'Résultat critique confirmé, suivi rapproché nécessaire.',
        ])->assertRedirect();

        $examRequest->refresh();
        $this->assertTrue($examRequest->approved_by_doctor);
        $this->assertNotEmpty($examRequest->doctor_interpretation);

        // 8. Center can no longer edit the approved results
        $this->actingAs($staff['user'])->get(route('center.results.create', $item))->assertRedirect();
        $this->actingAs($staff['user'])->post(route('center.results.store', $item), [
            'interpretation' => 'Modification interdite.',
            'parameters' => [[
                'name' => 'Glycémie',
                'value' => '1.0',
                'status' => 'normal',
                'range' => '0.7-1.1',
                'unit' => 'g/L',
            ]],
        ])->assertRedirect(route('center.exam-requests'));

        $this->assertSame('2.4', $examRequest->items->first()->resultLabo->details->first()->value);

        // 9. Patient can now read the validated results
        $this->actingAs($patient['user'])->get(route('patient.exam-request-detail', $examRequest))
            ->assertOk()
            ->assertJsonPath('exam_request.approved_by_doctor', true);
    }

    public function test_doctor_cannot_interpret_uncompleted_request(): void
    {
        $doctor = $this->makeDoctor();
        $patient = $this->makePatient();
        $this->grantAccess($doctor['doctor']->id, $patient['patient']->id);

        $labo = $this->makeLabo();
        $exam = $this->makeExam();

        $this->actingAs($doctor['user'])->post(route('doctor.create-exam-request'), [
            'patient_id' => $patient['patient']->id,
            'exam_ids' => [$exam->id],
        ]);

        $examRequest = ExamRequest::query()->firstOrFail();

        $this->actingAs($doctor['user'])->post(route('doctor.submit-interpretation', $examRequest), [
            'doctor_interpretation' => 'Interprétation prématurée.',
        ])->assertRedirect()->assertSessionHas('error');

        $this->assertFalse($examRequest->fresh()->approved_by_doctor);
    }
}
