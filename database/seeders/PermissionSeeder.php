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
                'name' => 'Examen Catalogue',
                'code' => 'exams-management',
                'route_name' => 'admin.dashboard',
                'icon' => '<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>',
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
                'icon' => '<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>',
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
                'icon' => '<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>',
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
                'icon' => '<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>',
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
                'icon' => '<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>',
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
                'icon' => '<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2v6m0 0a4 4 0 00-4 4v2a4 4 0 008 0v-2a4 4 0 00-4-4zm0 0V4" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 10h.01M18 10h.01" /></svg>',
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
                'icon' => '<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z" /></svg>',
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
                'icon' => '<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
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
                'icon' => '<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>',
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
                    'is_archive' => false
                ]
            );

            foreach ($p['actions'] as $act) {
                Action::updateOrCreate(
                    ['code' => $act['code']],
                    [
                        'feature_id' => $feature->id,
                        'name' => $act['name'],
                        'is_archive' => false
                    ]
                );
            }
        }
    }
}
