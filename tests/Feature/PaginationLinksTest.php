<?php

namespace Tests\Feature;

use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

/**
 * Livewire sayfalama görünümü (resources/views/vendor/livewire/tailwind.blade.php) arama motorlarının
 * izleyebileceği gerçek <a href="?page=N"> bağlantıları üretmeli.
 */
class PaginationLinksTest extends TestCase
{
    protected function paginator(int $currentPage, array $query = []): LengthAwarePaginator
    {
        return (new LengthAwarePaginator(range(1, 12), 50, 12, $currentPage, ['path' => '/eserler']))
            ->appends($query);
    }

    public function test_pages_are_real_links_with_livewire_fallback(): void
    {
        $html = $this->paginator(2)->links('livewire::tailwind')->toHtml();

        $this->assertStringContainsString('href="/eserler?page=3"', $html);
        $this->assertStringContainsString('wire:click.prevent="gotoPage(3, \'page\')"', $html);
        $this->assertStringContainsString('rel="prev"', $html);
        $this->assertStringContainsString('rel="next"', $html);
        $this->assertStringContainsString('href="/eserler?page=3" rel="next"', $html);
        $this->assertStringNotContainsString('<button', $html);
    }

    public function test_first_page_link_has_no_page_parameter(): void
    {
        $html = $this->paginator(2)->links('livewire::tailwind')->toHtml();
        $this->assertStringContainsString('href="/eserler" rel="prev"', $html);

        $html = $this->paginator(2, ['sortBy' => 'name'])->links('livewire::tailwind')->toHtml();
        $this->assertStringContainsString('href="/eserler?sortBy=name" rel="prev"', $html);
        $this->assertStringContainsString('href="/eserler?sortBy=name&amp;page=3" rel="next"', $html);
    }

    public function test_single_page_renders_nothing(): void
    {
        $paginator = new LengthAwarePaginator(range(1, 5), 5, 12, 1, ['path' => '/eserler']);
        $this->assertStringNotContainsString('<nav', $paginator->links('livewire::tailwind')->toHtml());
    }
}
