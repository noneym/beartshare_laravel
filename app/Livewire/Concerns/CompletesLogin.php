<?php

namespace App\Livewire\Concerns;

use App\Models\CartItem;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Girişi tamamlar: oturum açar, misafir sepetini kullanıcıya aktarır, oturum kimliğini yeniler.
 * Şifre adımı (Login) ve iki adımlı doğrulama adımı (TwoFactorChallenge) ortak kullanır.
 */
trait CompletesLogin
{
    protected function completeLogin(User $user, bool $remember, ?string $guestSessionId)
    {
        Auth::login($user, $remember);

        if ($guestSessionId) {
            $this->mergeGuestCartToUser($guestSessionId, $user->id);
        }

        session()->regenerate();

        return redirect()->intended(route('home'));
    }

    /**
     * Misafir sepetindeki ürünleri kullanıcıya aktar
     */
    protected function mergeGuestCartToUser(string $guestSessionId, int $userId): void
    {
        $guestCartItems = CartItem::where('session_id', $guestSessionId)
            ->whereNull('user_id')
            ->get();

        foreach ($guestCartItems as $guestItem) {
            // Kullanıcının sepetinde aynı eser var mı kontrol et
            $existingItem = CartItem::where('user_id', $userId)
                ->where('artwork_id', $guestItem->artwork_id)
                ->first();

            if (!$existingItem) {
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
}
