<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('consent_type', 50);
            $table->string('version', 20);
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('accepted_at');
            $table->timestamps();

            // A given user can only accept a specific version of a policy once
            $table->unique(['user_id', 'consent_type', 'version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consents');
    }
};
