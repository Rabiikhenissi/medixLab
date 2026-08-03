<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Enforce a single sample per exam request item (concurrency-safe).
     */
    public function up(): void
    {
        Schema::table('samples', function (Blueprint $table) {
            $table->unique('exam_request_item_id', 'samples_item_unique');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::table('samples', function (Blueprint $table) {
            $table->dropUnique('samples_item_unique');
        });
    }
};
