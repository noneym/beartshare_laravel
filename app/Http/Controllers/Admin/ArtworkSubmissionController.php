<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArtworkSubmission;
use Illuminate\Http\Request;

class ArtworkSubmissionController extends Controller
{
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

        $submissions = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

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
