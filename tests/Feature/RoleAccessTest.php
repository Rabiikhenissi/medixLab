<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Tests\CreatesUsers;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    public function test_guest_is_redirected_away_from_doctor_dashboard(): void
    {
        $this->get(route('doctor.dashboard'))
            ->assertRedirect();
    }

    public function test_patient_cannot_access_doctor_dashboard(): void
    {
        $fixture = $this->makePatient();

        $this->actingAs($fixture['user'])
            ->get(route('doctor.dashboard'))
            ->assertRedirect();
    }

    public function test_patient_cannot_access_center_dashboard(): void
    {
        $fixture = $this->makePatient();

        $this->actingAs($fixture['user'])
            ->get(route('center.dashboard'))
            ->assertRedirect();
    }

    public function test_doctor_cannot_access_patient_dashboard(): void
    {
        $fixture = $this->makeDoctor();

        $this->actingAs($fixture['user'])
            ->get(route('patient.dashboard'))
            ->assertRedirect();
    }

    public function test_doctor_cannot_access_admin_dashboard(): void
    {
        $fixture = $this->makeDoctor();

        $this->actingAs($fixture['user'])
            ->get(route('admin.dashboard'))
            ->assertRedirect();
    }

    public function test_doctor_can_access_own_dashboard(): void
    {
        $fixture = $this->makeDoctor();

        $this->actingAs($fixture['user'])
            ->get(route('doctor.dashboard'))
            ->assertOk();
    }

    public function test_patient_can_access_own_dashboard(): void
    {
        $fixture = $this->makePatient();

        $this->actingAs($fixture['user'])
            ->get(route('patient.dashboard'))
            ->assertOk();
    }
}
