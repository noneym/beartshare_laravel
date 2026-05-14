<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function index(Request $request)
    {
        $query = ContactMessage::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $messages = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        $stats = [
            'total'    => ContactMessage::count(),
            'new'      => ContactMessage::where('status', 'new')->count(),
            'replied'  => ContactMessage::where('status', 'replied')->count(),
        ];

        return view('admin.contact-messages.index', compact('messages', 'stats'));
    }

    public function show(ContactMessage $contactMessage)
    {
        if ($contactMessage->status === 'new') {
            $contactMessage->update(['status' => 'read']);
        }

        return view('admin.contact-messages.show', ['message' => $contactMessage]);
    }

    public function update(Request $request, ContactMessage $contactMessage)
    {
        $validated = $request->validate([
            'status'      => ['required', 'in:new,read,replied,closed'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $data = ['status' => $validated['status']];
        if (array_key_exists('admin_notes', $validated)) {
            $data['admin_notes'] = $validated['admin_notes'];
        }
        if ($validated['status'] === 'replied' && !$contactMessage->replied_at) {
            $data['replied_at'] = now();
        }

        $contactMessage->update($data);

        return back()->with('success', 'Mesaj güncellendi.');
    }

    public function destroy(ContactMessage $contactMessage)
    {
        $contactMessage->delete();
        return redirect()->route('admin.contact-messages.index')
            ->with('success', 'Mesaj silindi.');
    }
}
