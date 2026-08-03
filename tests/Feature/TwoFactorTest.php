<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use App\Services\TwoFactorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use PragmaRX\Google2FA\Google2FA;
use Tests\CreatesUsers;
use Tests\TestCase;

class TwoFactorTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    /** Generate a valid TOTP code for a given secret. */
    protected function currentCode(string $secret): string
    {
        return (new Google2FA)->getCurrentOtp($secret);
    }

    /** Enable 2FA on a user and return the stored secret. */
    protected function enableTwoFactor(User $user): string
    {
        $secret = app(TwoFactorService::class)->generateSecret();
        $user->update([
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
        ]);

        return $secret;
    }

    public function test_login_without_2fa_bypasses_challenge(): void
    {
        $doctor = $this->makeDoctor();

        $this->post(route('doctor.login'), [
            'email' => $doctor['user']->email,
            'password' => 'password',
        ])->assertRedirect(route('doctor.dashboard'));

        $this->assertAuthenticatedAs($doctor['user']);
    }

    public function test_login_with_2fa_requires_challenge_and_does_not_authenticate(): void
    {
        $doctor = $this->makeDoctor();
        $this->enableTwoFactor($doctor['user']);

        $this->post(route('doctor.login'), [
            'email' => $doctor['user']->email,
            'password' => 'password',
        ])->assertRedirect(route('two-factor.login'));

        $this->assertGuest();

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
        $secret = $this->enableTwoFactor($doctor['user']);

        $this->post(route('doctor.login'), [
            'email' => $doctor['user']->email,
            'password' => 'password',
        ])->assertRedirect(route('two-factor.login'));

        $this->post(route('two-factor.verify'), [
            'code' => $this->currentCode($secret),
        ])->assertRedirect(route('doctor.dashboard'));

        $this->assertAuthenticatedAs($doctor['user']);
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

    public function test_enable_flow_confirms_the_scanned_secret(): void
    {
        $doctor = $this->makeDoctor();
        $user = $doctor['user'];

        $this->actingAs($user)->get(route('profile.two-factor.setup'))->assertOk();

        $secret = session('two_factor.setup.secret');
        $this->assertNotEmpty($secret);

        $this->actingAs($user)->post(route('profile.two-factor.enable'), [
            'code' => $this->currentCode($secret),
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
