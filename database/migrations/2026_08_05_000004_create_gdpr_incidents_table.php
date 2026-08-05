<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gdpr_incidents', function (Blueprint $table) {
            $table->id();
            $table->string('incident_type', 50);
            $table->string('severity', 20)->default('medium');
            $table->text('description');
            $table->unsignedInteger('affected_users_count')->nullable();
            $table->timestamp('detected_at');
            $table->timestamp('notified_authority_at')->nullable();
            $table->timestamp('notified_affected_at')->nullable();
            $table->string('status', 20)->default('open');
            $table->text('resolution')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gdpr_incidents');
    }
};
