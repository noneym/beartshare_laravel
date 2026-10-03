<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Harici görsel URL'i -> R2 disk yolu eşlemesi (storage/app/image-url-map.json).
 *
 * images:migrate her taşımada buraya yazar, önce buraya bakar; böylece aynı görsel
 * (ör. eski sistem verisi yeniden aktarıldığında) tekrar indirilmez.
 */
class ImageUrlMap
{
    protected const FILE = 'image-url-map.json';

    protected static ?array $map = null;
    protected static bool $dirty = false;

    /**
     * Cloudflare Images URL'lerinde varyant (/public, /full) ve çift eğik çizgi farkı
     * aynı görseldir; anahtar olarak görsel kimliği kullanılır.
     */
    public static function key(string $src): string
    {
        $src = trim($src);
        if (str_starts_with($src, 'data:')) {
            return 'data:' . md5($src);
        }
        if (preg_match('#imagedelivery\.net/+[^/]+/+([0-9a-f-]{36})#i', $src, $m)) {
            return 'cf:' . strtolower($m[1]);
        }
        return preg_replace('#(?<!:)/{2,}#', '/', $src);
    }

    public static function get(string $src): ?string
    {
        static::load();
        return static::$map[static::key($src)] ?? null;
    }

    public static function put(string $src, string $path): void
    {
        static::load();
        $key = static::key($src);
        if ((static::$map[$key] ?? null) !== $path) {
            static::$map[$key] = $path;
            static::$dirty = true;
        }
    }

    public static function all(): array
    {
        static::load();
        return static::$map;
    }

    public static function count(): int
    {
        static::load();
        return count(static::$map);
    }

    public static function save(): void
    {
        if (static::$dirty) {
            Storage::disk('local')->put(static::FILE, json_encode(static::$map, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            static::$dirty = false;
        }
    }

    protected static function load(): void
    {
        if (static::$map !== null) {
            return;
        }
        $disk = Storage::disk('local');
        static::$map = $disk->exists(static::FILE)
            ? (json_decode($disk->get(static::FILE), true) ?: [])
            : [];
    }
}
