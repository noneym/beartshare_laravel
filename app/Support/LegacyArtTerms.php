<?php

namespace App\Support;

use App\Models\ArtTerm;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\DB;

/**
 * Sanat terimleri (eski sitedeki /art-terms): İngilizce terim + Türkçe karşılığı, eski adresle aynı slug.
 * Görseller Cloudflare Images'ta; images:migrate --only=art-terms R2'ye taşır.
 */
class LegacyArtTerms
{
    /**
     * @param callable(?string): ?string $mapImage eski görsel URL'ini (varsa) R2 yoluna çevirir
     * @param array<int, string|null> $currentImages yeni sistemdeki görseller (id => yol): yeni sistemde
     *        eklenen ya da değiştirilen görseller korunur
     */
    public static function import(ConnectionInterface $legacy, callable $mapImage, array $currentImages = []): int
    {
        $rows = [];
        $used = [];
        foreach ($legacy->table('art_terms')->orderBy('id')->get() as $t) {
            $title = trim((string) $t->title);
            if ($title === '') continue;
            // Slug bozuk kodlamalı orijinal başlıktan: eski sitenin adresiyle aynı kalsın
            $slug = ArtTerm::legacySlug($title) ?: 'terim-' . $t->id;
            // Eski site aynı slug'da ilk kaydı gösteriyordu; sonrakiler id ile ayrılır
            if (isset($used[$slug])) $slug .= '-' . $t->id;
            $used[$slug] = true;

            $rows[] = [
                'id' => $t->id,
                'title' => Mojibake::fix($title),
                'slug' => $slug,
                'title_tr' => Mojibake::fix(trim((string) $t->translated_title) ?: null),
                'description' => Mojibake::fix(trim((string) $t->text) ?: null),
                'description_tr' => Mojibake::fix(trim((string) $t->translated_text) ?: null),
                'image' => self::pickImage($mapImage($t->image_url ?: null), $currentImages[$t->id] ?? null),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('art_terms')->delete();
        foreach (array_chunk($rows, 100) as $chunk) DB::table('art_terms')->insert($chunk);

        return count($rows);
    }

    /**
     * Yeni sistemde eklenen ya da değiştirilen görsel (R2 yolu, eski görselin taşınmış hali değil) korunur;
     * yoksa eski sistemdeki görsel kullanılır.
     */
    protected static function pickImage(?string $legacy, ?string $current): ?string
    {
        $currentIsLocal = $current && !preg_match('#^https?://#', $current);
        if ($currentIsLocal && $current !== $legacy) {
            return $current;
        }
        return $legacy ?: $current;
    }
}
