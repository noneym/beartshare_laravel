<?php

namespace App\Livewire\Concerns;

use Illuminate\Support\Str;

/**
 * Sayfalanmış liste sayfaları için SEO yardımcıları (WithPagination ile birlikte kullanılır).
 */
trait PaginatedSeo
{
    /** 2. sayfadan itibaren başlığa "Sayfa N" eklenir; aynı başlıklı sayfalar oluşmaz. */
    protected function paginatedTitle(string $title): string
    {
        $page = $this->getPage();
        if ($page <= 1) {
            return $title;
        }

        return Str::contains($title, ' | ')
            ? Str::replaceFirst(' | ', " - Sayfa {$page} | ", $title)
            : "{$title} - Sayfa {$page}";
    }

    /** Canonical: filtre/sıralama parametreleri atılır, yalnızca sayfa numarası kalır (kendine işaret eder). */
    protected function paginatedCanonical(): string
    {
        $page = $this->getPage();

        return url()->current() . ($page > 1 ? '?page=' . $page : '');
    }
}
