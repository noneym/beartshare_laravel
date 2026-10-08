<?php

namespace Tests\Unit;

use App\Support\Slugger;
use PHPUnit\Framework\TestCase;

class SluggerTest extends TestCase
{
    public function test_turkish_characters_are_transliterated(): void
    {
        $this->assertSame('isimsiz', Slugger::base('İsimsiz'));
        $this->assertSame('kuslar', Slugger::base('Kuşlar'));
        $this->assertSame('istanbul-yeni-camiden-halice-bakis', Slugger::base('İstanbul Yeni Cami’den Haliç’e Bakış'));
        $this->assertSame('nese-erdok', Slugger::base("Neş'e Erdok"));
        $this->assertSame('nuri-iyem-1915-2005', Slugger::base('Nuri İyem (1915-2005)'));
    }

    public function test_long_titles_are_cut_at_word_boundary(): void
    {
        $slug = Slugger::base('Entre Printemps et Été / Literature MOURLOT 687 / Eser yurt dışı müzayede çıkışlıdır.');

        $this->assertLessThanOrEqual(60, strlen($slug));
        $this->assertSame('entre-printemps-et-ete-literature-mourlot-687-eser-yurt-disi', $slug);
        $this->assertTrue(Slugger::isClean($slug));
    }

    public function test_is_clean(): void
    {
        $this->assertTrue(Slugger::isClean('duvar-53'));
        $this->assertFalse(Slugger::isClean('nuri-iyem-1915-2005-'));
        $this->assertFalse(Slugger::isClean('-semih-zeki'));
        $this->assertFalse(Slugger::isClean('1-mayıs'));
        $this->assertFalse(Slugger::isClean(''));
    }

    public function test_unique_prefers_base_then_fallback_then_numeric_suffix(): void
    {
        $taken = fn (string $s) => in_array($s, ['isimsiz', 'isimsiz-nasip-iyem', 'isimsiz-nasip-iyem-2'], true);

        $this->assertSame('kemanci', Slugger::unique('kemanci', $taken, 'kemanci-adnan-turani'));
        $this->assertSame('isimsiz-nasip-iyem-3', Slugger::unique('isimsiz', $taken, 'isimsiz-nasip-iyem'));
        $this->assertSame('isimsiz-2', Slugger::unique('isimsiz', $taken));
    }
}
