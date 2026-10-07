<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

/**
 * Şifre sıfırlama bağlantısı ister. Hesabın var olup olmadığı açığa çıkmasın diye
 * sonuç ne olursa olsun aynı mesaj gösterilir.
 */
class ForgotPassword extends Component
{
    public $email = '';
    public $sent = false;

    public function sendLink()
    {
        $this->validate(
            ['email' => 'required|email'],
            [
                'email.required' => 'E-posta alanı zorunludur.',
                'email.email' => 'Geçerli bir e-posta adresi giriniz.',
            ]
        );

        $key = 'forgot-password:' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('email', 'Çok fazla deneme yaptınız. Lütfen ' . ceil(RateLimiter::availableIn($key) / 60) . ' dakika sonra tekrar deneyin.');
            return;
        }
        RateLimiter::hit($key, 15 * 60);

        // Kullanıcı yoksa ya da yakın zamanda istek yapıldıysa (broker throttle) sessizce geçilir
        Password::sendResetLink(['email' => trim($this->email)]);

        $this->sent = true;
    }

    public function render()
    {
        return view('livewire.auth.forgot-password')->layoutData([
            'title' => 'Şifremi Unuttum | BeArtShare',
            'metaDescription' => 'BeArtShare hesabınızın şifresini sıfırlayın.',
            'metaRobots' => 'noindex, nofollow',
        ]);
    }
}
