<?php

use Database\Seeders\DatabaseSeeder;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();
$seeder = new DatabaseSeeder;
$seeder->setContainer($app);
$seeder->run();
echo "Seeding completed successfully.\n";
