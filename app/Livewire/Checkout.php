<?php

namespace App\Livewire;

use App\Models\Address;
use App\Models\Artwork;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Checkout extends Component
{
    // Adres seçimleri
    public ?int $selectedShippingAddressId = null;
    public ?int $selectedBillingAddressId = null;
    public bool $sameBillingAddress = true;

    // Müşteri bilgileri
    public string $customer_name = '';
    public string $customer_email = '';
    public string $customer_phone = '';
    public string $notes = '';

    // Ödeme
    public string $payment_method = 'havale';

    // ArtPuan
    public bool $useArtPuan = false;

    // Sözleşmeler
    public bool $mesafeli_satis = false;
    public bool $on_bilgilendirme = false;

    // UI
    public bool $orderCompleted = false;
    public ?Order $completedOrder = null;

    // Adres Ekleme Modal
    public bool $showAddressModal = false;
    public string $addr_type = 'shipping';
    public string $addr_title = '';
    public string $addr_full_name = '';
    public string $addr_phone = '';
    public string $addr_city = '';
    public string $addr_district = '';
    public string $addr_address_line = '';
    public string $addr_invoice_type = 'individual';
    public string $addr_tc_no = '';
    public string $addr_company_name = '';
    public string $addr_tax_office = '';
    public string $addr_tax_number = '';
    public bool $addr_is_default = false;

    public function mount()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        $cartCount = CartItem::where('user_id', $user->id)->count();
        if ($cartCount === 0) {
            return redirect()->route('cart');
        }

        // Kullanıcı bilgilerini doldur
        $this->customer_name = $user->name ?? '';
        $this->customer_email = $user->email ?? '';
        $this->customer_phone = $user->phone ?? '';

        // Varsayılan adresleri seç
        $defaultShipping = Address::where('user_id', $user->id)->where('type', 'shipping')->where('is_default', true)->first();
        if ($defaultShipping) {
            $this->selectedShippingAddressId = $defaultShipping->id;
        }

        $defaultBilling = Address::where('user_id', $user->id)->where('type', 'billing')->where('is_default', true)->first();
        if ($defaultBilling) {
            $this->selectedBillingAddressId = $defaultBilling->id;
            $this->sameBillingAddress = false;
        }
    }

    protected function rules()
    {
        $rules = [
            'customer_name' => 'required|string|min:3|max:255',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string',
            'selectedShippingAddressId' => 'required|exists:addresses,id',
            'payment_method' => 'required|in:havale,kredi_karti',
            'mesafeli_satis' => 'accepted',
            'on_bilgilendirme' => 'accepted',
        ];

        if (!$this->sameBillingAddress) {
            $rules['selectedBillingAddressId'] = 'required|exists:addresses,id';
        }

        return $rules;
    }

    protected function messages()
    {
        return [
            'customer_name.required' => 'Ad soyad zorunludur.',
            'customer_email.required' => 'E-posta zorunludur.',
            'customer_email.email' => 'Geçerli bir e-posta giriniz.',
            'customer_phone.required' => 'Telefon numarası zorunludur.',
            'selectedShippingAddressId.required' => 'Lütfen bir teslimat adresi seçiniz.',
            'selectedShippingAddressId.exists' => 'Geçersiz teslimat adresi.',
            'selectedBillingAddressId.required' => 'Lütfen bir fatura adresi seçiniz.',
            'selectedBillingAddressId.exists' => 'Geçersiz fatura adresi.',
            'mesafeli_satis.accepted' => 'Mesafeli satış sözleşmesini kabul etmelisiniz.',
            'on_bilgilendirme.accepted' => 'Ön bilgilendirme formunu kabul etmelisiniz.',
        ];
    }

    public function openAddressModal(string $type = 'shipping')
    {
        $this->resetAddressForm();
        $this->addr_type = $type;
        $this->addr_full_name = Auth::user()->name ?? '';
        $this->addr_phone = Auth::user()->phone ?? '';
        if ($type === 'billing') {
            $this->addr_tc_no = Auth::user()->tc_no ?? '';
        }
        $this->showAddressModal = true;
    }

    public function closeAddressModal()
    {
        $this->showAddressModal = false;
        $this->resetAddressForm();
    }

    public function saveNewAddress()
    {
        $rules = [
            'addr_type' => 'required|in:shipping,billing',
            'addr_title' => 'required|string|max:100',
            'addr_full_name' => 'required|string|min:3|max:255',
            'addr_phone' => 'nullable|string',
            'addr_city' => 'required|string|min:2',
            'addr_district' => 'required|string|min:2',
            'addr_address_line' => 'required|string|min:10',
        ];

        if ($this->addr_type === 'billing') {
            $rules['addr_invoice_type'] = 'required|in:individual,corporate';
            if ($this->addr_invoice_type === 'individual') {
                $rules['addr_tc_no'] = 'required|string|size:11|regex:/^[0-9]+$/';
            } else {
                $rules['addr_company_name'] = 'required|string|min:2';
                $rules['addr_tax_office'] = 'required|string|min:2';
                $rules['addr_tax_number'] = 'required|string|min:10|max:11';
            }
        }

        $messages = [
            'addr_title.required' => 'Adres başlığı zorunludur.',
            'addr_full_name.required' => 'Ad soyad zorunludur.',
            'addr_full_name.min' => 'Ad soyad en az 3 karakter olmalıdır.',
            'addr_city.required' => 'Şehir zorunludur.',
            'addr_district.required' => 'İlçe zorunludur.',
            'addr_address_line.required' => 'Adres satırı zorunludur.',
            'addr_address_line.min' => 'Adres en az 10 karakter olmalıdır.',
            'addr_tc_no.required' => 'TC Kimlik No zorunludur.',
            'addr_tc_no.size' => 'TC Kimlik No 11 haneli olmalıdır.',
            'addr_tc_no.regex' => 'TC Kimlik No sadece rakamlardan oluşmalıdır.',
            'addr_company_name.required' => 'Şirket unvanı zorunludur.',
            'addr_tax_office.required' => 'Vergi dairesi zorunludur.',
            'addr_tax_number.required' => 'Vergi numarası zorunludur.',
            'addr_tax_number.min' => 'Vergi numarası en az 10 haneli olmalıdır.',
        ];

        $this->validate($rules, $messages);

        $userId = Auth::id();

        // Varsayılan yapılacaksa, diğerlerini kaldır
        if ($this->addr_is_default) {
            Address::where('user_id', $userId)
                ->where('type', $this->addr_type)
                ->update(['is_default' => false]);
        }

        // Bu tipteki ilk adres ise otomatik varsayılan yap
        $existingCount = Address::where('user_id', $userId)->where('type', $this->addr_type)->count();
        $isDefault = $this->addr_is_default || $existingCount === 0;

        $address = Address::create([
            'user_id' => $userId,
            'type' => $this->addr_type,
            'title' => $this->addr_title,
            'full_name' => $this->addr_full_name,
            'phone' => $this->addr_phone,
            'city' => $this->addr_city,
            'district' => $this->addr_district,
            'address_line' => $this->addr_address_line,
            'invoice_type' => $this->addr_type === 'billing' ? $this->addr_invoice_type : 'individual',
            'tc_no' => $this->addr_type === 'billing' && $this->addr_invoice_type === 'individual' ? $this->addr_tc_no : null,
            'company_name' => $this->addr_type === 'billing' && $this->addr_invoice_type === 'corporate' ? $this->addr_company_name : null,
            'tax_office' => $this->addr_type === 'billing' && $this->addr_invoice_type === 'corporate' ? $this->addr_tax_office : null,
            'tax_number' => $this->addr_type === 'billing' && $this->addr_invoice_type === 'corporate' ? $this->addr_tax_number : null,
            'is_default' => $isDefault,
        ]);

        // Yeni adresi otomatik seç
        if ($this->addr_type === 'shipping') {
            $this->selectedShippingAddressId = $address->id;
        } else {
            $this->selectedBillingAddressId = $address->id;
            $this->sameBillingAddress = false;
        }

        $this->showAddressModal = false;
        $this->resetAddressForm();
    }

    protected function resetAddressForm()
    {
        $this->reset([
            'addr_type', 'addr_title', 'addr_full_name', 'addr_phone',
            'addr_city', 'addr_district', 'addr_address_line',
            'addr_invoice_type', 'addr_tc_no', 'addr_company_name',
            'addr_tax_office', 'addr_tax_number', 'addr_is_default',
        ]);
        $this->resetValidation();
    }

    public function placeOrder()
    {
        $this->validate();

        $user = Auth::user();

        // Adresleri al
        $shippingAddress = Address::where('user_id', $user->id)->findOrFail($this->selectedShippingAddressId);

        $billingAddress = $this->sameBillingAddress
            ? $shippingAddress
            : Address::where('user_id', $user->id)->findOrFail($this->selectedBillingAddressId);

        // Sepet öğelerini al
        $cartItems = CartItem::with('artwork.artist')
            ->where('user_id', $user->id)
            ->get();

        if ($cartItems->isEmpty()) {
            session()->flash('error', 'Sepetiniz boş.');
            return redirect()->route('cart');
        }

        // Eserlerin hala satın alınabilir olduğunu kontrol et
        foreach ($cartItems as $item) {
            if (!$item->artwork || $item->artwork->is_sold || $item->artwork->is_reserved) {
                session()->flash('error', "'{$item->artwork->title}' eseri artık satın alınamaz.");
                $item->delete();
                return redirect()->route('cart');
            }
        }

        $totalTl = $cartItems->sum(fn($item) => $item->artwork->price_tl);
        $totalUsd = $cartItems->sum(fn($item) => $item->artwork->price_usd);
        $paymentCode = Order::generatePaymentCode();

        // ArtPuan hesaplama
        $artpuanUsed = 0;
        $discountTl = 0;
        if ($this->useArtPuan) {
            $user->refresh(); // Güncel bakiyeyi al
            $availableArtPuan = (float) $user->art_puan;
            if ($availableArtPuan > 0) {
                // ArtPuan direkt TL karşılığı olarak düşülür (1 AP = 1 TL)
                $artpuanUsed = min($availableArtPuan, $totalTl);
                $discountTl = $artpuanUsed;
            }
        }
        $finalTotalTl = $totalTl - $discountTl;

        // Fatura bilgileri
        $billingInfo = $billingAddress->address_line . ', ' . $billingAddress->district . '/' . $billingAddress->city;
        if ($billingAddress->type === 'billing' && $billingAddress->invoice_type === 'corporate') {
            $billingInfo = $billingAddress->company_name . ' | ' . $billingAddress->tax_office . ' V.D. ' . $billingAddress->tax_number . ' | ' . $billingInfo;
        }

        DB::beginTransaction();

        try {
            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'pending',
                'payment_method' => $this->payment_method,
                'payment_code' => $paymentCode,
                'total_tl' => $finalTotalTl,
                'total_usd' => $totalUsd,
                'artpuan_used' => $artpuanUsed,
                'discount_tl' => $discountTl,
                'customer_name' => $this->customer_name,
                'customer_email' => $this->customer_email,
                'customer_phone' => $this->customer_phone,
                'tc_no' => $billingAddress->tc_no ?? $user->tc_no,
                'shipping_address' => $shippingAddress->address_line,
                'billing_address' => $billingInfo,
                'city' => $shippingAddress->city,
                'district' => $shippingAddress->district,
                'notes' => $this->notes,
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'artwork_id' => $item->artwork_id,
                    'artwork_title' => $item->artwork->title,
                    'artist_name' => $item->artwork->artist->name ?? 'Bilinmeyen',
                    'quantity' => 1,
                    'price_tl' => $item->artwork->price_tl,
                    'price_usd' => $item->artwork->price_usd,
                ]);

                $item->artwork->update(['is_reserved' => true]);

                try {
                    $notificationService = new NotificationService();
                    $notificationService->notifyFavoriteWatchers($item->artwork, $user->id);
                } catch (\Exception $e) {
                    Log::error('Favori bildirim hatasi: ' . $e->getMessage());
                }
            }

            // ArtPuan harca ve log yaz
            if ($artpuanUsed > 0) {
                $user->spendArtPuan($artpuanUsed, [
                    'order_id' => $order->id,
                    'description' => "#{$order->order_number} nolu sipariş - " . number_format($artpuanUsed, 2, ',', '.') . " AP kullanıldı",
                ]);
            }

            // Kredi kartı ödemesinde sepeti henüz silme, ödeme sonucu gelince sileceğiz
            if ($this->payment_method !== 'kredi_karti') {
                CartItem::where('user_id', $user->id)->delete();
            }

            DB::commit();

            // Kredi kartı seçilmişse ödeme sayfasına yönlendir
            if ($this->payment_method === 'kredi_karti') {
                // Session'a order_id'yi kaydet
                session(['pending_payment_order_id' => $order->id]);
                return redirect()->route('payment.initiate', ['order_id' => $order->id]);
            }

            try {
                $notificationService = new NotificationService();
                $notificationService->notifyOrderCreated($order);
            } catch (\Exception $e) {
                Log::error('Siparis bildirim hatasi: ' . $e->getMessage());
            }

            $this->orderCompleted = true;
            $this->completedOrder = $order;
            $this->dispatch('cart-updated');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Siparis olusturma hatasi: ' . $e->getMessage());
            session()->flash('error', 'Sipariş oluşturulurken bir hata oluştu. Lütfen tekrar deneyiniz.');
        }
    }

    public function render()
    {
        $cartItems = collect();
        $totalTl = 0;
        $totalUsd = 0;
        $shippingAddresses = collect();
        $billingAddresses = collect();
        $userArtPuan = 0;
        $artpuanDiscount = 0;
        $finalTotal = 0;

        if (Auth::check() && !$this->orderCompleted) {
            $userId = Auth::id();
            $user = Auth::user();

            $cartItems = CartItem::with('artwork.artist')
                ->where('user_id', $userId)
                ->get();

            $totalTl = $cartItems->sum(fn($item) => $item->artwork->price_tl);
            $totalUsd = $cartItems->sum(fn($item) => $item->artwork->price_usd);

            // ArtPuan hesaplama
            $userArtPuan = (float) $user->art_puan;
            if ($this->useArtPuan && $userArtPuan > 0) {
                $artpuanDiscount = min($userArtPuan, $totalTl);
            }
            $finalTotal = $totalTl - $artpuanDiscount;

            $shippingAddresses = Address::where('user_id', $userId)
                ->where('type', 'shipping')
                ->orderByDesc('is_default')
                ->orderByDesc('created_at')
                ->get();

            $billingAddresses = Address::where('user_id', $userId)
                ->where('type', 'billing')
                ->orderByDesc('is_default')
                ->orderByDesc('created_at')
                ->get();
        }

        return view('livewire.checkout', [
            'cartItems' => $cartItems,
            'totalTl' => $totalTl,
            'totalUsd' => $totalUsd,
            'shippingAddresses' => $shippingAddresses,
            'billingAddresses' => $billingAddresses,
            'userArtPuan' => $userArtPuan,
            'artpuanDiscount' => $artpuanDiscount,
            'finalTotal' => $finalTotal,
        ])->layoutData([
            'title' => 'Ödeme | BeArtShare',
            'metaRobots' => 'noindex, nofollow',
        ]);
    }
}
