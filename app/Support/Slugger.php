<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * URL slug üretimi: Türkçe karakterler ASCII'ye çevrilir (İsimsiz → isimsiz, Kuşlar → kuslar),
 * uzun başlıklar kelime sınırından kısaltılır, çakışmalarda sayısal ek verilir.
 */
class Slugger
{
    /** Temiz bir slug mu: yalnızca a-z, 0-9 ve tek tire; başta/sonda tire yok. */
    public static function isClean(?string $slug): bool
    {
        return (bool) preg_match('/^[a-z0-9]+(-[a-z0-9]+)*$/', (string) $slug);
    }

    /** Metinden slug; $max karakteri aşarsa son tam kelimede kesilir. */
    public static function base(?string $text, int $max = 60): string
    {
        $slug = Str::slug((string) $text);

        if (strlen($slug) > $max) {
            $cut = substr($slug, 0, $max + 1);
            $pos = strrpos($cut, '-');
            $slug = rtrim(substr($slug, 0, $pos ?: $max), '-');
        }

        return $slug;
    }

    /**
     * Kullanılmayan bir slug döndürür. Önce $base, (varsa) sonra $fallback denenir;
     * ikisi de doluysa -2, -3 ... eki alır (varsa $fallback üzerine).
     *
     * @param  callable(string): bool  $taken  slug kullanımda mı
     */
    public static function unique(string $base, callable $taken, ?string $fallback = null): string
    {
        if (! $taken($base)) {
            return $base;
        }

        if ($fallback !== null && $fallback !== $base && ! $taken($fallback)) {
            return $fallback;
        }

        $root = $fallback ?? $base;
        for ($i = 2; $i < 1000; $i++) {
            if (! $taken("{$root}-{$i}")) {
                return "{$root}-{$i}";
            }
        }

        return $root . '-' . uniqid();
    }
}
