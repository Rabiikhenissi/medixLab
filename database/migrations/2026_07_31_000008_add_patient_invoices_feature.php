<?php

use App\Models\Action;
use App\Models\Feature;
use App\Models\Group;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $feature = Feature::updateOrCreate(
            ['code' => 'patient-invoices'],
            [
                'name' => 'Mes Factures',
                'route_name' => 'patient.invoices.index',
                'icon' => 'currency-dollar',
                'is_sidebar' => true,
                'order' => 206,
                'view_permission' => 'view-patient-invoices',
                'is_archive' => false,
            ]
        );

        $action = Action::updateOrCreate(
            ['code' => 'view-patient-invoices'],
            [
                'feature_id' => $feature->id,
                'name' => 'View patient invoices',
                'is_archive' => false,
            ]
        );

        $patientGroup = Group::where('code', 'patient')->first();
        if ($patientGroup) {
            $existingIds = $patientGroup->actions()->pluck('actions.id')->toArray();
            $patientGroup->actions()->syncWithoutDetaching(array_unique(array_merge($existingIds, [$action->id])));
        }
    }

    public function down(): void
    {
        $patientGroup = Group::where('code', 'patient')->first();
        if ($patientGroup) {
            $actionIds = Action::where('code', 'view-patient-invoices')->pluck('id')->toArray();
            $patientGroup->actions()->detach($actionIds);
        }

        Action::where('code', 'view-patient-invoices')->delete();
        Feature::where('code', 'patient-invoices')->delete();
    }
};
