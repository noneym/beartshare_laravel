<?php

namespace App\Livewire;

use App\Models\Faq;
use Livewire\Component;

class FaqPage extends Component
{
    public $selectedCategory = '';

    public function setCategory($category)
    {
        $this->selectedCategory = $this->selectedCategory === $category ? '' : $category;
    }

    public function render()
    {
        $query = Faq::active()->ordered();

        if ($this->selectedCategory) {
            $query->byCategory($this->selectedCategory);
        }

        $faqs = $query->get();

        // Kategorilere göre grupla
        $groupedFaqs = $faqs->groupBy('category');

        // Aktif kategorileri al
        $activeCategories = Faq::active()
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->filter()
            ->mapWithKeys(fn($cat) => [$cat => Faq::CATEGORIES[$cat] ?? $cat]);

        // FAQPage şeması: filtreden bağımsız, tüm aktif sorular
        $jsonLd = json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => Faq::active()->ordered()->get()->map(fn ($f) => [
                '@type' => 'Question',
                'name' => $f->question,
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => trim(strip_tags($f->answer))],
            ])->values()->all(),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return view('livewire.faq-page', [
            'faqs' => $faqs,
            'groupedFaqs' => $groupedFaqs,
            'categories' => $activeCategories,
        ])->layoutData([
            'title' => 'Sıkça Sorulan Sorular | BeArtShare',
            'metaDescription' => 'BeArtShare\'de eser satın alma, ödeme, teslimat, iade, ArtPuan ve eser kabulü hakkında sık sorulan sorular ve cevapları.',
            'metaKeywords' => 'sıkça sorulan sorular, beartshare sss, sanat eseri satın alma, teslimat, iade, artpuan',
            'jsonLd' => $jsonLd,
        ]);
    }
}
