<?php

namespace Tests\Feature;

use App\Models\Consent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Tests\TestCase;

class LegalConsentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    public function test_legal_pages_are_publicly_accessible(): void
    {
        $this->get(route('legal.terms'))->assertOk()->assertSee('Conditions Générales');
        $this->get(route('legal.privacy'))->assertOk()->assertSee('RGPD');
        $this->get(route('legal.mentions'))->assertOk()->assertSee('Mentions Légales');
    }

    public function test_registration_requires_consent_to_both_documents(): void
    {
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
        ])->assertSessionHasErrors(['accept_terms', 'accept_privacy']);

        $this->assertDatabaseMissing('users', ['email' => 'amina@example.com']);
    }

    public function test_registration_records_terms_and_privacy_consent(): void
    {
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

        $user = User::where('email', 'amina@example.com')->firstOrFail();

        $this->assertSame(1, Consent::where('user_id', $user->id)->where('consent_type', 'terms')->count());
        $this->assertSame(1, Consent::where('user_id', $user->id)->where('consent_type', 'privacy')->count());
        $this->assertSame('1.0', Consent::where('user_id', $user->id)->where('consent_type', 'terms')->value('version'));
        $this->assertNotNull(Consent::where('user_id', $user->id)->where('consent_type', 'terms')->value('accepted_at'));
    }
}
