<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Connect to OLD database directly via PDO
$oldDb = new PDO(
    'mysql:host=138.199.140.167;port=10003;dbname=beartshare;charset=utf8mb4',
    'mariadb',
    'd08807f7ff3394a00433'
);
$oldDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// List all tables
$tables = $oldDb->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
echo "=== OLD DB TABLES ===\n";
foreach ($tables as $t) {
    echo "- $t\n";
}
echo "\n";

// Show structure + row count of each table
foreach ($tables as $t) {
    $cols = $oldDb->query("SHOW COLUMNS FROM `$t`")->fetchAll(PDO::FETCH_ASSOC);
    $count = $oldDb->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();
    echo "=== $t ($count rows) ===\n";
    foreach ($cols as $c) {
        echo "  {$c['Field']} | {$c['Type']} | {$c['Null']} | {$c['Key']} | {$c['Default']}\n";
    }
    echo "\n";
}
