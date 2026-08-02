<?php

use App\Models\Action;
use App\Models\Feature;
use App\Models\Group;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $features = [
            [
                'name' => 'Examens Disponibles',
                'code' => 'center-available-exams',
                'route_name' => 'center.available-exams',
                'icon' => 'clipboard-document-list',
                'is_sidebar' => true,
                'order' => 306,
                'view_permission' => 'view-center-available-exams',
            ],
            [
                'name' => 'Configuration Machine',
                'code' => 'center-machine-configurations',
                'route_name' => 'center.machine-configurations.index',
                'icon' => 'cog-6-tooth',
                'is_sidebar' => true,
                'order' => 307,
                'view_permission' => 'view-center-machine-configurations',
            ],
            [
                'name' => 'Facturation',
                'code' => 'center-billing',
                'route_name' => 'center.billing.index',
                'icon' => 'currency-dollar',
                'is_sidebar' => true,
                'order' => 401,
                'view_permission' => 'view-center-billing',
            ],
            [
                'name' => 'Nomenclature CNAM',
                'code' => 'center-cnam',
                'route_name' => 'center.cnam.index',
                'icon' => 'clipboard-document-check',
                'is_sidebar' => true,
                'order' => 402,
                'view_permission' => 'view-center-cnam',
            ],
            [
                'name' => 'Suivi des Échantillons',
                'code' => 'center-samples',
                'route_name' => 'center.samples.index',
                'icon' => 'rectangle-stack',
                'is_sidebar' => true,
                'order' => 403,
                'view_permission' => 'view-center-samples',
            ],
        ];

        $actionIds = [];

        foreach ($features as $f) {
            $feature = Feature::updateOrCreate(
                ['code' => $f['code']],
                [
                    'name' => $f['name'],
                    'route_name' => $f['route_name'],
                    'icon' => $f['icon'],
                    'is_sidebar' => $f['is_sidebar'],
                    'order' => $f['order'],
                    'view_permission' => $f['view_permission'],
                    'is_archive' => false,
                ]
            );

            $action = Action::updateOrCreate(
                ['code' => $f['view_permission']],
                [
                    'feature_id' => $feature->id,
                    'name' => 'View '.$f['code'],
                    'is_archive' => false,
                ]
            );

            $actionIds[] = $action->id;
        }

        $centerGroup = Group::where('code', 'center')->first();
        if ($centerGroup) {
            $existingIds = $centerGroup->actions()->pluck('actions.id')->toArray();
            $centerGroup->actions()->syncWithoutDetaching(array_unique(array_merge($existingIds, $actionIds)));
        }
    }

    public function down(): void
    {
        $codes = [
            'center-available-exams',
            'center-machine-configurations',
            'center-billing',
            'center-cnam',
            'center-samples',
        ];

        $permissions = [
            'view-center-available-exams',
            'view-center-machine-configurations',
            'view-center-billing',
            'view-center-cnam',
            'view-center-samples',
        ];

        $centerGroup = Group::where('code', 'center')->first();
        if ($centerGroup) {
            $actionIds = Action::whereIn('code', $permissions)->pluck('id')->toArray();
            $centerGroup->actions()->detach($actionIds);
        }

        Action::whereIn('code', $permissions)->delete();
        Feature::whereIn('code', $codes)->delete();
    }
};
