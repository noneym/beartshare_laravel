<?php

namespace App\Models\Concerns;

use App\Models\SlugRedirect;

/**
 * Slug'ı değiştirilebilen modeller: eski adres SlugRedirect ile 301'e bağlanır.
 */
trait HasSlugRedirects
{
    /** Slug'ı değiştirir, eski slug'ı yönlendirme tablosuna yazar. Değişiklik yoksa false. */
    public function renameSlug(string $new): bool
    {
        if ($new === $this->slug) {
            return false;
        }

        $old = $this->slug;
        $this->slug = $new;
        $this->saveQuietly();

        if ($old) {
            SlugRedirect::record(static::class, $old, $this->id, $new);
        }

        return true;
    }

    /** Slug başka bir kayıtta ya da eski adres olarak kullanımda mı */
    public static function slugTaken(string $slug, ?int $ignoreId = null): bool
    {
        return static::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
            || SlugRedirect::exists(static::class, $slug);
    }
}
