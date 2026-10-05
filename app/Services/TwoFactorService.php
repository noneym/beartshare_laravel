<?php

namespace App\Services;

use App\Models\User;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Cache;
use PragmaRX\Google2FA\Google2FA;

/**
 * Üye iki adımlı doğrulama: Authenticator (TOTP) ve SMS kodu, kurtarma kodları.
 */
class TwoFactorService
{
    public const SMS_TTL = 300;          // SMS kodu geçerlilik (sn)
    public const SMS_RESEND_AFTER = 60;  // yeniden gönderim bekleme (sn)
    public const MAX_ATTEMPTS = 5;       // kod başına hatalı deneme

    protected Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    // ── Authenticator (TOTP) ──

    public function generateSecret(): string
    {
        return $this->google2fa->generateSecretKey(32);
    }

    public function qrSvg(User $user, string $secret): string
    {
        $url = $this->google2fa->getQRCodeUrl('BeArtShare', $user->email, $secret);
        $writer = new Writer(new ImageRenderer(new RendererStyle(200, 1), new SvgImageBackEnd()));

        return $writer->writeString($url);
    }

    public function verifyTotp(string $secret, string $code): bool
    {
        // ±1 zaman dilimi (30 sn) saat kaymasına tolerans
        return (bool) $this->google2fa->verifyKey($secret, $code, 1);
    }

    // ── SMS kodu ──

    /**
     * Telefona 6 haneli kod gönderir. $purpose: 'login' | 'enable'
     * @return true|string true ya da hata mesajı
     */
    public function sendSmsCode(User $user, string $purpose): bool|string
    {
        if (!$user->phone) {
            return 'Hesabınızda kayıtlı telefon numarası yok.';
        }

        $key = $this->smsKey($user, $purpose);
        $existing = Cache::get($key);
        if ($existing && now()->timestamp - $existing['sent_at'] < self::SMS_RESEND_AFTER) {
            $wait = self::SMS_RESEND_AFTER - (now()->timestamp - $existing['sent_at']);
            return "Yeni kod için {$wait} saniye bekleyin.";
        }

        $code = (string) random_int(100000, 999999);
        $result = (new NotificationService())->sendSmsWithLog(
            $user->phone,
            "BeArtShare doğrulama kodunuz: {$code}. Kod 5 dakika geçerlidir. Bu kodu kimseyle paylaşmayın.",
            'two_factor_' . $purpose,
            null,
            $user->id
        );

        if (!($result['success'] ?? false)) {
            return 'SMS gönderilemedi, lütfen biraz sonra tekrar deneyin.';
        }

        Cache::put($key, ['hash' => hash('sha256', $code), 'sent_at' => now()->timestamp, 'attempts' => 0], self::SMS_TTL);

        return true;
    }

    public function verifySmsCode(User $user, string $purpose, string $code): bool
    {
        $key = $this->smsKey($user, $purpose);
        $data = Cache::get($key);
        if (!$data) {
            return false;
        }

        if (hash_equals($data['hash'], hash('sha256', $code))) {
            Cache::forget($key);
            return true;
        }

        $data['attempts']++;
        $data['attempts'] >= self::MAX_ATTEMPTS
            ? Cache::forget($key) // çok fazla hatalı deneme: kod geçersiz, yenisi istenmeli
            : Cache::put($key, $data, self::SMS_TTL);

        return false;
    }

    public function smsCodeSentAt(User $user, string $purpose): ?int
    {
        return Cache::get($this->smsKey($user, $purpose))['sent_at'] ?? null;
    }

    protected function smsKey(User $user, string $purpose): string
    {
        return "two_factor:sms:{$purpose}:{$user->id}";
    }

    // ── Giriş doğrulaması ──

    /** Üyenin seçtiği yönteme göre kodu doğrular (kurtarma kodu da kabul edilir) */
    public function verifyLogin(User $user, string $code): bool
    {
        $code = trim($code);
        $digits = preg_replace('/\s+/', '', $code);

        if (preg_match('/^\d{6}$/', $digits)) {
            return $user->two_factor_method === 'totp'
                ? $this->verifyTotp((string) $user->two_factor_secret, $digits)
                : $this->verifySmsCode($user, 'login', $digits);
        }

        return $this->useRecoveryCode($user, $code);
    }

    // ── Kurtarma kodları ──

    /** @return string[] düz kodlar (yalnızca bir kez gösterilir); kullanıcıda hash'leri saklanır */
    public function generateRecoveryCodes(User $user): array
    {
        // Karışabilen karakterler (0/O, 1/I/L) yok: kâğıda yazılıp elle girilecek
        $alphabet = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';
        $part = fn () => implode('', array_map(fn () => $alphabet[random_int(0, strlen($alphabet) - 1)], range(1, 4)));
        $codes = collect(range(1, 8))
            ->map(fn () => $part() . '-' . $part())
            ->all();

        $user->forceFill([
            'two_factor_recovery_codes' => array_map(fn ($c) => hash('sha256', $c), $codes),
        ])->save();

        return $codes;
    }

    public function useRecoveryCode(User $user, string $code): bool
    {
        $hash = hash('sha256', strtoupper(trim($code)));
        $codes = $user->two_factor_recovery_codes ?? [];
        $index = array_search($hash, $codes, true);
        if ($index === false) {
            return false;
        }

        unset($codes[$index]);
        $user->forceFill(['two_factor_recovery_codes' => array_values($codes)])->save();

        return true;
    }

    // ── Aç / kapat ──

    /** @return string[] kurtarma kodları */
    public function enable(User $user, string $method, ?string $secret = null): array
    {
        $user->forceFill([
            'two_factor_method' => $method,
            'two_factor_secret' => $method === 'totp' ? $secret : null,
            'two_factor_confirmed_at' => now(),
        ])->save();

        return $this->generateRecoveryCodes($user);
    }

    public function disable(User $user): void
    {
        $user->forceFill([
            'two_factor_method' => null,
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();
    }
}
