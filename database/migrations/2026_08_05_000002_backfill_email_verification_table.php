<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Email verification was introduced after several accounts were created.
 * Backfill those existing accounts so nobody is locked out of their dashboard.
 */
return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $unverified = DB::table('users')
            ->whereNull('email_verified_at')
            ->select('id', 'created_at')
            ->get();

        foreach ($unverified as $user) {
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'email_verified_at' => $user->created_at ?? $now,
                    'updated_at' => $now,
                ]);
        }
    }

    public function down(): void
    {
        // No-op: we cannot safely un-verify accounts created before this change.
    }
};
