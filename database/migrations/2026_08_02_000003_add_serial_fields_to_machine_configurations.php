<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds RS-232 / USB serial connection settings to machine configurations
     * so the app can talk to analyzers wired directly to the PC.
     */
    public function up(): void
    {
        Schema::table('machine_configurations', function (Blueprint $table) {
            $table->string('serial_port', 50)->nullable()->after('protocol');
            $table->integer('baud_rate')->default(9600)->after('serial_port');
            $table->integer('data_bits')->default(8)->after('baud_rate');
            $table->integer('stop_bits')->default(1)->after('data_bits');
            $table->string('parity', 1)->default('N')->after('stop_bits');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('machine_configurations', function (Blueprint $table) {
            $table->dropColumn(['serial_port', 'baud_rate', 'data_bits', 'stop_bits', 'parity']);
        });
    }
};
