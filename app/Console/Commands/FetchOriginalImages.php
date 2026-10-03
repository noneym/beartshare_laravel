<?php

namespace App\Console\Commands;

use App\Models\Artist;
use App\Models\Artwork;
use App\Models\BlogPost;
use App\Support\ImageUrl;
use App\Support\ImageUrlMap;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

/**
 * images:migrate Cloudflare Images'ın "full" varyantını çekmiş, o da 768px'e ayarlı.
 * Bu komut yüklenen dosyanın kendisini (/blob) indirip R2'ye yeni anahtarla koyar
 * ve kayıtları günceller. Yeni anahtar sayesinde Thumbor/CDN önbelleği sorun olmaz.
 */
class FetchOriginalImages extends Command
{
    protected $signature = 'images:originals
        {--limit=0 : En fazla kaç görsel indirilsin (0 = hepsi)}
        {--concurrency=6 : Aynı anda kaç indirme}
        {--dry-run : İndirme/yazma yapma, sadece planı göster}
        {--delete-old : Yenisiyle değiştirilmiş ve artık kullanılmayan eski dosyaları R2\'den sil}';

    protected $description = 'Cloudflare Images orijinallerini (tam çözünürlük) R2\'ye alır';

    protected const PROGRESS = 'image-originals.json';

    /** eski R2 yolu => yeni R2 yolu */
    protected array $done = [];

    public function handle(): int
    {
        ini_set('memory_limit', '2048M');
        $local = Storage::disk('local');
        $this->done = $local->exists(self::PROGRESS) ? json_decode($local->get(self::PROGRESS), true) : [];

        if ($this->option('delete-old')) {
            return $this->deleteOld();
        }

        $cfg = config('services.cloudflare_images');
        if (!$cfg['email'] || !$cfg['key'] || !$cfg['account_id']) {
            $this->error('CLOUDFLARE_EMAIL / CLOUDFLARE_API_KEY tanımlı değil.');
            return self::FAILURE;
        }

        $todo = $this->pending();
        $this->info(count($todo) . ' görsel bekliyor, ' . count($this->done) . ' tanesi daha önce alınmış.');

        if ($limit = (int) $this->option('limit')) {
            $todo = array_slice($todo, 0, $limit, true);
        }

        $disk = Storage::disk(config('filesystems.uploads'));
        $base = "https://api.cloudflare.com/client/v4/accounts/{$cfg['account_id']}/images/v1";
        $fail = 0;
        $bytes = 0;
        $started = microtime(true);

        foreach (array_chunk($todo, max(1, (int) $this->option('concurrency')), true) as $chunk) {
            if ($this->option('dry-run')) {
                foreach ($chunk as $old => $uuid) $this->line("  {$old}  <=  {$uuid}");
                continue;
            }

            $responses = Http::pool(fn (Pool $pool) => array_map(
                fn ($old, $uuid) => $pool->as($old)
                    ->timeout(180)
                    ->withHeaders(['X-Auth-Email' => $cfg['email'], 'X-Auth-Key' => $cfg['key']])
                    ->get("{$base}/{$uuid}/blob"),
                array_keys($chunk),
                $chunk,
            ));

            foreach ($chunk as $old => $uuid) {
                $res = $responses[$old] ?? null;
                $type = $res instanceof \Illuminate\Http\Client\Response ? strtolower(explode(';', (string) $res->header('Content-Type'))[0]) : '';
                if (!$res instanceof \Illuminate\Http\Client\Response || !$res->successful() || !str_starts_with($type, 'image/')) {
                    $status = $res instanceof \Illuminate\Http\Client\Response ? $res->status() : 'bağlantı hatası';
                    $this->line("  <fg=red>HATA</> {$old} ({$uuid}): {$status}");
                    $fail++;
                    continue;
                }

                $body = $res->body();
                $new = sprintf('%s/%s.%s', dirname($old), substr(sha1('orig:' . $uuid), 0, 16), $this->extension($type));

                if (!$disk->put($new, $body, ['ContentType' => $type])) {
                    $this->line("  <fg=red>yazılamadı</> {$new}");
                    $fail++;
                    continue;
                }

                $size = @getimagesizefromstring($body);
                $bytes += strlen($body);
                $this->done[$old] = $new;
                ImageUrlMap::put("https://imagedelivery.net/x/{$uuid}/public", $new);
                $this->line(sprintf('  <fg=green>ok</> %s  %s  %.1f MB', $new, $size ? "{$size[0]}x{$size[1]}" : '?', strlen($body) / 1048576));
            }

            $this->saveProgress();
        }

        if ($this->option('dry-run')) {
            return self::SUCCESS;
        }

        $this->saveProgress();
        $updated = $this->updateRecords();

        $this->newLine();
        $this->info(sprintf('Alınan: %d  Hatalı: %d  Toplam: %.0f MB  Süre: %ds  Güncellenen kayıt: %d',
            count($todo) - $fail, $fail, $bytes / 1048576, microtime(true) - $started, $updated));
        $this->line('Eski dosyaları silmek için: php artisan images:originals --delete-old');

        return $fail ? self::FAILURE : self::SUCCESS;
    }

    /**
     * Veritabanında kullanılan ve henüz orijinali alınmamış R2 yolları => Cloudflare görsel kimliği.
     */
    protected function pending(): array
    {
        $uuidByPath = [];
        foreach (ImageUrlMap::all() as $key => $path) {
            if (str_starts_with($key, 'cf:')) $uuidByPath[$path] = substr($key, 3);
        }

        // yeni (orijinal) yollar da eşlemede; onları tekrar indirme
        $originals = array_flip($this->done);

        $todo = [];
        $add = function (?string $path) use (&$todo, $uuidByPath, $originals) {
            if ($path && isset($uuidByPath[$path]) && !isset($this->done[$path]) && !isset($originals[$path])) {
                $todo[$path] = $uuidByPath[$path];
            }
        };

        foreach (Artwork::orderByDesc('is_active')->orderBy('id')->get(['id', 'images', 'is_active']) as $a) {
            foreach ((array) $a->images as $p) $add($p);
        }
        foreach (Artist::get(['avatar', 'image']) as $r) {
            $add($r->avatar);
            $add($r->image);
        }
        foreach (BlogPost::get(['image', 'content']) as $post) {
            $add($post->image);
            foreach ($this->contentPaths((string) $post->content) as $p) $add($p);
        }

        return $todo;
    }

    protected function updateRecords(): int
    {
        $map = $this->done;
        $swap = fn ($p) => $map[$p] ?? $p;
        $n = 0;

        foreach (Artwork::all() as $a) {
            $images = (array) $a->images;
            $new = array_map($swap, $images);
            if ($new !== $images) {
                $a->forceFill(['images' => $new])->saveQuietly();
                $n++;
            }
        }

        foreach (Artist::all() as $r) {
            $update = array_filter([
                'avatar' => isset($map[$r->avatar]) ? $map[$r->avatar] : null,
                'image' => isset($map[$r->image]) ? $map[$r->image] : null,
            ]);
            if ($update) {
                $r->forceFill($update)->saveQuietly();
                $n++;
            }
        }

        ini_set('pcre.backtrack_limit', '500000000');
        foreach (BlogPost::all() as $post) {
            $update = [];
            if (isset($map[$post->image])) $update['image'] = $map[$post->image];

            $content = (string) $post->content;
            $newContent = preg_replace_callback('/(<img\b[^>]*\bsrc=)(["\'])([^"\']+)\2/i', function ($m) use ($map) {
                $path = ImageUrl::pathFromUrl($m[3]);
                return $path && isset($map[$path]) ? $m[1] . $m[2] . ImageUrl::make($map[$path], 'blog') . $m[2] : $m[0];
            }, $content);
            if ($newContent !== null && $newContent !== $content) $update['content'] = $newContent;

            if ($update) {
                $post->forceFill($update)->saveQuietly();
                $n++;
            }
        }

        return $n;
    }

    protected function contentPaths(string $html): array
    {
        ini_set('pcre.backtrack_limit', '500000000');
        preg_match_all('/<img\b[^>]*\bsrc=(["\'])([^"\']+)\1/i', $html, $m);
        return array_filter(array_map([ImageUrl::class, 'pathFromUrl'], $m[2] ?? []));
    }

    protected function deleteOld(): int
    {
        $used = [];
        foreach (Artwork::get(['images']) as $a) {
            foreach ((array) $a->images as $p) $used[$p] = true;
        }
        foreach (Artist::get(['avatar', 'image']) as $r) {
            $used[$r->avatar] = $used[$r->image] = true;
        }
        foreach (BlogPost::get(['image', 'content']) as $post) {
            $used[$post->image] = true;
            foreach ($this->contentPaths((string) $post->content) as $p) $used[$p] = true;
        }

        $old = array_filter(array_keys($this->done), fn ($p) => !isset($used[$p]));
        if (!$old) {
            $this->info('Silinecek eski dosya yok.');
            return self::SUCCESS;
        }
        if (!$this->confirm(count($old) . ' eski dosya R2\'den kalıcı olarak silinecek. Devam?')) {
            return self::SUCCESS;
        }

        $disk = Storage::disk(config('filesystems.uploads'));
        foreach (array_chunk($old, 500) as $chunk) {
            $disk->delete($chunk);
        }
        $this->info(count($old) . ' eski dosya silindi.');

        return self::SUCCESS;
    }

    protected function saveProgress(): void
    {
        Storage::disk('local')->put(self::PROGRESS, json_encode($this->done, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        ImageUrlMap::save();
    }

    protected function extension(string $type): string
    {
        return ['image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif', 'image/avif' => 'avif'][$type] ?? 'jpg';
    }
}
