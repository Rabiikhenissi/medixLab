<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('translations');
    }

    public function down(): void
    {
        // Recreate only if needed
    }
};
