<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

/**
 * Purge anonymised accounts whose data has passed the RGPD retention period.
 *
 * Only fully-anonymised accounts (set by the GDPR erasure flow) with NO clinical
 * records are removed. Accounts that still carry exam requests, samples, invoices
 * or CNAM affiliations are kept (they are already anonymised, so they are no
 * longer personal data) because their clinical records must stay for the
 * laboratory's legal retention duty.
 */
class GprRetentionPurgeCommand extends Command
{
    protected $signature = 'gdpr:retention-purge
        {--days= : Retention period in days (defaults to config legal.retention_days)}
        {--dry-run : List matching accounts without deleting anything}';

    protected $description = 'Delete anonymised accounts past the RGPD retention period (clinical records are preserved)';

    public function handle(): int
    {
        $days = (int) ($this->option('days') ?: config('legal.retention_days', 90));
        $cutoff = now()->subDays($days);

        $users = User::query()
            ->where('is_archive', true)
            ->where('first_name', 'Anonymisé')
            ->where('updated_at', '<=', $cutoff)
            ->whereDoesntHave('patient.examRequests')
            ->whereDoesntHave('patient.samples')
            ->whereDoesntHave('patient.invoices')
            ->whereDoesntHave('patient.cnamAffiliation')
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

        $kept = User::query()
            ->where('is_archive', true)
            ->where('first_name', 'Anonymisé')
            ->where('updated_at', '<=', $cutoff)
            ->whereHas('patient', function ($query) {
                $query->where(fn ($q) => $q->whereHas('examRequests')
                    ->orWhereHas('samples')
                    ->orWhereHas('invoices')
                    ->orWhereHas('cnamAffiliation'));
            })
            ->count();

        $this->info("Retention purge finished: {$purged} account(s) removed, {$users->count()} matched the criteria.");
        if ($kept > 0) {
            $this->info("{$kept} anonymised account(s) kept: they carry clinical records which must stay for the laboratory's legal retention duty.");
        }

        return self::SUCCESS;
    }
}
