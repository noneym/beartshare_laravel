<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationTemplate;
use App\Support\NotificationTemplates;
use Illuminate\Http\Request;

/**
 * Admin > Sistem > Bildirim Şablonları: müşteriye giden SMS / e-posta metinleri.
 */
class NotificationTemplateController extends Controller
{
    public function index()
    {
        $templates = collect(array_keys(NotificationTemplates::DEFINITIONS))->map(fn ($key) => NotificationTemplates::get($key));

        return view('admin.notification-templates.index', compact('templates'));
    }

    public function edit(string $key)
    {
        abort_unless(isset(NotificationTemplates::DEFINITIONS[$key]), 404);
        $template = NotificationTemplates::get($key);
        $sample = NotificationTemplates::SAMPLE;

        return view('admin.notification-templates.edit', compact('template', 'sample'));
    }

    public function update(Request $request, string $key)
    {
        abort_unless(isset(NotificationTemplates::DEFINITIONS[$key]), 404);
        $t = NotificationTemplates::get($key);

        $data = $request->validate([
            'sms_body' => $t['has_sms'] ? 'required|string|max:1000' : 'nullable',
            'email_subject' => $t['has_email'] ? 'required|string|max:255' : 'nullable',
            'email_body' => $t['has_email'] ? 'required|string|max:50000' : 'nullable',
        ], [
            'sms_body.required' => 'SMS metni boş olamaz.',
            'email_subject.required' => 'E-posta konusu boş olamaz.',
            'email_body.required' => 'E-posta metni boş olamaz.',
        ]);

        NotificationTemplate::updateOrCreate(['key' => $key], [
            'sms_body' => $t['has_sms'] ? $data['sms_body'] : null,
            'email_subject' => $t['has_email'] ? $data['email_subject'] : null,
            'email_body' => $t['has_email'] ? $data['email_body'] : null,
            'sms_enabled' => $t['has_sms'] ? $request->boolean('sms_enabled') : true,
            // Şifre sıfırlama kapatılamaz
            'email_enabled' => ($t['has_email'] && $key !== 'password_reset') ? $request->boolean('email_enabled') : true,
        ]);

        return redirect()->route('admin.notification-templates.edit', $key)->with('success', 'Şablon kaydedildi.');
    }

    /** Admin değişikliğini siler; koddaki varsayılan metin geçerli olur */
    public function reset(string $key)
    {
        abort_unless(isset(NotificationTemplates::DEFINITIONS[$key]), 404);
        NotificationTemplate::where('key', $key)->delete();

        return redirect()->route('admin.notification-templates.edit', $key)->with('success', 'Varsayılan metne dönüldü.');
    }

    /** Düzenleme ekranındaki önizleme (örnek değerlerle) */
    public function preview(Request $request)
    {
        $html = $request->boolean('html');
        $text = (string) $request->input('text', '');
        $filled = NotificationTemplates::fill($text, NotificationTemplates::SAMPLE, $html);

        return response()->json([
            'result' => $html ? NotificationTemplates::layout($filled) : $filled,
            'length' => mb_strlen($filled),
        ]);
    }
}
