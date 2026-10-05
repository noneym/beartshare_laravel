<?php

namespace App\Livewire\Auth;

use App\Livewire\Concerns\CompletesLogin;
use App\Models\User;
use App\Services\TwoFactorService;
use Livewire\Component;

class Login extends Component
{
    use CompletesLogin;

    public $email = '';
    public $password = '';
    public $remember = false;

    protected function rules()
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string',
        ];
    }

    protected function messages()
    {
        return [
            'email.required' => 'E-posta alanı zorunludur.',
            'email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'password.required' => 'Şifre alanı zorunludur.',
        ];
    }

    public function login()
    {
        $this->validate();

        // Login öncesi session ID'yi kaydet (misafir sepeti için)
        $guestSessionId = session()->getId();

        // Eski sistemden gelen hesaplar (SHA1) da checkPassword ile doğrulanıp yükseltilir
        $user = User::where('email', trim($this->email))->first();
        if (!$user || !$user->checkPassword($this->password)) {
            $this->addError('email', 'E-posta veya şifre hatalı.');
            return;
        }

        // İki adımlı doğrulama açıksa oturum henüz açılmaz; kod ekranına geçilir
        if ($user->hasTwoFactor()) {
            session()->put('two_factor_login', [
                'user_id' => $user->id,
                'remember' => (bool) $this->remember,
                'guest_session' => $guestSessionId,
                'expires_at' => now()->addMinutes(10)->timestamp,
                'attempts' => 0,
            ]);

            if ($user->two_factor_method === 'sms') {
                $sent = app(TwoFactorService::class)->sendSmsCode($user, 'login');
                if ($sent !== true) {
                    session()->flash('two_factor_notice', $sent);
                }
            }

            return redirect()->route('two-factor.challenge');
        }

        return $this->completeLogin($user, (bool) $this->remember, $guestSessionId);
    }

    public function render()
    {
        return view('livewire.auth.login')->layoutData([
            'title' => 'Giriş Yap | BeArtShare',
            'metaDescription' => 'BeArtShare hesabınıza giriş yapın.',
            'metaRobots' => 'noindex, nofollow',
        ]);
    }
}
