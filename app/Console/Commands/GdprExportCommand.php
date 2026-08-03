<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\GdprService;
use Illuminate\Console\Command;

class GdprExportCommand extends Command
{
    protected $signature = 'gdpr:export {user : User ID or email}';

    protected $description = 'Export every personal data held about a user as a JSON file (right of portability)';

    public function handle(GdprService $gdpr): int
    {
        $user = $this->findUser($this->argument('user'));

        if (! $user) {
            $this->error("User '{$this->argument('user')}' not found.");

            return self::FAILURE;
        }

        $export = $gdpr->export($user);

        $directory = storage_path('app/gdpr/exports');
        $filename = 'user-'.$user->id.'-'.now()->format('Ymd-His').'.json';
        $path = $directory.DIRECTORY_SEPARATOR.$filename;

        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        file_put_contents($path, json_encode($export, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->info("Export written to {$path}");

        return self::SUCCESS;
    }

    private function findUser(string $subject): ?User
    {
        return is_numeric($subject)
            ? User::find($subject)
            : User::where('email', $subject)->first();
    }
}
