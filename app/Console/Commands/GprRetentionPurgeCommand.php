<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

/**
 * Purge anonymised accounts whose data has passed the RGPD retention period.
 * Only fully-anonymised accounts (set by the GDPR erasure flow) are removed;
 * real clinical records stay under the laboratory's legal retention duty.
 */
class GprRetentionPurgeCommand extends Command
{
    protected $signature = 'gdpr:retention-purge
        {--days= : Retention period in days (defaults to config legal.retention_days)}
        {--dry-run : List matching accounts without deleting anything}';

    protected $description = 'Delete anonymised accounts past the RGPD retention period';

    public function handle(): int
    {
        $days = (int) ($this->option('days') ?: config('legal.retention_days', 90));
        $cutoff = now()->subDays($days);

        $users = User::query()
            ->where('is_archive', true)
            ->where('first_name', 'Anonymisé')
            ->where('updated_at', '<=', $cutoff)
            ->get();

        $purged = 0;

        foreach ($users as $user) {
            if ($this->option('dry-run')) {
                $this->line("Would purge anonymised account #{$user->id} (anonymised {$user->updated_at->format('d/m/Y')})");

                continue;
            }

            $user->delete();
            $purged++;
            $this->line("Purged anonymised account #{$user->id}");
        }

        $this->info("Retention purge finished: {$purged} account(s) removed, {$users->count()} matched the criteria.");

        return self::SUCCESS;
    }
}
