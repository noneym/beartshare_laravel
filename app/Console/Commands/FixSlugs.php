<?php

namespace App\Console\Commands;

use App\Models\Artist;
use App\Models\Artwork;
use App\Models\BlogPost;
use App\Support\Slugger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

/**
 * Eski siteden bozuk aktarılan slug'ları düzeltir (İsimsiz → "simsiz", Kuşlar → "kular", sondaki tireler).
 * Eski adresler slug_redirects tablosuna yazılır ve 301 ile yeni adrese gider.
 */
class FixSlugs extends Command
{
    protected $signature = 'slugs:fix {--dry-run : Değişiklikleri uygulamadan listele}';

    protected $description = 'Bozuk eser / sanatçı / blog slug\'larını düzeltir, eski adresleri 301\'e bağlar';

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');
        $rows = [];
        // Dry-run'da kayıt yapılmadığı için yeni verilen slug'lar burada tutulur (çakışma kontrolü)
        $reserved = [];

        // Eser: slug "eser-adi-sanatci-adi" (+ isteğe bağlı sayısal ek) değilse yeniden üretilir
        foreach (Artwork::with('artist')->orderBy('id')->get() as $artwork) {
            $base = Artwork::expectedSlugBase($artwork->title, $artwork->artist?->name);
            if (preg_match('/^' . preg_quote($base, '/') . '(-\d+)?$/', (string) $artwork->slug)) {
                continue;
            }

            $new = Artwork::generateSlug($artwork->title, $artwork->artist?->name, $artwork->id, $reserved);
            if ($new !== $artwork->slug) {
                $reserved[] = $new;
                $rows[] = ['Eser', $artwork->id, $artwork->slug, $new];
                $dry || $artwork->renameSlug($new);
            }
        }

        // Sanatçı ve blog: yalnızca biçimi bozuk olanlar (baş/son tire, Türkçe karakter)
        foreach (Artist::orderBy('id')->get() as $artist) {
            if (Slugger::isClean($artist->slug)) {
                continue;
            }

            $new = Artist::generateSlug($artist->name, $artist->id, $reserved);
            if ($new !== $artist->slug) {
                $reserved[] = $new;
                $rows[] = ['Sanatçı', $artist->id, $artist->slug, $new];
                $dry || $artist->renameSlug($new);
            }
        }

        foreach (BlogPost::orderBy('id')->get() as $post) {
            if (Slugger::isClean($post->slug)) {
                continue;
            }

            $new = BlogPost::generateSlug($post->title, $post->id, $reserved);
            if ($new !== $post->slug) {
                $reserved[] = $new;
                $rows[] = ['Blog', $post->id, $post->slug, $new];
                $dry || $post->renameSlug($new);
            }
        }

        if (! $rows) {
            $this->info('Düzeltilecek slug yok.');

            return self::SUCCESS;
        }

        $this->table(['Tür', 'ID', 'Eski slug', 'Yeni slug'], $rows);

        if ($dry) {
            $this->warn(count($rows) . ' slug değişecek (dry-run, uygulanmadı).');
        } else {
            Cache::forget('sitemap.xml');
            $this->info(count($rows) . ' slug güncellendi; eski adresler 301 ile yönlendiriliyor. Sitemap önbelleği temizlendi.');
        }

        return self::SUCCESS;
    }
}
