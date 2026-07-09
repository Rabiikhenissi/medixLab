<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('features', function (Blueprint $table) {
            $table->string('route_name')->nullable()->after('code');
            $table->text('icon')->nullable()->after('route_name');
            $table->boolean('is_sidebar')->default(true)->after('icon');
            $table->integer('order')->default(0)->after('is_sidebar');
            $table->string('view_permission')->nullable()->after('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('features', function (Blueprint $table) {
            $table->dropColumn(['route_name', 'icon', 'is_sidebar', 'order', 'view_permission']);
        });
    }
};
