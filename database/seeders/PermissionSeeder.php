<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\Action;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            [
                'name' => 'Users Management',
                'code' => 'users-management',
                'actions' => [
                    ['code' => 'view-users', 'name' => 'View users'],
                    ['code' => 'create-users', 'name' => 'Create users'],
                    ['code' => 'edit-users', 'name' => 'Edit users'],
                    ['code' => 'delete-users', 'name' => 'Delete users'],
                ]
            ],
            [
                'name' => 'Group Management',
                'code' => 'groups-management',
                'actions' => [
                    ['code' => 'view-groups', 'name' => 'View groups'],
                    ['code' => 'create-groups', 'name' => 'Create groups'],
                    ['code' => 'edit-groups', 'name' => 'Edit groups'],
                    ['code' => 'delete-groups', 'name' => 'Delete groups'],
                ]
            ],
            [
                'name' => 'Exams Management',
                'code' => 'exams-management',
                'actions' => [
                    ['code' => 'view-exams', 'name' => 'View exams'],
                    ['code' => 'create-exams', 'name' => 'Create exams'],
                    ['code' => 'edit-exams', 'name' => 'Edit exams'],
                    ['code' => 'archive-exams', 'name' => 'Archive/Restore exams'],
                ]
            ],
            [
                'name' => 'Laboratory Management',
                'code' => 'laboratory-management',
                'actions' => [
                    ['code' => 'view-laboratories', 'name' => 'View laboratories'],
                    ['code' => 'add-laboratory', 'name' => 'Add laboratory'],
                    ['code' => 'modify-laboratory', 'name' => 'Modify laboratory'],
                    ['code' => 'delete-laboratory', 'name' => 'Delete laboratory'],
                ]
            ],
        ];

        foreach ($permissions as $p) {
            $feature = Feature::updateOrCreate(
                ['code' => $p['code']],
                ['name' => $p['name'], 'is_archive' => false]
            );

            foreach ($p['actions'] as $act) {
                Action::updateOrCreate(
                    ['code' => $act['code']],
                    ['feature_id' => $feature->id, 'name' => $act['name'], 'is_archive' => false]
                );
            }
        }
    }
}
