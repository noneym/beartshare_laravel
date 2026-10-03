<?php

namespace App\Console\Commands;

use App\Models\Artist;
use App\Models\Artwork;
use App\Models\BlogPost;
use App\Support\ImageUrlMap;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class MigrateImagesToR2 extends Command
{
    protected $signature = 'images:migrate
        {--only= : artworks|artists|blog|content (boş = hepsi; content = blog yazısı içindeki gömülü görseller)}
        {--limit=0 : En fazla kaç kayıt işlensin (0 = sınırsız)}
        {--from=0 : Başlangıç id (dahil)}
        {--to=0 : Bitiş id (dahil, 0 = sınırsız)}
        {--dry-run : İndirme/yazma yapma, sadece planı göster}
        {--rollback= : Yedek JSON dosyasından eski URL\'leri geri yükle}';

    protected $description = 'Harici (Cloudflare Images vb.) görselleri R2 diskine taşır ve kayıtları günceller';

    protected array $backup = [];
    protected int $ok = 0;
    protected int $fail = 0;
    protected int $skipped = 0;

    public function handle(): int
    {
        if ($file = $this->option('rollback')) {
            return $this->rollback($file);
        }

        $only = $this->option('only');
        $limit = (int) $this->option('limit');
        $dry = (bool) $this->option('dry-run');
        $from = (int) $this->option('from');
        $to = (int) $this->option('to');
        $disk = Storage::disk(config('filesystems.uploads'));

        $this->info('Hedef disk: ' . config('filesystems.uploads') . ($dry ? '  [DRY RUN]' : ''));

        // Gömülü base64 görseller içeren çok büyük içeriklerde PCRE geri izleme sınırı aşılıyor
        ini_set('pcre.backtrack_limit', '500000000');
        ini_set('memory_limit', '1024M');

        if (!$only || $only === 'artworks') {
            $q = Artwork::query()->orderBy('id');
            if ($from) $q->where('id', '>=', $from);
            if ($to) $q->where('id', '<=', $to);
            if ($limit) $q->limit($limit);
            foreach ($q->get() as $artwork) {
                $images = (array) $artwork->images;
                if (!$images) continue;
                $changed = false;
                $new = [];
                foreach ($images as $i => $src) {
                    $new[$i] = $src;
                    if (!$this->isExternal($src)) { $this->skipped++; continue; }
                    $key = sprintf('artworks/%d/%s', $artwork->id, substr(sha1($src), 0, 16));
                    $stored = $this->transfer($disk, $src, $key, $dry);
                    if ($stored) { $new[$i] = $stored; $changed = true; }
                }
                if ($changed && !$dry) {
                    $this->backup['artworks'][$artwork->id] = $images;
                    $artwork->forceFill(['images' => array_values($new)])->saveQuietly();
                }
            }
        }

        if (!$only || $only === 'artists') {
            $q = Artist::query()->orderBy('id');
            if ($from) $q->where('id', '>=', $from);
            if ($to) $q->where('id', '<=', $to);
            if ($limit) $q->limit($limit);
            foreach ($q->get() as $artist) {
                $update = [];
                foreach (['avatar', 'image'] as $field) {
                    $src = $artist->{$field};
                    if (!$src || !$this->isExternal($src)) { if ($src) $this->skipped++; continue; }
                    $key = sprintf('artists/%d/%s-%s', $artist->id, $field, substr(sha1($src), 0, 12));
                    $stored = $this->transfer($disk, $src, $key, $dry);
                    if ($stored) $update[$field] = $stored;
                }
                if ($update && !$dry) {
                    $this->backup['artists'][$artist->id] = ['avatar' => $artist->avatar, 'image' => $artist->image];
                    $artist->forceFill($update)->saveQuietly();
                }
            }
        }

        if (!$only || $only === 'blog') {
            $q = BlogPost::query()->orderBy('id');
            if ($from) $q->where('id', '>=', $from);
            if ($to) $q->where('id', '<=', $to);
            if ($limit) $q->limit($limit);
            foreach ($q->get() as $post) {
                $src = $post->image;
                if (!$src || !$this->isExternal($src)) { if ($src) $this->skipped++; continue; }
                $key = sprintf('blog/%d/%s', $post->id, substr(sha1($src), 0, 12));
                $stored = $this->transfer($disk, $src, $key, $dry);
                if ($stored && !$dry) {
                    $this->backup['blog'][$post->id] = $src;
                    $post->forceFill(['image' => $stored])->saveQuietly();
                }
            }
        }

        if (!$only || $only === 'content') {
            $q = BlogPost::query()->orderBy('id');
            if ($from) $q->where('id', '>=', $from);
            if ($to) $q->where('id', '<=', $to);
            if ($limit) $q->limit($limit);
            foreach ($q->get() as $post) {
                $original = (string) $post->content;
                if ($original === '') continue;
                $self = $this;
                $changed = false;
                $new = preg_replace_callback('/(<img\b[^>]*\bsrc=)(["\'])([^"\']+)\2/i', function ($m) use ($self, $disk, $post, $dry, &$changed) {
                    $src = $m[3];
                    $thumbor = config('images.thumbor_url');
                    if ($thumbor && str_starts_with($src, $thumbor . '/')) { $self->skipped++; return $m[0]; }

                    $keyBase = sprintf('blog/%d/content-%s', $post->id, substr(sha1($src), 0, 12));
                    $stored = null;
                    if (str_starts_with($src, 'data:image/')) {
                        $stored = $self->storeDataUri($disk, $src, $keyBase, $dry);
                    } elseif ($self->isExternal($src)) {
                        $stored = $self->transfer($disk, $src, $keyBase, $dry);
                    } else {
                        $self->skipped++;
                        return $m[0];
                    }
                    if (!$stored) return $m[0];
                    $changed = true;
                    return $m[1] . $m[2] . \App\Support\ImageUrl::make($stored, 'blog') . $m[2];
                }, $original);

                if ($new === null) {
                    $this->line("  <fg=red>regex hatası</> blog #{$post->id}: " . preg_last_error_msg());
                    $this->fail++;
                    continue;
                }

                if ($changed && !$dry) {
                    $this->backup['content'][$post->id] = $original;
                    $post->forceFill(['content' => $new])->saveQuietly();
                    $this->line("  <fg=green>içerik güncellendi</> blog #{$post->id}");
                }
            }
        }

        ImageUrlMap::save();

        if (!$dry && $this->backup) {
            $path = 'image-migration-backup-' . date('Ymd-His') . '.json';
            Storage::disk('local')->put($path, json_encode($this->backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            $this->info("Yedek: storage/app/{$path}  (geri almak için --rollback={$path})");
        }

        $this->newLine();
        $this->info("Taşınan: {$this->ok}   Hatalı: {$this->fail}   Atlanan (zaten yerel): {$this->skipped}");

        return $this->fail ? self::FAILURE : self::SUCCESS;
    }

    protected function isExternal(string $path): bool
    {
        return str_starts_with($path, 'http://') || str_starts_with($path, 'https://');
    }

    /**
     * Harici URL'i indirip diske yazar; başarılıysa uzantılı disk yolunu döner.
     */
    protected function transfer($disk, string $src, string $keyBase, bool $dry): ?string
    {
        // Daha önce taşınmış görsel: yeniden indirme
        if ($known = ImageUrlMap::get($src)) {
            $this->ok++;
            $this->line("  <fg=cyan>eşleme</> {$known}");
            return $dry ? null : $known;
        }

        $stored = $this->download($disk, $src, $keyBase, $dry);
        if ($stored) {
            ImageUrlMap::put($src, $stored);
        }
        return $stored;
    }

    protected function download($disk, string $src, string $keyBase, bool $dry): ?string
    {
        // Bazı kayıtlarda "host//path" gibi çift eğik çizgi var; şema sonrası fazlalıkları tekle
        $clean = preg_replace('#(?<!:)/{2,}#', '/', trim($src));

        // Cloudflare Images: /public gibi küçültülmüş varyant yerine /full (orijinal) dene
        $candidates = [$clean];
        if (str_contains($clean, 'imagedelivery.net/') && !str_ends_with($clean, '/full')) {
            array_unshift($candidates, preg_replace('#/[^/]+$#', '/full', $clean));
        }

        foreach ($candidates as $url) {
            try {
                $head = Http::timeout(30)->retry(2, 500)
                    ->withHeaders(['User-Agent' => 'BeArtShare-ImageMigrator/1.0 (+https://www.beartshare.com; info@beartshare.com)'])
                    ->get($url);
            } catch (\Throwable $e) {
                $this->line("  <fg=yellow>indirilemedi</> {$url} ({$e->getMessage()})");
                continue;
            }
            if (!$head->successful()) {
                continue;
            }

            $ext = $this->extensionFor($head->header('Content-Type'), $url);
            $key = "{$keyBase}.{$ext}";

            if ($dry) {
                $this->line("  {$src}  ->  {$key}  (" . number_format(strlen($head->body()) / 1024, 0) . ' KB)');
                $this->ok++;
                return null;
            }

            if ($disk->exists($key) || $disk->put($key, $head->body(), ['ContentType' => $head->header('Content-Type')])) {
                $this->ok++;
                $this->line("  <fg=green>ok</> {$key}");
                return $key;
            }

            $this->line("  <fg=red>yazılamadı</> {$key}");
            $this->fail++;
            return null;
        }

        $this->line("  <fg=red>HATA</> {$src}");
        $this->fail++;
        return null;
    }

    /**
     * data:image/...;base64,... URI'sini diske yazar.
     */
    protected function storeDataUri($disk, string $dataUri, string $keyBase, bool $dry): ?string
    {
        if ($known = ImageUrlMap::get($dataUri)) {
            $this->ok++;
            return $dry ? null : $known;
        }
        $stored = $this->writeDataUri($disk, $dataUri, $keyBase, $dry);
        if ($stored) {
            ImageUrlMap::put($dataUri, $stored);
        }
        return $stored;
    }

    protected function writeDataUri($disk, string $dataUri, string $keyBase, bool $dry): ?string
    {
        if (!preg_match('#^data:(image/[a-z0-9.+-]+);base64,(.+)$#is', $dataUri, $m)) {
            $this->fail++;
            return null;
        }
        $bytes = base64_decode($m[2], true);
        if ($bytes === false) {
            $this->fail++;
            return null;
        }
        $key = $keyBase . '.' . $this->extensionFor($m[1], '');
        if ($dry) {
            $this->line("  data-uri ({$m[1]}, " . number_format(strlen($bytes) / 1024, 0) . " KB)  ->  {$key}");
            $this->ok++;
            return null;
        }
        if ($disk->exists($key) || $disk->put($key, $bytes, ['ContentType' => $m[1]])) {
            $this->ok++;
            $this->line("  <fg=green>ok</> {$key}");
            return $key;
        }
        $this->fail++;
        return null;
    }

    protected function extensionFor(?string $contentType, string $url): string
    {
        $map = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif', 'image/avif' => 'avif', 'image/svg+xml' => 'svg'];
        $ct = strtolower(trim(explode(';', (string) $contentType)[0]));
        if (isset($map[$ct])) return $map[$ct];
        $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
        return in_array($ext, $map, true) ? $ext : 'jpg';
    }

    protected function rollback(string $file): int
    {
        if (!Storage::disk('local')->exists($file)) {
            $this->error("Yedek bulunamadı: storage/app/{$file}");
            return self::FAILURE;
        }
        $data = json_decode(Storage::disk('local')->get($file), true);
        foreach ($data['artworks'] ?? [] as $id => $images) {
            Artwork::whereKey($id)->update(['images' => json_encode($images)]);
        }
        foreach ($data['artists'] ?? [] as $id => $fields) {
            Artist::whereKey($id)->update($fields);
        }
        foreach ($data['blog'] ?? [] as $id => $image) {
            BlogPost::whereKey($id)->update(['image' => $image]);
        }
        foreach ($data['content'] ?? [] as $id => $content) {
            BlogPost::whereKey($id)->update(['content' => $content]);
        }
        $this->info('Eski URL\'ler geri yüklendi. (R2\'deki dosyalar silinmedi.)');
        return self::SUCCESS;
    }
}
