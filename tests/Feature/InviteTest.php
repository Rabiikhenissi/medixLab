<?php

namespace Tests\Feature;

use App\Mail\InviteMail;
use App\Models\Action;
use App\Models\Admin;
use App\Models\Consent;
use App\Models\Feature;
use App\Models\Group;
use App\Models\Invite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Mail;
use Tests\CreatesUsers;
use Tests\TestCase;

class InviteTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    /** Build an admin whose group holds the user-management permission. */
    protected function makeUsersAdmin(bool $withPermission = true): User
    {
        $feature = Feature::create(['code' => 'users-management', 'name' => 'Utilisateurs']);
        foreach (['view-users', 'create-users', 'edit-users', 'delete-users'] as $code) {
            Action::create(['code' => $code, 'name' => $code, 'feature_id' => $feature->id]);
        }
        $group = Group::create(['code' => 'admin', 'name' => 'Admin']);
        if ($withPermission) {
            $group->actions()->attach(Action::whereIn('code', ['view-users', 'create-users'])->pluck('id'));
        }
        $user = $this->makeUser(['group_id' => $group->id]);
        Admin::create(['user_id' => $user->id]);
        cache()->forget("group_permissions_{$group->id}");

        return $user;
    }

    protected function makeDoctorGroup(): Group
    {
        return Group::create(['code' => 'doctor', 'name' => 'Doctor']);
    }

    public function test_admin_with_permission_can_view_invite_form(): void
    {
        $admin = $this->makeUsersAdmin(true);

        $this->actingAs($admin)
            ->get(route('admin.users.invite'))
            ->assertOk()
            ->assertSee('Inviter par Email');
    }

    public function test_admin_without_permission_cannot_invite(): void
    {
        $admin = $this->makeUsersAdmin(false);

        $this->actingAs($admin)
            ->get(route('admin.users.invite'))
            ->assertForbidden();
    }

    public function test_admin_can_send_an_invitation_email(): void
    {
        Mail::fake();

        $admin = $this->makeUsersAdmin(true);
        $group = $this->makeDoctorGroup();

        $this->actingAs($admin)
            ->post(route('admin.users.invite.store'), [
                'first_name' => 'Sami',
                'last_name' => 'Ben Salah',
                'email' => 'sami@example.com',
                'group_id' => $group->id,
            ])
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('success');

        $invite = Invite::where('email', 'sami@example.com')->firstOrFail();
        $this->assertSame(Invite::STATUS_PENDING, $invite->status);
        $this->assertSame($group->id, $invite->group_id);
        $this->assertSame($admin->id, $invite->invited_by);
        $this->assertFalse($invite->isExpired());

        Mail::assertSent(InviteMail::class, function (InviteMail $mail) use ($invite) {
            return $mail->invite->is($invite);
        });
    }

    public function test_invitation_is_not_sent_twice_for_the_same_email(): void
    {
        Mail::fake();

        $admin = $this->makeUsersAdmin(true);
        $group = $this->makeDoctorGroup();

        Invite::create([
            'email' => 'sami@example.com',
            'token' => 'token-a',
            'group_id' => $group->id,
            'invited_by' => $admin->id,
            'status' => Invite::STATUS_PENDING,
            'expires_at' => now()->addDays(7),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.users.invite.store'), [
                'email' => 'sami@example.com',
                'group_id' => $group->id,
            ])
            ->assertSessionHasErrors(['email']);

        Mail::assertNothingSent();
    }

    public function test_invited_person_can_activate_their_account(): void
    {
        $group = $this->makeDoctorGroup();

        $invite = Invite::create([
            'email' => 'sami@example.com',
            'token' => 'valid-token-123',
            'group_id' => $group->id,
            'first_name' => 'Sami',
            'last_name' => 'Ben Salah',
            'status' => Invite::STATUS_PENDING,
            'expires_at' => now()->addDays(7),
        ]);

        $this->get(route('invite.accept', $invite->token))
            ->assertOk()
            ->assertSee('sami@example.com');

        $this->post(route('invite.accept.store', $invite->token), [
            'first_name' => 'Sami',
            'last_name' => 'Ben Salah',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'accept_terms' => '1',
            'accept_privacy' => '1',
        ])->assertRedirect(route('doctor.dashboard'));

        $user = User::where('email', 'sami@example.com')->firstOrFail();
        $this->assertNotNull($user->email_verified_at);
        $this->assertSame($group->id, $user->group_id);
        $this->assertNotNull($user->doctor);
        $this->assertDatabaseHas('doctors', ['user_id' => $user->id]);

        $this->assertSame(1, Consent::where('user_id', $user->id)->where('consent_type', 'terms')->count());
        $this->assertSame(1, Consent::where('user_id', $user->id)->where('consent_type', 'privacy')->count());

        $this->assertSame(Invite::STATUS_ACCEPTED, $invite->fresh()->status);
        $this->assertNotNull($invite->fresh()->accepted_at);

        $this->assertAuthenticatedAs($user);
    }

    public function test_invited_person_must_accept_both_documents(): void
    {
        $group = $this->makeDoctorGroup();

        $invite = Invite::create([
            'email' => 'sami@example.com',
            'token' => 'valid-token-456',
            'group_id' => $group->id,
            'status' => Invite::STATUS_PENDING,
            'expires_at' => now()->addDays(7),
        ]);

        $this->post(route('invite.accept.store', $invite->token), [
            'first_name' => 'Sami',
            'last_name' => 'Ben Salah',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHasErrors(['accept_terms', 'accept_privacy']);

        $this->assertDatabaseMissing('users', ['email' => 'sami@example.com']);
    }

    public function test_expired_invite_is_rejected(): void
    {
        $group = $this->makeDoctorGroup();

        $invite = Invite::create([
            'email' => 'sami@example.com',
            'token' => 'expired-token-789',
            'group_id' => $group->id,
            'status' => Invite::STATUS_PENDING,
            'expires_at' => now()->subDay(),
        ]);

        $this->get(route('invite.accept', $invite->token))->assertRedirect(route('home'));

        $this->post(route('invite.accept.store', $invite->token), [
            'first_name' => 'Sami',
            'last_name' => 'Ben Salah',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'accept_terms' => '1',
            'accept_privacy' => '1',
        ])->assertRedirect(route('home'));

        $this->assertDatabaseMissing('users', ['email' => 'sami@example.com']);
    }

    public function test_already_accepted_invite_cannot_be_used_again(): void
    {
        $group = $this->makeDoctorGroup();

        $invite = Invite::create([
            'email' => 'sami@example.com',
            'token' => 'used-token-101',
            'group_id' => $group->id,
            'status' => Invite::STATUS_ACCEPTED,
            'expires_at' => now()->addDays(7),
        ]);

        $this->get(route('invite.accept', $invite->token))->assertRedirect(route('home'));
    }
}
