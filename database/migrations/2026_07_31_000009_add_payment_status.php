<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('status', 20)->default('confirmed')->after('payment_method');
            $table->timestamp('confirmed_at')->nullable()->after('notes');
        });

        // Set existing payments as confirmed
        DB::table('payments')->update(['status' => 'confirmed', 'confirmed_at' => now()]);
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['status', 'confirmed_at']);
        });
    }
};
