<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\NotifyAdminRecipients;
use App\Models\NotificationRecipient;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Admin > Bildirim Alıcıları: yeni sipariş / iletişim / eser başvurusu bildirimlerinin
 * kimlere, hangi kanaldan gideceği.
 */
class NotificationRecipientController extends Controller
{
    public function index()
    {
        $recipients = NotificationRecipient::orderByDesc('is_active')->orderBy('name')->get();
        $events = NotificationRecipient::EVENTS;

        return view('admin.notification-recipients.index', compact('recipients', 'events'));
    }

    public function store(Request $request)
    {
        NotificationRecipient::create($this->validated($request));

        return back()->with('success', 'Alıcı eklendi.');
    }

    public function update(Request $request, NotificationRecipient $notificationRecipient)
    {
        $notificationRecipient->update($this->validated($request));

        return back()->with('success', 'Alıcı güncellendi.');
    }

    public function destroy(NotificationRecipient $notificationRecipient)
    {
        $notificationRecipient->delete();

        return back()->with('success', 'Alıcı silindi.');
    }

    /** Seçili alıcıya, seçtiği kanallardan deneme bildirimi (kuyruktan) */
    public function test(NotificationRecipient $notificationRecipient)
    {
        $html = "<div style='font-family:Arial,sans-serif;font-size:14px;'>Bu bir deneme bildirimidir. BeArtShare yönetici bildirimleri bu adrese ulaşıyor.</div>";
        NotifyAdminRecipients::dispatch(
            'test', 'BeArtShare deneme bildirimi', $html,
            'BeArtShare deneme bildirimi: yönetici SMS bildirimleri bu numaraya ulaşıyor.',
            onlyRecipientId: $notificationRecipient->id,
        );

        return back()->with('success', "{$notificationRecipient->name} için deneme bildirimi kuyruğa alındı. Sonucu Bildirim Log'da görebilirsiniz.");
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:200', 'required_if:via_email,1'],
            'phone' => ['nullable', 'string', 'max:20', 'required_if:via_sms,1'],
            'events' => ['required', 'array', 'min:1'],
            'events.*' => [Rule::in(array_keys(NotificationRecipient::EVENTS))],
        ], [
            'email.required_if' => 'E-posta bildirimi için e-posta adresi gerekli.',
            'phone.required_if' => 'SMS bildirimi için telefon gerekli.',
            'events.required' => 'En az bir bildirim türü seçin.',
        ]);

        // Telefon: 05XX / +90 5XX → 5XXXXXXXXX (SMS servisinin beklediği biçim)
        $phone = preg_replace('/\D/', '', (string) ($data['phone'] ?? ''));
        if (strlen($phone) === 12 && str_starts_with($phone, '90')) $phone = substr($phone, 2);
        if (strlen($phone) === 11 && str_starts_with($phone, '0')) $phone = substr($phone, 1);

        return [
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $phone ?: null,
            'events' => array_values($data['events']),
            'via_email' => $request->boolean('via_email'),
            'via_sms' => $request->boolean('via_sms'),
            'is_active' => $request->boolean('is_active'),
        ];
    }
}
