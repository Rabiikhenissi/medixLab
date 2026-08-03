<?php

namespace Tests\Feature;

use App\Models\Action;
use App\Models\Admin;
use App\Models\ChatMessage;
use App\Models\ExamRequest;
use App\Models\ExamRequestItem;
use App\Models\Feature;
use App\Models\Group;
use App\Models\Notification;
use App\Models\ResultLabo;
use App\Models\ResultLaboDetail;
use App\Models\User;
use App\Services\GdprService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Str;
use Tests\CreatesUsers;
use Tests\TestCase;

class GdprTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);

        $dir = storage_path('app/gdpr/exports');
        if (is_dir($dir)) {
            array_map('unlink', glob($dir.'/*.json') ?: []);
        }
    }

    /** Build an admin whose group holds the GDPR permissions. */
    protected function makeGdprAdmin(bool $withPermission = true): User
    {
        $feature = Feature::create(['code' => 'gdpr-management', 'name' => 'RGPD']);
        foreach (['view-gdpr', 'manage-gdpr'] as $code) {
            Action::create(['code' => $code, 'name' => $code, 'feature_id' => $feature->id]);
        }
        $group = Group::create(['code' => 'admin', 'name' => 'Admin']);
        if ($withPermission) {
            $group->actions()->attach(Action::whereIn('code', ['view-gdpr', 'manage-gdpr'])->pluck('id'));
        }
        $user = $this->makeUser(['group_id' => $group->id]);
        Admin::create(['user_id' => $user->id]);
        cache()->forget("group_permissions_{$group->id}");

        return $user;
    }

    /** Build a patient with a completed prescription, results, messages and notifications. */
    protected function makeRichPatient(): array
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
            'clinical_notes' => 'Bilan glycémique',
        ]);

        $item = ExamRequestItem::create([
            'exam_request_id' => $examRequest->id,
            'exam_id' => $exam->id,
        ]);

        $result = ResultLabo::create([
            'exam_request_item_id' => $item->id,
            'staff_id' => null,
            'interpretation' => 'Interprétation du labo',
            'is_archive' => false,
        ]);

        ResultLaboDetail::create([
            'result_labo_id' => $result->id,
            'parameter' => 'Glycemie',
            'value' => '0.90',
            'status' => 'normal',
            'reference_range' => '0.70 - 1.10',
            'unit' => 'g/L',
            'is_archive' => false,
        ]);

        Notification::create([
            'user_id' => $patient['user']->id,
            'title' => 'Résultat disponible',
            'message' => 'Votre résultat est prêt',
        ]);

        ChatMessage::create([
            'sender_id' => $patient['user']->id,
            'receiver_id' => $doctor['user']->id,
            'message' => 'Bonjour docteur',
        ]);

        return ['user' => $patient['user'], 'doctor' => $doctor, 'request' => $examRequest];
    }

    public function test_export_command_writes_portable_json(): void
    {
        $fixture = $this->makeRichPatient();
        $user = $fixture['user'];

        $this->artisan('gdpr:export', ['user' => (string) $user->id])->assertSuccessful();

        $files = glob(storage_path('app/gdpr/exports/user-'.$user->id.'-*.json')) ?: [];
        usort($files, fn ($a, $b) => filemtime($a) <=> filemtime($b));
        $this->assertCount(1, $files);

        $data = json_decode(file_get_contents($files[0]), true);
        $this->assertSame($user->email, $data['account']['email']);
        $this->assertSame(['patient'], $data['subject']['roles']);
        $this->assertCount(1, $data['medical_records']);
        $this->assertSame('Glycemie', $data['medical_records'][0]['items'][0]['result']['details'][0]['parameter']);
        $this->assertCount(1, $data['notifications']);
        $this->assertSame('Bonjour docteur', $data['chat_messages'][0]['message']);
    }

    public function test_erase_command_anonymises_account_and_keeps_clinical_data(): void
    {
        $fixture = $this->makeRichPatient();
        $user = $fixture['user'];

        $this->artisan('gdpr:erase', ['user' => (string) $user->id])
            ->expectsQuestion('This action is irreversible. Continue?', 'yes')
            ->assertSuccessful();

        $user->refresh();
        $this->assertSame('Anonymisé', $user->first_name);
        $this->assertSame('anonyme-'.$user->id.'@medixlab.invalid', $user->email);
        $this->assertNull($user->phone);
        $this->assertTrue($user->is_archive);

        $patient = $user->patient;
        $this->assertSame('ANON-P-'.$user->id, $patient->patient_code);
        $this->assertNull($patient->date_of_birth);

        // Clinical records preserved for lab retention.
        $this->assertSame(1, ExamRequest::where('patient_id', $patient->id)->count());
        $this->assertSame(1, ResultLaboDetail::count());

        // Personal communication wiped.
        $this->assertSame(0, Notification::where('user_id', $user->id)->count());
        $this->assertSame(0, ChatMessage::count());
    }

    public function test_erase_hard_deletes_account_and_profile(): void
    {
        $fixture = $this->makeRichPatient();
        $user = $fixture['user'];

        $this->artisan('gdpr:erase', ['user' => (string) $user->id, '--hard' => true])
            ->expectsQuestion('This action is irreversible. Continue?', 'yes')
            ->assertSuccessful();

        $this->assertNull(User::find($user->id));
        $this->assertNull($user->patient()->first());

        // Hard erasure removes the clinical records as well (full cascade).
        $this->assertSame(0, ExamRequest::count());
        $this->assertSame(0, ResultLaboDetail::count());
    }

    public function test_admin_with_permission_can_view_gdpr_page(): void
    {
        $admin = $this->makeGdprAdmin(true);

        $this->actingAs($admin)
            ->get(route('admin.gdpr'))
            ->assertOk()
            ->assertSee('RGPD');
    }

    public function test_admin_without_permission_cannot_view_gdpr_page(): void
    {
        $admin = $this->makeGdprAdmin(false);

        $this->actingAs($admin)
            ->get(route('admin.gdpr'))
            ->assertForbidden();
    }

    public function test_export_endpoint_streams_json(): void
    {
        $admin = $this->makeGdprAdmin(true);
        $fixture = $this->makeRichPatient();

        $this->actingAs($admin)
            ->get(route('admin.gdpr.export', $fixture['user']))
            ->assertOk()
            ->assertHeader('Content-Disposition', 'attachment; filename=gdpr-export-user-'.$fixture['user']->id.'.json');

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'export',
            'entity_type' => 'User',
            'entity_id' => $fixture['user']->id,
        ]);
    }

    public function test_erase_endpoint_anonymises_and_logs_audit(): void
    {
        $admin = $this->makeGdprAdmin(true);
        $fixture = $this->makeRichPatient();
        $user = $fixture['user'];

        $this->actingAs($admin)
            ->post(route('admin.gdpr.erase', $user), ['confirm' => '1'])
            ->assertRedirect(route('admin.gdpr'));

        $user->refresh();
        $this->assertSame('Anonymisé', $user->first_name);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'erase',
            'entity_type' => 'User',
            'entity_id' => $user->id,
        ]);
    }

    public function test_erase_endpoint_requires_confirmation(): void
    {
        $admin = $this->makeGdprAdmin(true);
        $fixture = $this->makeRichPatient();

        $this->actingAs($admin)
            ->post(route('admin.gdpr.erase', $fixture['user']))
            ->assertRedirect(route('admin.gdpr'))
            ->assertSessionHas('error');

        $this->assertSame($fixture['user']->email, $fixture['user']->refresh()->email);
    }

    public function test_anonymised_email_is_unique(): void
    {
        $fixture = $this->makeRichPatient();
        $user = $fixture['user'];

        (new GdprService)->erase($user, false);

        $count = User::where('email', 'anonyme-'.$user->id.'@medixlab.invalid')->count();
        $this->assertSame(1, $count);
        $this->assertTrue(Str::endsWith($user->refresh()->email, '@medixlab.invalid'));
    }
}
