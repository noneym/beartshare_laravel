<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// List all databases
$dbs = DB::select('SHOW DATABASES');
echo "=== DATABASES ===\n";
foreach ($dbs as $db) {
    echo "- " . $db->Database . "\n";
}
