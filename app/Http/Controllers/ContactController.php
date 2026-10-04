<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\NotificationService;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:150'],
            'email'   => ['required', 'email', 'max:200'],
            'phone'   => ['nullable', 'string', 'max:30'],
            'subject' => ['required', 'string', 'max:50'],
            'message' => ['required', 'string', 'max:5000'],
            'kvkk'    => ['accepted'],
        ], [
            'name.required'    => 'Lütfen adınızı ve soyadınızı yazın.',
            'email.required'   => 'Lütfen e-posta adresinizi yazın.',
            'email.email'      => 'Geçerli bir e-posta adresi yazın.',
            'subject.required' => 'Lütfen bir konu seçin.',
            'message.required' => 'Lütfen mesajınızı yazın.',
            'kvkk.accepted'    => 'KVKK aydınlatma metnini kabul etmelisiniz.',
        ]);

        $contactMessage = ContactMessage::create([
            'name'          => $validated['name'],
            'email'         => $validated['email'],
            'phone'         => $validated['phone'] ?? null,
            'subject'       => $validated['subject'],
            'message'       => $validated['message'],
            'kvkk_accepted' => true,
            'status'        => 'new',
            'ip_address'    => $request->ip(),
        ]);

        try {
            $this->notifyAdmin($contactMessage);
        } catch (\Exception $e) {
            Log::error('Contact message admin notification failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Mesajınız iletildi. En kısa sürede size dönüş yapacağız.');
    }

    protected function notifyAdmin(ContactMessage $message): void
    {
        $subject = '[BeArtShare İletişim] ' . $message->subject_label . ' - ' . $message->name;

        $body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: #14171c; padding: 24px;'>
                <h1 style='color: #fff; font-size: 18px; margin: 0;'>BeArtShare - Yeni İletişim Mesajı</h1>
            </div>
            <div style='padding: 24px; background: #fff; border: 1px solid #eee;'>
                <table style='width: 100%; font-size: 14px; color: #333;'>
                    <tr><td style='padding: 6px 0; width: 130px; color: #888;'>Gönderen:</td><td>" . e($message->name) . "</td></tr>
                    <tr><td style='padding: 6px 0; color: #888;'>E-posta:</td><td><a href='mailto:" . e($message->email) . "'>" . e($message->email) . "</a></td></tr>
                    <tr><td style='padding: 6px 0; color: #888;'>Telefon:</td><td>" . e($message->phone ?: '-') . "</td></tr>
                    <tr><td style='padding: 6px 0; color: #888;'>Konu:</td><td>" . e($message->subject_label) . "</td></tr>
                    <tr><td style='padding: 6px 0; color: #888;'>Tarih:</td><td>" . $message->created_at->format('d.m.Y H:i') . "</td></tr>
                </table>
                <hr style='margin: 16px 0; border: 0; border-top: 1px solid #eee;'>
                <p style='color: #888; font-size: 12px; margin: 0 0 8px;'>Mesaj:</p>
                <div style='background: #f8f8f8; padding: 16px; border-left: 3px solid #D4A017; white-space: pre-wrap;'>" . e($message->message) . "</div>
            </div>
        </div>";

        (new NotificationService())->sendAdminNotification($subject, $body, 'contact_message', $message->email, $message->name);
    }
}
