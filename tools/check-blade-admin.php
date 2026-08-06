<?php

use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
$compiler = $app['blade.compiler'];

$files = [
    'admin/activity-pdf.blade.php',
    'admin/activity.blade.php',
    'admin/available-exams/create.blade.php',
    'admin/available-exams/edit.blade.php',
    'admin/available-exams/index.blade.php',
    'admin/dashboard.blade.php',
    'admin/exams/create.blade.php',
    'admin/exams/edit.blade.php',
    'admin/exams/index.blade.php',
    'admin/exams/show.blade.php',
    'admin/features/create.blade.php',
    'admin/features/edit.blade.php',
    'admin/features/index.blade.php',
    'admin/gdpr-incidents.blade.php',
    'admin/gdpr.blade.php',
    'admin/groups/create.blade.php',
    'admin/groups/edit.blade.php',
    'admin/groups/index.blade.php',
    'admin/laboratories/create.blade.php',
    'admin/laboratories/edit.blade.php',
    'admin/laboratories/index.blade.php',
    'admin/users/create.blade.php',
    'admin/users/edit.blade.php',
    'admin/users/index.blade.php',
    'admin/users/invite.blade.php',
];

$base = resource_path('views');
$ok = 0;
foreach ($files as $f) {
    $p = $base.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $f);
    if (! is_file($p)) {
        echo "MISSING: $f\n";

        continue;
    }
    try {
        $compiler->compileString(file_get_contents($p));
        $ok++;
    } catch (Throwable $e) {
        echo "ERROR: $f -> ".$e->getMessage()."\n";
    }
}
echo "Compiled OK: $ok/".count($files)."\n";
