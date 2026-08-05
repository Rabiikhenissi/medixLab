<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesUsers;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    public function test_default_locale_is_french(): void
    {
        $this->get('/home')
            ->assertOk()
            ->assertSee('Des')
            ->assertSee('Espace Médecin')
            ->assertSee('Connexion Médecin');
    }

    public function test_lang_parameter_switches_to_english_and_persists(): void
    {
        $this->get('/home?lang=en')
            ->assertOk()
            ->assertSee('Advanced')
            ->assertSee('Doctor Space')
            ->assertSee('Doctor Login');

        $this->assertSame('en', session('locale'));

        // The switch is sticky for the rest of the session
        $this->get('/home')
            ->assertOk()
            ->assertSee('Advanced')
            ->assertDontSee('Espace Médecin');
    }

    public function test_switcher_preserves_the_current_path(): void
    {
        $this->get('/patient/login')
            ->assertOk()
            ->assertSee('/patient/login?lang=en');

        $this->get('/patient/login?lang=en')
            ->assertOk()
            ->assertSee('Patient Space');
    }

    public function test_invalid_lang_param_falls_back_to_default(): void
    {
        $this->get('/home?lang=xx')
            ->assertOk()
            ->assertSee('Espace Médecin');

        $this->assertNull(session('locale'));
    }

    public function test_role_pages_translate(): void
    {
        $this->get(route('doctor.login').'?lang=en')->assertSee('Doctor Space');
        $this->get(route('patient.login').'?lang=en')->assertSee('Patient Space');
        $this->get(route('center.login').'?lang=en')->assertSee('Medical Center Space');

        $this->get(route('patient.register').'?lang=en')
            ->assertSee('Patient Registration')
            ->assertSee('First name');

        $this->get(route('doctor.register').'?lang=en')
            ->assertSee('Doctor Registration')
            ->assertSee('Select a specialty');

        $this->get(route('center.register').'?lang=en')
            ->assertSee('Center Registration')
            ->assertSee('Center name');
    }

    public function test_authenticated_dashboard_renders_with_switcher(): void
    {
        $doctor = $this->makeDoctor();

        $this->actingAs($doctor['user'])
            ->withSession(['locale' => 'en'])
            ->get(route('doctor.dashboard'))
            ->assertOk()
            ->assertSee('?lang=fr');
    }
}
