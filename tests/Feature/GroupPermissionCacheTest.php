<?php

namespace Tests\Feature;

use App\Models\Action;
use App\Models\Admin;
use App\Models\Feature;
use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Cache;
use Tests\CreatesUsers;
use Tests\TestCase;

class GroupPermissionCacheTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    private Group $group;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);

        $feature = Feature::create(['code' => 'groups-management', 'name' => 'Roles & Permissions']);
        $create = Action::create(['code' => 'create-groups', 'name' => 'Create groups', 'feature_id' => $feature->id]);
        $edit = Action::create(['code' => 'edit-groups', 'name' => 'Edit groups', 'feature_id' => $feature->id]);
        $beta = Action::create(['code' => 'group-beta', 'name' => 'Beta', 'feature_id' => $feature->id]);

        $this->group = Group::create(['name' => 'Editors', 'code' => 'editors']);
        $this->group->actions()->attach([$create->id, $edit->id, $beta->id]);

        $this->admin = $this->makeUser(['group_id' => $this->group->id]);
        Admin::create(['user_id' => $this->admin->id]);
    }

    public function test_updating_group_permissions_flushes_cached_permission_map(): void
    {
        $this->assertTrue($this->admin->hasPermission('edit-groups'));
        $this->assertTrue(Cache::has('group_permissions_'.$this->group->id));

        $beta = Action::where('code', 'group-beta')->firstOrFail();

        $this->actingAs($this->admin)
            ->put(route('admin.groups.update', $this->group), [
                'name' => 'Editors',
                'code' => 'editors',
                'actions' => [$beta->id],
            ])
            ->assertRedirect(route('admin.groups.index'));

        $this->assertFalse(Cache::has('group_permissions_'.$this->group->id));
        $this->assertFalse($this->admin->hasPermission('edit-groups'));
        $this->assertTrue($this->admin->hasPermission('group-beta'));
    }

    public function test_creating_group_with_actions_builds_fresh_permission_cache(): void
    {
        $beta = Action::where('code', 'group-beta')->firstOrFail();

        $this->actingAs($this->admin)
            ->post(route('admin.groups.store'), [
                'name' => 'Operators',
                'code' => 'operators',
                'actions' => [$beta->id],
            ])
            ->assertRedirect(route('admin.groups.index'));

        $group = Group::where('code', 'operators')->firstOrFail();
        $operator = $this->makeUser(['group_id' => $group->id]);

        $this->assertTrue($operator->hasPermission('group-beta'));
        $this->assertFalse($operator->hasPermission('edit-groups'));
        $this->assertSame(['group-beta'], Cache::get('group_permissions_'.$group->id));
    }
}
