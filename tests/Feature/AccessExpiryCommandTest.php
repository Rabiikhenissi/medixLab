<?php

namespace Tests\Feature;

use App\Models\DoctorPatientAccess;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesUsers;
use Tests\TestCase;

class AccessExpiryCommandTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    public function test_command_notifies_both_parties_when_access_expires_within_seven_days(): void
    {
        $doctor = $this->makeDoctor();
        $patient = $this->makePatient();

        DoctorPatientAccess::create([
            'doctor_id' => $doctor['doctor']->id,
            'patient_id' => $patient['patient']->id,
            'access_status' => 'granted',
            'expires_at' => now()->addDays(2),
        ]);

        $this->artisan('access:expiry-check')->assertSuccessful();

        $this->assertSame(1, Notification::where('user_id', $patient['user']->id)->count());
        $this->assertSame(1, Notification::where('user_id', $doctor['user']->id)->count());
        $this->assertStringStartsWith('Accès expire dans', Notification::where('user_id', $patient['user']->id)->value('title'));
    }

    public function test_command_ignores_accesses_expiring_after_seven_days(): void
    {
        $doctor = $this->makeDoctor();
        $patient = $this->makePatient();

        DoctorPatientAccess::create([
            'doctor_id' => $doctor['doctor']->id,
            'patient_id' => $patient['patient']->id,
            'access_status' => 'granted',
            'expires_at' => now()->addDays(10),
        ]);

        $this->artisan('access:expiry-check')->assertSuccessful();

        $this->assertSame(0, Notification::count());
    }
}
