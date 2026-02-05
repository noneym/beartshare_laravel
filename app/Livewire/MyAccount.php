<?php

namespace App\Livewire;

use App\Models\Address;
use App\Models\ArtPuanLog;
use App\Models\CartItem;
use App\Models\Favorite;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class MyAccount extends Component
{
    // Aktif sekme
    public string $activeTab = 'overview';

    // Profil düzenleme
    public string $edit_name = '';
    public string $edit_email = '';
    public string $edit_phone = '';
    public bool $showEditProfile = false;

    // Şifre değiştirme
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';
    public bool $showChangePassword = false;

    // Referans kodu bağlama
    public string $referral_code_input = '';

    public function mount(?string $tab = null)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if ($tab && in_array($tab, ['overview', 'orders', 'favorites', 'artpuan', 'addresses', 'settings'])) {
            $this->activeTab = $tab;
        }
    }

    public function setTab(string $tab)
    {
        $this->activeTab = $tab;
        $this->showEditProfile = false;
        $this->showChangePassword = false;
        $this->resetValidation();
    }

    // ── Profil Düzenleme ──

    public function openEditProfile()
    {
        $user = Auth::user();
        $this->edit_name = $user->name ?? '';
        $this->edit_email = $user->email ?? '';
        $this->edit_phone = $user->phone ?? '';
        $this->showEditProfile = true;
    }

    public function saveProfile()
    {
        $this->validate([
            'edit_name' => 'required|string|min:3|max:255',
            'edit_email' => 'required|email|unique:users,email,' . Auth::id(),
            'edit_phone' => 'nullable|string',
        ], [
            'edit_name.required' => 'Ad soyad zorunludur.',
            'edit_name.min' => 'Ad soyad en az 3 karakter olmalıdır.',
            'edit_email.required' => 'E-posta zorunludur.',
            'edit_email.email' => 'Geçerli bir e-posta giriniz.',
            'edit_email.unique' => 'Bu e-posta zaten kullanılıyor.',
        ]);

        $user = Auth::user();
        $user->update([
            'name' => $this->edit_name,
            'email' => $this->edit_email,
            'phone' => $this->edit_phone,
        ]);

        $this->showEditProfile = false;
        session()->flash('success', 'Profil bilgileriniz güncellendi.');
    }

    // ── Şifre Değiştirme ──

    public function openChangePassword()
    {
        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        $this->showChangePassword = true;
    }

    public function changePassword()
    {
        $this->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'Mevcut şifrenizi giriniz.',
            'new_password.required' => 'Yeni şifre zorunludur.',
            'new_password.min' => 'Yeni şifre en az 8 karakter olmalıdır.',
            'new_password.confirmed' => 'Şifreler eşleşmiyor.',
        ]);

        $user = Auth::user();

        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Mevcut şifreniz yanlış.');
            return;
        }

        $user->update(['password' => Hash::make($this->new_password)]);

        $this->showChangePassword = false;
        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        session()->flash('success', 'Şifreniz başarıyla değiştirildi.');
    }

    // ── Referans Kodu Bağlama ──

    public function bindReferralCode()
    {
        $this->validate([
            'referral_code_input' => 'required|string|min:3|max:20',
        ], [
            'referral_code_input.required' => 'Lütfen bir referans kodu girin.',
            'referral_code_input.min' => 'Referans kodu en az 3 karakter olmalıdır.',
        ]);

        $user = Auth::user();

        // Zaten bir referansı varsa engelle
        if ($user->referred_by) {
            $this->addError('referral_code_input', 'Zaten bir referans koduna bağlısınız.');
            return;
        }

        // Kodu temizle
        $code = strtolower(trim($this->referral_code_input));

        // Kendi kodu mu kontrol et
        if ($code === $user->referral_code) {
            $this->addError('referral_code_input', 'Kendi referans kodunuzu kullanamazsınız.');
            return;
        }

        // Referans sahibini bul
        $referrer = User::where('referral_code', $code)->first();

        if (!$referrer) {
            $this->addError('referral_code_input', 'Bu referans kodu geçersiz. Lütfen kontrol edin.');
            return;
        }

        // Bağla
        $user->update(['referred_by' => $referrer->id]);

        $this->referral_code_input = '';
        $this->resetValidation('referral_code_input');
        session()->flash('success', $referrer->name . ' adlı kullanıcının referans koduna başarıyla bağlandınız! Artık alışverişlerinizde her ikiniz de ArtPuan kazanacaksınız.');
    }

    public function render()
    {
        $user = Auth::user();
        $userId = $user->id;

        // Genel İstatistikler (overview için)
        $stats = [
            'total_orders' => Order::where('user_id', $userId)->count(),
            'completed_orders' => Order::where('user_id', $userId)->where('status', 'delivered')->count(),
            'pending_orders' => Order::where('user_id', $userId)->where('status', 'pending')->count(),
            'total_spent' => Order::where('user_id', $userId)->whereIn('status', ['confirmed', 'shipped', 'delivered'])->sum('total_tl'),
            'total_artpuan' => $user->art_puan ?? 0,
            'favorites_count' => Favorite::where('user_id', $userId)->count(),
            'referrals_count' => User::where('referred_by', $userId)->count(),
            'addresses_count' => Address::where('user_id', $userId)->count(),
        ];

        // Siparişler
        $orders = collect();
        if ($this->activeTab === 'orders' || $this->activeTab === 'overview') {
            $ordersQuery = Order::with('items.artwork')
                ->where('user_id', $userId)
                ->latest();

            $orders = $this->activeTab === 'overview'
                ? $ordersQuery->take(3)->get()
                : $ordersQuery->get();
        }

        // Favoriler
        $favorites = collect();
        if ($this->activeTab === 'favorites' || $this->activeTab === 'overview') {
            $favQuery = Favorite::with('artwork.artist')
                ->where('user_id', $userId)
                ->latest();

            $favorites = $this->activeTab === 'overview'
                ? $favQuery->take(4)->get()
                : $favQuery->get();
        }

        // ArtPuan logları
        $artPuanLogs = collect();
        if ($this->activeTab === 'artpuan' || $this->activeTab === 'overview') {
            $logQuery = ArtPuanLog::with(['order', 'sourceUser'])
                ->where('user_id', $userId)
                ->latest();

            $artPuanLogs = $this->activeTab === 'overview'
                ? $logQuery->take(5)->get()
                : $logQuery->get();
        }

        // Adresler
        $shippingAddresses = collect();
        $billingAddresses = collect();
        if ($this->activeTab === 'addresses') {
            $shippingAddresses = Address::where('user_id', $userId)->where('type', 'shipping')->orderByDesc('is_default')->get();
            $billingAddresses = Address::where('user_id', $userId)->where('type', 'billing')->orderByDesc('is_default')->get();
        }

        return view('livewire.my-account', [
            'user' => $user,
            'stats' => $stats,
            'orders' => $orders,
            'favorites' => $favorites,
            'artPuanLogs' => $artPuanLogs,
            'shippingAddresses' => $shippingAddresses,
            'billingAddresses' => $billingAddresses,
        ])->layoutData([
            'title' => 'Hesabım | BeArtShare',
        ]);
    }
}
