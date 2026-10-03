<?php

namespace App\Console\Commands;

use App\Support\ImageUrl;
use App\Support\ImageUrlMap;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Eski CodeIgniter sistemindeki (bağlantı: `legacy`) tüm veriyi yeni şemaya aktarır.
 * Senaryo ve kurallar: docs/legacy-import.md
 */
class ImportLegacy extends Command
{
    protected $signature = 'legacy:import
        {--fresh : Hedef veritabanını sıfırla (migrate:fresh), korunan tabloları yedekten geri yükle}
        {--backup= : Ön yedek dosyası (storage/app/ altında); boşsa önce yeni yedek alınır}
        {--images : Aktarım sonunda images:migrate çalıştır (eşlemesi olmayan görseller R2\'ye taşınır)}
        {--keep-cancelled-earnings : İptal/iade edilen siparişlerden kazanılmış puanları geri alma}
        {--force : Onay sorma}';

    protected $description = 'Eski sistem veritabanını (kullanıcılar, eserler, siparişler, ArtPuan, blog...) yeni sisteme aktarır';

    /** Yeni sistemde yönetilen, sıfırlamadan sonra yedekten geri yüklenen tablolar */
    protected const PRESERVED_TABLES = ['categories', 'faqs', 'static_pages', 'art_terms', 'blog_categories'];

    /** Eski blog kategorilerinden İngilizce olanlar (yeni site Türkçe) — yazılar pasif aktarılır */
    protected const EN_BLOG_CATEGORIES = [4, 6, 8];

    protected const NOTE_PURCHASE = 'Sipariş için puan kazanıldı';
    protected const NOTE_REFERRAL = 'Referans sayesinde puan kazanıldı';
    protected const NOTE_REFUND = 'Sipariş iptal edildiği için puan iadesi';

    /** Puanı/harcaması geri alınan sipariş durumları */
    protected const VOID_STATUSES = ['cancelled', 'returned', 'refunded'];

    protected $legacy;
    protected array $backup = [];
    protected array $userMap = [];      // eski user id -> yeni user id
    protected array $userNames = [];    // yeni user id -> ad soyad
    protected array $artworkIds = [];   // aktarılan eser id'leri
    protected array $artistNames = [];
    protected array $rates = [];        // [timestamp, kur] artan sırada
    protected array $warnings = [];

    public function handle(): int
    {
        ini_set('memory_limit', '1024M');
        $this->legacy = DB::connection('legacy');

        try {
            $this->legacy->getPdo();
        } catch (\Throwable $e) {
            $this->error('Eski veritabanına bağlanılamadı (OLD_DB_* ayarları): ' . $e->getMessage());
            return self::FAILURE;
        }

        $target = config('database.connections.' . config('database.default'));
        $this->warn("Hedef veritabanı: {$target['host']}:{$target['port']}/{$target['database']}");
        if ($this->option('fresh')) {
            $this->warn('--fresh: hedef veritabanındaki TÜM tablolar silinip yeniden oluşturulacak.');
        }
        if (!$this->option('force') && !$this->confirm('Devam edilsin mi?')) {
            return self::FAILURE;
        }

        // 1) Ön yedek
        $backupFile = $this->option('backup') ?: $this->takeBackup();
        $this->backup = json_decode(Storage::disk('local')->get($backupFile), true);
        $this->info("Ön yedek: storage/app/{$backupFile}");

        // 2) Daha önce R2'ye taşınmış görsellerin eşlemesi (yalnızca ilk çalıştırmada kurulur)
        $this->bootstrapImageMap();

        // 3) Sıfırlama
        if ($this->option('fresh')) {
            $this->info('migrate:fresh...');
            Artisan::call('migrate:fresh', ['--force' => true]);
            $this->restorePreserved();
        } elseif (DB::table('users')->exists() || DB::table('artworks')->exists()) {
            $this->error('Hedef tablolar boş değil. Sıfırlayarak aktarmak için --fresh kullanın.');
            return self::FAILURE;
        }

        $this->loadRates();

        Schema::disableForeignKeyConstraints();
        try {
            $this->importUsers();
            $this->importAddresses();
            $this->importArtists();
            $this->importArtworks();
            $this->importFavoritesAndCart();
            $voidOrders = $this->importOrders();
            $this->importBlog();
            $this->importArtPuan($voidOrders);
        } finally {
            Schema::enableForeignKeyConstraints();
        }

        ImageUrlMap::save();

        if ($this->option('images')) {
            $this->info('images:migrate...');
            $this->call('images:migrate');
        } else {
            $pending = $this->countExternalImages();
            if ($pending) {
                $this->warn("R2'de karşılığı olmayan {$pending} görsel var: php artisan images:migrate");
            }
        }

        foreach ($this->warnings as $w) {
            $this->line("  <fg=yellow>!</> {$w}");
        }
        $this->info('Aktarım tamamlandı.');

        return self::SUCCESS;
    }

    // ───────────────────────── Yedek / sıfırlama ─────────────────────────

    protected function takeBackup(): string
    {
        $out = [];
        foreach (DB::select('SHOW TABLES') as $t) {
            $name = array_values((array) $t)[0];
            $out[$name] = DB::table($name)->get();
        }
        $file = 'db-backups/pre-legacy-import-' . date('Ymd-His') . '.json';
        Storage::disk('local')->put($file, json_encode($out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        return $file;
    }

    protected function restorePreserved(): void
    {
        foreach (self::PRESERVED_TABLES as $table) {
            $rows = $this->backup[$table] ?? [];
            foreach (array_chunk($rows, 200) as $chunk) {
                DB::table($table)->insert($chunk);
            }
            $this->line("  {$table}: " . count($rows) . ' kayıt geri yüklendi');
        }
    }

    protected function backupRows(string $table): Collection
    {
        return collect($this->backup[$table] ?? []);
    }

    /**
     * images:migrate yedekleri (eski URL'ler) + ön yedekteki R2 yolları eşleştirilerek
     * URL -> R2 haritası kurulur. Yedeklerdeki id'ler aktarım öncesi veritabanına aittir;
     * bu yüzden harita yalnızca ilk kez (dosya yokken) kurulur.
     */
    protected function bootstrapImageMap(): void
    {
        if (ImageUrlMap::count() > 0) {
            $this->line('  Görsel eşlemesi mevcut: ' . ImageUrlMap::count() . ' kayıt');
            return;
        }

        $artworks = $this->backupRows('artworks')->keyBy('id');
        $artists = $this->backupRows('artists')->keyBy('id');
        $posts = $this->backupRows('blog_posts')->keyBy('id');
        $isLocal = fn ($p) => is_string($p) && $p !== '' && !preg_match('#^(https?:|data:)#', $p);

        foreach (Storage::disk('local')->files() as $file) {
            if (!str_starts_with($file, 'image-migration-backup-')) continue;
            $data = json_decode(Storage::disk('local')->get($file), true) ?: [];

            foreach ($data['artworks'] ?? [] as $id => $urls) {
                $paths = json_decode($artworks[$id]['images'] ?? '[]', true) ?: [];
                foreach (array_values($urls) as $i => $url) {
                    if (isset($paths[$i]) && $isLocal($paths[$i])) ImageUrlMap::put($url, $paths[$i]);
                }
            }
            foreach ($data['artists'] ?? [] as $id => $fields) {
                foreach (['avatar', 'image'] as $f) {
                    $new = $artists[$id][$f] ?? null;
                    if (!empty($fields[$f]) && $isLocal($new)) ImageUrlMap::put($fields[$f], $new);
                }
            }
            foreach ($data['blog'] ?? [] as $id => $url) {
                $new = $posts[$id]['image'] ?? null;
                if ($url && $isLocal($new)) ImageUrlMap::put($url, $new);
            }
            foreach ($data['content'] ?? [] as $id => $original) {
                $before = $this->imgSources($original);
                $after = $this->imgSources($posts[$id]['content'] ?? '');
                if (count($before) !== count($after)) continue;
                foreach ($before as $i => $src) {
                    $path = ImageUrl::pathFromUrl($after[$i]);
                    if ($path && $path !== $src) ImageUrlMap::put($src, $path);
                }
            }
        }

        ImageUrlMap::save();
        $this->line('  Görsel eşlemesi kuruldu: ' . ImageUrlMap::count() . ' kayıt');
    }

    protected function imgSources(string $html): array
    {
        ini_set('pcre.backtrack_limit', '500000000');
        preg_match_all('/<img\b[^>]*\bsrc=(["\'])([^"\']+)\1/i', $html, $m);
        return $m[2] ?? [];
    }

    protected function mapImage(?string $url): ?string
    {
        // Bazı eski kayıtlarda JSON iki kez kodlanmış: "https:\/\/..." → ters eğik çizgiler temizlenir
        $url = trim(str_replace('\\', '', (string) $url));
        // Eski İngilizce yazılardaki yer tutucu görsel (kaynak site kapalı)
        if ($url === '' || str_contains($url, 'clipground.com/images/image-placeholder')) return null;
        return ImageUrlMap::get($url) ?? $url;
    }

    protected function countExternalImages(): int
    {
        $n = 0;
        foreach (DB::table('artworks')->pluck('images') as $json) {
            foreach (json_decode($json ?: '[]', true) ?: [] as $p) {
                if (preg_match('#^https?:#', $p)) $n++;
            }
        }
        $n += DB::table('artists')->where('avatar', 'like', 'http%')->count();
        $n += DB::table('blog_posts')->where('image', 'like', 'http%')->count();
        return $n;
    }

    // ───────────────────────── Yardımcılar ─────────────────────────

    /**
     * Eski sistem tarihleri PHP tarafında Europe/Istanbul yerel saatiyle yazılmış metinlerdir.
     * MySQL varsayılanıyla (CURRENT_TIMESTAMP) dolan sütunlar ise sunucu saatinde (UTC) — $utc=true.
     */
    protected function date($value, bool $utc = false): ?string
    {
        $value = trim((string) $value);
        if ($value === '' || str_starts_with($value, '0000')) return null;
        try {
            $c = Carbon::parse($value, $utc ? 'UTC' : 'Europe/Istanbul');
        } catch (\Throwable) {
            return null;
        }
        if ($c->year < 2000) return null;
        return $c->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s');
    }

    protected function normalizePhone(?string $phone): ?string
    {
        $digits = preg_replace('/\D/', '', (string) $phone);
        if ($digits === '') return null;
        if (strlen($digits) === 12 && str_starts_with($digits, '90')) $digits = substr($digits, 2);
        elseif (strlen($digits) === 11 && str_starts_with($digits, '0')) $digits = substr($digits, 1);
        return strlen($digits) <= 15 ? $digits : null;
    }

    protected function loadRates(): void
    {
        $this->rates = $this->legacy->table('exchange_rates')->where('currency_code', 'USD')->where('satis', '>', 0)
            ->orderBy('timestamp')->get(['timestamp', 'satis'])
            ->map(fn ($r) => [strtotime($r->timestamp), (float) $r->satis])->all();
    }

    /** Verilen tarihteki (yoksa en yakın önceki / ilk) USD satış kuru */
    protected function rateAt(?string $date): float
    {
        if (!$this->rates) return 0;
        if (!$date) return end($this->rates)[1];
        $ts = strtotime($date);
        $rate = $this->rates[0][1];
        foreach ($this->rates as [$t, $r]) {
            if ($t > $ts) break;
            $rate = $r;
        }
        return $rate;
    }

    protected function usd(float $tl, float $rate): float
    {
        return $rate > 0 ? round($tl / $rate, 2) : 0;
    }

    protected function uniqueSlug(string $base, string $table, int $id, array &$used): string
    {
        $slug = Str::slug($base) ?: (string) $id;
        if (isset($used[$slug])) $slug .= '-' . $id;
        $used[$slug] = true;
        return $slug;
    }

    // ───────────────────────── Kullanıcılar ─────────────────────────

    protected function importUsers(): void
    {
        $existing = $this->backupRows('users')->keyBy(fn ($u) => mb_strtolower(trim($u['email'])));
        $emails = $phones = $tcs = $refs = [];
        $rows = [];

        foreach ($this->legacy->table('users')->orderBy('id')->get() as $u) {
            $email = trim((string) $u->email);
            $key = mb_strtolower($email);
            if ($email === '' || isset($emails[$key])) {
                // Aynı e-posta ile ikinci hesap: siparişleri vb. ilk hesaba bağlanır
                if (isset($emails[$key])) {
                    $this->userMap[$u->id] = $emails[$key];
                    $this->warnings[] = "Kullanıcı #{$u->id} ({$email}) aynı e-postalı #{$emails[$key]} ile birleştirildi";
                }
                continue;
            }
            $emails[$key] = $u->id;
            $this->userMap[$u->id] = $u->id;

            $phone = $this->normalizePhone($u->mobile);
            if ($phone && isset($phones[$phone])) {
                $this->warnings[] = "Kullanıcı #{$u->id}: telefon {$phone} #{$phones[$phone]} ile çakışıyor, boş bırakıldı";
                $phone = null;
            }
            if ($phone) $phones[$phone] = $u->id;

            $tc = preg_replace('/\D/', '', (string) $u->tcno);
            $tc = strlen($tc) === 11 && !isset($tcs[$tc]) ? $tc : null;
            if ($tc) $tcs[$tc] = true;

            $ref = strtolower(trim((string) $u->ref_code));
            if ($ref === '' || isset($refs[$ref]) || strlen($ref) > 10) {
                do { $ref = strtolower(Str::random(7)); } while (isset($refs[$ref]));
            }
            $refs[$ref] = true;

            // Yeni sistemde aynı e-postayla hesabı olan (ör. admin) kişinin yeni şifresi korunur
            $current = $existing[$key] ?? null;
            if ($current && str_starts_with((string) $current['password'], '$2y$')) {
                $password = $current['password'];
                $legacyPassword = false;
            } elseif (preg_match('/^[0-9a-f]{40}$/i', (string) $u->password)) {
                $password = Hash::make(strtolower($u->password), ['rounds' => 10]);
                $legacyPassword = true;
            } elseif (str_starts_with((string) $u->password, '$2y$')) {
                $password = $u->password;
                $legacyPassword = false;
            } else {
                $password = Hash::make(Str::random(32));
                $legacyPassword = false;
                $this->warnings[] = "Kullanıcı #{$u->id}: şifre biçimi tanınmadı, şifre sıfırlama gerekir";
            }

            $created = $this->date($u->created_at);
            $name = preg_replace('/\s+/u', ' ', trim(trim((string) $u->name) . ' ' . trim((string) $u->lastname)));
            $this->userNames[$u->id] = $name;

            $birth = null;
            if ($u->birth_date && !str_starts_with((string) $u->birth_date, '0000')) {
                try {
                    $birth = Carbon::parse($u->birth_date)->format('Y-m-d');
                } catch (\Throwable) {
                }
            }

            $rows[] = [
                'id' => $u->id,
                'name' => $name ?: $email,
                'tc_no' => $tc,
                'birth_date' => $birth,
                'phone' => $phone,
                'phone_verified_at' => $phone ? $created : null, // eski sistemde kayıt SMS doğrulamasıyla
                'referral_code' => $ref,
                'referred_by' => null,
                'art_puan' => 0,
                'email' => $email,
                'email_verified_at' => $u->mail_verify ? $created : null,
                'password' => $password,
                'legacy_password' => $legacyPassword,
                'is_admin' => (bool) $u->admin || (bool) ($current['is_admin'] ?? false),
                'created_at' => $created,
                'updated_at' => $this->date($u->updated_at) ?? $created,
            ];
        }

        foreach (array_chunk($rows, 200) as $chunk) {
            DB::table('users')->insert($chunk);
        }

        // Yalnızca yeni sistemde olan hesaplar (ör. yeni eklenen adminler)
        $added = 0;
        foreach ($existing as $key => $u) {
            if (isset($emails[$key])) continue;
            $phone = $u['phone'] && !isset($phones[$u['phone']]) ? $u['phone'] : null;
            $tc = $u['tc_no'] && !isset($tcs[$u['tc_no']]) ? $u['tc_no'] : null;
            $ref = $u['referral_code'] && !isset($refs[$u['referral_code']]) ? $u['referral_code'] : strtolower(Str::random(7));
            $refs[$ref] = true;
            DB::table('users')->insert(Arr::except(array_merge($u, [
                'phone' => $phone, 'tc_no' => $tc, 'referral_code' => $ref,
                'referred_by' => null, 'art_puan' => 0,
            ]), ['id']) + ['legacy_password' => false]);
            $added++;
        }

        // Referanslar: kişi başına ilk kayıt geçerli
        $refCount = 0;
        $seen = [];
        foreach ($this->legacy->table('ref_users')->orderBy('id')->get() as $r) {
            $uid = $this->userMap[$r->user_id] ?? null;
            $rid = $this->userMap[$r->ref_user_id] ?? null;
            if (!$uid || !$rid || $uid === $rid || isset($seen[$uid])) continue;
            $seen[$uid] = true;
            DB::table('users')->where('id', $uid)->update(['referred_by' => $rid]);
            $refCount++;
        }

        $this->info('Kullanıcılar: ' . count($rows) . " aktarıldı, {$added} yeni sistem hesabı korundu, {$refCount} referans bağlandı");
    }

    protected function importAddresses(): void
    {
        $users = DB::table('users')->get(['id', 'name', 'phone', 'tc_no', 'created_at'])->keyBy('id');
        $rows = [];
        $hasDefault = [];

        foreach ($this->legacy->table('user_address')->orderBy('id')->get() as $a) {
            $uid = $this->userMap[$a->user_id] ?? null;
            if (!$uid || !isset($users[$uid])) continue;
            $u = $users[$uid];
            $line = trim((string) $a->address);
            $country = trim((string) $a->country);
            if ($country !== '' && !in_array(mb_strtolower($country), ['turkey', 'türkiye', 'turkiye'], true)) {
                $line .= ', ' . $country;
            }
            $rows[] = [
                'id' => $a->id,
                'user_id' => $uid,
                'type' => 'shipping',
                'title' => trim((string) $a->name) ?: 'Adres',
                'full_name' => $u->name,
                'phone' => $u->phone ?? '',
                'city' => trim((string) $a->city),
                'district' => trim((string) $a->district),
                'address_line' => $line,
                'invoice_type' => 'individual',
                'tc_no' => $u->tc_no,
                'is_default' => !isset($hasDefault[$uid]),
                'created_at' => $u->created_at,
                'updated_at' => $u->created_at,
            ];
            $hasDefault[$uid] = true;
        }

        DB::table('addresses')->insert($rows);
        $this->info('Adresler: ' . count($rows));
    }

    // ───────────────────────── Sanatçılar / eserler ─────────────────────────

    protected function importArtists(): void
    {
        $current = $this->backupRows('artists')->keyBy('old_id');
        $used = [];
        foreach ($current as $c) $used[$c['slug']] = true;
        $rows = [];

        foreach ($this->legacy->table('artists')->orderBy('id')->get() as $a) {
            $c = $current[$a->id] ?? null;
            $year = fn ($v) => preg_match('/(\d{4})/', (string) $v, $m) ? (int) $m[1] : null;
            $created = $this->date($a->created_at);
            $this->artistNames[$a->id] = trim($a->name);

            $rows[] = [
                'id' => $a->id,
                'old_id' => $a->id,
                'name' => trim($a->name),
                'slug' => $c['slug'] ?? $this->uniqueSlug($a->name, 'artists', $a->id, $used),
                'birth_year' => $year($a->born_date),
                'death_year' => $year($a->death_date),
                'biography' => $a->detail ?: null,
                'image' => $c['image'] ?? null,
                'avatar' => $this->mapImage($a->avatar),
                'is_active' => (bool) $a->active,
                'sort_order' => $c['sort_order'] ?? 0,
                'created_at' => $created,
                'updated_at' => $this->date($a->updated_at) ?? $created,
            ];
        }

        DB::table('artists')->insert($rows);
        $this->info('Sanatçılar: ' . count($rows));
    }

    protected function guessCategory(string $technique): ?int
    {
        static $ids = null;
        $ids ??= DB::table('categories')->pluck('id', 'slug')->all();
        $t = mb_strtolower($technique, 'UTF-8');
        $rules = [
            'bronz-heykel' => ['bronz'],
            'seramik' => ['seramik', 'porselen'],
            'heykel' => ['heykel', 'polyester', 'terra cotta', 'mermer', 'ahşap'],
            'litografi' => ['litografi', 'lithograph'],
            'serigrafi' => ['serigrafi', 'serigraf', 'silkscreen'],
            'gravur' => ['gravür', 'ofort', 'etching', 'aquatint', 'linol'],
            'baski-print' => ['baskı', 'print', 'giclee', 'giclée', 'edition'],
            'fotograf' => ['fotoğraf', 'photograph'],
            'dijital-sanat' => ['dijital', 'digital'],
            'karisik-teknik' => ['karışık teknik', 'karisik teknik'],
            'mixed-media' => ['mixed media'],
            'kolaj' => ['kolaj', 'collage'],
            'akrilik-panel' => ['akrilik panel'],
            'yagli-boya' => ['yağlıboya', 'yağlı boya', 'yagli boya', 'yağ boya', 'oil'],
            'akrilik' => ['akrilik', 'acrylic'],
            'suluboya' => ['suluboya', 'sulu boya', 'watercolor', 'aquarel'],
            'guaj' => ['guaj', 'gouache'],
            'pastel' => ['pastel'],
            'murekkep' => ['mürekkep', 'çini mürekkebi', 'ink'],
            'karakalem-cizim' => ['karakalem', 'kalem', 'çizim', 'füzen', 'kömür'],
        ];
        foreach ($rules as $slug => $words) {
            foreach ($words as $w) {
                if (str_contains($t, $w) && isset($ids[$slug])) return $ids[$slug];
            }
        }
        return null;
    }

    protected function importArtworks(): void
    {
        $current = $this->backupRows('artworks')->keyBy('old_id');
        $used = [];
        foreach ($current as $c) $used[$c['slug']] = true;
        $details = $this->legacy->table('product_detail')->get()->groupBy('product_id');
        $rate = $this->rateAt(null);
        $rows = [];
        $skipped = [];

        foreach ($this->legacy->table('products')->orderBy('id')->get() as $p) {
            if (!$p->artis_id || !isset($this->artistNames[$p->artis_id])) {
                $skipped[] = $p->id;
                continue;
            }
            $d = $details[$p->id] ?? collect();
            $tr = $d->firstWhere('lang', 'tr') ?? $d->first();
            $c = $current[$p->id] ?? null;

            $title = trim((string) ($tr->title ?? '')) ?: 'İsimsiz';
            $description = trim(implode("\n", array_filter([
                trim((string) ($tr->description ?? '')),
                trim((string) ($tr->additional_description ?? '')),
            ])));
            $technique = trim((string) ($tr->canvas_type ?? ''));
            $price = (float) preg_replace('/[^\d.]/', '', (string) $p->price);
            $images = array_values(array_filter(array_map(
                fn ($u) => $this->mapImage($u),
                json_decode((string) $p->image, true) ?: ($p->image ? [$p->image] : [])
            )));
            $created = $this->date($p->created_at);

            $rows[] = [
                'id' => $p->id,
                'old_id' => $p->id,
                'artist_id' => $p->artis_id,
                'category_id' => $c['category_id'] ?? $this->guessCategory($technique),
                'title' => $title,
                'slug' => $c['slug'] ?? $this->uniqueSlug($title, 'artworks', $p->id, $used),
                'description' => $description ?: null,
                'sale_note' => trim(strip_tags((string) ($tr->basket_description ?? ''))) !== '' ? trim($tr->basket_description) : null,
                'tags' => trim((string) ($tr->tags ?? '')) ?: null,
                'technique' => $technique ?: null,
                'dimensions' => trim((string) $p->canvas_size) ?: null,
                'year' => $p->year > 0 ? $p->year : null,
                'price_tl' => $price,
                'price_usd' => $this->usd($price, $rate),
                'is_sold' => (bool) $p->sold_out,
                'is_reserved' => (bool) $p->reserved,
                'allow_credit_card' => (bool) ($c['allow_credit_card'] ?? false),
                'owner_name' => $c['owner_name'] ?? null,
                'admin_notes' => $c['admin_notes'] ?? null,
                'is_active' => !$p->passive,
                'is_featured' => (bool) ($c['is_featured'] ?? false),
                'type' => $p->type === 'shared' ? 'shared' : 'wholesale',
                'sort_order' => (int) $p->sort_index,
                'images' => json_encode($images, JSON_UNESCAPED_SLASHES),
                'created_at' => $created,
                'updated_at' => $this->date($p->updated_at) ?? $created,
            ];
            $this->artworkIds[$p->id] = $title;
        }

        foreach (array_chunk($rows, 100) as $chunk) {
            DB::table('artworks')->insert($chunk);
        }
        if ($skipped) {
            $this->warnings[] = 'Sanatçısı olmayan test ürünleri atlandı: #' . implode(', #', $skipped);
        }
        $this->info('Eserler: ' . count($rows) . " (USD kuru {$rate})");
    }

    protected function importFavoritesAndCart(): void
    {
        $fav = [];
        $seen = [];
        foreach ($this->legacy->table('product_favorites')->orderBy('id')->get() as $f) {
            $uid = $this->userMap[$f->user_id] ?? null;
            if (!$uid || !isset($this->artworkIds[$f->product_id]) || isset($seen["$uid-$f->product_id"])) continue;
            $seen["$uid-$f->product_id"] = true;
            $at = $this->date($f->created_at);
            $fav[] = ['user_id' => $uid, 'artwork_id' => $f->product_id, 'created_at' => $at, 'updated_at' => $at];
        }
        DB::table('favorites')->insert($fav);

        $available = DB::table('artworks')->where('is_sold', false)->where('is_active', true)->pluck('id')->flip();
        $cart = [];
        foreach ($this->legacy->table('basket')->get() as $b) {
            $uid = $this->userMap[$b->user_id] ?? null;
            if (!$uid || !isset($available[$b->product_id])) continue;
            $at = $this->date($b->reserved_at);
            $cart[] = ['user_id' => $uid, 'session_id' => null, 'artwork_id' => $b->product_id, 'created_at' => $at, 'updated_at' => $at];
        }
        DB::table('cart_items')->insert($cart);

        $this->info('Favoriler: ' . count($fav) . ', sepet: ' . count($cart));
    }

    // ───────────────────────── Siparişler ─────────────────────────

    /** @return array<int,string> iptal/iade edilmiş sipariş id => durum */
    protected function importOrders(): array
    {
        $statusMap = [
            'awaiting' => 'pending', 'success' => 'confirmed', 'shipped' => 'shipped',
            'delivered' => 'delivered', 'cancelled' => 'cancelled', 'returned' => 'returned', 'refunded' => 'refunded',
        ];
        $methodMap = ['bank_transfer' => 'havale', 'credit_card' => 'kredi_karti', 'points' => 'artpuan'];

        $items = $this->legacy->table('order_products')->get()->groupBy(fn ($r) => (int) $r->order_id);
        $payments = $this->legacy->table('payments')->orderBy('id')->get()->groupBy('order_id');
        $notes = $this->legacy->table('order_notes')->orderBy('id')->get()->groupBy('order_id');
        $shipments = $this->legacy->table('shipment_info')->orderBy('id')->get()->groupBy('order_id');
        $addresses = $this->legacy->table('user_address')->get()->keyBy('id');
        $users = DB::table('users')->get(['id', 'name', 'email', 'phone', 'tc_no'])->keyBy('id');
        $productArtist = $this->legacy->table('products')->pluck('artis_id', 'id');
        $productArtistName = $this->legacy->table('products')->pluck('artist_name', 'id');

        $orders = $orderItems = $transactions = [];
        $void = [];
        $usedNumbers = [];
        $dupPayments = 0;

        foreach ($this->legacy->table('orders')->orderBy('id')->get() as $o) {
            $uid = $this->userMap[$o->user_id] ?? null;
            $u = $uid ? $users[$uid] : null;
            $status = $statusMap[$o->status] ?? 'pending';
            $created = $this->date($o->created_at);
            $rate = $this->rateAt($created);
            $lines = $items[$o->id] ?? collect();

            $subtotal = $lines->sum(fn ($l) => (float) $l->price * max(1, (int) $l->quantity));
            $points = (float) $o->points_used;
            $totalUsd = 0;

            foreach ($lines as $l) {
                $price = (float) $l->price;
                $usd = $this->usd($price, $rate);
                $totalUsd += $usd * max(1, (int) $l->quantity);
                $artistId = $productArtist[$l->product_id] ?? null;
                $orderItems[] = [
                    'order_id' => $o->id,
                    'artwork_id' => isset($this->artworkIds[$l->product_id]) ? $l->product_id : null,
                    'artwork_title' => $this->artworkIds[$l->product_id] ?? ('Eser #' . $l->product_id),
                    'artist_name' => $this->artistNames[$artistId] ?? ($productArtistName[$l->product_id] ?? null),
                    'quantity' => max(1, (int) $l->quantity),
                    'price_tl' => $price,
                    'price_usd' => $usd,
                    'created_at' => $created,
                    'updated_at' => $created,
                ];
            }

            // Ödemeler: aynı sipariş için mükerrer "success" kayıtları (çift onay hatası) tek sayılır
            $paid = null;
            $seenPay = [];
            foreach ($payments[$o->id] ?? [] as $p) {
                $sig = $p->payment_type . '|' . (float) $p->amount . '|' . $p->status;
                if (isset($seenPay[$sig])) { $dupPayments++; continue; }
                $seenPay[$sig] = true;
                $pAt = $this->date($p->created_at);
                if ($p->status === 'success' && !$paid) $paid = $pAt;
                $transactions[] = [
                    'order_id' => $o->id,
                    'transaction_id' => 'LEGACY-' . $p->id,
                    'gateway' => $p->payment_type === 'credit_card' ? 'kredi_karti' : ($p->payment_type === 'points' ? 'artpuan' : 'havale'),
                    'amount' => (float) $p->amount,
                    'currency' => 'TRY',
                    'status' => ['success' => 'completed', 'awaiting_payment' => 'pending', 'cancelled' => 'failed'][$p->status] ?? 'pending',
                    'installment_count' => 0,
                    'created_at' => $pAt,
                    'updated_at' => $this->date($p->updated_at) ?? $pAt,
                ];
            }

            $history = [];
            $lastNote = null;
            foreach ($notes[$o->id] ?? [] as $n) {
                $at = $this->date($n->created_at);
                $lastNote = max($lastNote, $at);
                $history[] = ($at ? Carbon::parse($at)->format('d.m.Y') : '-') . ' — ' . $this->translateNote($n->note);
            }
            $ship = ($shipments[$o->id] ?? collect())->last();
            if ($ship) {
                $at = $this->date($ship->created_at, true);
                $history[] = ($at ? Carbon::parse($at)->format('d.m.Y H:i') : '-') . " — Kargo: {$ship->shipping_company} / {$ship->tracking_number}";
            }
            $history[] = "Eski sistemden aktarıldı (sipariş #{$o->id}, {$o->uniq_id})";

            $addr = $addresses[$o->address_id] ?? null;
            $number = $o->uniq_id && !isset($usedNumbers[$o->uniq_id]) ? $o->uniq_id : 'ORD-LEGACY-' . $o->id;
            $usedNumbers[$number] = true;

            if (in_array($status, self::VOID_STATUSES, true)) {
                $void[$o->id] = $status;
            }

            $orders[] = [
                'id' => $o->id,
                'user_id' => $uid,
                'order_number' => $number,
                'status' => $status,
                'paid_at' => $paid,
                'payment_method' => $methodMap[$o->payment_method] ?? ($o->payment_method ?: 'havale'),
                'payment_code' => '1050' . $o->id, // eski sistemde havale açıklama kodu
                'total_tl' => max(0, $subtotal - $points),
                'total_usd' => round($totalUsd, 2),
                'artpuan_used' => $points,
                'discount_tl' => $points,
                'customer_name' => $u->name ?? '-',
                'customer_email' => $u->email ?? '-',
                'customer_phone' => $u->phone ?? null,
                'tc_no' => $u->tc_no ?? null,
                'shipping_address' => $addr ? trim($addr->address) : '-',
                'billing_address' => $addr ? trim($addr->address) . ', ' . $addr->district . '/' . $addr->city : null,
                'city' => $addr->city ?? null,
                'district' => $addr->district ?? null,
                'shipping_company' => $ship->shipping_company ?? null,
                'tracking_number' => $ship->tracking_number ?? null,
                'notes' => $o->note ?: null,
                'admin_notes' => implode("\n", $history),
                'confirmed_at' => in_array($status, ['confirmed', 'shipped', 'delivered', 'returned', 'refunded'], true) ? $paid : null,
                'created_at' => $created,
                'updated_at' => max($created, $paid, $lastNote),
            ];
        }

        foreach (array_chunk($orders, 100) as $chunk) DB::table('orders')->insert($chunk);
        foreach (array_chunk($orderItems, 200) as $chunk) DB::table('order_items')->insert($chunk);
        foreach (array_chunk($transactions, 200) as $chunk) DB::table('payment_transactions')->insert($chunk);

        if ($dupPayments) {
            $this->warnings[] = "{$dupPayments} mükerrer ödeme kaydı (aynı siparişe çift onay) atlandı";
        }
        $this->info('Siparişler: ' . count($orders) . ', kalemler: ' . count($orderItems) . ', ödemeler: ' . count($transactions));

        return $void;
    }

    protected function translateNote(string $note): string
    {
        return strtr($note, [
            ' awaiting ' => ' "Ödeme Bekleniyor" ', ' success ' => ' "Onaylandı" ', ' shipped ' => ' "Kargoda" ',
            ' delivered ' => ' "Teslim Edildi" ', ' cancelled ' => ' "İptal" ', ' returned ' => ' "İade" ', ' refunded ' => ' "Ücret İadesi" ',
        ]);
    }

    // ───────────────────────── Blog ─────────────────────────

    protected function importBlog(): void
    {
        $current = $this->backupRows('blog_posts')->keyBy(fn ($p) => $p['title'] . '|' . $p['created_at']);
        $categories = DB::table('blog_categories')->pluck('id')->flip();
        $used = [];
        $rows = [];

        foreach ($this->legacy->table('blog_post')->orderBy('id')->get() as $p) {
            $created = $this->date($p->created_at);
            $c = $current[$p->title . '|' . $created] ?? null;
            $slug = $c['slug'] ?? null;
            if (!$slug || isset($used[$slug])) {
                $slug = $this->uniqueSlug($p->slug ?: $p->title, 'blog_posts', $p->id, $used);
            }
            $used[$slug] = true;

            $rows[] = [
                'id' => $p->id,
                'title' => trim($p->title),
                'slug' => $slug,
                'content' => (string) $p->content,
                'blog_category_id' => isset($categories[$p->category]) ? $p->category : null,
                'image' => $this->mapImage($p->image),
                'user_id' => $this->userMap[$p->user_id] ?? null,
                'is_active' => (int) $p->status === 1 && !in_array((int) $p->category, self::EN_BLOG_CATEGORIES, true),
                'created_at' => $created,
                'updated_at' => $this->date($p->updated_at) ?? $created,
            ];
        }

        // Tek tek: bazı yazılarda gömülü base64 görseller var (MB'larca), paket sınırına takılmasın
        foreach ($rows as $row) DB::table('blog_posts')->insert($row);
        $this->info('Blog yazıları: ' . count($rows) . ' (İngilizce kategoriler pasif)');
    }

    // ───────────────────────── ArtPuan ─────────────────────────

    /**
     * Eski sistemde bakiye = SUM(points) − SUM(ref_point_cash_out), status alanı tutarsız
     * (points.status hep 0; cash_out'ta 1 = checkout'ta ayrıldı, 0 = onayda kesinleşti) ve
     * harcama tablosunda mükerrer / yanlış kullanıcıya yazılmış kayıtlar var. Bu yüzden
     * defter siparişlerden yeniden kurulur:
     *   + kazanım (alış %1, referans) — iptal/iade siparişlerinkiler hariç, mükerrerler tek
     *   + kampanya / manuel puanlar olduğu gibi
     *   − harcama: her siparişin points_used değeri, sipariş tarihinde, siparişin sahibinden
     *   + iade: iptal/iade edilen siparişte kullanılan puan geri
     */
    protected function importArtPuan(array $voidOrders): void
    {
        $orders = $this->legacy->table('orders')->get()->keyBy('id');
        $orderNumbers = DB::table('orders')->pluck('order_number', 'id');
        $paidAmounts = $this->legacy->table('payments')->where('status', 'success')->get()->groupBy('order_id')
            ->map(fn ($g) => (float) $g->first()->amount);
        $keepVoid = $this->option('keep-cancelled-earnings');

        $events = [];
        $dedupe = [];
        $refundDates = [];
        $revoked = [];

        foreach ($this->legacy->table('points')->orderBy('id')->get() as $p) {
            $uid = $this->userMap[$p->user_id] ?? null;
            if (!$uid || (float) $p->points == 0) continue;
            $at = $this->date($p->created_at);
            $note = trim((string) $p->note);
            $order = $p->order_id ? ($orders[$p->order_id] ?? null) : null;

            if ($note === self::NOTE_REFUND) {
                // Defterde siparişten yeniden üretilir; yalnızca tarihi kullanılır
                if ($p->order_id) $refundDates[$p->order_id] ??= $at;
                continue;
            }

            if ($note === self::NOTE_REFERRAL) {
                $type = 'referral';
            } elseif ($order && (int) $order->user_id === (int) $p->user_id && ($note === self::NOTE_PURCHASE
                    || abs((float) $p->points - 0.01 * ($paidAmounts[$p->order_id] ?? 0)) <= 1)) {
                $type = 'purchase';
            } else {
                $type = preg_match('/kampanya|hediye|ödül|odul|satıcı|satici|noter|eser|yılbaşı/iu', $note) ? 'bonus' : 'manual';
            }

            if (in_array($type, ['purchase', 'referral'], true)) {
                $sig = "{$type}|{$uid}|{$p->order_id}";
                if (isset($dedupe[$sig])) { $revoked[] = [$uid, (float) $p->points, "mükerrer {$type} #{$p->order_id}"]; continue; }
                $dedupe[$sig] = true;
                if (!$keepVoid && isset($voidOrders[$p->order_id])) {
                    $revoked[] = [$uid, (float) $p->points, "{$voidOrders[$p->order_id]} sipariş #{$p->order_id}"];
                    continue;
                }
            }

            $num = $p->order_id ? ($orderNumbers[$p->order_id] ?? $p->order_id) : null;
            $events[] = [
                'user_id' => $uid,
                'order_id' => $order ? $p->order_id : null,
                'source_user_id' => $type === 'referral' ? ($this->userMap[$p->ref_user_id] ?? null) : null,
                'type' => $type,
                'amount' => (float) $p->points,
                'description' => match ($type) {
                    'purchase' => "#{$num} nolu sipariş - %1 ArtPuan",
                    'referral' => ($this->userNames[$this->userMap[$p->ref_user_id] ?? 0] ?? 'Referans') . " referansı - #{$num} siparişi",
                    default => $note ?: 'ArtPuan',
                },
                'created_at' => $at,
            ];
        }

        foreach ($orders as $o) {
            $uid = $this->userMap[$o->user_id] ?? null;
            $pts = (float) $o->points_used;
            if (!$uid || $pts <= 0) continue;
            $num = $orderNumbers[$o->id] ?? $o->id;
            $at = $this->date($o->created_at);
            $events[] = [
                'user_id' => $uid, 'order_id' => $o->id, 'source_user_id' => null, 'type' => 'spend',
                'amount' => -$pts, 'description' => "#{$num} siparişte ArtPuan kullanımı", 'created_at' => $at,
            ];
            if (isset($voidOrders[$o->id])) {
                $refundAt = $refundDates[$o->id] ?? Carbon::parse($at)->addSecond()->format('Y-m-d H:i:s');
                $events[] = [
                    'user_id' => $uid, 'order_id' => $o->id, 'source_user_id' => null, 'type' => 'refund',
                    'amount' => $pts, 'description' => "#{$num} sipariş " . ($voidOrders[$o->id] === 'cancelled' ? 'iptali' : 'iadesi') . ' - ArtPuan iade',
                    'created_at' => max($refundAt, $at),
                ];
            }
        }

        // Kronolojik bakiye
        usort($events, fn ($a, $b) => [$a['user_id'], $a['created_at'], $a['amount'] < 0 ? 0 : 1] <=> [$b['user_id'], $b['created_at'], $b['amount'] < 0 ? 0 : 1]);
        $balances = [];
        $deducted = [];
        $logs = [];
        foreach ($events as $e) {
            $uid = $e['user_id'];
            $bal = $balances[$uid] ?? 0;
            if ($e['type'] === 'spend') {
                // Eski sistem bakiye kontrolünü atlayabiliyordu; bakiye eksiye düşürülmez,
                // o an mevcut olan kadar düşülür (fazlası fiilen indirim olarak verilmiş)
                $want = -$e['amount'];
                $take = round(min($want, max(0, $bal)), 2);
                if ($take < $want) {
                    $e['description'] .= ' (' . number_format($want, 0, ',', '.') . ' AP kullanıldı, o tarihteki bakiye ' . number_format(max(0, $bal), 0, ',', '.') . ' AP)';
                    $this->warnings[] = "Kullanıcı #{$uid}: sipariş #{$e['order_id']} için {$want} AP kullanılmış, bakiye {$bal} — yalnızca bakiye kadar düşüldü";
                }
                $deducted[$e['order_id']] = $take;
                $e['amount'] = -$take;
            } elseif ($e['type'] === 'refund') {
                $e['amount'] = $deducted[$e['order_id']] ?? $e['amount'];
            }
            if ($e['amount'] == 0) continue;
            $balances[$uid] = round($bal + $e['amount'], 2);
            $logs[] = $e + ['artwork_id' => null, 'balance_after' => $balances[$uid], 'updated_at' => $e['created_at']];
        }

        foreach (array_chunk($logs, 200) as $chunk) DB::table('art_puan_logs')->insert($chunk);
        foreach ($balances as $uid => $bal) {
            DB::table('users')->where('id', $uid)->update(['art_puan' => $bal]);
        }

        $this->artPuanReport($balances, $revoked);
        $this->info('ArtPuan: ' . count($logs) . ' hareket, ' . count(array_filter($balances)) . ' kullanıcıda bakiye, toplam ' . number_format(array_sum($balances), 2, ',', '.'));
    }

    /** Eski sistemin gösterdiği bakiye ile yeni defteri karşılaştırır (storage/app/legacy-import-artpuan.csv) */
    protected function artPuanReport(array $balances, array $revoked): void
    {
        $old = [];
        foreach ($this->legacy->table('points')->selectRaw('user_id, SUM(points) s')->groupBy('user_id')->get() as $r) {
            $uid = $this->userMap[$r->user_id] ?? null;
            if ($uid) $old[$uid] = ($old[$uid] ?? 0) + (float) $r->s;
        }
        foreach ($this->legacy->table('ref_point_cash_out')->selectRaw('user_id, SUM(points) s')->groupBy('user_id')->get() as $r) {
            $uid = $this->userMap[$r->user_id] ?? $r->user_id;
            $old[$uid] = ($old[$uid] ?? 0) - (float) $r->s;
        }
        $reasons = [];
        foreach ($revoked as [$uid, $pts, $why]) {
            $reasons[$uid][] = "-{$pts} ({$why})";
        }

        $emails = DB::table('users')->pluck('email', 'id');
        $lines = ["user_id;email;eski_bakiye;yeni_bakiye;fark;notlar"];
        $diffCount = 0;
        foreach (array_unique(array_merge(array_keys($old), array_keys($balances))) as $uid) {
            $o = round($old[$uid] ?? 0, 2);
            $n = round($balances[$uid] ?? 0, 2);
            if (abs($o - $n) < 0.01) continue;
            $diffCount++;
            $lines[] = implode(';', [$uid, $emails[$uid] ?? '(yok)', $o, $n, round($n - $o, 2), implode(' | ', $reasons[$uid] ?? [])]);
        }
        Storage::disk('local')->put('legacy-import-artpuan.csv', "\xEF\xBB\xBF" . implode("\n", $lines));
        $this->line("  ArtPuan farkı olan kullanıcı: {$diffCount} (rapor: storage/app/legacy-import-artpuan.csv)");
    }
}
