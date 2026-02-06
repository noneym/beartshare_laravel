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

        return view('livewire.faq-page', [
            'faqs' => $faqs,
            'groupedFaqs' => $groupedFaqs,
            'categories' => $activeCategories,
        ]);
    }
}
