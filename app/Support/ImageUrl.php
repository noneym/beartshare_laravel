<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class ImageUrl
{
    /**
     * Depodaki bir görsel yolu (veya tam URL) için servis URL'i üret.
     *
     * @param  string|null  $path    "artworks/abc.jpg" gibi disk yolu ya da http(s) URL
     * @param  int|string   $width   piksel ya da preset adı ('card', 'avatar' ...)
     * @param  int          $height  0 = oranı koru
     * @param  array        $filters ek Thumbor filtreleri, ör. ['format(webp)']
     */
    public static function make(?string $path, int|string $width = 0, int $height = 0, array $filters = [], bool $smart = true): ?string
    {
        if (!$path) {
            return null;
        }

        if (is_string($width)) {
            [$width, $height] = config("images.presets.{$width}", [0, 0]);
        }

        // Harici URL'ler (ör. imagedelivery.net) olduğu gibi döner
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $thumbor = config('images.thumbor_url');
        if (!$thumbor) {
            return Storage::disk(config('filesystems.uploads'))->url($path);
        }

        $parts = [];
        if ($width || $height) {
            $parts[] = (int) $width . 'x' . (int) $height;
        }
        if ($smart && $width && $height) {
            $parts[] = 'smart';
        }
        $allFilters = array_merge(config('images.default_filters', []), $filters);
        if ($allFilters) {
            $parts[] = 'filters:' . implode(':', $allFilters);
        }
        $parts[] = ltrim($path, '/');

        $operation = implode('/', $parts);

        $key = config('images.thumbor_key');
        if ($key && !config('images.thumbor_unsafe')) {
            // Thumbor urlsafe base64 kullanır ve '=' dolgusunu korur
            $signature = strtr(base64_encode(hash_hmac('sha1', $operation, $key, true)), '+/', '-_');
            return "{$thumbor}/{$signature}/{$operation}";
        }

        return "{$thumbor}/unsafe/{$operation}";
    }
}
