<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArtistController extends Controller
{
    public function index()
    {
        $artists = Artist::withCount('artworks')
            ->latest()
            ->paginate(20);

        return view('admin.artists.index', compact('artists'));
    }

    public function create()
    {
        return view('admin.artists.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'birth_year' => 'nullable|integer|min:1800|max:' . date('Y'),
            'death_year' => 'nullable|integer|min:1800|max:' . date('Y'),
            'biography' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('artists', 'public');
        }

        Artist::create($validated);

        return redirect()->route('admin.artists.index')
            ->with('success', 'Sanatçı başarıyla eklendi.');
    }

    public function edit(Artist $artist)
    {
        return view('admin.artists.edit', compact('artist'));
    }

    public function update(Request $request, Artist $artist)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'birth_year' => 'nullable|integer|min:1800|max:' . date('Y'),
            'death_year' => 'nullable|integer|min:1800|max:' . date('Y'),
            'biography' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('artists', 'public');
        }

        $artist->update($validated);

        return redirect()->route('admin.artists.index')
            ->with('success', 'Sanatçı başarıyla güncellendi.');
    }

    public function destroy(Artist $artist)
    {
        $artist->delete();

        return redirect()->route('admin.artists.index')
            ->with('success', 'Sanatçı başarıyla silindi.');
    }
}
