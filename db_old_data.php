<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$oldDb = new PDO(
    'mysql:host=138.199.140.167;port=10003;dbname=beartshare;charset=utf8mb4',
    'mariadb',
    'd08807f7ff3394a00433'
);
$oldDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 1. Artists (ALL)
echo "=== ARTISTS (91) ===\n";
$artists = $oldDb->query("SELECT * FROM artists ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
foreach ($artists as $a) {
    $det = substr($a['detail'] ?? '', 0, 150);
    echo "ID:{$a['id']} | {$a['name']} | born:{$a['born_date']} | death:{$a['death_date']} | slug:{$a['slug']} | avatar:{$a['avatar']} | active:{$a['active']}\n";
    echo "  bio: {$det}...\n";
}

// 2. Products (ALL 202)
echo "\n=== PRODUCTS (202) ===\n";
$products = $oldDb->query("SELECT * FROM products ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
foreach ($products as $p) {
    $imgs = substr($p['image'] ?? '', 0, 200);
    echo "ID:{$p['id']} | artist:{$p['artist_name']} | artist_id:{$p['artis_id']} | type:{$p['type']} | size:{$p['canvas_size']} | year:{$p['year']} | price:{$p['price']} | sold:{$p['sold_out']} | passive:{$p['passive']} | sort:{$p['sort_index']}\n";
    echo "  images: {$imgs}\n";
}

// 3. Product details (for titles/descriptions)
echo "\n=== PRODUCT_DETAIL (first 50 TR) ===\n";
$details = $oldDb->query("SELECT * FROM product_detail WHERE lang='tr' ORDER BY product_id LIMIT 50")->fetchAll(PDO::FETCH_ASSOC);
foreach ($details as $d) {
    $desc = substr($d['description'] ?? '', 0, 100);
    echo "PID:{$d['product_id']} | {$d['title']} | tech:{$d['canvas_type']} | slug:{$d['slug']} | tags:{$d['tags']}\n";
    echo "  desc: {$desc}\n";
}

// 4. Blog categories
echo "\n=== BLOG_CATEGORY ===\n";
$cats = $oldDb->query("SELECT * FROM blog_category")->fetchAll(PDO::FETCH_ASSOC);
foreach ($cats as $c) {
    echo "ID:{$c['id']} | {$c['title']} | {$c['slug']}\n";
}

// 5. Blog posts (first 20)
echo "\n=== BLOG_POST (first 20) ===\n";
$posts = $oldDb->query("SELECT id,title,category,image,status,slug,created_at FROM blog_post ORDER BY id LIMIT 20")->fetchAll(PDO::FETCH_ASSOC);
foreach ($posts as $p) {
    echo "ID:{$p['id']} | {$p['title']} | cat:{$p['category']} | status:{$p['status']} | slug:{$p['slug']} | img:{$p['image']}\n";
}

// 6. Art terms (first 10)
echo "\n=== ART_TERMS (sample 10) ===\n";
$terms = $oldDb->query("SELECT id,title,translated_title,image,image_url FROM art_terms LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
foreach ($terms as $t) {
    echo "ID:{$t['id']} | {$t['title']} | en:{$t['translated_title']} | img:{$t['image']} | url:{$t['image_url']}\n";
}

// 7. Static pages
echo "\n=== STATIC_PAGES ===\n";
$pages = $oldDb->query("SELECT id,title,slug,lang,type FROM static_pages")->fetchAll(PDO::FETCH_ASSOC);
foreach ($pages as $p) {
    echo "ID:{$p['id']} | {$p['title']} | slug:{$p['slug']} | lang:{$p['lang']} | type:{$p['type']}\n";
}

// 8. Orders summary
echo "\n=== ORDERS SUMMARY ===\n";
$orderStats = $oldDb->query("SELECT status, COUNT(*) as cnt FROM orders GROUP BY status")->fetchAll(PDO::FETCH_ASSOC);
foreach ($orderStats as $s) {
    echo "  {$s['status']}: {$s['cnt']}\n";
}
