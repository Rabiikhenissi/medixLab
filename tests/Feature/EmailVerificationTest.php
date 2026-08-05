<?php

namespace Tests\Feature;

use App\Mail\VerificationMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\CreatesUsers;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    public function test_unverified_user_is_blocked_from_dashboard_and_sees_notice(): void
    {
        $user = $this->makeUser(['email_verified_at' => null]);
        $this->actingAs($user)
            ->get(route('patient.dashboard'))
            ->assertRedirect(route('verification.notice'));

        $this->actingAs($user)
            ->get(route('verification.notice'))
            ->assertOk()
            ->assertSee($user->email);
    }

    public function test_verified_user_can_access_dashboard(): void
    {
        $user = $this->makePatient()['user'];
        $this->actingAs($user)
            ->get(route('patient.dashboard'))
            ->assertOk();
    }

    public function test_valid_signed_link_verifies_and_redirects_to_dashboard(): void
    {
        Mail::fake();
        $user = $this->makeUser(['email_verified_at' => null]);

        $user->sendEmailVerificationNotification();

        $url = null;
        Mail::assertSent(VerificationMail::class, function (VerificationMail $mail) use ($user, &$url) {
            $url = $mail->verificationUrl;

            return $mail->hasTo($user->email);
        });

        $this->assertNotNull($url);

        $this->actingAs($user)
            ->get($url)
            ->assertRedirect(route('patient.dashboard'));

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_invalid_hash_is_rejected(): void
    {
        $user = $this->makeUser(['email_verified_at' => null]);

        $this->actingAs($user)
            ->get(route('verification.verify', ['id' => $user->id, 'hash' => str_repeat('0', 40)]))
            ->assertForbidden();

        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function test_resend_issues_a_fresh_verification_email(): void
    {
        Mail::fake();
        $user = $this->makeUser(['email_verified_at' => null]);
        $this->actingAs($user);

        $this->post(route('verification.send'))->assertRedirect();

        Mail::assertSent(VerificationMail::class);
        $this->assertSame(1, Mail::sent(VerificationMail::class, fn ($mail) => $mail->hasTo($user->email))->count());
    }

    public function test_registration_sends_a_verification_email(): void
    {
        Mail::fake();

        $this->post(route('patient.register'), [
            'role' => 'patient',
            'first_name' => 'Amina',
            'last_name' => 'Bouaziz',
            'email' => 'amina@example.com',
            'phone' => '22334455',
            'country' => 'TN',
            'state_code' => 'TUN',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'accept_terms' => '1',
            'accept_privacy' => '1',
        ])->assertRedirect(route('patient.dashboard'));

        Mail::assertSent(VerificationMail::class);
    }
}
