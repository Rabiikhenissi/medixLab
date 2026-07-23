<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Group;
use App\Models\User;
use App\Models\Action;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            GroupSeeder::class,
            LabSeeder::class,
            ExamSeeder::class,
            UserSeeder::class,
            ExamResourceSeeder::class,
            DemoDataSeeder::class,
        ]);

        $adminGroup = Group::where('code', 'admin')->first();
        if ($adminGroup) {
            $adminGroup->actions()->sync(Action::pluck('id'));
        }

        $doctorGroup = Group::where('code', 'doctor')->first();
        if ($doctorGroup) {
            $doctorGroup->actions()->sync(
                Action::whereIn('code', [
                    'view-doctor-dashboard',
                    'view-patient-search',
                    'view-doctor-exam-groups',
                ])->pluck('id')
            );
        }

        $patientGroup = Group::where('code', 'patient')->first();
        if ($patientGroup) {
            $patientGroup->actions()->sync(
                Action::whereIn('code', [
                    'view-patient-dashboard',
                    'view-patient-exam-requests',
                ])->pluck('id')
            );
        }

        $centerGroup = Group::where('code', 'center')->first();
        if ($centerGroup) {
            $centerGroup->actions()->sync(
                Action::whereIn('code', [
                    'view-center-dashboard',
                    'view-center-exam-requests',
                    'view-center-working-hours',
                    'view-center-consumables',
                    'view-center-equipment',
                ])->pluck('id')
            );
        }
    }
}
