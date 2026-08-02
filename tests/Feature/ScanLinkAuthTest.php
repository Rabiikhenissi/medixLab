<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Tests\CreatesUsers;
use Tests\TestCase;

class ScanLinkAuthTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    public function test_guest_cannot_link_a_doctor_via_qr_code(): void
    {
        $doctor = $this->makeDoctor();

        $this->post(route('patient.scan-doctor-link', $doctor['doctor']->doctor_code))
            ->assertRedirect();
    }

    public function test_patient_can_link_doctor_via_qr_code(): void
    {
        $doctor = $this->makeDoctor();
        $patient = $this->makePatient();

        $this->actingAs($patient['user'])
            ->post(route('patient.scan-doctor-link', $doctor['doctor']->doctor_code))
            ->assertRedirect(route('patient.dashboard'));

        $this->assertDatabaseHas('doctor_patient_access', [
            'doctor_id' => $doctor['doctor']->id,
            'patient_id' => $patient['patient']->id,
            'access_status' => 'granted',
        ]);
    }

    public function test_doctor_cannot_link_as_patient_via_own_qr(): void
    {
        $doctorA = $this->makeDoctor();
        $doctorB = $this->makeDoctor();

        $this->actingAs($doctorB['user'])
            ->post(route('patient.scan-doctor-link', $doctorA['doctor']->doctor_code))
            ->assertRedirect(route('patient.dashboard'));

        $this->assertDatabaseCount('doctor_patient_access', 0);
    }
}
