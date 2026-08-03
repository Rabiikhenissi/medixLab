<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class DatabaseBackupCommand extends Command
{
    protected $signature = 'backup:database {--keep=14 : Number of backups to keep}';

    protected $description = 'Create a compressed database backup (mysqldump/pg_dump/sqlite) and prune old ones';

    public function handle(): int
    {
        $backupDir = 'backups';
        $timestamp = now()->format('Y-m-d-His');
        $driver = config('database.default');
        $filename = $driver.'-'.$timestamp;
        $rawPath = $this->dumpRaw($driver, $filename);
        $gzPath = $rawPath.'.gz';

        $content = file_get_contents($rawPath);
        file_put_contents($gzPath, gzencode($content, 6));
        unlink($rawPath);

        $this->info("Backup created: {$gzPath}");

        $this->prune($backupDir, (int) $this->option('keep'));

        return self::SUCCESS;
    }

    private function dumpRaw(string $driver, string $filename): string
    {
        $backupDir = 'backups';
        $rawPath = Storage::path($backupDir.'/'.$filename);

        if (! is_dir(dirname($rawPath))) {
            mkdir(dirname($rawPath), 0775, true);
        }

        return match ($driver) {
            'mysql' => $this->dumpMysql($rawPath),
            'pgsql' => $this->dumpPgsql($rawPath),
            default => $this->dumpSqlite($rawPath),
        };
    }

    private function dumpMysql(string $rawPath): string
    {
        $c = config('database.connections.mysql');
        $binary = env('MYSQLDUMP_PATH', 'mysqldump');

        $process = new Process([
            $binary,
            '--single-transaction',
            '--no-tablespaces',
            '--routines',
            '--triggers',
            '--quick',
            '--host='.$c['host'],
            '--port='.($c['port'] ?? 3306),
            '--user='.$c['username'],
            $c['database'],
        ], null, ['MYSQL_PWD' => $c['password'] ?? '']);

        return $this->runProcess($process, $rawPath);
    }

    private function dumpPgsql(string $rawPath): string
    {
        $c = config('database.connections.pgsql');
        $binary = env('PGDUMP_PATH', 'pg_dump');

        $process = new Process([
            $binary,
            '--host='.$c['host'],
            '--port='.($c['port'] ?? 5432),
            '--username='.$c['username'],
            '--format=custom',
            $c['database'],
        ], null, ['PGPASSWORD' => $c['password'] ?? '']);

        return $this->runProcess($process, $rawPath);
    }

    private function dumpSqlite(string $rawPath): string
    {
        $database = config('database.connections.sqlite.database');
        if ($database === ':memory:') {
            $database = database_path('database.sqlite');
        }

        copy($database, $rawPath);

        return $rawPath;
    }

    private function runProcess(Process $process, string $rawPath): string
    {
        $process->setTimeout(600);
        $process->run();

        if (! $process->isSuccessful()) {
            $this->error($process->getErrorOutput() ?: 'Dump failed.');

            throw new \RuntimeException('Database dump failed: '.$process->getErrorOutput());
        }

        file_put_contents($rawPath, $process->getOutput());

        return $rawPath;
    }

    private function prune(string $backupDir, int $keep): void
    {
        $files = collect(Storage::files($backupDir))
            ->filter(fn ($f) => str_ends_with($f, '.gz'))
            ->sortByDesc(fn ($f) => Storage::lastModified($f));

        $files->slice($keep)->each(function ($f) {
            Storage::delete($f);
            $this->line("Pruned old backup: {$f}");
        });
    }
}
