<?php

namespace App\Livewire;

use App\Services\TwoFactorService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * Hesabım > Hesap Ayarları > İki Adımlı Doğrulama: SMS ya da Authenticator ile aç, kapat,
 * kurtarma kodlarını yenile. Authenticator kurulumundaki gizli anahtar istemciye (Livewire
 * durumuna) konmaz, session'da tutulur.
 */
class TwoFactorSettings extends Component
{
    /** null | sms | totp | disable | regenerate */
    public ?string $step = null;
    public string $code = '';
    public string $password = '';
    /** Yalnızca açılış / yenileme sonrası bir kez gösterilir */
    public array $recoveryCodes = [];

    protected const SETUP_KEY = 'two_factor_setup_secret';

    public function startSms(TwoFactorService $twoFactor): void
    {
        $this->resetForm();
        $sent = $twoFactor->sendSmsCode(Auth::user(), 'enable');
        if ($sent !== true) {
            $this->addError('code', $sent);
            return;
        }
        $this->step = 'sms';
    }

    public function resendSms(TwoFactorService $twoFactor): void
    {
        $sent = $twoFactor->sendSmsCode(Auth::user(), 'enable');
        $sent === true ? session()->flash('two_factor_status', 'Yeni kod gönderildi.') : $this->addError('code', $sent);
    }

    public function startTotp(TwoFactorService $twoFactor): void
    {
        $this->resetForm();
        session()->put(self::SETUP_KEY, $twoFactor->generateSecret());
        $this->step = 'totp';
    }

    public function confirm(TwoFactorService $twoFactor): void
    {
        $this->validate(['code' => 'required|digits:6'], [
            'code.required' => 'Kodu girin.',
            'code.digits' => 'Kod 6 haneli olmalı.',
        ]);
        $user = Auth::user();

        if ($this->step === 'sms') {
            if (!$twoFactor->verifySmsCode($user, 'enable', $this->code)) {
                $this->addError('code', 'Kod hatalı ya da süresi dolmuş.');
                return;
            }
            $codes = $twoFactor->enable($user, 'sms');
        } elseif ($this->step === 'totp') {
            $secret = session(self::SETUP_KEY);
            if (!$secret || !$twoFactor->verifyTotp($secret, $this->code)) {
                $this->addError('code', 'Kod hatalı. Uygulamadaki güncel kodu girin.');
                return;
            }
            $codes = $twoFactor->enable($user, 'totp', $secret);
            session()->forget(self::SETUP_KEY);
        } else {
            return;
        }

        $this->resetForm();
        $this->recoveryCodes = $codes;
        session()->flash('two_factor_status', 'İki adımlı doğrulama açıldı.');
    }

    public function askPassword(string $for): void
    {
        $this->resetForm();
        $this->step = $for === 'regenerate' ? 'regenerate' : 'disable';
    }

    public function disable(TwoFactorService $twoFactor): void
    {
        if (!$this->checkPassword()) return;
        $twoFactor->disable(Auth::user());
        $this->resetForm();
        session()->flash('two_factor_status', 'İki adımlı doğrulama kapatıldı.');
    }

    public function regenerate(TwoFactorService $twoFactor): void
    {
        if (!$this->checkPassword()) return;
        $codes = $twoFactor->generateRecoveryCodes(Auth::user());
        $this->resetForm();
        $this->recoveryCodes = $codes;
        session()->flash('two_factor_status', 'Yeni kurtarma kodları oluşturuldu; eskiler artık geçersiz.');
    }

    public function cancel(): void
    {
        session()->forget(self::SETUP_KEY);
        $this->resetForm();
    }

    public function hideCodes(): void
    {
        $this->recoveryCodes = [];
    }

    protected function checkPassword(): bool
    {
        $this->validate(['password' => 'required'], ['password.required' => 'Şifrenizi girin.']);
        if (!Auth::user()->checkPassword($this->password)) {
            $this->addError('password', 'Şifre hatalı.');
            return false;
        }
        return true;
    }

    protected function resetForm(): void
    {
        $this->step = null;
        $this->code = '';
        $this->password = '';
        $this->resetErrorBag();
    }

    public function render(TwoFactorService $twoFactor)
    {
        $user = Auth::user();
        $secret = $this->step === 'totp' ? session(self::SETUP_KEY) : null;

        return view('livewire.two-factor-settings', [
            'user' => $user,
            'qrSvg' => $secret ? $twoFactor->qrSvg($user, $secret) : null,
            'secret' => $secret ? trim(chunk_split($secret, 4, ' ')) : null,
            'remainingCodes' => count($user->two_factor_recovery_codes ?? []),
        ]);
    }
}
