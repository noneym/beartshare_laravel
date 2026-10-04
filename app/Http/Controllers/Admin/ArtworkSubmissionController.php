<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\SortsIndex;
use App\Http\Controllers\Controller;
use App\Models\ArtworkSubmission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ArtworkSubmissionController extends Controller
{
    use SortsIndex;

    public function index(Request $request)
    {
        $query = ArtworkSubmission::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('artist_name', 'like', "%{$search}%")
                  ->orWhere('artwork_title', 'like', "%{$search}%");
            });
        }

        // Başlık sıralaması (yoksa en yeni üstte)
        $this->applySort($query, $request, [
            'created' => 'created_at',
            'name' => 'name',
            'artist' => 'artist_name',
            'price' => 'expected_price',
            'photos' => fn (Builder $q, string $dir) => $q->orderByRaw('COALESCE(JSON_LENGTH(images), 0) ' . $dir),
            'status' => 'status',
        ]) || $query->orderByDesc('created_at');

        $submissions = $query->paginate(20)->withQueryString();

        $stats = [
            'total'      => ArtworkSubmission::count(),
            'new'        => ArtworkSubmission::where('status', 'new')->count(),
            'reviewing'  => ArtworkSubmission::where('status', 'reviewing')->count(),
            'accepted'   => ArtworkSubmission::where('status', 'accepted')->count(),
        ];

        return view('admin.artwork-submissions.index', compact('submissions', 'stats'));
    }

    public function show(ArtworkSubmission $artworkSubmission)
    {
        if ($artworkSubmission->status === 'new') {
            $artworkSubmission->update(['status' => 'reviewing']);
        }

        return view('admin.artwork-submissions.show', ['submission' => $artworkSubmission]);
    }

    public function update(Request $request, ArtworkSubmission $artworkSubmission)
    {
        $validated = $request->validate([
            'status'      => ['required', 'in:new,reviewing,accepted,rejected,closed'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $artworkSubmission->update($validated);

        return back()->with('success', 'Başvuru güncellendi.');
    }

    public function destroy(ArtworkSubmission $artworkSubmission)
    {
        $artworkSubmission->delete();
        return redirect()->route('admin.artwork-submissions.index')
            ->with('success', 'Başvuru silindi.');
    }
}
