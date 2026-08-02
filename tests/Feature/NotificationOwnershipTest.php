<?php

namespace Tests\Feature;

use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Tests\CreatesUsers;
use Tests\TestCase;

class NotificationOwnershipTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    public function test_patient_cannot_mark_another_users_notification_as_read(): void
    {
        $owner = $this->makePatient();
        $attacker = $this->makePatient();

        $notification = Notification::create([
            'user_id' => $owner['user']->id,
            'title' => 'Confidentiel',
            'message' => 'Données médicales',
        ]);

        $this->actingAs($attacker['user'])
            ->post(route('patient.mark-as-read', $notification->id))
            ->assertForbidden();

        $this->assertFalse($notification->fresh()->is_read);
    }

    public function test_doctor_cannot_mark_another_users_notification_as_read(): void
    {
        $owner = $this->makeDoctor();
        $attacker = $this->makeDoctor();

        $notification = Notification::create([
            'user_id' => $owner['user']->id,
            'title' => 'Confidentiel',
            'message' => 'Données médicales',
        ]);

        $this->actingAs($attacker['user'])
            ->post(route('doctor.mark-as-read', $notification->id))
            ->assertForbidden();

        $this->assertFalse($notification->fresh()->is_read);
    }

    public function test_patient_can_mark_own_notification_as_read(): void
    {
        $owner = $this->makePatient();

        $notification = Notification::create([
            'user_id' => $owner['user']->id,
            'title' => 'Test',
            'message' => 'Message',
        ]);

        $this->actingAs($owner['user'])
            ->post(route('patient.mark-as-read', $notification->id))
            ->assertOk();

        $this->assertTrue($notification->fresh()->is_read);
    }
}
