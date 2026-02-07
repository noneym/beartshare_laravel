<?php

namespace App\Livewire\Auth;

use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
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

        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            $this->addError('email', 'E-posta veya şifre hatalı.');
            return;
        }

        // Misafir sepetindeki ürünleri kullanıcıya aktar
        $this->mergeGuestCartToUser($guestSessionId, Auth::id());

        session()->regenerate();

        return redirect()->intended(route('home'));
    }

    /**
     * Misafir sepetindeki ürünleri kullanıcıya aktar
     */
    protected function mergeGuestCartToUser($guestSessionId, $userId)
    {
        // Misafir sepetindeki ürünleri bul
        $guestCartItems = CartItem::where('session_id', $guestSessionId)
            ->whereNull('user_id')
            ->get();

        foreach ($guestCartItems as $guestItem) {
            // Kullanıcının sepetinde aynı eser var mı kontrol et
            $existingItem = CartItem::where('user_id', $userId)
                ->where('artwork_id', $guestItem->artwork_id)
                ->first();

            if (!$existingItem) {
                // Yoksa misafir ürününü kullanıcıya aktar
                $guestItem->update([
                    'user_id' => $userId,
                    'session_id' => null,
                ]);
            } else {
                // Zaten varsa misafir ürününü sil (duplicate olmasın)
                $guestItem->delete();
            }
        }
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
