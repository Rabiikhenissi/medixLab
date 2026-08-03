<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_parameters', function (Blueprint $table) {
            $table->decimal('critical_low', 10, 3)
                ->nullable()
                ->after('normal_range');

            $table->decimal('critical_high', 10, 3)
                ->nullable()
                ->after('critical_low');
        });
    }

    public function down(): void
    {
        Schema::table('exam_parameters', function (Blueprint $table) {
            $table->dropColumn(['critical_low', 'critical_high']);
        });
    }
};
