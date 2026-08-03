<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\GdprService;
use Illuminate\Console\Command;

class GdprEraseCommand extends Command
{
    protected $signature = 'gdpr:erase {user : User ID or email} {--hard : Also delete the account and its profile}';

    protected $description = 'Anonymise (or fully erase) a user account while keeping clinical records for lab retention';

    public function handle(GdprService $gdpr): int
    {
        $user = is_numeric($this->argument('user'))
            ? User::find($this->argument('user'))
            : User::where('email', $this->argument('user'))->first();

        if (! $user) {
            $this->error("User '{$this->argument('user')}' not found.");

            return self::FAILURE;
        }

        if (! $this->confirm('This action is irreversible. Continue?', false)) {
            $this->info('Aborted.');

            return self::SUCCESS;
        }

        $gdpr->erase($user, (bool) $this->option('hard'));

        $this->info($this->option('hard')
            ? "Account #{$user->id} fully erased."
            : "Account #{$user->id} anonymised.");

        return self::SUCCESS;
    }
}
