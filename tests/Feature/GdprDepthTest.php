<?php

namespace Tests\Feature;

use App\Models\Action;
use App\Models\Admin;
use App\Models\AuditLog;
use App\Models\Feature;
use App\Models\GdprIncident;
use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Tests\CreatesUsers;
use Tests\TestCase;

class GdprDepthTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);

        $feature = Feature::create(['code' => 'gdpr', 'name' => 'RGPD']);
        Action::create(['code' => 'view-gdpr', 'name' => 'View RGPD', 'feature_id' => $feature->id]);
        Action::create(['code' => 'manage-gdpr', 'name' => 'Manage RGPD', 'feature_id' => $feature->id]);

        $group = Group::create(['name' => 'Admins', 'code' => 'admins']);
        $group->actions()->attach(Action::pluck('id'));

        $this->admin = $this->makeUser(['group_id' => $group->id]);
        Admin::create(['user_id' => $this->admin->id]);
    }

    public function test_retention_purge_removes_anonymised_accounts_past_retention(): void
    {
        $old = $this->makeUser([
            'first_name' => 'Anonymisé',
            'last_name' => 'Utilisateur',
            'is_archive' => true,
        ]);
        $old->forceFill(['updated_at' => now()->subDays(120)])->saveQuietly();

        $recent = $this->makeUser([
            'first_name' => 'Anonymisé',
            'last_name' => 'Utilisateur',
            'is_archive' => true,
        ]);
        $recent->forceFill(['updated_at' => now()->subDays(1)])->saveQuietly();

        $active = $this->makeUser(['is_archive' => false]);

        $this->artisan('gdpr:retention-purge')->assertSuccessful();

        $this->assertDatabaseMissing('users', ['id' => $old->id]);
        $this->assertDatabaseHas('users', ['id' => $recent->id]);
        $this->assertDatabaseHas('users', ['id' => $active->id]);
    }

    public function test_retention_purge_dry_run_deletes_nothing(): void
    {
        $old = $this->makeUser([
            'first_name' => 'Anonymisé',
            'last_name' => 'Utilisateur',
            'is_archive' => true,
        ]);
        $old->forceFill(['updated_at' => now()->subDays(120)])->saveQuietly();

        $this->artisan('gdpr:retention-purge', ['--dry-run' => true])->assertSuccessful();

        $this->assertDatabaseHas('users', ['id' => $old->id]);
    }

    public function test_admin_can_declare_and_resolve_an_incident(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.gdpr.incidents.store'), [
                'incident_type' => 'data_breach',
                'severity' => 'high',
                'description' => 'Accès non autorisé détecté sur un compte administrateur.',
                'affected_users_count' => 5,
                'detected_at' => now()->format('Y-m-d'),
            ])
            ->assertRedirect(route('admin.gdpr.incidents'));

        $incident = GdprIncident::firstOrFail();
        $this->assertSame('open', $incident->status);
        $this->assertSame(5, $incident->affected_users_count);

        $this->assertSame(1, AuditLog::where('action', 'gdpr-incident-report')->count());

        $this->actingAs($this->admin)
            ->post(route('admin.gdpr.incidents.resolve', $incident), [
                'resolution' => 'Mots de passe révoqués et audit approfondi lancé.',
            ])
            ->assertRedirect(route('admin.gdpr.incidents'));

        $this->assertSame('resolved', $incident->fresh()->status);
    }

    public function test_incident_register_is_listed_for_admins(): void
    {
        GdprIncident::create([
            'incident_type' => 'data_loss',
            'severity' => 'medium',
            'description' => 'Fichier d\'export supprimé par erreur.',
            'detected_at' => now(),
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.gdpr.incidents'))
            ->assertOk()
            ->assertSee('Fichier d\'export');
    }
}
