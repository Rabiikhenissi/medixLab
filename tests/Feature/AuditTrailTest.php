<?php

namespace Tests\Feature;

use App\Models\Action;
use App\Models\Admin;
use App\Models\AuditLog;
use App\Models\ExamRequest;
use App\Models\ExamRequestItem;
use App\Models\Feature;
use App\Models\Group;
use App\Models\Sample;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesUsers;
use Tests\TestCase;

class AuditTrailTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    /** Build an admin account whose group holds the view-activity permission. */
    protected function makeAdminWithPermission(): User
    {
        $feature = Feature::create(['code' => 'activity-logs', 'name' => 'Activite']);
        $action = Action::create(['code' => 'view-activity', 'name' => 'View activity logs', 'feature_id' => $feature->id]);
        $group = Group::create(['code' => 'admin', 'name' => 'Admin']);
        $group->actions()->attach($action->id);

        $user = $this->makeUser(['group_id' => $group->id]);
        Admin::create(['user_id' => $user->id]);
        cache()->forget("group_permissions_{$group->id}");

        return $user;
    }

    /** Build an admin account whose group holds no activity permission. */
    protected function makeAdminWithoutPermission(): User
    {
        $group = Group::create(['code' => 'admin', 'name' => 'Admin']);
        $user = $this->makeUser(['group_id' => $group->id]);
        Admin::create(['user_id' => $user->id]);
        cache()->forget("group_permissions_{$group->id}");

        return $user;
    }

    /** Create a minimal prescription -> sample chain. */
    protected function makeSampleChain(): array
    {
        $labo = $this->makeLabo();
        $fixture = $this->makePatient();
        $exam = $this->makeExam();

        $examRequest = ExamRequest::create([
            'doctor_id' => null,
            'patient_id' => $fixture['patient']->id,
            'labo_id' => $labo->id,
            'status' => 'pending',
        ]);

        $item = ExamRequestItem::create([
            'exam_request_id' => $examRequest->id,
            'exam_id' => $exam->id,
        ]);

        $sample = Sample::create([
            'sample_code' => 'SMP-TEST-00001',
            'exam_request_item_id' => $item->id,
            'patient_id' => $fixture['patient']->id,
            'labo_id' => $labo->id,
            'status' => 'pending',
        ]);

        return ['examRequest' => $examRequest, 'item' => $item, 'sample' => $sample, 'patient' => $fixture['patient']];
    }

    public function test_sample_lifecycle_is_audited_with_diffs(): void
    {
        $chain = $this->makeSampleChain();
        $user = $this->makeUser();

        $this->actingAs($user);

        $chain['sample']->update(['status' => 'collected']);

        $entries = AuditLog::where('entity_type', 'Sample')
            ->where('entity_id', $chain['sample']->id)
            ->orderBy('id')
            ->get();

        $this->assertCount(2, $entries, 'creation and update should both be audited');

        $this->assertSame('created', $entries[0]->action);
        $this->assertSame('updated', $entries[1]->action);
        $this->assertSame('collected', $entries[1]->changes['status']['new']);
        $this->assertSame('pending', $entries[1]->changes['status']['old']);
        $this->assertSame($user->id, $entries[1]->user_id);
    }

    public function test_exam_request_status_transition_is_audited(): void
    {
        $chain = $this->makeSampleChain();
        $this->actingAs($this->makeUser());

        $chain['examRequest']->update(['status' => 'collected']);

        $entry = AuditLog::where('entity_type', 'ExamRequest')
            ->where('entity_id', $chain['examRequest']->id)
            ->where('action', 'updated')
            ->first();

        $this->assertNotNull($entry);
        $this->assertSame('collected', $entry->changes['status']['new']);
    }

    public function test_system_actions_without_user_are_audited(): void
    {
        $chain = $this->makeSampleChain();

        $this->assertNotNull(
            AuditLog::where('entity_type', 'Sample')
                ->where('entity_id', $chain['sample']->id)
                ->whereNull('user_id')
                ->where('action', 'created')
                ->first()
        );
    }

    public function test_login_timestamp_update_is_not_audited(): void
    {
        $user = $this->makeUser();
        $before = AuditLog::where('entity_type', 'User')->where('entity_id', $user->id)->count();

        $user->update(['last_login_at' => now()]);

        $this->assertSame(
            $before,
            AuditLog::where('entity_type', 'User')->where('entity_id', $user->id)->count()
        );
    }

    public function test_user_update_description_is_human_readable(): void
    {
        $user = $this->makeUser(['first_name' => 'Sarra', 'last_name' => 'Trabelsi']);
        $this->actingAs($this->makeUser());

        $user->update(['email' => 'sarra.nouvelle@medixlab.test']);

        $entry = AuditLog::where('entity_type', 'User')
            ->where('entity_id', $user->id)
            ->where('action', 'updated')
            ->latest('id')
            ->first();

        $this->assertNotNull($entry);
        $this->assertSame(
            'Modification de Compte utilisateur #'.$user->id.' (Sarra Trabelsi) — champs modifiés : email',
            $entry->description
        );
    }

    public function test_admin_with_permission_can_view_activity_page(): void
    {
        $admin = $this->makeAdminWithPermission();

        $this->actingAs($admin)
            ->get(route('admin.activity'))
            ->assertOk()
            ->assertSee('Journal des actions');
    }

    public function test_admin_without_permission_cannot_view_activity_page(): void
    {
        $admin = $this->makeAdminWithoutPermission();

        $this->actingAs($admin)
            ->get(route('admin.activity'))
            ->assertForbidden();
    }

    public function test_admin_with_permission_can_export_activity_pdf(): void
    {
        $admin = $this->makeAdminWithPermission();
        $this->makeSampleChain();

        $this->actingAs($admin)
            ->get(route('admin.activity.export'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf')
            ->assertHeader('Content-Disposition', 'attachment; filename=journal-activite-'.now()->format('Ymd-Hi').'.pdf');
    }

    public function test_admin_without_permission_cannot_export_activity_pdf(): void
    {
        $admin = $this->makeAdminWithoutPermission();

        $this->actingAs($admin)
            ->get(route('admin.activity.export'))
            ->assertForbidden();
    }
}
