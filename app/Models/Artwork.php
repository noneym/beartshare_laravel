<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Concerns\HasSlugRedirects;
use App\Support\Slugger;

class Artwork extends Model
{
    use HasFactory, HasSlugRedirects;

    protected $fillable = [
        'artist_id',
        'category_id',
        'title',
        'slug',
        'description',
        'sale_note',
        'tags',
        'technique',
        'dimensions',
        'extra_note',
        'year',
        'price_tl',
        'price_usd',
        'is_sold',
        'sold_at',
        'is_active',
        'is_featured',
        'featured_weight',
        'is_reserved',
        'allow_credit_card',
        'owner_name',
        'admin_notes',
        'type',
        'sort_order',
        'old_id',
        'images',
        'cover_image',
        'hide_from_gallery',
    ];

    protected $casts = [
        'is_sold' => 'boolean',
        'sold_at' => 'datetime',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'featured_weight' => 'integer',
        'is_reserved' => 'boolean',
        'allow_credit_card' => 'boolean',
        'hide_from_gallery' => 'boolean',
        'images' => 'array',
        'price_tl' => 'decimal:2',
        'price_usd' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($artwork) {
            if (empty($artwork->slug)) {
                $artist = $artwork->artist_id ? Artist::find($artwork->artist_id) : null;
                $artwork->slug = static::generateSlug($artwork->title, $artist?->name);
            }
        });

        // Satış tarihi: satıldı işaretlenince atanır, satıştan çıkınca temizlenir
        static::saving(function ($artwork) {
            if ($artwork->is_sold && !$artwork->sold_at) {
                $artwork->sold_at = now();
            } elseif (!$artwork->is_sold && $artwork->sold_at) {
                $artwork->sold_at = null;
            }
        });
    }

    /**
     * Satılmış eserlerin satış tarihini ve satış anındaki USD fiyatını siparişlerden doldurur
     * (en son geçerli sipariş: ödeme / onay tarihi). Siparişi olmayanlar dokunulmadan kalır.
     */
    public static function backfillSaleData(): int
    {
        $rows = DB::table('order_items as oi')
            ->join('orders as o', 'o.id', '=', 'oi.order_id')
            ->whereNull('o.deleted_at')
            ->whereIn('o.status', ['paid', 'confirmed', 'shipped', 'delivered'])
            ->whereNotNull('oi.artwork_id')
            ->orderBy('o.id')
            ->get(['oi.artwork_id', 'oi.price_usd', DB::raw('COALESCE(o.paid_at, o.confirmed_at, o.created_at) as sold_at')])
            ->keyBy('artwork_id'); // aynı eserin birden çok siparişi varsa en sonuncusu

        $n = 0;
        foreach ($rows as $artworkId => $r) {
            $update = ['sold_at' => $r->sold_at];
            if ((float) $r->price_usd > 0) {
                $update['price_usd'] = $r->price_usd;
            }
            $n += DB::table('artworks')->where('id', $artworkId)->where('is_sold', true)->update($update);
        }
        return $n;
    }

    /** Satış fiyatı / tarihi yalnızca üyelere gösterilir */
    public function getSalePriceVisibleAttribute(): bool
    {
        return !$this->is_sold || auth()->check();
    }

    /**
     * Başlıktan slug: "isimsiz". Aynı başlık başka eserde de varsa sanatçı adı eklenir:
     * "isimsiz-nasip-iyem"; o da doluysa -2, -3 ...
     */
    public static function generateSlug(?string $title, ?string $artistName, ?int $ignoreId = null, array $reserved = []): string
    {
        $base = Slugger::base($title) ?: 'eser';
        $withArtist = $artistName ? Slugger::base(Slugger::base($title, 50) . ' ' . $artistName, 90) : null;

        $shared = static::query()
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->pluck('title')
            ->contains(fn ($t) => Slugger::base($t) === $base);

        $preferred = ($shared && $withArtist) ? $withArtist : $base;

        return Slugger::unique(
            $preferred,
            fn (string $s) => in_array($s, $reserved, true) || static::slugTaken($s, $ignoreId),
            $withArtist !== $preferred ? $withArtist : null,
        );
    }

    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getFirstImageAttribute()
    {
        if ($this->images && count($this->images) > 0) {
            return $this->images[0];
        }
        return null;
    }

    /**
     * Listeleme sayfalarındaki görsel: kapak fotoğrafı yüklüyse o, yoksa ilk görsel.
     */
    public function getListImageAttribute()
    {
        return $this->cover_image ?: $this->first_image;
    }

    /** Görsel alt metni: "Sanatçı - Eser Adı - Yıl" (yıl varsa) */
    public function getImageAltAttribute(): string
    {
        return implode(' - ', array_filter([
            $this->artist?->name,
            $this->title,
            $this->year ? (string) $this->year : null,
        ]));
    }

    public function getListImageUrlAttribute()
    {
        return \App\Support\ImageUrl::web($this->list_image, 'card');
    }

    /** Kart görseli için srcset: 450w (mobil / küçük kart) ve 900w (retina). */
    public function getListImageSrcsetAttribute(): ?string
    {
        if (! $this->list_image) {
            return null;
        }

        return \App\Support\ImageUrl::web($this->list_image, 450) . ' 450w, '
            . \App\Support\ImageUrl::web($this->list_image, 900) . ' 900w';
    }

    /**
     * Get the first image as a full URL (handles both external URLs and local storage paths).
     */
    public function getFirstImageUrlAttribute()
    {
        return \App\Support\ImageUrl::web($this->first_image, 'card');
    }

    /**
     * İlk görselin belirli boyutta URL'i (preset adı veya piksel).
     */
    public function imageUrl(int|string $width = 'card', int $height = 0): ?string
    {
        return \App\Support\ImageUrl::make($this->first_image, $width, $height);
    }

    /**
     * Get all images as full URLs (detay/lightbox boyutu).
     */
    public function getImageUrlsAttribute()
    {
        if (!$this->images || count($this->images) === 0) {
            return [];
        }
        return array_map(fn ($image) => \App\Support\ImageUrl::web($image, 'detail'), $this->images);
    }

    public function getFormattedPriceTlAttribute()
    {
        return number_format($this->price_tl, 0, ',', '.') . ' TL';
    }

    public function getFormattedPriceUsdAttribute()
    {
        return number_format($this->price_usd, 0, ',', '.') . ' $';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_sold', false)->where('is_reserved', false)->where('is_active', true);
    }

    /** Sanal galeriye (3D sergi) dahil edilen eserler */
    public function scopeInGallery($query)
    {
        return $query->where('hide_from_gallery', false);
    }

    /**
     * Vitrin sırası: öne çıkanlar önce, aralarında sıra ağırlığı yüksek olan, eşitlerde en yeni.
     * (ArtPuan kolajı 1-3, Hakkımızda duvarı 4-5. eserleri bu sıradan alır)
     */
    public function scopeShowcaseOrder($query)
    {
        return $query->orderByDesc('is_featured')->orderByDesc('featured_weight')->latest();
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeReserved($query)
    {
        return $query->where('is_reserved', true);
    }

    /**
     * Bu eseri favorilerine eklemiş kullanıcılar
     */
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }
}
