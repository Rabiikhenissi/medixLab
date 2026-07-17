<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\Action;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [

            [
                'name' => 'Examen Catalogue',
                'code' => 'exams-management',
                'route_name' => 'admin.dashboard',
                'icon' => 'home',
                'is_sidebar' => true,
                'order' => 1,
                'view_permission' => 'view-exams',
                'actions' => [
                    ['code' => 'view-exams', 'name' => 'View exams'],
                    ['code' => 'create-exams', 'name' => 'Create exams'],
                    ['code' => 'edit-exams', 'name' => 'Edit exams'],
                    ['code' => 'archive-exams', 'name' => 'Archive/Restore exams'],
                ]
            ],

            [
                'name' => 'Utilisateurs',
                'code' => 'users-management',
                'route_name' => 'admin.users.index',
                'icon' => 'users',
                'is_sidebar' => true,
                'order' => 2,
                'view_permission' => 'view-users',
                'actions' => [
                    ['code' => 'view-users', 'name' => 'View users'],
                    ['code' => 'create-users', 'name' => 'Create users'],
                    ['code' => 'edit-users', 'name' => 'Edit users'],
                    ['code' => 'delete-users', 'name' => 'Delete users'],
                ]
            ],

            [
                'name' => 'Rôles & Permissions',
                'code' => 'groups-management',
                'route_name' => 'admin.groups.index',
                'icon' => 'shield-check',
                'is_sidebar' => true,
                'order' => 3,
                'view_permission' => 'view-groups',
                'actions' => [
                    ['code' => 'view-groups', 'name' => 'View groups'],
                    ['code' => 'create-groups', 'name' => 'Create groups'],
                    ['code' => 'edit-groups', 'name' => 'Edit groups'],
                    ['code' => 'delete-groups', 'name' => 'Delete groups'],
                ]
            ],

            [
                'name' => 'Gestion des Modules',
                'code' => 'features-management',
                'route_name' => 'admin.features.index',
                'icon' => 'cog-6-tooth',
                'is_sidebar' => true,
                'order' => 4,
                'view_permission' => 'view-features',
                'actions' => [
                    ['code' => 'view-features', 'name' => 'View features'],
                    ['code' => 'create-features', 'name' => 'Create features'],
                    ['code' => 'edit-features', 'name' => 'Edit features'],
                    ['code' => 'delete-features', 'name' => 'Delete features'],
                ]
            ],

            [
                'name' => 'Laboratoires',
                'code' => 'laboratory-management',
                'route_name' => 'admin.laboratories.index',
                'icon' => 'beaker',
                'is_sidebar' => true,
                'order' => 10,
                'view_permission' => 'view-laboratories',
                'actions' => [
                    ['code' => 'view-laboratories', 'name' => 'View laboratories'],
                    ['code' => 'add-laboratory', 'name' => 'Add laboratory'],
                    ['code' => 'modify-laboratory', 'name' => 'Modify laboratory'],
                    ['code' => 'delete-laboratory', 'name' => 'Delete laboratory'],
                ]
            ],
            [
                'name' => 'Patients',
                'code' => 'patients-management',
                'route_name' => '#',
                'icon' => 'user',
                'is_sidebar' => true,
                'order' => 5,
                'view_permission' => 'view-patients',
                'actions' => [
                    ['code' => 'view-patients', 'name' => 'View patients'],
                    ['code' => 'create-patients', 'name' => 'Create patients'],
                    ['code' => 'edit-patients', 'name' => 'Edit patients'],
                    ['code' => 'delete-patients', 'name' => 'Delete patients'],
                ]
            ],

            [
                'name' => 'Médecins',
                'code' => 'doctors-management',
                'route_name' => '#',
                'icon' => 'academic-cap',
                'is_sidebar' => true,
                'order' => 6,
                'view_permission' => 'view-doctors',
                'actions' => [
                    ['code' => 'view-doctors', 'name' => 'View doctors'],
                    ['code' => 'create-doctors', 'name' => 'Create doctors'],
                    ['code' => 'edit-doctors', 'name' => 'Edit doctors'],
                    ['code' => 'delete-doctors', 'name' => 'Delete doctors'],
                ]
            ],

            [
                'name' => 'Activité',
                'code' => 'activity-logs',
                'route_name' => '#',
                'icon' => 'chart-bar',
                'is_sidebar' => true,
                'order' => 7,
                'view_permission' => 'view-activity',
                'actions' => [
                    ['code' => 'view-activity', 'name' => 'View activity logs'],
                ]
            ],

            [
                'name' => 'Historique',
                'code' => 'history',
                'route_name' => '#',
                'icon' => 'clock',
                'is_sidebar' => true,
                'order' => 8,
                'view_permission' => 'view-history',
                'actions' => [
                    ['code' => 'view-history', 'name' => 'View history'],
                ]
            ],

            [
                'name' => 'Paramètres',
                'code' => 'settings',
                'route_name' => '#',
                'icon' => 'cog-6-tooth',
                'is_sidebar' => true,
                'order' => 99,
                'view_permission' => 'view-settings',
                'actions' => [
                    ['code' => 'view-settings', 'name' => 'View settings'],
                ]
            ],

        ];


        foreach ($permissions as $p) {

            $feature = Feature::updateOrCreate(
                ['code' => $p['code']],
                [
                    'name' => $p['name'],
                    'route_name' => $p['route_name'],
                    'icon' => $p['icon'],
                    'is_sidebar' => $p['is_sidebar'],
                    'order' => $p['order'],
                    'view_permission' => $p['view_permission'],
                    'is_archive' => false,
                ]
            );


            foreach ($p['actions'] as $act) {

                Action::updateOrCreate(
                    ['code' => $act['code']],
                    [
                        'feature_id' => $feature->id,
                        'name' => $act['name'],
                        'is_archive' => false,
                    ]
                );

            }
        }
    }
}