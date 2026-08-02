<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\ExamRequest;
use App\Models\ExamRequestItem;
use App\Models\Labo;
use App\Models\Patient;
use App\Models\ResultLabo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Tests\CreatesUsers;
use Tests\TestCase;

class ResultIdorTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    private function makeLabRequest(Labo $labo, Doctor $doctor, Patient $patient): array
    {
        $exam = $this->makeExam();
        $request = ExamRequest::create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'labo_id' => $labo->id,
            'status' => 'collected',
        ]);
        $item = ExamRequestItem::create([
            'exam_request_id' => $request->id,
            'exam_id' => $exam->id,
        ]);

        return [$request, $item];
    }

    public function test_staff_cannot_access_results_of_another_lab_item(): void
    {
        $labA = $this->makeLabo('Labo A');
        $labB = $this->makeLabo('Labo B');
        $doctor = $this->makeDoctor();
        $patient = $this->makePatient();
        $staffA = $this->makeStaff($labA);

        [, $itemB] = $this->makeLabRequest($labB, $doctor['doctor'], $patient['patient']);

        $this->actingAs($staffA['user'])
            ->get(route('center.results.create', $itemB->id))
            ->assertForbidden();
    }

    public function test_staff_can_access_results_of_own_lab_item(): void
    {
        $labA = $this->makeLabo('Labo A');
        $doctor = $this->makeDoctor();
        $patient = $this->makePatient();
        $staffA = $this->makeStaff($labA);

        [, $itemA] = $this->makeLabRequest($labA, $doctor['doctor'], $patient['patient']);

        $this->actingAs($staffA['user'])
            ->get(route('center.results.create', $itemA->id))
            ->assertOk();
    }

    public function test_staff_cannot_edit_results_of_another_lab(): void
    {
        $labA = $this->makeLabo('Labo A');
        $labB = $this->makeLabo('Labo B');
        $doctor = $this->makeDoctor();
        $patient = $this->makePatient();
        $staffA = $this->makeStaff($labA);
        $staffB = $this->makeStaff($labB);

        [$reqB, $itemB] = $this->makeLabRequest($labB, $doctor['doctor'], $patient['patient']);

        $resultB = ResultLabo::create([
            'exam_request_item_id' => $itemB->id,
            'staff_id' => $staffB['staff']->id,
            'interpretation' => null,
            'is_archive' => false,
        ]);

        $this->actingAs($staffA['user'])
            ->get(route('center.results.edit', $resultB->id))
            ->assertForbidden();
    }

    public function test_staff_cannot_submit_results_for_another_lab(): void
    {
        $labA = $this->makeLabo('Labo A');
        $labB = $this->makeLabo('Labo B');
        $doctor = $this->makeDoctor();
        $patient = $this->makePatient();
        $staffA = $this->makeStaff($labA);

        [, $itemB] = $this->makeLabRequest($labB, $doctor['doctor'], $patient['patient']);

        $this->actingAs($staffA['user'])
            ->post(route('center.results.store', $itemB->id), [
                'interpretation' => 'test',
                'parameters' => [
                    ['name' => 'Glycémie', 'value' => '0.9', 'status' => 'normal', 'range' => '0.7-1.1', 'unit' => 'g/L'],
                ],
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('result_labos', [
            'exam_request_item_id' => $itemB->id,
        ]);
    }
}
