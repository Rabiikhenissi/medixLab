<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Group;
use App\Models\Action;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create standard groups
        $groups = [
            ['code' => 'admin', 'name' => 'Admin'],
            ['code' => 'doctor', 'name' => 'Doctor'],
            ['code' => 'patient', 'name' => 'Patient'],
            ['code' => 'center', 'name' => 'Medical Center'],
        ];

        foreach ($groups as $g) {
            Group::updateOrCreate(
                ['code' => $g['code']],
                ['name' => $g['name'], 'is_archive' => false]
            );
        }

        // 2. Call the permission seeder
        $this->call(PermissionSeeder::class);

        // 3. Assign all permissions to the admin group
        $adminGroup = Group::where('code', 'admin')->first();
        if ($adminGroup) {
            $allActionIds = Action::all()->pluck('id');
            // Check if relationship is defined - we will update the model shortly, but we can do it via DB insert or sync
            // We will define belongsToMany 'actions' in Group model, so sync is perfect.
            $adminGroup->actions()->sync($allActionIds);
        }

        // 4. Ensure there is at least one admin user in DB
        $adminGroup = Group::where('code', 'admin')->first();
        $adminUser = User::where('email', 'admin@medix.com')->first();
        if (!$adminUser) {
            $user = User::create([
                'first_name' => 'System',
                'last_name' => 'Admin',
                'email' => 'admin@medix.com',
                'phone' => '12345678',
                'password' => Hash::make('password'),
                'group_id' => $adminGroup->id,
                'address' => 'Medix Lab Headquarters',
                'is_archive' => false,
            ]);

            \App\Models\Admin::create([
                'user_id' => $user->id,
            ]);
        } else {
            $adminUser->update(['group_id' => $adminGroup->id]);
        }
    }
}
