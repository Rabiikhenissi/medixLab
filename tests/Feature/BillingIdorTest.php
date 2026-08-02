<?php

namespace Tests\Feature;

use App\Models\ExamRequest;
use App\Models\ExamRequestItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Tests\CreatesUsers;
use Tests\TestCase;

class BillingIdorTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    public function test_staff_cannot_bill_against_another_labs_exam_request(): void
    {
        $labA = $this->makeLabo('Labo A');
        $labB = $this->makeLabo('Labo B');
        $doctor = $this->makeDoctor();
        $patient = $this->makePatient();
        $staffA = $this->makeStaff($labA);

        $exam = $this->makeExam();
        $requestB = ExamRequest::create([
            'doctor_id' => $doctor['doctor']->id,
            'patient_id' => $patient['patient']->id,
            'labo_id' => $labB->id,
            'status' => 'completed',
        ]);
        $itemB = ExamRequestItem::create([
            'exam_request_id' => $requestB->id,
            'exam_id' => $exam->id,
        ]);

        $this->actingAs($staffA['user'])
            ->post(route('center.billing.store'), [
                'exam_request_id' => $requestB->id,
                'items' => [
                    [
                        'exam_request_item_id' => $itemB->id,
                        'description' => 'Glycémie',
                        'quantity' => 1,
                        'unit_price' => 20,
                        'cnam_code' => null,
                        'valeur_b' => null,
                    ],
                ],
                'notes' => 'Fraude',
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('invoices', 0);
    }

    public function test_staff_can_bill_against_own_labs_exam_request(): void
    {
        $labA = $this->makeLabo('Labo A');
        $doctor = $this->makeDoctor();
        $patient = $this->makePatient();
        $staffA = $this->makeStaff($labA);

        $exam = $this->makeExam();
        $requestA = ExamRequest::create([
            'doctor_id' => $doctor['doctor']->id,
            'patient_id' => $patient['patient']->id,
            'labo_id' => $labA->id,
            'status' => 'completed',
        ]);
        $itemA = ExamRequestItem::create([
            'exam_request_id' => $requestA->id,
            'exam_id' => $exam->id,
        ]);

        $this->actingAs($staffA['user'])
            ->post(route('center.billing.store'), [
                'exam_request_id' => $requestA->id,
                'items' => [
                    [
                        'exam_request_item_id' => $itemA->id,
                        'description' => 'Glycémie',
                        'quantity' => 1,
                        'unit_price' => 20,
                        'cnam_code' => null,
                        'valeur_b' => null,
                    ],
                ],
                'notes' => null,
            ])
            ->assertRedirect();

        $this->assertDatabaseCount('invoices', 1);
    }
}
