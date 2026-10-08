<?php

namespace App\Http\Controllers;

use App\Models\ArtworkSubmission;
use App\Models\User;
use App\Services\NotificationService;
use App\Support\ImageUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

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
            // Üye olmayan başvurana hesap açıldığı için sözleşme onayı zorunlu
            'terms' => Auth::check() ? 'nullable' : 'accepted',
        ], [
            'terms.accepted' => 'Başvuru için kullanım koşullarını ve KVKK sözleşmesini kabul etmelisiniz.',
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
                $imagePaths[] = $image->store('artwork-submissions', config('filesystems.uploads'));
            }
        }

        // Başvuran üye değilse otomatik hesap aç / mevcut hesaba bağla
        [$user, $newAccount] = $this->resolveUser($validated);

        // Veritabanina kaydet
        $submission = ArtworkSubmission::create([
            'user_id' => $user?->id,
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'artist_name' => $validated['artist_name'],
            'artwork_title' => $validated['artwork_title'],
            'technique' => $validated['technique'] ?? null,
            'dimensions' => $validated['dimensions'] ?? null,
            'year' => $validated['year'] ?? null,
            'expected_price' => $validated['expected_price'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'images' => $imagePaths,
            'status' => 'new',
            'ip_address' => $request->ip(),
        ]);

        // E-posta gonder (admin'e)
        try {
            $data = $validated;
            $data['images'] = $imagePaths;
            $data['id'] = $submission->id;

            (new NotificationService())->sendAdminNotification(
                'Yeni Eser Başvurusu - ' . $data['artwork_title'],
                $this->buildSubmissionEmail($data),
                'artwork_submission',
                $data['email'],
                $data['name'],
                $user?->id
            );
        } catch (\Exception $e) {
            Log::error('Eser kabul email hatasi: ' . $e->getMessage());
        }

        Log::info('Yeni eser basvurusu', [
            'id' => $submission->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'artist' => $validated['artist_name'],
            'artwork' => $validated['artwork_title'],
            'images' => count($imagePaths),
        ]);

        $message = 'Başvurunuz başarıyla alındı. Ekibimiz en kısa sürede sizinle iletişime geçecektir.';
        if ($newAccount) {
            $message .= ' Sizin için bir BeArtShare hesabı oluşturuldu; giriş bilgileriniz e-posta adresinize gönderildi.';
        }

        return redirect()->route('eser-kabulu')->with('success', $message);
    }

    /**
     * Giriş yapmışsa o kullanıcı; değilse e-posta/telefonla eşleşen hesap; yoksa yeni hesap açar.
     *
     * @return array{0: ?User, 1: bool} [kullanıcı, yeni hesap açıldı mı]
     */
    protected function resolveUser(array $data): array
    {
        if (Auth::check()) {
            return [Auth::user(), false];
        }

        $phone = preg_replace('/[^0-9]/', '', $data['phone']);
        if (str_starts_with($phone, '90') && strlen($phone) === 12) {
            $phone = substr($phone, 2);
        }
        if (str_starts_with($phone, '0')) {
            $phone = substr($phone, 1);
        }

        $existing = User::where('email', $data['email'])->first()
            ?? ($phone ? User::where('phone', $phone)->first() : null);
        if ($existing) {
            // Mevcut hesaba bağla ama başkası adına oturum açma
            return [$existing, false];
        }

        try {
            $password = Str::password(10, symbols: false);
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $phone ?: null,
                'password' => Hash::make($password),
            ]);
            Auth::login($user);
            $this->sendWelcomeEmail($user);

            return [$user, true];
        } catch (\Throwable $e) {
            Log::error('Eser kabul otomatik üyelik hatası: ' . $e->getMessage());
            return [null, false];
        }
    }

    /**
     * Hoş geldin e-postası: şifre e-postayla gönderilmez, "şifreni belirle" bağlantısı (şifre sıfırlama token'ı) gider.
     */
    protected function sendWelcomeEmail(User $user): void
    {
        $setUrl = route('password.reset', ['token' => Password::broker()->createToken($user), 'email' => $user->email]);
        $forgotUrl = route('password.request');
        $name = e($user->name);
        $email = e($user->email);

        $html = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: #14171c; padding: 24px; text-align: center;'>
                <h1 style='color: #fff; font-size: 20px; margin: 0;'>BeArtShare</h1>
            </div>
            <div style='padding: 32px 24px; background: #fff; color: #333; font-size: 14px; line-height: 1.6;'>
                <p>Merhaba {$name},</p>
                <p>Eser başvurunuz alındı. Başvurunuzu takip edebilmeniz için sizin adınıza bir BeArtShare hesabı oluşturduk.</p>
                <div style='background: #f8f8f8; border-left: 3px solid #D4A017; padding: 16px; margin: 20px 0;'>
                    <p style='margin: 0;'><strong>E-posta:</strong> {$email}</p>
                </div>
                <p>Hesabınıza giriş yapabilmek için aşağıdaki bağlantıdan şifrenizi belirleyin.</p>
                <p style='text-align: center; margin: 28px 0;'>
                    <a href='{$setUrl}' style='background: #14171c; color: #fff; padding: 12px 32px; text-decoration: none;'>Şifremi Belirle</a>
                </p>
                <p style='font-size: 12px; color: #888;'>Bağlantının süresi dolduysa <a href='{$forgotUrl}' style='color: #888;'>şifremi unuttum</a> sayfasından yeni bir bağlantı isteyebilirsiniz.</p>
            </div>
        </div>";

        try {
            Mail::html($html, function ($m) use ($user) {
                $m->to($user->email, $user->name)
                    ->subject('BeArtShare hesabınız oluşturuldu')
                    ->from(config('mail.from.address', 'info@beartshare.com'), 'BeArtShare');
            });
        } catch (\Throwable $e) {
            Log::error('Hoş geldin e-postası gönderilemedi: ' . $e->getMessage());
        }
    }

    /**
     * Basvuru e-posta sablonu
     */
    protected function buildSubmissionEmail(array $data): string
    {
        // Form girdileri HTML'e kaçışlanarak basılır
        $v = fn ($key) => e(trim((string) ($data[$key] ?? '')) ?: '-');
        $images = $data['images'] ?? [];
        $adminUrl = isset($data['id']) ? route('admin.artwork-submissions.show', $data['id']) : null;

        $thumbs = '';
        foreach ($images as $path) {
            $thumbs .= "<a href='" . e(ImageUrl::make($path, 'detail')) . "' style='display:inline-block;margin:0 8px 8px 0;'>"
                . "<img src='" . e(ImageUrl::make($path, 'thumb')) . "' alt='' width='160' style='width:160px;height:auto;border:1px solid #eee;'></a>";
        }

        $row = fn ($label, $value) => "<tr><td style='padding: 4px 0; font-weight: bold; width: 140px; vertical-align: top;'>{$label}:</td><td>{$value}</td></tr>";

        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: #14171c; padding: 24px; text-align: center;'>
                <h1 style='color: #fff; font-size: 20px; margin: 0;'>Yeni Eser Başvurusu</h1>
            </div>
            <div style='padding: 32px 24px; background: #fff;'>
                <h2 style='color: #333; font-size: 16px; margin: 0 0 16px; border-bottom: 1px solid #eee; padding-bottom: 8px;'>Kişisel Bilgiler</h2>
                <table style='width: 100%; font-size: 14px; color: #555;'>
                    " . $row('Ad Soyad', $v('name')) . "
                    " . $row('Telefon', $v('phone')) . "
                    " . $row('E-posta', "<a href='mailto:" . $v('email') . "'>" . $v('email') . "</a>") . "
                </table>

                <h2 style='color: #333; font-size: 16px; margin: 24px 0 16px; border-bottom: 1px solid #eee; padding-bottom: 8px;'>Eser Bilgileri</h2>
                <table style='width: 100%; font-size: 14px; color: #555;'>
                    " . $row('Sanatçı', $v('artist_name')) . "
                    " . $row('Eser Adı', $v('artwork_title')) . "
                    " . $row('Teknik', $v('technique')) . "
                    " . $row('Boyutlar', $v('dimensions')) . "
                    " . $row('Yapım Yılı', $v('year')) . "
                    " . $row('Beklenen Fiyat', $v('expected_price')) . "
                </table>

                <h2 style='color: #333; font-size: 16px; margin: 24px 0 16px; border-bottom: 1px solid #eee; padding-bottom: 8px;'>Notlar</h2>
                <p style='color: #555; font-size: 14px; line-height: 1.6; white-space: pre-wrap;'>" . $v('notes') . "</p>
                " . ($thumbs ? "<h2 style='color: #333; font-size: 16px; margin: 24px 0 16px; border-bottom: 1px solid #eee; padding-bottom: 8px;'>Fotoğraflar (" . count($images) . ")</h2><div>{$thumbs}</div>" : '') . "
                " . ($adminUrl ? "<p style='margin: 24px 0 0;'><a href='" . e($adminUrl) . "' style='display:inline-block;background:#D4A017;color:#fff;padding:10px 18px;text-decoration:none;'>Admin panelde görüntüle</a></p>" : '') . "
            </div>
            <div style='padding: 16px 24px; background: #f8f8f8; text-align: center;'>
                <p style='color: #999; font-size: 11px; margin: 0;'>BeArtShare Eser Kabul Sistemi &copy; " . date('Y') . "</p>
            </div>
        </div>";
    }
}
