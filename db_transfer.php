<?php
/**
 * Data Transfer Script: Old DB (port 10003) → New DB (port 13026)
 * Transfers: artists, products→artworks, product_detail, blog, art_terms, static_pages
 */
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

// Old DB connection
$old = new PDO(
    'mysql:host=138.199.140.167;port=10003;dbname=beartshare;charset=utf8mb4',
    'mariadb',
    'd08807f7ff3394a00433'
);
$old->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$now = now()->toDateTimeString();

// Disable FK checks for truncate
DB::statement('SET FOREIGN_KEY_CHECKS=0');

// ===========================
// 1. TRANSFER ARTISTS
// ===========================
echo "=== Transferring Artists ===\n";
DB::table('artists')->truncate();

$oldArtists = $old->query("SELECT * FROM artists ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
$artistIdMap = []; // old_id => new_id

foreach ($oldArtists as $a) {
    $birthYear = is_numeric($a['born_date']) && $a['born_date'] > 0 ? (int)$a['born_date'] : null;
    $deathYear = is_numeric($a['death_date']) && $a['death_date'] > 0 ? (int)$a['death_date'] : null;
    if ($a['death_date'] === '-' || $a['death_date'] === '.' || $a['death_date'] === '0') {
        $deathYear = null;
    }

    $slug = $a['slug'] ?: Str::slug($a['name']);
    // Ensure unique slug
    $existingSlug = DB::table('artists')->where('slug', $slug)->exists();
    if ($existingSlug) {
        $slug = $slug . '-' . $a['id'];
    }

    $bio = $a['detail'];
    // Clean up bio
    if ($bio === '-' || $bio === '0' || trim($bio) === '') {
        $bio = null;
    }
    // Clean HTML entities
    if ($bio) {
        $bio = html_entity_decode($bio, ENT_QUOTES, 'UTF-8');
        $bio = strip_tags($bio);
        $bio = trim($bio);
    }

    $newId = DB::table('artists')->insertGetId([
        'old_id' => $a['id'],
        'name' => trim($a['name']),
        'slug' => $slug,
        'birth_year' => $birthYear,
        'death_year' => $deathYear,
        'biography' => $bio,
        'image' => null,
        'avatar' => $a['avatar'],
        'is_active' => (int)$a['active'],
        'sort_order' => 0,
        'created_at' => $a['created_at'] ?: $now,
        'updated_at' => $a['updated_at'] ?: $now,
    ]);

    $artistIdMap[$a['id']] = $newId;
}
echo "  Transferred " . count($oldArtists) . " artists\n";

// ===========================
// 2. TRANSFER PRODUCTS → ARTWORKS
// ===========================
echo "\n=== Transferring Products → Artworks ===\n";
DB::table('artworks')->truncate();

$oldProducts = $old->query("SELECT * FROM products ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);

// Get product details (Turkish)
$oldDetails = $old->query("SELECT * FROM product_detail WHERE lang='tr'")->fetchAll(PDO::FETCH_ASSOC);
$detailMap = [];
foreach ($oldDetails as $d) {
    $detailMap[$d['product_id']] = $d;
}

$artworkCount = 0;
foreach ($oldProducts as $p) {
    // Map old artist_id to new
    $newArtistId = null;
    if (!empty($p['artis_id']) && $p['artis_id'] > 0 && isset($artistIdMap[$p['artis_id']])) {
        $newArtistId = $artistIdMap[$p['artis_id']];
    }

    // Skip products without valid artist
    if (!$newArtistId) {
        continue;
    }

    $detail = $detailMap[$p['id']] ?? null;

    $title = $detail ? $detail['title'] : ($p['artist_name'] ?? 'İsimsiz');
    $title = trim($title);
    if (empty($title)) $title = 'İsimsiz';

    // Generate slug
    $slug = $detail && $detail['slug'] ? $detail['slug'] : Str::slug($title . '-' . $p['id']);
    // Ensure unique slug
    $existingSlug = DB::table('artworks')->where('slug', $slug)->exists();
    if ($existingSlug) {
        $slug = $slug . '-' . $p['id'];
    }

    $description = $detail ? $detail['description'] : null;
    if ($description) {
        $description = html_entity_decode($description, ENT_QUOTES, 'UTF-8');
        $description = trim($description);
    }
    if ($description === '' || $description === '-') $description = null;

    $technique = $detail ? $detail['canvas_type'] : null;
    if ($technique) {
        $technique = trim($technique);
    }
    if ($technique === '' || $technique === '-') $technique = null;

    $tags = $detail ? $detail['tags'] : null;

    $year = is_numeric($p['year']) && $p['year'] > 0 ? (int)$p['year'] : null;
    $price = is_numeric($p['price']) ? (float)$p['price'] : 0;

    // Calculate USD (approximate, old DB didn't always have USD)
    $priceUsd = round($price / 36.5, 2); // Approximate TL→USD

    // Images - parse JSON
    $images = null;
    if (!empty($p['image'])) {
        $imgRaw = $p['image'];
        // Fix escaped slashes
        $imgRaw = str_replace('\\\/', '/', $imgRaw);
        $imgRaw = str_replace('\\/', '/', $imgRaw);
        $decoded = json_decode($imgRaw, true);
        if (is_array($decoded) && count($decoded) > 0) {
            // Clean URLs
            $cleaned = [];
            foreach ($decoded as $url) {
                $url = str_replace('\\', '', $url);
                $url = trim($url);
                if (!empty($url) && filter_var($url, FILTER_VALIDATE_URL)) {
                    $cleaned[] = $url;
                }
            }
            if (count($cleaned) > 0) {
                $images = json_encode($cleaned, JSON_UNESCAPED_SLASHES);
            }
        }
    }

    $type = ($p['type'] === 'shared') ? 'shared' : 'wholesale';
    $sortOrder = (int)($p['sort_index'] ?? 0);

    DB::table('artworks')->insert([
        'old_id' => $p['id'],
        'artist_id' => $newArtistId,
        'category_id' => 1, // Default Yağlı Boya
        'title' => $title,
        'slug' => $slug,
        'description' => $description,
        'tags' => $tags,
        'technique' => $technique,
        'dimensions' => $p['canvas_size'],
        'year' => $year,
        'price_tl' => $price,
        'price_usd' => $priceUsd,
        'is_sold' => (int)($p['sold_out'] ?? 0),
        'is_active' => $p['passive'] ? 0 : 1,
        'is_featured' => ($sortOrder > 5000) ? 1 : 0,
        'type' => $type,
        'sort_order' => $sortOrder,
        'images' => $images,
        'created_at' => $p['created_at'] ?: $now,
        'updated_at' => $p['updated_at'] ?: $now,
    ]);
    $artworkCount++;
}
echo "  Transferred $artworkCount artworks (skipped " . (count($oldProducts) - $artworkCount) . " without valid artist)\n";

// ===========================
// 3. TRANSFER BLOG CATEGORIES
// ===========================
echo "\n=== Transferring Blog Categories ===\n";
DB::table('blog_categories')->truncate();

$oldCats = $old->query("SELECT * FROM blog_category ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
$blogCatMap = [];

foreach ($oldCats as $c) {
    $newId = DB::table('blog_categories')->insertGetId([
        'title' => $c['title'],
        'slug' => $c['slug'] ?: Str::slug($c['title']),
        'is_active' => true,
        'created_at' => $c['created_at'] ?: $now,
        'updated_at' => $c['updated_at'] ?: $now,
    ]);
    $blogCatMap[$c['id']] = $newId;
}
echo "  Transferred " . count($oldCats) . " blog categories\n";

// ===========================
// 4. TRANSFER BLOG POSTS
// ===========================
echo "\n=== Transferring Blog Posts ===\n";
DB::table('blog_posts')->truncate();

$oldPosts = $old->query("SELECT * FROM blog_post ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
$postCount = 0;

foreach ($oldPosts as $p) {
    $catId = isset($blogCatMap[$p['category']]) ? $blogCatMap[$p['category']] : null;

    $slug = $p['slug'] ?: Str::slug($p['title']);
    $existingSlug = DB::table('blog_posts')->where('slug', $slug)->exists();
    if ($existingSlug) {
        $slug = $slug . '-' . $p['id'];
    }

    $content = $p['content'];
    if ($content) {
        $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
    }

    DB::table('blog_posts')->insert([
        'title' => $p['title'] ?? 'İsimsiz',
        'slug' => $slug,
        'content' => $content,
        'blog_category_id' => $catId,
        'image' => $p['image'],
        'user_id' => null,
        'is_active' => (int)($p['status'] ?? 1),
        'created_at' => $p['created_at'] ?: $now,
        'updated_at' => $p['updated_at'] ?: $now,
    ]);
    $postCount++;
}
echo "  Transferred $postCount blog posts\n";

// ===========================
// 5. TRANSFER ART TERMS (Sanat Sözlüğü)
// ===========================
echo "\n=== Transferring Art Terms ===\n";
DB::table('art_terms')->truncate();

$oldTerms = $old->query("SELECT * FROM art_terms ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
$termCount = 0;

foreach ($oldTerms as $t) {
    $desc = $t['text'];
    if ($desc) {
        $desc = html_entity_decode($desc, ENT_QUOTES, 'UTF-8');
        $desc = trim($desc);
    }
    if ($desc === '' || $desc === '-') $desc = null;

    $image = $t['image_url'] ?: $t['image'];

    DB::table('art_terms')->insert([
        'title' => $t['title'],
        'description' => $desc,
        'image' => $image,
        'is_active' => true,
        'created_at' => $now,
        'updated_at' => $now,
    ]);
    $termCount++;
}
echo "  Transferred $termCount art terms\n";

// ===========================
// 6. TRANSFER STATIC PAGES (Turkish only)
// ===========================
echo "\n=== Transferring Static Pages ===\n";
DB::table('static_pages')->truncate();

$oldPages = $old->query("SELECT * FROM static_pages WHERE lang='tr' ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
$pageCount = 0;

foreach ($oldPages as $p) {
    $slug = $p['slug'] ?: Str::slug($p['title']);
    $existingSlug = DB::table('static_pages')->where('slug', $slug)->exists();
    if ($existingSlug) {
        $slug = $slug . '-' . $p['id'];
    }

    $content = $p['content'];
    if ($content) {
        $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
    }

    DB::table('static_pages')->insert([
        'title' => $p['title'] ?? '',
        'slug' => $slug,
        'description' => $p['description'],
        'content' => $content,
        'type' => $p['type'],
        'is_active' => true,
        'created_at' => $now,
        'updated_at' => $now,
    ]);
    $pageCount++;
}
echo "  Transferred $pageCount static pages\n";

// ===========================
// SUMMARY
// ===========================
echo "\n=============================\n";
echo "=== TRANSFER COMPLETE ===\n";
echo "=============================\n";
echo "Artists:         " . DB::table('artists')->count() . "\n";
echo "Artworks:        " . DB::table('artworks')->count() . "\n";
echo "Categories:      " . DB::table('categories')->count() . "\n";
echo "Blog Categories: " . DB::table('blog_categories')->count() . "\n";
echo "Blog Posts:      " . DB::table('blog_posts')->count() . "\n";
echo "Art Terms:       " . DB::table('art_terms')->count() . "\n";
echo "Static Pages:    " . DB::table('static_pages')->count() . "\n";
echo "Users:           " . DB::table('users')->count() . "\n";

// Re-enable FK checks
DB::statement('SET FOREIGN_KEY_CHECKS=1');
