<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Tests\CreatesUsers;
use Tests\TestCase;

class LoginDiagnosticTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    public function test_login_pages_render(): void
    {
        $this->get(route('doctor.login'))->assertStatus(200);
        $this->get(route('patient.login'))->assertStatus(200);
        $this->get(route('center.login'))->assertStatus(200);
    }

    public function test_doctor_login_post(): void
    {
        $fixture = $this->makeDoctor();
        $email = $fixture['user']->email;

        $this->post(route('doctor.login'), [
            'email' => $email,
            'password' => 'password',
        ])->assertRedirect();
    }

    public function test_center_login_post(): void
    {
        $labo = $this->makeLabo();
        $fixture = $this->makeStaff($labo);
        $email = $fixture['user']->email;

        $this->post(route('center.login'), [
            'email' => $email,
            'password' => 'password',
        ])->assertRedirect();
    }

    public function test_admin_login_post(): void
    {
        $user = $this->makeUser();
        Admin::create(['user_id' => $user->id]);

        $this->post(route('doctor.login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));
    }

    public function test_doctor_login_reaches_dashboard(): void
    {
        $fixture = $this->makeDoctor();
        $this->actingAs($fixture['user'])
            ->get(route('doctor.dashboard'))
            ->assertOk();
    }

    public function test_center_login_reaches_dashboard(): void
    {
        $labo = $this->makeLabo();
        $fixture = $this->makeStaff($labo);
        $this->actingAs($fixture['user'])
            ->get(route('center.dashboard'))
            ->assertOk();
    }
}
