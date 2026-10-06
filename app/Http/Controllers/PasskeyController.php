<?php

namespace App\Http\Controllers;

use App\Livewire\Concerns\CompletesLogin;
use App\Models\Passkey;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use lbuchs\WebAuthn\Binary\ByteBuffer;
use lbuchs\WebAuthn\WebAuthn;
use lbuchs\WebAuthn\WebAuthnException;

/**
 * Passkey (WebAuthn): Hesabım'dan passkey ekleme ve şifresiz giriş.
 * Tarayıcı tarafı: resources/views/partials/passkey-script.blade.php
 * Passkey ile giriş zaten "cihaz + biyometri/PIN" olduğundan ayrıca 2FA kodu sorulmaz.
 */
class PasskeyController extends Controller
{
    use CompletesLogin;

    protected const REGISTER_KEY = 'passkey_register_challenge';
    protected const LOGIN_KEY = 'passkey_login_challenge';

    public function __construct()
    {
        // Özellik kapalıyken uç noktalar yok sayılır (PASSKEYS_ENABLED)
        $this->middleware(function ($request, $next) {
            abort_unless(config('passkeys.enabled'), 404);
            return $next($request);
        });
    }

    // ── Kayıt (giriş yapmış üye) ──

    public function registerOptions(): JsonResponse
    {
        $user = Auth::user();
        if ($user->passkeys()->count() >= config('passkeys.max_per_user')) {
            return response()->json(['message' => 'En fazla ' . config('passkeys.max_per_user') . ' passkey ekleyebilirsiniz.'], 422);
        }

        $webAuthn = $this->webAuthn();
        $exclude = $user->passkeys()->pluck('credential_id')
            ->map(fn ($id) => self::b64uDecode($id))->all();

        $args = $webAuthn->getCreateArgs(
            (string) $user->id,
            $user->email,
            $user->name,
            config('passkeys.timeout'),
            'required',   // keşfedilebilir anahtar: e-posta yazmadan giriş için
            'required',   // biyometri / PIN zorunlu
            null,
            $exclude
        );
        session()->put(self::REGISTER_KEY, $webAuthn->getChallenge()->getBinaryString());

        return response()->json($args);
    }

    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'clientDataJSON' => 'required|string',
            'attestationObject' => 'required|string',
            'name' => 'nullable|string|max:100',
        ]);
        $challenge = session()->pull(self::REGISTER_KEY);
        if (!$challenge) {
            return response()->json(['message' => 'Oturum süresi doldu, tekrar deneyin.'], 422);
        }

        try {
            $result = $this->webAuthn()->processCreate(
                self::b64uDecode($data['clientDataJSON']),
                self::b64uDecode($data['attestationObject']),
                $challenge,
                true,   // kullanıcı doğrulaması (biyometri / PIN)
                true,   // kullanıcı varlığı
                false,  // üretici kök sertifikası zorunlu değil (çoğu passkey 'none' kanıt formatında)
                false
            );
        } catch (WebAuthnException $e) {
            Log::info('Passkey kaydi reddedildi: ' . $e->getMessage(), ['user_id' => Auth::id()]);
            return response()->json(['message' => 'Passkey doğrulanamadı: ' . $e->getMessage()], 422);
        }

        $passkey = Auth::user()->passkeys()->create([
            'name' => trim((string) ($data['name'] ?? '')) ?: $this->deviceName($request),
            'credential_id' => self::b64uEncode($result->credentialId),
            'public_key' => $result->credentialPublicKey,
            'sign_count' => (int) $result->signatureCounter,
            'rp_id' => $this->rpId(),
            'aaguid' => $result->AAGUID ? bin2hex($result->AAGUID instanceof ByteBuffer ? $result->AAGUID->getBinaryString() : $result->AAGUID) : null,
            'backed_up' => (bool) ($result->isBackedUp ?? false),
        ]);

        return response()->json(['ok' => true, 'name' => $passkey->name]);
    }

    public function destroy(Passkey $passkey): JsonResponse
    {
        abort_unless($passkey->user_id === Auth::id(), 404);
        $passkey->delete();

        return response()->json(['ok' => true]);
    }

    // ── Şifresiz giriş (misafir) ──

    public function loginOptions(): JsonResponse
    {
        $webAuthn = $this->webAuthn();
        // Boş izin listesi: tarayıcı bu siteye kayıtlı passkey'leri kendisi önerir
        $args = $webAuthn->getGetArgs([], config('passkeys.timeout'), true, true, true, true, true, 'required');
        session()->put(self::LOGIN_KEY, $webAuthn->getChallenge()->getBinaryString());

        return response()->json($args);
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id' => 'required|string|max:1024',
            'clientDataJSON' => 'required|string',
            'authenticatorData' => 'required|string',
            'signature' => 'required|string',
            'userHandle' => 'nullable|string',
        ]);
        $challenge = session()->pull(self::LOGIN_KEY);
        if (!$challenge) {
            return response()->json(['message' => 'Oturum süresi doldu, tekrar deneyin.'], 422);
        }

        $passkey = Passkey::with('user')
            ->where('credential_id', $data['id'])
            ->where('rp_id', $this->rpId())
            ->first();
        if (!$passkey || !$passkey->user) {
            return response()->json(['message' => 'Bu passkey hesabımızda kayıtlı değil. Şifrenizle giriş yapıp Hesabım’dan passkey ekleyebilirsiniz.'], 422);
        }

        $webAuthn = $this->webAuthn();
        try {
            $webAuthn->processGet(
                self::b64uDecode($data['clientDataJSON']),
                self::b64uDecode($data['authenticatorData']),
                self::b64uDecode($data['signature']),
                $passkey->public_key,
                $challenge,
                $passkey->sign_count ?: null,
                true,
                true
            );
        } catch (WebAuthnException $e) {
            Log::info('Passkey girisi reddedildi: ' . $e->getMessage(), ['passkey_id' => $passkey->id]);
            return response()->json(['message' => 'Passkey doğrulanamadı.'], 422);
        }

        // Cihazın bildirdiği kullanıcı kimliği passkey'in sahibiyle aynı olmalı
        if (!empty($data['userHandle']) && self::b64uDecode($data['userHandle']) !== (string) $passkey->user_id) {
            return response()->json(['message' => 'Passkey doğrulanamadı.'], 422);
        }

        $passkey->forceFill([
            'sign_count' => (int) ($webAuthn->getSignatureCounter() ?? $passkey->sign_count),
            'last_used_at' => now(),
        ])->save();

        $guestSessionId = session()->getId();
        Auth::login($passkey->user, true);
        $this->mergeGuestCartToUser($guestSessionId, $passkey->user_id);
        session()->regenerate();

        return response()->json(['ok' => true, 'redirect' => redirect()->intended(route('home'))->getTargetUrl()]);
    }

    // ── Yardımcılar ──

    protected function webAuthn(): WebAuthn
    {
        // Yalnızca 'none' ve platform/şifre yöneticisi formatları; base64url JSON
        return new WebAuthn(config('passkeys.rp_name'), $this->rpId(), null, true);
    }

    protected function rpId(): string
    {
        return config('passkeys.rp_id') ?: request()->getHost();
    }

    protected function deviceName(Request $request): string
    {
        $ua = (string) $request->userAgent();
        return match (true) {
            str_contains($ua, 'iPhone') => 'iPhone',
            str_contains($ua, 'iPad') => 'iPad',
            str_contains($ua, 'Android') => 'Android',
            str_contains($ua, 'Macintosh') => 'Mac',
            str_contains($ua, 'Windows') => 'Windows',
            default => 'Passkey',
        };
    }

    public static function b64uEncode(string $binary): string
    {
        return rtrim(strtr(base64_encode($binary), '+/', '-_'), '=');
    }

    public static function b64uDecode(string $value): string
    {
        return (string) base64_decode(strtr($value, '-_', '+/') . str_repeat('=', (4 - strlen($value) % 4) % 4), true);
    }
}
