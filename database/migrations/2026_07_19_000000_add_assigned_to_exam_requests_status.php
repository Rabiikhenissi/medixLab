<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (config('database.default') === 'mysql') {
            DB::statement("ALTER TABLE exam_requests MODIFY COLUMN status ENUM('pending', 'assigned', 'collected', 'processing', 'completed', 'cancelled') NOT NULL DEFAULT 'pending'");
        } else {
            Schema::table('exam_requests', function (Blueprint $table) {
                $table->string('status')->default('pending')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (config('database.default') === 'mysql') {
            DB::statement("ALTER TABLE exam_requests MODIFY COLUMN status ENUM('pending', 'collected', 'processing', 'completed', 'cancelled') NOT NULL DEFAULT 'pending'");
        }
    }
};
