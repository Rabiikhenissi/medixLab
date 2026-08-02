<?php

use App\Models\Feature;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Feature::where('code', 'laboratory-management')->update([
            'route_name' => 'admin.laboratories.index',
            'icon' => 'beaker',
            'is_sidebar' => true,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Feature::where('code', 'laboratory-management')->update([
            'route_name' => null,
            'icon' => null,
            'is_sidebar' => false,
        ]);
    }
};
