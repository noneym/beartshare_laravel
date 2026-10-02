<?php

namespace App\Console\Commands;

use App\Models\Artist;
use App\Models\Artwork;
use App\Models\BlogPost;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class MigrateImagesToR2 extends Command
{
    protected $signature = 'images:migrate
        {--only= : artworks|artists|blog (boş = hepsi)}
        {--limit=0 : En fazla kaç kayıt işlensin (0 = sınırsız)}
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
        $disk = Storage::disk(config('filesystems.uploads'));

        $this->info('Hedef disk: ' . config('filesystems.uploads') . ($dry ? '  [DRY RUN]' : ''));

        if (!$only || $only === 'artworks') {
            $q = Artwork::query()->orderBy('id');
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
        // Cloudflare Images: /public gibi küçültülmüş varyant yerine /full (orijinal) dene
        $candidates = [$src];
        if (str_contains($src, 'imagedelivery.net/') && !str_ends_with($src, '/full')) {
            array_unshift($candidates, preg_replace('#/[^/]+$#', '/full', $src));
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
        $this->info('Eski URL\'ler geri yüklendi. (R2\'deki dosyalar silinmedi.)');
        return self::SUCCESS;
    }
}
