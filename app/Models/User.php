<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'tc_no',
        'phone',
        'email',
        'password',
        'referral_code',
        'referred_by',
        'art_puan',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'password' => 'hashed',
        'art_puan' => 'decimal:2',
        'is_admin' => 'boolean',
    ];

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoriteArtworks()
    {
        return $this->belongsToMany(Artwork::class, 'favorites')->withTimestamps();
    }

    public function hasFavorited(int $artworkId): bool
    {
        return $this->favorites()->where('artwork_id', $artworkId)->exists();
    }

    // ── Referral ──

    /**
     * Bu kullanıcıyı referans eden kişi
     */
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    /**
     * Bu kullanıcının referans ettiği kişiler
     */
    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    /**
     * Benzersiz referans kodu oluştur
     */
    public static function generateReferralCode(): string
    {
        do {
            $code = strtolower(substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 7));
        } while (self::where('referral_code', $code)->exists());

        return $code;
    }

    /**
     * Kayıt sonrası otomatik referans kodu ata
     */
    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (empty($user->referral_code)) {
                $user->referral_code = self::generateReferralCode();
            }
        });
    }

    /**
     * Referans linkini döndür
     */
    public function getReferralLinkAttribute(): string
    {
        return url('/?ref=' . $this->referral_code);
    }

    // ── Addresses ──

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function shippingAddresses()
    {
        return $this->hasMany(Address::class)->where('type', 'shipping');
    }

    public function billingAddresses()
    {
        return $this->hasMany(Address::class)->where('type', 'billing');
    }

    public function defaultShippingAddress()
    {
        return $this->hasOne(Address::class)->where('type', 'shipping')->where('is_default', true);
    }

    public function defaultBillingAddress()
    {
        return $this->hasOne(Address::class)->where('type', 'billing')->where('is_default', true);
    }

    // ── Orders ──

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // ── ArtPuan ──

    public function artPuanLogs()
    {
        return $this->hasMany(ArtPuanLog::class);
    }

    /**
     * ArtPuan ekle ve log kaydı oluştur
     */
    public function addArtPuan(float $amount, array $meta = []): ArtPuanLog
    {
        $this->increment('art_puan', $amount);
        $this->refresh();

        return ArtPuanLog::create([
            'user_id' => $this->id,
            'order_id' => $meta['order_id'] ?? null,
            'artwork_id' => $meta['artwork_id'] ?? null,
            'source_user_id' => $meta['source_user_id'] ?? null,
            'type' => $meta['type'] ?? 'purchase',
            'amount' => $amount,
            'balance_after' => $this->art_puan,
            'description' => $meta['description'] ?? null,
        ]);
    }

    /**
     * ArtPuan harca ve log kaydı oluştur
     */
    public function spendArtPuan(float $amount, array $meta = []): ArtPuanLog
    {
        if ($amount > $this->art_puan) {
            throw new \Exception('Yetersiz ArtPuan bakiyesi.');
        }

        $this->decrement('art_puan', $amount);
        $this->refresh();

        return ArtPuanLog::create([
            'user_id' => $this->id,
            'order_id' => $meta['order_id'] ?? null,
            'artwork_id' => $meta['artwork_id'] ?? null,
            'source_user_id' => $meta['source_user_id'] ?? null,
            'type' => 'spend',
            'amount' => -$amount,
            'balance_after' => $this->art_puan,
            'description' => $meta['description'] ?? 'ArtPuan harcandı',
        ]);
    }

    /**
     * Formatlanmış ArtPuan
     */
    public function getFormattedArtPuanAttribute(): string
    {
        return number_format($this->art_puan, 2, ',', '.') . ' AP';
    }
}
