<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BackupCommandTest extends TestCase
{
    private string $tempDb;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tempDb = tempnam(sys_get_temp_dir(), 'medix-backup-').'.sqlite';
        $pdo = new \PDO('sqlite:'.$this->tempDb);
        $pdo->exec('CREATE TABLE ping (id INTEGER PRIMARY KEY, note TEXT)');
        $pdo->exec("INSERT INTO ping (note) VALUES ('ok')");

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', $this->tempDb);
    }

    protected function tearDown(): void
    {
        @unlink($this->tempDb);
        parent::tearDown();
    }

    public function test_backup_command_creates_gzipped_dump(): void
    {
        Storage::fake('local');

        $this->artisan('backup:database')->assertSuccessful();

        $files = Storage::files('backups');
        $this->assertCount(1, $files);
        $this->assertStringEndsWith('.gz', $files[0]);

        $content = gzdecode(Storage::get($files[0]));
        $this->assertNotNull($content);
        $this->assertNotFalse(strpos($content, 'ping'));
    }

    public function test_backup_command_prunes_old_backups(): void
    {
        Storage::fake('local');

        foreach (range(1, 4) as $i) {
            Storage::put("backups/sqlite-old-{$i}.gz", gzencode('dump-'.$i));
            touch(Storage::path("backups/sqlite-old-{$i}.gz"), now()->subDays(10 - $i)->timestamp);
        }

        $this->artisan('backup:database', ['--keep' => '2'])->assertSuccessful();

        $files = Storage::files('backups');
        $this->assertCount(2, $files);
    }
}
