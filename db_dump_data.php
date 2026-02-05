<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Dump all data from main tables
$tables = ['users', 'categories', 'artists', 'artworks', 'orders', 'order_items', 'cart_items'];

foreach ($tables as $table) {
    $rows = DB::table($table)->get();
    echo "=== $table (" . count($rows) . " rows) ===\n";
    foreach ($rows as $row) {
        $arr = (array)$row;
        // Truncate long fields for readability
        foreach ($arr as $k => &$v) {
            if (is_string($v) && strlen($v) > 120) {
                $v = substr($v, 0, 120) . '...';
            }
        }
        echo json_encode($arr, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
    }
    echo "\n";
}
