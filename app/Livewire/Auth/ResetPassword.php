<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Livewire\Component;

/**
 * E-postadaki bağlantıyla gelinen yeni şifre belirleme sayfası. Başarılı olunca
 * giriş sayfasına yönlendirilir (iki adımlı doğrulama atlanmasın diye otomatik giriş yapılmaz).
 */
class ResetPassword extends Component
{
    public $token = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';

    public function mount(string $token)
    {
        $this->token = $token;
        $this->email = (string) request()->query('email', '');
    }

    public function resetPassword()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.required' => 'E-posta alanı zorunludur.',
            'email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'password.required' => 'Yeni şifre zorunludur.',
            'password.min' => 'Şifre en az 8 karakter olmalıdır.',
            'password.confirmed' => 'Şifreler eşleşmiyor.',
        ]);

        $status = Password::reset(
            [
                'email' => trim($this->email),
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token' => $this->token,
            ],
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'legacy_password' => false,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            // Geçersiz/süresi dolmuş token ve bilinmeyen e-posta aynı mesajı alır (hesap varlığı açığa çıkmasın)
            $this->addError('email', 'Bu şifre sıfırlama bağlantısı geçersiz ya da süresi dolmuş. E-posta adresinizi kontrol edin veya yeni bir bağlantı isteyin.');
            return;
        }

        session()->flash('status', 'Şifreniz güncellendi. Yeni şifrenizle giriş yapabilirsiniz.');

        return redirect()->route('login');
    }

    public function render()
    {
        return view('livewire.auth.reset-password')->layoutData([
            'title' => 'Yeni Şifre Belirle | BeArtShare',
            'metaDescription' => 'BeArtShare hesabınız için yeni şifre belirleyin.',
            'metaRobots' => 'noindex, nofollow',
        ]);
    }
}
