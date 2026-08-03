<?php

namespace Tests\Feature;

use App\Models\Action;
use App\Models\Exam;
use App\Models\Group;
use App\Models\Labo;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class FreshInstallTest extends TestCase
{
    private string $tempDb;

    protected function setUp(): void
    {
        parent::setUp();

        // use a real file-backed SQLite database so `migrate:fresh` (which
        // runs VACUUM) works, unlike the :memory: connection used by default
        $this->tempDb = tempnam(sys_get_temp_dir(), 'medix-install-').'.sqlite';
        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', $this->tempDb);
    }

    protected function tearDown(): void
    {
        @unlink($this->tempDb);
        parent::tearDown();
    }

    public function test_migrate_fresh_with_seed_produces_a_working_installation(): void
    {
        // simulate a brand-new deployment: wipe everything, re-migrate and seed
        $this->artisan('migrate:fresh', ['--seed' => true])->assertExitCode(0);

        // core groups exist
        $this->assertDatabaseHas('groups', ['code' => 'admin']);
        $this->assertDatabaseHas('groups', ['code' => 'doctor']);
        $this->assertDatabaseHas('groups', ['code' => 'patient']);
        $this->assertDatabaseHas('groups', ['code' => 'center']);

        // a seeded administrator account is usable
        $admin = User::where('email', 'admin@medix.com')->first();
        $this->assertNotNull($admin);
        $this->assertSame('admin', $admin->group?->code);
        $this->assertNotNull($admin->admin);

        // RBAC: the admin group is granted every seeded action
        $adminGroup = Group::where('code', 'admin')->firstOrFail();
        $this->assertSame(Action::count(), $adminGroup->actions()->count());

        // catalogs and demo content were seeded
        $this->assertTrue(Exam::count() > 0, 'Exams should be seeded.');
        $this->assertTrue(Labo::count() > 0, 'Laboratories should be seeded.');
        $this->assertTrue(User::count() > 1, 'Seed accounts should be created.');

        // new schema objects introduced by the hardening phase are present
        $this->assertTrue(Schema::hasColumn('users', 'two_factor_secret'));
        $this->assertTrue(Schema::hasColumn('users', 'two_factor_confirmed_at'));
        $this->assertTrue(Schema::hasTable('audit_logs'));
    }

    public function test_migrations_are_idempotent_from_a_clean_state(): void
    {
        $this->artisan('migrate')->assertExitCode(0);

        // re-running migrate on a fully migrated database is a no-op
        $this->artisan('migrate')->assertExitCode(0);

        $this->assertTrue(Schema::hasTable('users'));
    }
}
