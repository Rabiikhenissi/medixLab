<?php

namespace Tests\Feature;

use App\Models\Action;
use App\Models\Admin;
use App\Models\Feature;
use App\Models\Group;
use App\Models\Labo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Tests\CreatesUsers;
use Tests\TestCase;

class LaboratoryCountryTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);

        $feature = Feature::create(['code' => 'laboratories', 'name' => 'Laboratories']);
        Action::create(['code' => 'add-laboratory', 'name' => 'Add laboratory', 'feature_id' => $feature->id]);
        Action::create(['code' => 'modify-laboratory', 'name' => 'Modify laboratory', 'feature_id' => $feature->id]);

        $group = Group::create(['name' => 'Admins', 'code' => 'admins']);
        $group->actions()->attach(Action::pluck('id'));

        $this->admin = $this->makeUser(['group_id' => $group->id]);
        Admin::create(['user_id' => $this->admin->id]);
    }

    public function test_center_registration_stores_country_code_as_french_name(): void
    {
        $this->post(route('center.register'), [
            'role' => 'center',
            'center_name' => 'Laboratoire Atlas',
            'responsible' => 'Sarra Trabelsi',
            'email' => 'atlas@example.com',
            'phone' => '12345678',
            'city' => 'Tunis',
            'country' => 'TN',
            'address' => 'Rue de la Liberté',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'accept_terms' => '1',
            'accept_privacy' => '1',
        ]);

        $labo = Labo::where('email', 'atlas@example.com')->firstOrFail();

        $this->assertSame('Tunisie', $labo->country);
        $this->assertSame('Tunis', $labo->city);
    }

    public function test_center_registration_keeps_unknown_country_value(): void
    {
        $this->post(route('center.register'), [
            'role' => 'center',
            'center_name' => 'Laboratoire Nord',
            'responsible' => 'Ali Ben Ali',
            'email' => 'nord@example.com',
            'phone' => '87654321',
            'city' => 'Lyon',
            'country' => 'France',
            'address' => '1 rue Principale',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'accept_terms' => '1',
            'accept_privacy' => '1',
        ]);

        $labo = Labo::where('email', 'nord@example.com')->firstOrFail();

        $this->assertSame('France', $labo->country);
    }

    public function test_admin_can_create_laboratory_with_country(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.laboratories.store'), [
                'name' => 'Laboratoire Atlas',
                'city' => 'Casablanca',
                'country' => 'Maroc',
                'email' => 'atlas@labo.com',
                'phone' => '12345678',
                'address' => '12 Rue Mohammed V',
            ])
            ->assertRedirect(route('admin.laboratories.index'));

        $labo = Labo::where('email', 'atlas@labo.com')->firstOrFail();

        $this->assertSame('Maroc', $labo->country);
        $this->assertSame('Casablanca', $labo->city);
    }

    public function test_admin_can_update_laboratory_country(): void
    {
        $labo = Labo::create([
            'name' => 'Laboratoire Atlas',
            'city' => 'Casablanca',
            'country' => 'Maroc',
        ]);

        $this->actingAs($this->admin)
            ->put(route('admin.laboratories.update', $labo), [
                'name' => 'Laboratoire Atlas',
                'city' => 'Alger',
                'country' => 'Algérie',
            ])
            ->assertRedirect(route('admin.laboratories.index'));

        $this->assertSame('Algérie', $labo->fresh()->country);
        $this->assertSame('Alger', $labo->fresh()->city);
    }
}
