<?php

namespace App\Http\Controllers;

use App\Models\ArtTerm;

/**
 * Sanat terimleri sözlüğü (eski sitedeki /art-terms).
 */
class ArtTermController extends Controller
{
    public function index()
    {
        // Eski sitedeki gibi İngilizce terimin baş harfine göre gruplanır
        $groups = ArtTerm::active()->orderBy('title')->get()
            ->groupBy(fn ($t) => mb_strtoupper(mb_substr($t->title, 0, 1)));

        return view('pages.art-terms.index', compact('groups'));
    }

    public function show(string $slug)
    {
        $term = ArtTerm::active()->where('slug', $slug)->firstOrFail();

        $related = ArtTerm::active()
            ->where('id', '!=', $term->id)
            ->where('title', 'like', mb_substr($term->title, 0, 1) . '%')
            ->orderBy('title')
            ->limit(6)
            ->get();

        return view('pages.art-terms.show', compact('term', 'related'));
    }
}
