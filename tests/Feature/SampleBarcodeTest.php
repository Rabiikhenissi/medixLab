<?php

namespace Tests\Feature;

use App\Models\DoctorPatientAccess;
use App\Models\ExamRequest;
use App\Models\ExamRequestItem;
use App\Models\Sample;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Tests\CreatesUsers;
use Tests\TestCase;

class SampleBarcodeTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    /** Build the full context required to collect a sample. */
    protected function makeContext(): array
    {
        $doctor = $this->makeDoctor();
        $patient = $this->makePatient();
        DoctorPatientAccess::create([
            'doctor_id' => $doctor['doctor']->id,
            'patient_id' => $patient['patient']->id,
            'access_status' => 'granted',
        ]);

        $labo = $this->makeLabo();
        $staff = $this->makeStaff($labo);
        $exam = $this->makeExam();

        $examRequest = ExamRequest::create([
            'doctor_id' => $doctor['doctor']->id,
            'patient_id' => $patient['patient']->id,
            'labo_id' => $labo->id,
            'status' => 'assigned',
        ]);

        $item = ExamRequestItem::create([
            'exam_request_id' => $examRequest->id,
            'exam_id' => $exam->id,
        ]);

        return compact('labo', 'staff', 'examRequest', 'item');
    }

    public function test_creating_a_sample_generates_a_unique_barcode_and_logs_it(): void
    {
        $ctx = $this->makeContext();

        $this->actingAs($ctx['staff']['user'])
            ->post(route('center.samples.store'), [
                'exam_request_item_id' => $ctx['item']->id,
                'material_type' => 'Sang',
            ])
            ->assertRedirect();

        $sample = Sample::where('exam_request_item_id', $ctx['item']->id)->firstOrFail();

        $this->assertMatchesRegularExpression(
            '/^SMP-'.$ctx['labo']->id.'-\d{8}-[0-9A-F]{6}$/',
            $sample->sample_code
        );
        $this->assertSame('pending', $sample->status);
        $this->assertSame('Sang', $sample->material_type);
        $this->assertSame($ctx['examRequest']->patient_id, $sample->patient_id);

        $this->assertDatabaseHas('sample_barcode_logs', [
            'sample_id' => $sample->id,
            'action' => 'created',
        ]);
    }

    public function test_double_submit_is_idempotent(): void
    {
        $ctx = $this->makeContext();

        $this->actingAs($ctx['staff']['user'])->post(route('center.samples.store'), [
            'exam_request_item_id' => $ctx['item']->id,
        ])->assertRedirect();

        $first = Sample::where('exam_request_item_id', $ctx['item']->id)->firstOrFail();

        $this->actingAs($ctx['staff']['user'])->post(route('center.samples.store'), [
            'exam_request_item_id' => $ctx['item']->id,
        ])->assertRedirect(route('center.samples.show', $first->id));

        $this->assertSame(1, Sample::where('exam_request_item_id', $ctx['item']->id)->count());
    }

    public function test_database_rejects_a_second_sample_for_the_same_item(): void
    {
        $ctx = $this->makeContext();

        $this->actingAs($ctx['staff']['user'])->post(route('center.samples.store'), [
            'exam_request_item_id' => $ctx['item']->id,
        ])->assertRedirect();

        $this->expectException(QueryException::class);

        Sample::create([
            'sample_code' => 'SMP-9-20260804-ABCDEF',
            'exam_request_item_id' => $ctx['item']->id,
            'patient_id' => $ctx['examRequest']->patient_id,
            'labo_id' => $ctx['labo']->id,
            'status' => 'pending',
        ]);
    }

    public function test_barcode_lookup_returns_the_sample_and_logs_the_scan(): void
    {
        $ctx = $this->makeContext();

        $this->actingAs($ctx['staff']['user'])->post(route('center.samples.store'), [
            'exam_request_item_id' => $ctx['item']->id,
        ])->assertRedirect();

        $sample = Sample::where('exam_request_item_id', $ctx['item']->id)->firstOrFail();

        $this->actingAs($ctx['staff']['user'])->post(route('center.samples.lookup'), [
            'code' => $sample->sample_code,
        ])->assertOk()->assertJsonPath('sample_code', $sample->sample_code);

        $this->assertDatabaseHas('sample_barcode_logs', [
            'sample_id' => $sample->id,
            'action' => 'scanned',
        ]);
    }

    public function test_barcode_lookup_rejects_unknown_code_and_other_laboratories(): void
    {
        $ctx = $this->makeContext();

        $this->actingAs($ctx['staff']['user'])->post(route('center.samples.store'), [
            'exam_request_item_id' => $ctx['item']->id,
        ])->assertRedirect();

        $sample = Sample::where('exam_request_item_id', $ctx['item']->id)->firstOrFail();

        $this->actingAs($ctx['staff']['user'])->post(route('center.samples.lookup'), [
            'code' => 'NOPE-123',
        ])->assertStatus(404);

        $otherLabo = $this->makeLabo('Autre labo');
        $otherStaff = $this->makeStaff($otherLabo);

        $this->actingAs($otherStaff['user'])->post(route('center.samples.lookup'), [
            'code' => $sample->sample_code,
        ])->assertStatus(403);
    }
}
