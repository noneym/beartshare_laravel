<?php

namespace App\Livewire\Auth;

use App\Models\CartItem;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\SmsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Register extends Component
{
    // Step 1: Registration form
    public $name = '';
    public $tc_no = '';
    public $phone = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $terms = false;
    public $referral_code = '';
    public $referrer_name = '';

    // Step 2: SMS verification
    public $verification_code = '';
    public $step = 1; // 1 = form, 2 = sms verification
    public $resend_cooldown = 0;
    public $sms_error = '';

    public function mount()
    {
        // URL'den ?ref= veya cookie'den referans kodunu al
        $ref = request()->query('ref') ?: request()->cookie('referral_code');

        if ($ref) {
            $this->referral_code = $ref;
            $this->lookupReferrer();
        }
    }

    /**
     * Referans kodunu kontrol et ve referans eden kişiyi bul
     */
    public function updatedReferralCode()
    {
        $this->lookupReferrer();
    }

    private function lookupReferrer()
    {
        $this->referrer_name = '';

        if (empty($this->referral_code)) {
            return;
        }

        $referrer = User::where('referral_code', strtolower(trim($this->referral_code)))->first();

        if ($referrer) {
            // İsmi maskele: "Mehmet Y."
            $parts = explode(' ', $referrer->name);
            $firstName = $parts[0];
            $lastName = isset($parts[1]) ? mb_substr($parts[1], 0, 1) . '.' : '';
            $this->referrer_name = trim($firstName . ' ' . $lastName);
        }
    }

    protected function rules()
    {
        if ($this->step === 1) {
            return [
                'name' => 'required|string|min:3|max:255',
                'tc_no' => 'required|string|size:11|regex:/^[0-9]+$/|unique:users,tc_no',
                'phone' => ['required', 'string', 'regex:/^0?5[0-9]{9}$/', function ($attribute, $value, $fail) {
                    $normalized = $this->normalizePhone($value);
                    if (User::where('phone', $normalized)->exists()) {
                        $fail('Bu telefon numarası zaten kayıtlı.');
                    }
                }],
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6|confirmed',
                'terms' => 'accepted',
            ];
        }

        return [
            'verification_code' => 'required|string|size:6',
        ];
    }

    protected function messages()
    {
        return [
            'name.required' => 'Ad soyad zorunludur.',
            'name.min' => 'Ad soyad en az 3 karakter olmalıdır.',
            'tc_no.required' => 'TC Kimlik No zorunludur.',
            'tc_no.size' => 'TC Kimlik No 11 haneli olmalıdır.',
            'tc_no.regex' => 'TC Kimlik No sadece rakamlardan oluşmalıdır.',
            'tc_no.unique' => 'Bu TC Kimlik No ile zaten bir hesap mevcut.',
            'phone.required' => 'Cep telefonu numarası zorunludur.',
            'phone.regex' => 'Geçerli bir cep telefonu numarası giriniz. (5XX XXX XX XX)',
            'phone.unique' => 'Bu telefon numarası zaten kayıtlı.',
            'email.required' => 'E-posta zorunludur.',
            'email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'email.unique' => 'Bu e-posta adresi zaten kayıtlı.',
            'password.required' => 'Şifre zorunludur.',
            'password.min' => 'Şifre en az 6 karakter olmalıdır.',
            'password.confirmed' => 'Şifreler eşleşmiyor.',
            'terms.accepted' => 'Kullanım şartlarını kabul etmelisiniz.',
            'verification_code.required' => 'Doğrulama kodu zorunludur.',
            'verification_code.size' => 'Doğrulama kodu 6 haneli olmalıdır.',
        ];
    }

    /**
     * Step 1: Validate form and send SMS
     */
    public function sendVerification()
    {
        $this->step = 1;
        $this->validate();

        // TC Kimlik No basit doğrulama (ilk hane 0 olamaz)
        if (str_starts_with($this->tc_no, '0')) {
            $this->addError('tc_no', 'Geçerli bir TC Kimlik No giriniz.');
            return;
        }

        // Telefon numarasını normalize et
        $phone = $this->normalizePhone($this->phone);

        // Doğrulama kodu oluştur ve session'a kaydet
        $smsService = new SmsService();
        $code = $smsService->generateVerificationCode();

        session([
            'sms_verification_code' => $code,
            'sms_verification_phone' => $phone,
            'sms_verification_expires' => now()->addMinutes(5),
            'sms_verification_attempts' => 0,
        ]);

        // SMS gönder ve logla
        $notificationService = new NotificationService();
        $message = "BeArtShare dogrulama kodunuz: {$code}. Bu kodu kimseyle paylasmayin.";
        $result = $notificationService->sendSmsWithLog($phone, $message, 'sms_verification');
        $sent = $result['success'];

        if (!$sent) {
            $this->sms_error = 'SMS gönderilemedi. Lütfen daha sonra tekrar deneyiniz.';
            // Geliştirme ortamında yine de devam ettir
            if (config('app.debug')) {
                $this->sms_error = '';
                $this->step = 2;
                $this->dispatch('start-countdown');
            }
            return;
        }

        $this->sms_error = '';
        $this->step = 2;
        $this->resend_cooldown = 120;
        $this->dispatch('start-countdown');
    }

    /**
     * Step 2: Verify SMS code and create user
     */
    public function verifyAndRegister()
    {
        $this->step = 2;
        $this->validate();

        $sessionCode = session('sms_verification_code');
        $sessionPhone = session('sms_verification_phone');
        $expiresAt = session('sms_verification_expires');
        $attempts = session('sms_verification_attempts', 0);

        // Deneme sayısını kontrol et
        if ($attempts >= 5) {
            $this->addError('verification_code', 'Çok fazla yanlış deneme. Lütfen yeni kod isteyiniz.');
            return;
        }

        // Süre kontrolü
        if (!$expiresAt || now()->isAfter($expiresAt)) {
            $this->addError('verification_code', 'Doğrulama kodunun süresi dolmuş. Lütfen yeni kod isteyiniz.');
            return;
        }

        // Deneme sayısını artır
        session(['sms_verification_attempts' => $attempts + 1]);

        // Kodu kontrol et
        // Debug modda '000000' kabul et
        $isValid = ($this->verification_code === $sessionCode);
        if (config('app.debug') && $this->verification_code === '000000') {
            $isValid = true;
        }

        if (!$isValid) {
            $this->addError('verification_code', 'Doğrulama kodu hatalı. Kalan deneme: ' . (4 - $attempts));
            return;
        }

        // Referans eden kişiyi bul
        $referredBy = null;
        if (!empty($this->referral_code)) {
            $referrer = User::where('referral_code', strtolower(trim($this->referral_code)))->first();
            if ($referrer) {
                $referredBy = $referrer->id;
            }
        }

        // Kullanıcıyı oluştur
        $phone = $this->normalizePhone($this->phone);

        $user = User::create([
            'name' => $this->name,
            'tc_no' => $this->tc_no,
            'phone' => $phone,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'referred_by' => $referredBy,
        ]);

        // Telefon doğrulandı olarak işaretle
        $user->phone_verified_at = now();
        $user->save();

        // Session temizle
        session()->forget([
            'sms_verification_code',
            'sms_verification_phone',
            'sms_verification_expires',
            'sms_verification_attempts',
        ]);

        // Referans çerezini temizle
        cookie()->queue(cookie()->forget('referral_code'));

        // Misafir sepetini kullanıcıya aktar (login öncesi session ID ile)
        $guestSessionId = session()->getId();
        $this->mergeGuestCartToUser($guestSessionId, $user->id);

        Auth::login($user);

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

    /**
     * SMS kodunu tekrar gönder
     */
    public function resendCode()
    {
        $phone = $this->normalizePhone($this->phone);

        $smsService = new SmsService();
        $code = $smsService->generateVerificationCode();

        session([
            'sms_verification_code' => $code,
            'sms_verification_phone' => $phone,
            'sms_verification_expires' => now()->addMinutes(5),
            'sms_verification_attempts' => 0,
        ]);

        // SMS gönder ve logla
        $notificationService = new NotificationService();
        $message = "BeArtShare dogrulama kodunuz: {$code}. Bu kodu kimseyle paylasmayin.";
        $result = $notificationService->sendSmsWithLog($phone, $message, 'sms_verification_resend');
        $sent = $result['success'];

        if (!$sent && !config('app.debug')) {
            $this->sms_error = 'SMS gönderilemedi. Lütfen daha sonra tekrar deneyiniz.';
            return;
        }

        $this->sms_error = '';
        $this->resend_cooldown = 120;
        $this->dispatch('start-countdown');
        $this->dispatch('code-resent');
    }

    /**
     * Forma geri dön
     */
    public function backToForm()
    {
        $this->step = 1;
        $this->verification_code = '';
        $this->sms_error = '';
        $this->resetErrorBag();
    }

    /**
     * Telefon numarasını normalize et
     */
    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // 05XX -> 5XX formatına çevir
        if (str_starts_with($phone, '0')) {
            $phone = substr($phone, 1);
        }

        return $phone;
    }

    public function render()
    {
        return view('livewire.auth.register')->layoutData([
            'title' => 'Kayıt Ol | BeArtShare',
            'metaDescription' => 'BeArtShare\'e üye olun. Sanat eserlerini favorileyin, sepetinize ekleyin ve ArtPuan kazanın.',
            'metaRobots' => 'noindex, nofollow',
        ]);
    }
}
