<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// List all tables
$tables = DB::select('SHOW TABLES');
echo "=== TABLES ===\n";
foreach ($tables as $table) {
    $name = array_values((array)$table)[0];
    echo "- $name\n";
}
echo "\n";

// Show structure of each table
foreach ($tables as $table) {
    $name = array_values((array)$table)[0];
    echo "=== TABLE: $name ===\n";
    $columns = DB::select("SHOW COLUMNS FROM `$name`");
    foreach ($columns as $col) {
        echo "  {$col->Field} | {$col->Type} | " . ($col->Null === 'YES' ? 'NULL' : 'NOT NULL') . " | {$col->Key} | {$col->Default}\n";
    }
    $count = DB::table($name)->count();
    echo "  [Row count: $count]\n\n";
}
