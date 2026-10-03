<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendBulkEmail;
use App\Jobs\SendBulkSms;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Str;

/**
 * Toplu SMS / e-posta. Gönderimler kuyruğa (alıcı başına bir iş, tek batch) atılır;
 * ilerleme Admin > Kuyruk sayfasında izlenir. Değişkenler: App\Support\MessageTemplate
 */
class MessageController extends Controller
{
    // ── SMS ──

    public function smsForm(Request $request)
    {
        $users = User::orderBy('name')->get();
        $preselected = $request->input('users', []);

        return view('admin.messages.sms', compact('users', 'preselected'));
    }

    public function sendSms(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'message' => 'required|string|max:480',
        ], [
            'user_ids.required' => 'En az bir kullanici secmelisiniz.',
            'message.required' => 'Mesaj alani zorunludur.',
            'message.max' => 'SMS mesaji en fazla 480 karakter olabilir.',
        ]);

        $users = User::whereIn('id', $validated['user_ids'])->get(['id', 'phone']);
        $withPhone = $users->filter(fn ($u) => $u->phone);
        $noPhone = $users->count() - $withPhone->count();

        if ($withPhone->isEmpty()) {
            return back()->withInput()->with('error', 'Seçilen kullanıcıların telefon numarası yok.');
        }

        $batch = Bus::batch($withPhone->map(fn ($u) => new SendBulkSms($u->id, $validated['message']))->values()->all())
            ->name('SMS: ' . Str::limit($validated['message'], 60))
            ->allowFailures()
            ->dispatch();

        $msg = "{$withPhone->count()} SMS gönderim kuyruğuna alındı.";
        if ($noPhone > 0) $msg .= " {$noPhone} kullanıcının telefonu yok, atlandı.";

        return redirect()->route('admin.queue.index', ['batch' => $batch->id])->with('success', $msg);
    }

    // ── Email ──

    public function emailForm(Request $request)
    {
        $users = User::orderBy('name')->get();
        $preselected = $request->input('users', []);

        return view('admin.messages.email', compact('users', 'preselected'));
    }

    public function sendEmail(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ], [
            'user_ids.required' => 'En az bir kullanici secmelisiniz.',
            'subject.required' => 'Konu alani zorunludur.',
            'body.required' => 'E-posta icerigi zorunludur.',
        ]);

        $users = User::whereIn('id', $validated['user_ids'])->get(['id', 'email']);
        $withEmail = $users->filter(fn ($u) => $u->email);

        $batch = Bus::batch($withEmail->map(fn ($u) => new SendBulkEmail($u->id, $validated['subject'], $validated['body']))->values()->all())
            ->name('E-posta: ' . Str::limit($validated['subject'], 60))
            ->allowFailures()
            ->dispatch();

        return redirect()->route('admin.queue.index', ['batch' => $batch->id])
            ->with('success', "{$withEmail->count()} e-posta gönderim kuyruğuna alındı.");
    }
}
