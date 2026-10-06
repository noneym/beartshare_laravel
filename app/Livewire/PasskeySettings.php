<?php

namespace App\Livewire;

use App\Models\Passkey;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * Hesabım > Hesap Ayarları > Passkey'ler: listele, sil. Ekleme tarayıcıda yapılır
 * (window.Passkey.register), sonra liste yenilenir.
 */
class PasskeySettings extends Component
{
    public ?int $confirmDelete = null;

    public function delete(int $id): void
    {
        Passkey::where('user_id', Auth::id())->whereKey($id)->delete();
        $this->confirmDelete = null;
        session()->flash('passkey_status', 'Passkey silindi.');
    }

    public function added(string $name): void
    {
        session()->flash('passkey_status', "“{$name}” passkey'i eklendi. Artık bu cihazla şifresiz giriş yapabilirsiniz.");
    }

    public function render()
    {
        return view('livewire.passkey-settings', [
            'passkeys' => Auth::user()->passkeys()->latest()->get(),
            'max' => config('passkeys.max_per_user'),
        ]);
    }
}
