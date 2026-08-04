<?php

namespace Database\Seeders;

use App\Models\Action;
use App\Models\Feature;
use App\Models\Group;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            ['code' => 'admin', 'name' => 'Admin', 'role_table' => 'admin'],
            ['code' => 'doctor', 'name' => 'Doctor', 'role_table' => 'doctor'],
            ['code' => 'patient', 'name' => 'Patient', 'role_table' => 'patient'],
            ['code' => 'center', 'name' => 'Medical Center', 'role_table' => 'staff'],
        ];

        foreach ($groups as $g) {
            Group::updateOrCreate(['code' => $g['code']], ['name' => $g['name'], 'role_table' => $g['role_table'], 'is_archive' => false]);
        }

        $permissions = [
            [
                'name' => 'Examen Catalogue', 'code' => 'exams-management',
                'route_name' => 'admin.dashboard', 'icon' => 'home', 'is_sidebar' => true, 'order' => 1,
                'view_permission' => 'view-exams',
                'actions' => [
                    ['code' => 'view-exams', 'name' => 'View exams'],
                    ['code' => 'create-exams', 'name' => 'Create exams'],
                    ['code' => 'edit-exams', 'name' => 'Edit exams'],
                    ['code' => 'archive-exams', 'name' => 'Archive/Restore exams'],
                ],
            ],
            [
                'name' => 'Utilisateurs', 'code' => 'users-management',
                'route_name' => 'admin.users.index', 'icon' => 'users', 'is_sidebar' => true, 'order' => 2,
                'view_permission' => 'view-users',
                'actions' => [
                    ['code' => 'view-users', 'name' => 'View users'],
                    ['code' => 'create-users', 'name' => 'Create users'],
                    ['code' => 'edit-users', 'name' => 'Edit users'],
                    ['code' => 'delete-users', 'name' => 'Delete users'],
                ],
            ],
            [
                'name' => 'Roles & Permissions', 'code' => 'groups-management',
                'route_name' => 'admin.groups.index', 'icon' => 'shield-check', 'is_sidebar' => true, 'order' => 3,
                'view_permission' => 'view-groups',
                'actions' => [
                    ['code' => 'view-groups', 'name' => 'View groups'],
                    ['code' => 'create-groups', 'name' => 'Create groups'],
                    ['code' => 'edit-groups', 'name' => 'Edit groups'],
                    ['code' => 'delete-groups', 'name' => 'Delete groups'],
                ],
            ],
            [
                'name' => 'Modules', 'code' => 'features-management',
                'route_name' => 'admin.features.index', 'icon' => 'cog-6-tooth', 'is_sidebar' => true, 'order' => 4,
                'view_permission' => 'view-features',
                'actions' => [
                    ['code' => 'view-features', 'name' => 'View features'],
                    ['code' => 'create-features', 'name' => 'Create features'],
                    ['code' => 'edit-features', 'name' => 'Edit features'],
                    ['code' => 'delete-features', 'name' => 'Delete features'],
                ],
            ],
            [
                'name' => 'Laboratoires', 'code' => 'laboratory-management',
                'route_name' => 'admin.laboratories.index', 'icon' => 'beaker', 'is_sidebar' => true, 'order' => 10,
                'view_permission' => 'view-laboratories',
                'actions' => [
                    ['code' => 'view-laboratories', 'name' => 'View laboratories'],
                    ['code' => 'add-laboratory', 'name' => 'Add laboratory'],
                    ['code' => 'modify-laboratory', 'name' => 'Modify laboratory'],
                    ['code' => 'delete-laboratory', 'name' => 'Delete laboratory'],
                ],
            ],
            [
                'name' => 'Patients', 'code' => 'patients-management',
                'route_name' => '#', 'icon' => 'user', 'is_sidebar' => true, 'order' => 5,
                'view_permission' => 'view-patients',
                'actions' => [
                    ['code' => 'view-patients', 'name' => 'View patients'],
                    ['code' => 'create-patients', 'name' => 'Create patients'],
                    ['code' => 'edit-patients', 'name' => 'Edit patients'],
                    ['code' => 'delete-patients', 'name' => 'Delete patients'],
                ],
            ],
            [
                'name' => 'Medecins', 'code' => 'doctors-management',
                'route_name' => '#', 'icon' => 'academic-cap', 'is_sidebar' => true, 'order' => 6,
                'view_permission' => 'view-doctors',
                'actions' => [
                    ['code' => 'view-doctors', 'name' => 'View doctors'],
                    ['code' => 'create-doctors', 'name' => 'Create doctors'],
                    ['code' => 'edit-doctors', 'name' => 'Edit doctors'],
                    ['code' => 'delete-doctors', 'name' => 'Delete doctors'],
                ],
            ],
            [
                'name' => 'Activite', 'code' => 'activity-logs',
                'route_name' => '#', 'icon' => 'chart-bar', 'is_sidebar' => true, 'order' => 7,
                'view_permission' => 'view-activity',
                'actions' => [['code' => 'view-activity', 'name' => 'View activity logs']],
            ],
            [
                'name' => 'Historique', 'code' => 'history',
                'route_name' => '#', 'icon' => 'clock', 'is_sidebar' => true, 'order' => 8,
                'view_permission' => 'view-history',
                'actions' => [['code' => 'view-history', 'name' => 'View history']],
            ],
            [
                'name' => 'Parametres', 'code' => 'settings',
                'route_name' => '#', 'icon' => 'cog-6-tooth', 'is_sidebar' => true, 'order' => 99,
                'view_permission' => 'view-settings',
                'actions' => [['code' => 'view-settings', 'name' => 'View settings']],
            ],
            [
                'name' => 'Tableau de bord Docteur', 'code' => 'doctor-dashboard',
                'route_name' => 'doctor.dashboard', 'icon' => 'home', 'is_sidebar' => true, 'order' => 101,
                'view_permission' => 'view-doctor-dashboard',
                'actions' => [['code' => 'view-doctor-dashboard', 'name' => 'View doctor dashboard']],
            ],
            [
                'name' => 'Rechercher Patient', 'code' => 'doctor-patient-search',
                'route_name' => 'doctor.patient-search', 'icon' => 'magnifying-glass', 'is_sidebar' => true, 'order' => 102,
                'view_permission' => 'view-patient-search',
                'actions' => [['code' => 'view-patient-search', 'name' => 'Search patient']],
            ],
            [
                'name' => 'Groupes d\'examens', 'code' => 'doctor-exam-groups',
                'route_name' => 'doctor.exam-groups.index', 'icon' => 'folder', 'is_sidebar' => true, 'order' => 103,
                'view_permission' => 'view-doctor-exam-groups',
                'actions' => [['code' => 'view-doctor-exam-groups', 'name' => 'Manage doctor exam groups']],
            ],
            [
                'name' => 'Tableau de bord Patient', 'code' => 'patient-dashboard',
                'route_name' => 'patient.dashboard', 'icon' => 'home', 'is_sidebar' => true, 'order' => 201,
                'view_permission' => 'view-patient-dashboard',
                'actions' => [['code' => 'view-patient-dashboard', 'name' => 'View patient dashboard']],
            ],
            [
                'name' => 'Mes Factures', 'code' => 'patient-invoices',
                'route_name' => 'patient.invoices.index', 'icon' => 'currency-dollar', 'is_sidebar' => true, 'order' => 206,
                'view_permission' => 'view-patient-invoices',
                'actions' => [['code' => 'view-patient-invoices', 'name' => 'View patient invoices']],
            ],
            [
                'name' => 'Mes Analyses', 'code' => 'patient-exam-requests',
                'route_name' => 'patient.get-exam-requests', 'icon' => 'document-text', 'is_sidebar' => true, 'order' => 202,
                'view_permission' => 'view-patient-exam-requests',
                'actions' => [['code' => 'view-patient-exam-requests', 'name' => 'View patient exam requests']],
            ],
            [
                'name' => 'Tableau de bord Etablissement', 'code' => 'center-dashboard',
                'route_name' => 'center.dashboard', 'icon' => 'home', 'is_sidebar' => true, 'order' => 301,
                'view_permission' => 'view-center-dashboard',
                'actions' => [['code' => 'view-center-dashboard', 'name' => 'View center dashboard']],
            ],
            [
                'name' => 'Demandes d\'analyses', 'code' => 'center-exam-requests',
                'route_name' => 'center.exam-requests', 'icon' => 'document-text', 'is_sidebar' => true, 'order' => 302,
                'view_permission' => 'view-center-exam-requests',
                'actions' => [['code' => 'view-center-exam-requests', 'name' => 'View center exam requests']],
            ],
            [
                'name' => 'Horaires', 'code' => 'center-working-hours',
                'route_name' => 'center.working-hours', 'icon' => 'clock', 'is_sidebar' => true, 'order' => 303,
                'view_permission' => 'view-center-working-hours',
                'actions' => [['code' => 'view-center-working-hours', 'name' => 'View center working hours']],
            ],
            [
                'name' => 'Stock & Consommables', 'code' => 'center-consumables',
                'route_name' => 'center.consumables', 'icon' => 'beaker', 'is_sidebar' => true, 'order' => 304,
                'view_permission' => 'view-center-consumables',
                'actions' => [['code' => 'view-center-consumables', 'name' => 'View center consumables']],
            ],
            [
                'name' => 'Equipements & Maintenance', 'code' => 'center-equipment',
                'route_name' => 'center.equipment', 'icon' => 'wrench', 'is_sidebar' => true, 'order' => 305,
                'view_permission' => 'view-center-equipment',
                'actions' => [['code' => 'view-center-equipment', 'name' => 'View center equipment']],
            ],
            // LIS Features
            [
                'name' => 'Examens Disponibles', 'code' => 'center-available-exams',
                'route_name' => 'center.available-exams', 'icon' => 'clipboard-document-list', 'is_sidebar' => true, 'order' => 306,
                'view_permission' => 'view-center-available-exams',
                'actions' => [['code' => 'view-center-available-exams', 'name' => 'View available exams']],
            ],
            [
                'name' => 'Configuration Machine', 'code' => 'center-machine-configurations',
                'route_name' => 'center.machine-configurations.index', 'icon' => 'cog-6-tooth', 'is_sidebar' => true, 'order' => 307,
                'view_permission' => 'view-center-machine-configurations',
                'actions' => [['code' => 'view-center-machine-configurations', 'name' => 'View machine configurations']],
            ],
            [
                'name' => 'Facturation', 'code' => 'center-billing',
                'route_name' => 'center.billing.index', 'icon' => 'currency-dollar', 'is_sidebar' => true, 'order' => 401,
                'view_permission' => 'view-center-billing',
                'actions' => [['code' => 'view-center-billing', 'name' => 'View billing']],
            ],
            [
                'name' => 'Nomenclature CNAM', 'code' => 'center-cnam',
                'route_name' => 'center.cnam.index', 'icon' => 'clipboard-document-check', 'is_sidebar' => true, 'order' => 402,
                'view_permission' => 'view-center-cnam',
                'actions' => [['code' => 'view-center-cnam', 'name' => 'View CNAM']],
            ],
            [
                'name' => 'Suivi des Échantillons', 'code' => 'center-samples',
                'route_name' => 'center.samples.index', 'icon' => 'rectangle-stack', 'is_sidebar' => true, 'order' => 403,
                'view_permission' => 'view-center-samples',
                'actions' => [['code' => 'view-center-samples', 'name' => 'View samples']],
            ],
        ];

        foreach ($permissions as $p) {
            $feature = Feature::updateOrCreate(
                ['code' => $p['code']],
                [
                    'name' => $p['name'], 'route_name' => $p['route_name'], 'icon' => $p['icon'],
                    'is_sidebar' => $p['is_sidebar'], 'order' => $p['order'],
                    'view_permission' => $p['view_permission'], 'is_archive' => false,
                ]
            );

            foreach ($p['actions'] as $act) {
                Action::updateOrCreate(
                    ['code' => $act['code']],
                    ['feature_id' => $feature->id, 'name' => $act['name'], 'is_archive' => false]
                );
            }
        }

        $this->command->info('Groups, features, actions seeded.');
    }
}
