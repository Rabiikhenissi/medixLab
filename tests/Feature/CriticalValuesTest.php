<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\ExamParameter;
use App\Models\ExamRequest;
use App\Models\ExamRequestItem;
use App\Models\Group;
use App\Models\ResultLabo;
use App\Models\ResultLaboDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Tests\CreatesUsers;
use Tests\TestCase;

class CriticalValuesTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    /** Build an admin account (no permissions needed for exam CRUD). */
    protected function makeAdmin(): User
    {
        $group = Group::create(['code' => 'admin', 'name' => 'Admin']);
        $user = $this->makeUser(['group_id' => $group->id]);
        Admin::create(['user_id' => $user->id]);
        cache()->forget("group_permissions_{$group->id}");

        return $user;
    }

    public function test_exam_parameter_flags_critical_values_from_thresholds(): void
    {
        $exam = $this->makeExam();

        $parameter = ExamParameter::create([
            'exam_id' => $exam->id,
            'name' => 'Glycemie',
            'unit' => 'g/L',
            'normal_range' => '0.70 - 1.10',
            'critical_low' => 0.400,
            'critical_high' => 3.000,
        ]);

        $this->assertTrue($parameter->hasCriticalThresholds());
        $this->assertFalse($parameter->isCriticalValue(0.90));
        $this->assertTrue($parameter->isCriticalValue(3.50));
        $this->assertTrue($parameter->isCriticalValue(0.20));
        $this->assertFalse($parameter->isCriticalValue(1.20));
    }

    public function test_exam_parameter_without_thresholds_never_flags_critical(): void
    {
        $exam = $this->makeExam();

        $parameter = ExamParameter::create([
            'exam_id' => $exam->id,
            'name' => 'CRP',
            'unit' => 'mg/L',
            'normal_range' => '0 - 6',
        ]);

        $this->assertFalse($parameter->hasCriticalThresholds());
        $this->assertFalse($parameter->isCriticalValue(9999));
    }

    public function test_admin_can_create_exam_with_critical_thresholds(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)
            ->post(route('admin.exams.store'), [
                'code' => 'GLYC-CRIT',
                'name' => 'Glycemie critique',
                'category' => 'biochemistry',
                'parameters' => [
                    [
                        'name' => 'Glycemie',
                        'unit' => 'g/L',
                        'normal_range' => '0.70 - 1.10',
                        'critical_low' => '0.400',
                        'critical_high' => '3.000',
                    ],
                ],
            ])
            ->assertRedirect();

        $parameter = ExamParameter::whereHas('exam', fn ($q) => $q->where('code', 'GLYC-CRIT'))->firstOrFail();

        $this->assertSame('0.400', (string) $parameter->critical_low);
        $this->assertSame('3.000', (string) $parameter->critical_high);
    }

    public function test_result_store_accepts_critical_status(): void
    {
        $labo = $this->makeLabo();
        $staff = $this->makeStaff($labo);
        $exam = $this->makeExam();
        $patient = $this->makePatient();
        $doctor = $this->makeDoctor();

        $examRequest = ExamRequest::create([
            'doctor_id' => $doctor['doctor']->id,
            'patient_id' => $patient['patient']->id,
            'labo_id' => $labo->id,
            'status' => 'collected',
        ]);

        $item = ExamRequestItem::create([
            'exam_request_id' => $examRequest->id,
            'exam_id' => $exam->id,
        ]);

        $this->actingAs($staff['user'])
            ->post(route('center.results.store', $item->id), [
                'interpretation' => 'Valeur critique a transmettre',
                'parameters' => [
                    ['name' => 'Glycemie', 'value' => '3.50', 'status' => 'critical', 'range' => '0.70 - 1.10', 'unit' => 'g/L'],
                ],
            ])
            ->assertRedirect();

        $detail = ResultLaboDetail::where('parameter', 'Glycemie')->firstOrFail();

        $this->assertSame('critical', $detail->status);
        $this->assertTrue($detail->isCritical());
        $this->assertTrue($detail->isAbnormal());
    }

    public function test_result_update_accepts_critical_status(): void
    {
        $labo = $this->makeLabo();
        $staff = $this->makeStaff($labo);
        $exam = $this->makeExam();
        $patient = $this->makePatient();
        $doctor = $this->makeDoctor();

        $examRequest = ExamRequest::create([
            'doctor_id' => $doctor['doctor']->id,
            'patient_id' => $patient['patient']->id,
            'labo_id' => $labo->id,
            'status' => 'collected',
        ]);

        $item = ExamRequestItem::create([
            'exam_request_id' => $examRequest->id,
            'exam_id' => $exam->id,
        ]);

        $result = ResultLabo::create([
            'exam_request_item_id' => $item->id,
            'staff_id' => $staff['staff']->id,
            'interpretation' => 'Premier resultat',
            'is_archive' => false,
        ]);

        $this->actingAs($staff['user'])
            ->post(route('center.results.update', $result->id), [
                'interpretation' => 'Resultat mis a jour',
                'parameters' => [
                    ['name' => 'Glycemie', 'value' => '0.20', 'status' => 'critical', 'range' => '0.70 - 1.10', 'unit' => 'g/L'],
                ],
            ])
            ->assertRedirect();

        $detail = $result->details()->firstOrFail();

        $this->assertSame('critical', $detail->status);
    }

    public function test_invalid_status_is_rejected(): void
    {
        $labo = $this->makeLabo();
        $staff = $this->makeStaff($labo);
        $exam = $this->makeExam();
        $patient = $this->makePatient();
        $doctor = $this->makeDoctor();

        $examRequest = ExamRequest::create([
            'doctor_id' => $doctor['doctor']->id,
            'patient_id' => $patient['patient']->id,
            'labo_id' => $labo->id,
            'status' => 'collected',
        ]);

        $item = ExamRequestItem::create([
            'exam_request_id' => $examRequest->id,
            'exam_id' => $exam->id,
        ]);

        $this->actingAs($staff['user'])
            ->post(route('center.results.store', $item->id), [
                'parameters' => [
                    ['name' => 'Glycemie', 'value' => '3.50', 'status' => 'extreme', 'range' => '0.70 - 1.10', 'unit' => 'g/L'],
                ],
            ])
            ->assertSessionHasErrors('parameters.0.status');

        $this->assertSame(0, ResultLaboDetail::count());
    }

    public function test_critical_status_rendered_on_print_view(): void
    {
        $labo = $this->makeLabo();
        $exam = $this->makeExam();
        $patient = $this->makePatient();
        $doctor = $this->makeDoctor();

        $examRequest = ExamRequest::create([
            'doctor_id' => $doctor['doctor']->id,
            'patient_id' => $patient['patient']->id,
            'labo_id' => $labo->id,
            'status' => 'completed',
            'approved_by_doctor' => true,
        ]);

        $item = ExamRequestItem::create([
            'exam_request_id' => $examRequest->id,
            'exam_id' => $exam->id,
        ]);

        $result = ResultLabo::create([
            'exam_request_item_id' => $item->id,
            'staff_id' => null,
            'interpretation' => null,
            'is_archive' => false,
        ]);

        ResultLaboDetail::create([
            'result_labo_id' => $result->id,
            'parameter' => 'Glycemie',
            'value' => '3.50',
            'status' => 'critical',
            'reference_range' => '0.70 - 1.10',
            'unit' => 'g/L',
            'is_archive' => false,
        ]);

        $this->actingAs($patient['user'])
            ->get(route('patient.print-exam-request', $examRequest->id))
            ->assertOk()
            ->assertSee('Critique');
    }
}
