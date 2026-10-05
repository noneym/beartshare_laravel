<?php

namespace App\Livewire\Auth;

use App\Livewire\Concerns\CompletesLogin;
use App\Models\User;
use App\Services\TwoFactorService;
use Livewire\Component;

/**
 * Girişte iki adımlı doğrulama: şifresi doğrulanan üye SMS / Authenticator kodu ya da
 * kurtarma kodu girer. Bekleyen giriş session('two_factor_login') içinde, 10 dk geçerli.
 */
class TwoFactorChallenge extends Component
{
    use CompletesLogin;

    public string $code = '';
    public bool $useRecovery = false;
    public string $method = 'sms';
    public ?string $maskedPhone = null;

    public function mount()
    {
        $user = $this->pendingUser();
        if (!$user) {
            return redirect()->route('login');
        }

        $this->method = $user->two_factor_method;
        $this->maskedPhone = $user->phone ? substr($user->phone, 0, 3) . ' *** ** ' . substr($user->phone, -2) : null;
    }

    public function verify(TwoFactorService $twoFactor)
    {
        $this->validate(['code' => 'required|string|max:20'], ['code.required' => 'Kodu girin.']);

        $pending = session('two_factor_login');
        $user = $this->pendingUser();
        if (!$user) {
            session()->flash('error', 'Oturum süresi doldu, lütfen tekrar giriş yapın.');
            return redirect()->route('login');
        }

        if (!$twoFactor->verifyLogin($user, $this->code)) {
            $pending['attempts']++;
            if ($pending['attempts'] >= TwoFactorService::MAX_ATTEMPTS) {
                session()->forget('two_factor_login');
                session()->flash('error', 'Çok fazla hatalı deneme. Lütfen tekrar giriş yapın.');
                return redirect()->route('login');
            }
            session()->put('two_factor_login', $pending);
            $this->addError('code', 'Kod hatalı ya da süresi dolmuş.');
            return;
        }

        session()->forget('two_factor_login');

        return $this->completeLogin($user, (bool) $pending['remember'], $pending['guest_session'] ?? null);
    }

    public function resend(TwoFactorService $twoFactor)
    {
        $user = $this->pendingUser();
        if (!$user || $user->two_factor_method !== 'sms') {
            return;
        }

        $sent = $twoFactor->sendSmsCode($user, 'login');
        $sent === true
            ? session()->flash('two_factor_notice', 'Yeni kod gönderildi.')
            : $this->addError('code', $sent);
    }

    public function toggleRecovery(): void
    {
        $this->useRecovery = !$this->useRecovery;
        $this->code = '';
        $this->resetErrorBag();
    }

    protected function pendingUser(): ?User
    {
        $pending = session('two_factor_login');
        if (!$pending || ($pending['expires_at'] ?? 0) < now()->timestamp) {
            session()->forget('two_factor_login');
            return null;
        }

        $user = User::find($pending['user_id']);

        return $user && $user->hasTwoFactor() ? $user : null;
    }

    public function render()
    {
        return view('livewire.auth.two-factor-challenge')->layoutData([
            'title' => 'Doğrulama Kodu | BeArtShare',
            'metaRobots' => 'noindex, nofollow',
        ]);
    }
}
