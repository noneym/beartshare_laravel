<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ArtworkSubmissionController extends Controller
{
    /**
     * Eser kabul basvuru formunu isle
     */
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'artist_name' => 'required|string|max:255',
            'artwork_title' => 'required|string|max:255',
            'technique' => 'nullable|string|max:100',
            'dimensions' => 'nullable|string|max:100',
            'year' => 'nullable|string|max:10',
            'expected_price' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:2000',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'name.required' => 'Ad soyad zorunludur.',
            'phone.required' => 'Telefon numarasi zorunludur.',
            'email.required' => 'E-posta zorunludur.',
            'email.email' => 'Gecerli bir e-posta adresi giriniz.',
            'artist_name.required' => 'Sanatci adi zorunludur.',
            'artwork_title.required' => 'Eser adi zorunludur.',
            'images.max' => 'En fazla 5 fotograf yukleyebilirsiniz.',
            'images.*.image' => 'Yuklenen dosya bir gorsel olmalidir.',
            'images.*.mimes' => 'Sadece JPG, PNG veya WEBP formatlarinda yukleyebilirsiniz.',
            'images.*.max' => 'Her gorsel en fazla 5MB olabilir.',
        ]);

        // Fotograflari kaydet
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('artwork-submissions', 'public');
            }
        }

        // E-posta gonder (admin'e)
        try {
            $data = $validated;
            $data['images'] = $imagePaths;

            Mail::html($this->buildSubmissionEmail($data), function ($message) use ($data) {
                $message->to('info@beartshare.com')
                    ->subject('Yeni Eser Basvurusu - ' . $data['artwork_title'])
                    ->from(config('mail.from.address', 'info@beartshare.com'), 'BeArtShare')
                    ->replyTo($data['email'], $data['name']);
            });
        } catch (\Exception $e) {
            Log::error('Eser kabul email hatasi: ' . $e->getMessage());
        }

        Log::info('Yeni eser basvurusu', [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'artist' => $validated['artist_name'],
            'artwork' => $validated['artwork_title'],
            'images' => count($imagePaths),
        ]);

        return redirect()->route('eser-kabulu')
            ->with('success', 'Basvurunuz basariyla alindi. Ekibimiz en kisa surede sizinle iletisime gececektir.');
    }

    /**
     * Basvuru e-posta sablonu
     */
    protected function buildSubmissionEmail(array $data): string
    {
        $technique = $data['technique'] ?? '-';
        $dimensions = $data['dimensions'] ?? '-';
        $year = $data['year'] ?? '-';
        $price = $data['expected_price'] ?? '-';
        $notes = $data['notes'] ?? '-';
        $imageCount = count($data['images'] ?? []);

        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: #5C4290; padding: 24px; text-align: center;'>
                <h1 style='color: #fff; font-size: 20px; margin: 0;'>Yeni Eser Basvurusu</h1>
            </div>
            <div style='padding: 32px 24px; background: #fff;'>
                <h2 style='color: #333; font-size: 16px; margin: 0 0 16px; border-bottom: 1px solid #eee; padding-bottom: 8px;'>Kisisel Bilgiler</h2>
                <table style='width: 100%; font-size: 14px; color: #555;'>
                    <tr><td style='padding: 4px 0; font-weight: bold; width: 140px;'>Ad Soyad:</td><td>{$data['name']}</td></tr>
                    <tr><td style='padding: 4px 0; font-weight: bold;'>Telefon:</td><td>{$data['phone']}</td></tr>
                    <tr><td style='padding: 4px 0; font-weight: bold;'>E-posta:</td><td><a href='mailto:{$data['email']}'>{$data['email']}</a></td></tr>
                </table>

                <h2 style='color: #333; font-size: 16px; margin: 24px 0 16px; border-bottom: 1px solid #eee; padding-bottom: 8px;'>Eser Bilgileri</h2>
                <table style='width: 100%; font-size: 14px; color: #555;'>
                    <tr><td style='padding: 4px 0; font-weight: bold; width: 140px;'>Sanatci:</td><td>{$data['artist_name']}</td></tr>
                    <tr><td style='padding: 4px 0; font-weight: bold;'>Eser Adi:</td><td>{$data['artwork_title']}</td></tr>
                    <tr><td style='padding: 4px 0; font-weight: bold;'>Teknik:</td><td>{$technique}</td></tr>
                    <tr><td style='padding: 4px 0; font-weight: bold;'>Boyutlar:</td><td>{$dimensions}</td></tr>
                    <tr><td style='padding: 4px 0; font-weight: bold;'>Yapim Yili:</td><td>{$year}</td></tr>
                    <tr><td style='padding: 4px 0; font-weight: bold;'>Beklenen Fiyat:</td><td>{$price}</td></tr>
                    <tr><td style='padding: 4px 0; font-weight: bold;'>Fotograf Sayisi:</td><td>{$imageCount} adet</td></tr>
                </table>

                <h2 style='color: #333; font-size: 16px; margin: 24px 0 16px; border-bottom: 1px solid #eee; padding-bottom: 8px;'>Notlar</h2>
                <p style='color: #555; font-size: 14px; line-height: 1.6;'>{$notes}</p>
            </div>
            <div style='padding: 16px 24px; background: #f8f8f8; text-align: center;'>
                <p style='color: #999; font-size: 11px; margin: 0;'>BeArtShare Eser Kabul Sistemi &copy; " . date('Y') . "</p>
            </div>
        </div>";
    }
}
