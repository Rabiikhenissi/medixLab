<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Services\TourService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Tests\CreatesUsers;
use Tests\TestCase;

class TourTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    private function makeRoleUser(string $code): array
    {
        $group = Group::create(['code' => $code, 'name' => ucfirst($code)]);

        $user = match ($code) {
            'doctor' => $this->makeDoctor()['user'],
            'patient' => $this->makePatient()['user'],
            'center' => $this->makeStaff($this->makeLabo())['user'],
            default => $this->makeUser(),
        };

        $user->update(['group_id' => $group->id]);
        cache()->forget("group_permissions_{$group->id}");

        return [$user, $group];
    }

    public function test_tour_complete_endpoint_marks_the_tour_as_completed(): void
    {
        [$user] = $this->makeRoleUser('doctor');

        $this->actingAs($user)
            ->postJson(route('tour.complete'))
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertNotNull($user->fresh()->tour_completed_at);
    }

    public function test_doctor_dashboard_renders_the_tour_with_autostart_for_a_new_user(): void
    {
        [$user] = $this->makeRoleUser('doctor');

        $this->actingAs($user)
            ->get(route('doctor.dashboard'))
            ->assertOk()
            ->assertSee('medix-tour-root', false)
            ->assertSee('data-autostart="1"', false)
            ->assertSee('data-complete-url', false);
    }

    public function test_tour_does_not_autostart_once_completed(): void
    {
        [$user] = $this->makeRoleUser('doctor');
        $user->update(['tour_completed_at' => now()]);

        $this->actingAs($user)
            ->get(route('doctor.dashboard'))
            ->assertOk()
            ->assertSee('medix-tour-root', false)
            ->assertSee('data-autostart="0"', false);
    }

    public function test_every_role_has_a_tour_and_all_translations_resolve(): void
    {
        foreach (config('tour.roles') as $role => $steps) {
            $this->assertNotEmpty($steps, "Tour steps missing for role [{$role}].");

            foreach (['fr', 'en'] as $locale) {
                app()->setLocale($locale);

                foreach ($steps as $key => $step) {
                    $title = __($step['title']);
                    $text = __($step['text']);

                    $this->assertNotSame(
                        $step['title'],
                        $title,
                        "Missing tour translation [{$step['title']}] in [{$locale}] for role [{$role}]."
                    );
                    $this->assertNotSame(
                        $step['text'],
                        $text,
                        "Missing tour translation [{$step['text']}] in [{$locale}] for role [{$role}]."
                    );
                }
            }
        }

        foreach (['fr', 'en'] as $locale) {
            app()->setLocale($locale);
            foreach (TourService::UI_KEYS as $key => $translationKey) {
                $this->assertNotSame(
                    $translationKey,
                    __($translationKey),
                    "Missing tour UI translation [{$translationKey}] in [{$locale}]."
                );
            }
        }
    }

    public function test_tour_steps_are_available_for_every_role(): void
    {
        foreach (['admin', 'doctor', 'patient', 'center'] as $role) {
            $this->assertTrue(
                TourService::hasSteps($role),
                "No guided tour defined for role [{$role}]."
            );
        }
    }
}
