<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Mesaj icerigindeki degiskenleri kullanici bilgileriyle degistir
     */
    protected function replaceVariables(string $content, User $user): string
    {
        $firstName = explode(' ', trim($user->name))[0];

        return str_replace([
            '{isim}',
            '{ad}',
            '{email}',
            '{telefon}',
            '{artpuan}',
            '{referans_kodu}',
            '{referans_linki}',
            '{id}',
        ], [
            $user->name,
            $firstName,
            $user->email ?? '',
            $user->phone ?? '',
            number_format($user->art_puan, 2, ',', '.'),
            $user->referral_code ?? '',
            $user->referral_link ?? '',
            $user->id,
        ], $content);
    }

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

        $users = User::whereIn('id', $validated['user_ids'])->get();
        $notificationService = new NotificationService();

        $sent = 0;
        $failed = 0;
        $noPhone = 0;

        foreach ($users as $user) {
            if (!$user->phone) {
                $noPhone++;
                continue;
            }

            $personalizedMessage = $this->replaceVariables($validated['message'], $user);

            $result = $notificationService->sendSmsWithLog(
                $user->phone,
                $personalizedMessage,
                'admin_sms',
                null,
                $user->id
            );

            if ($result['success']) {
                $sent++;
            } else {
                $failed++;
            }
        }

        $msg = "{$sent} kullaniciya SMS basariyla gonderildi.";
        if ($failed > 0) $msg .= " {$failed} gonderim basarisiz oldu.";
        if ($noPhone > 0) $msg .= " {$noPhone} kullanicinin telefon numarasi yok.";

        return redirect()->route('admin.messages.sms')
            ->with('success', $msg);
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

        $users = User::whereIn('id', $validated['user_ids'])->get();
        $notificationService = new NotificationService();

        $sent = 0;
        $failed = 0;

        foreach ($users as $user) {
            if (!$user->email) {
                $failed++;
                continue;
            }

            try {
                $personalizedSubject = $this->replaceVariables($validated['subject'], $user);
                $personalizedBody = $this->replaceVariables($validated['body'], $user);

                $notificationService->sendAdminEmail(
                    $user->email,
                    $personalizedSubject,
                    $personalizedBody,
                    $user->id
                );
                $sent++;
            } catch (\Exception $e) {
                $failed++;
            }
        }

        $msg = "{$sent} kullaniciya e-posta basariyla gonderildi.";
        if ($failed > 0) $msg .= " {$failed} gonderim basarisiz oldu.";

        return redirect()->route('admin.messages.email')
            ->with('success', $msg);
    }
}
