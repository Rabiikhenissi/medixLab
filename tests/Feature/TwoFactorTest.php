<?php

namespace Tests\Feature;

use App\Mail\TwoFactorCodeMail;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Mail;
use Tests\CreatesUsers;
use Tests\TestCase;

class TwoFactorTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
        Mail::fake();
    }

    /** Enable email-based 2FA on a user (as if the flow had been completed). */
    protected function enableTwoFactor(User $user): void
    {
        $user->update(['two_factor_confirmed_at' => now()]);
    }

    /** Return the plain code captured from the last sent 2FA email. */
    protected function pendingCode(User $user): string
    {
        $codes = $this->sentCodes();

        return (string) end($codes);
    }

    /** All plain codes captured from sent 2FA emails, oldest first. */
    protected function sentCodes(): array
    {
        $codes = [];

        Mail::assertSent(TwoFactorCodeMail::class, function (TwoFactorCodeMail $mail) use (&$codes) {
            $codes[] = $mail->code;

            return true;
        });

        return $codes;
    }

    public function test_login_without_2fa_bypasses_challenge(): void
    {
        $doctor = $this->makeDoctor();

        $this->post(route('doctor.login'), [
            'email' => $doctor['user']->email,
            'password' => 'password',
        ])->assertRedirect(route('doctor.dashboard'));

        $this->assertAuthenticatedAs($doctor['user']);
        Mail::assertNothingSent();
    }

    public function test_login_with_2fa_emails_code_and_requires_challenge(): void
    {
        $doctor = $this->makeDoctor();
        $this->enableTwoFactor($doctor['user']);

        $this->post(route('doctor.login'), [
            'email' => $doctor['user']->email,
            'password' => 'password',
        ])->assertRedirect(route('two-factor.login'));

        $this->assertGuest();
        $this->assertSame(6, strlen($this->pendingCode($doctor['user'])));
        Mail::assertSent(TwoFactorCodeMail::class, function (TwoFactorCodeMail $mail) use ($doctor) {
            return $mail->hasTo($doctor['user']->email);
        });

        $this->get(route('two-factor.login'))
            ->assertOk()
            ->assertSee('Vérification en deux étapes');
    }

    public function test_login_challenge_page_without_pending_login_redirects_home(): void
    {
        $this->get(route('two-factor.login'))->assertRedirect(route('home'));
    }

    public function test_valid_code_completes_the_pending_login(): void
    {
        $doctor = $this->makeDoctor();
        $this->enableTwoFactor($doctor['user']);

        $this->post(route('doctor.login'), [
            'email' => $doctor['user']->email,
            'password' => 'password',
        ])->assertRedirect(route('two-factor.login'));

        $code = $this->pendingCode($doctor['user']);

        $this->post(route('two-factor.verify'), [
            'code' => $code,
        ])->assertRedirect(route('doctor.dashboard'));

        $this->assertAuthenticatedAs($doctor['user']);
        $this->assertNull($doctor['user']->fresh()->two_factor_code);
    }

    public function test_invalid_code_keeps_user_guest(): void
    {
        $doctor = $this->makeDoctor();
        $this->enableTwoFactor($doctor['user']);

        $this->post(route('doctor.login'), [
            'email' => $doctor['user']->email,
            'password' => 'password',
        ]);

        $this->post(route('two-factor.verify'), [
            'code' => '000000',
        ])->assertSessionHasErrors('code');

        $this->assertGuest();
    }

    public function test_resend_challenge_issues_a_fresh_code(): void
    {
        $doctor = $this->makeDoctor();
        $this->enableTwoFactor($doctor['user']);

        $this->post(route('doctor.login'), [
            'email' => $doctor['user']->email,
            'password' => 'password',
        ]);

        $first = $this->pendingCode($doctor['user']);

        $this->post(route('two-factor.resend-challenge'))
            ->assertSessionHas('status');

        $second = $this->pendingCode($doctor['user']);

        $this->assertNotSame($first, $second);
    }

    public function test_admin_login_also_requires_challenge(): void
    {
        $user = $this->makeUser();
        Admin::create(['user_id' => $user->id]);
        $this->enableTwoFactor($user);

        $this->post(route('doctor.login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('two-factor.login'));

        $this->assertGuest();
    }

    public function test_enable_flow_confirms_the_emailed_code(): void
    {
        $doctor = $this->makeDoctor();
        $user = $doctor['user'];

        $this->actingAs($user)->get(route('profile.two-factor.setup'))->assertOk();
        $code = $this->pendingCode($user);
        $this->assertSame(6, strlen($code));

        $this->actingAs($user)->post(route('profile.two-factor.enable'), [
            'code' => $code,
        ])->assertSessionHas('success');

        $this->assertTrue($user->fresh()->twoFactorEnabled());
    }

    public function test_enable_flow_rejects_an_invalid_code(): void
    {
        $doctor = $this->makeDoctor();
        $user = $doctor['user'];

        $this->actingAs($user)->get(route('profile.two-factor.setup'))->assertOk();

        $this->actingAs($user)->post(route('profile.two-factor.enable'), [
            'code' => '000000',
        ])->assertSessionHasErrors('code');

        $this->assertFalse($user->fresh()->twoFactorEnabled());
    }

    public function test_enable_flow_resend_issues_a_fresh_code(): void
    {
        $doctor = $this->makeDoctor();
        $user = $doctor['user'];

        $this->actingAs($user)->get(route('profile.two-factor.setup'))->assertOk();
        $first = $this->pendingCode($user);

        $this->actingAs($user)->post(route('profile.two-factor.resend'))
            ->assertSessionHas('status');

        $this->assertNotSame($first, $this->pendingCode($user));
    }

    public function test_disable_requires_the_current_password(): void
    {
        $doctor = $this->makeDoctor();
        $user = $doctor['user'];
        $this->enableTwoFactor($user);

        $this->actingAs($user)->post(route('profile.two-factor.disable'), [
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('password');

        $this->assertTrue($user->fresh()->twoFactorEnabled());

        $this->actingAs($user)->post(route('profile.two-factor.disable'), [
            'password' => 'password',
        ])->assertSessionHas('success');

        $this->assertFalse($user->fresh()->twoFactorEnabled());
    }
}
