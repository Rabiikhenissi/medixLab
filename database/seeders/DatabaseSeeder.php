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
        // Run seeders
        $this->call([
            PermissionSeeder::class,
            ExamSeeder::class,   // <-- Add this
        ]);
        // 3. Assign all permissions to the admin group
        $adminGroup = Group::where('code', 'admin')->first();
        if ($adminGroup) {
            $allActionIds = Action::all()->pluck('id');
            // Check if relationship is defined - we will update the model shortly, but we can do it via DB insert or sync
            // We will define belongsToMany 'actions' in Group model, so sync is perfect.
            $adminGroup->actions()->sync($allActionIds);
        }

        // Assign specific permissions to the doctor group
        $doctorGroup = Group::where('code', 'doctor')->first();
        if ($doctorGroup) {
            $doctorActionIds = Action::whereIn('code', [
                'view-doctor-dashboard',
                'view-patient-search',
                'view-doctor-exam-groups'
            ])->pluck('id');
            $doctorGroup->actions()->sync($doctorActionIds);
        }

        // Assign specific permissions to the patient group
        $patientGroup = Group::where('code', 'patient')->first();
        if ($patientGroup) {
            $patientActionIds = Action::whereIn('code', [
                'view-patient-dashboard',
                'view-patient-exam-requests'
            ])->pluck('id');
            $patientGroup->actions()->sync($patientActionIds);
        }

        // Assign specific permissions to the center group
        $centerGroup = Group::where('code', 'center')->first();
        if ($centerGroup) {
            $centerActionIds = Action::whereIn('code', [
                'view-center-dashboard',
                'view-center-exam-requests',
                'view-center-working-hours',
                'view-center-consumables',
                'view-center-equipment'
            ])->pluck('id');
            $centerGroup->actions()->sync($centerActionIds);
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
