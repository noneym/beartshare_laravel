<?php

namespace App\Livewire;

use App\Models\Artwork;
use App\Models\ArtworkView;
use App\Models\CartItem;
use App\Models\Favorite;
use App\Models\SlugRedirect;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Component;

class ArtworkDetail extends Component
{
    public Artwork $artwork;
    public $currentImage = 0;
    public bool $isFavorited = false;
    public int $viewCount24h = 0;
    public int $favoriteCount = 0;
    public int $artpuanEarn = 0;

    public function mount($slug)
    {
        // Slug değiştiyse eski adres 301 ile yenisine gider
        $this->artwork = Artwork::with('artist', 'category')->where('slug', $slug)->first()
            ?? SlugRedirect::redirectOr404(Artwork::class, $slug, 'artwork.detail');

        if (auth()->check()) {
            $this->isFavorited = auth()->user()->hasFavorited($this->artwork->id);
        }

        $this->recordView();
        $this->loadStats();
    }

    protected function recordView(): void
    {
        $userId = auth()->id();
        $sessionId = session()->getId();
        // Aynı kişi 30 sn içinde yenilerse tek sayılır; 30 sn sonra yeniden görüntüleme olarak kaydedilir
        $cutoff = Carbon::now()->subSeconds(30);

        $existing = ArtworkView::where('artwork_id', $this->artwork->id)
            ->where('viewed_at', '>=', $cutoff)
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->when(!$userId, fn($q) => $q->where('session_id', $sessionId)->whereNull('user_id'))
            ->exists();

        if (!$existing) {
            ArtworkView::create([
                'artwork_id' => $this->artwork->id,
                'user_id' => $userId,
                'session_id' => $userId ? null : $sessionId,
                'viewed_at' => Carbon::now(),
            ]);
        }
    }

    protected function loadStats(): void
    {
        $cutoff = Carbon::now()->subHours(24);

        $this->viewCount24h = ArtworkView::where('artwork_id', $this->artwork->id)
            ->where('viewed_at', '>=', $cutoff)
            ->count();

        $this->favoriteCount = $this->artwork->favoritedBy()->count();

        $this->artpuanEarn = (int) floor(($this->artwork->price_tl ?? 0) * 0.01);
    }

    public function setImage($index)
    {
        $this->currentImage = $index;
    }

    public function toggleFavorite()
    {
        if (!auth()->check()) {
            $this->dispatch('show-login-modal');
            return;
        }

        $userId = auth()->id();
        $artworkId = $this->artwork->id;

        $existing = Favorite::where('user_id', $userId)
            ->where('artwork_id', $artworkId)
            ->first();

        if ($existing) {
            $existing->delete();
            $this->isFavorited = false;
            $this->dispatch('toast', message: 'Favorilerden kaldırıldı.', type: 'info');
        } else {
            Favorite::create([
                'user_id' => $userId,
                'artwork_id' => $artworkId,
            ]);
            $this->isFavorited = true;
            $this->dispatch('toast', message: 'Favorilere eklendi!', type: 'success');
        }
    }

    public function addToCart()
    {
        if ($this->artwork->is_sold) {
            $this->dispatch('cart-error', message: 'Bu eser satılmıştır.');
            return;
        }

        if ($this->artwork->is_reserved) {
            $this->dispatch('cart-error', message: 'Bu eser rezerve edilmiştir.');
            return;
        }

        $userId = auth()->id();
        $sessionId = session()->getId();

        $exists = CartItem::where('artwork_id', $this->artwork->id)
            ->where(function ($query) use ($userId, $sessionId) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('session_id', $sessionId);
                }
            })
            ->exists();

        if ($exists) {
            $this->dispatch('cart-info', message: 'Bu eser zaten sepetinizde.');
            return;
        }

        CartItem::create([
            'user_id' => $userId,
            'session_id' => $userId ? null : $sessionId,
            'artwork_id' => $this->artwork->id,
        ]);

        $this->dispatch('cart-updated');
        $this->dispatch('cart-added',
            message: 'Eser sepete eklendi!',
            title: $this->artwork->title,
            artist: $this->artwork->artist->name ?? '',
            image: $this->artwork->imageUrl('thumb'),
            price: $this->artwork->formatted_price_tl,
        );
    }

    public function render()
    {
        $relatedArtworks = Artwork::with('artist')
            ->available()
            ->where('artist_id', $this->artwork->artist_id)
            ->where('id', '!=', $this->artwork->id)
            ->take(4)
            ->get();

        $artwork = $this->artwork;
        $artistName = $artwork->artist ? $artwork->artist->name : 'Bilinmeyen Sanatçı';
        $category = $artwork->category ? $artwork->category->name : '';
        $year = $artwork->year ? (string) $artwork->year : null;

        // "İsimsiz" gibi tekrar eden başlıklar: teknik ve yıl ile ayrıştırılır (aynı başlıklı sayfalar oluşmasın)
        // Teknik uzun olabilir ("Tuval üzerine yağlıboya, imzalı. Provenance ..."): ilk parçası alınır
        $technique = Str::limit(trim(Str::before(Str::before((string) $artwork->technique, ','), '.')), 40, '');
        $specs = array_values(array_filter([$technique, $year]));
        $sameTitle = Artwork::where('id', '!=', $artwork->id)->where('title', $artwork->title)->exists();
        $titleLabel = $artwork->title . ($sameTitle && $specs ? ' (' . implode(', ', $specs) . ')' : '');

        // Meta açıklaması: 160 karakteri aşarsa kelime sınırından kesilir
        $cut = fn (string $t) => mb_strlen($t) <= 160 ? $t : preg_replace('/\s+\S*$/u', '', mb_substr($t, 0, 157)) . '…';
        // Açıklama alanı bazen "." gibi yer tutucu: 20 karakterden kısa ise yok sayılır
        $plain = trim(preg_replace('/\s+/u', ' ', strip_tags(html_entity_decode((string) $artwork->description, ENT_QUOTES, 'UTF-8'))), " 	
.,;:-–");
        if (mb_strlen($plain) >= 20) {
            $description = $cut($plain);
        } else {
            // Açıklama yoksa sanatçı, başlık, teknik, ölçü ve yıldan üretilir; kuyruk sığdığı kadar uzun seçilir
            $details = implode(', ', array_filter([$technique, $artwork->dimensions, $year]));
            $head = "{$artistName} – {$artwork->title}" . ($details ? ". {$details}" : '') . '.';
            $description = null;
            foreach ([
                ' BeArtShare online sanat galerisinde orijinal eser; güvenli alışveriş, ArtPuan kazancı.',
                ' BeArtShare online sanat galerisinde orijinal eser.',
                " BeArtShare'de orijinal eser.",
                '',
            ] as $tail) {
                if (mb_strlen($head . $tail) <= 160) {
                    $description = $head . $tail;
                    break;
                }
            }
            $description ??= $cut($head);
        }

        $price = $artwork->price_tl ? number_format($artwork->price_tl, 0, ',', '.') . ' ₺' : '';
        // Paylaşım görseli: 1200x630, eser kırpılmadan (bulanık dolgu); görsel yoksa site varsayılanı
        $imageUrl = \App\Support\ImageUrl::social($artwork->first_image) ?? asset('images/og-default.jpg');
        $images = $artwork->images
            ? array_map(fn ($i) => \App\Support\ImageUrl::make($i, 'detail'), $artwork->images)
            : [$imageUrl];

        $product = array_filter([
            '@type' => 'Product',
            'name' => $artwork->title,
            'description' => $description,
            'image' => $images,
            'sku' => (string) $artwork->id,
            'material' => $artwork->technique ?: null,
            'size' => $artwork->dimensions ?: null,
            'productionDate' => $year,
            'brand' => [
                '@type' => 'Brand',
                'name' => $artistName,
            ],
            // Satılmış eserin satış fiyatı yalnızca üyelere gösterilir; yapısal veride de yer almaz
            'offers' => $artwork->is_sold ? null : [
                '@type' => 'Offer',
                'url' => url()->current(),
                'price' => $artwork->price_tl ?? 0,
                'priceCurrency' => 'TRY',
                'availability' => $artwork->is_sold
                    ? 'https://schema.org/SoldOut'
                    : ($artwork->is_reserved ? 'https://schema.org/LimitedAvailability' : 'https://schema.org/InStock'),
                'seller' => [
                    '@type' => 'Organization',
                    'name' => 'BeArtShare',
                ],
                // Teslimat ve iade şartları sayfasıyla uyumlu: Türkiye'ye sigortalı kargo (ücret siparişte),
                // teslimden itibaren 14 gün cayma hakkı, iade kargo bedeli alıcıya ait
                'shippingDetails' => [
                    '@type' => 'OfferShippingDetails',
                    'shippingDestination' => ['@type' => 'DefinedRegion', 'addressCountry' => 'TR'],
                ],
                'hasMerchantReturnPolicy' => [
                    '@type' => 'MerchantReturnPolicy',
                    'applicableCountry' => 'TR',
                    'returnPolicyCategory' => 'https://schema.org/MerchantReturnFiniteReturnWindow',
                    'merchantReturnDays' => 14,
                    'returnMethod' => 'https://schema.org/ReturnByMail',
                    'returnFees' => 'https://schema.org/ReturnShippingFees',
                    'refundType' => 'https://schema.org/FullRefund',
                    'merchantReturnLink' => route('teslimat-iade'),
                ],
            ],
            'category' => $category,
            'url' => url()->current(),
        ], fn ($v) => $v !== null);

        // Ekrandaki kırıntı yolu ile aynı: Ana Sayfa > Eserler > Sanatçı > Eser
        $crumbs = [['Ana Sayfa', route('home')], ['Eserler', route('artworks')]];
        if ($artwork->artist) {
            $crumbs[] = [$artwork->artist->name, route('artist.detail', $artwork->artist->slug)];
        }
        $crumbs[] = [$artwork->title, url()->current()];
        $breadcrumb = [
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($crumbs)->map(fn ($c, $i) => [
                '@type' => 'ListItem', 'position' => $i + 1, 'name' => $c[0], 'item' => $c[1],
            ])->values()->all(),
        ];

        $jsonLd = json_encode([
            '@context' => 'https://schema.org',
            '@graph' => [$product, $breadcrumb],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return view('livewire.artwork-detail', [
            'relatedArtworks' => $relatedArtworks,
        ])->layoutData([
            'title' => "{$titleLabel} - {$artistName} | BeArtShare",
            'metaDescription' => $description,
            'metaKeywords' => implode(', ', array_filter([$artwork->title, $artistName, $category, 'orijinal eser', 'sanat eseri', 'tablo'])),
            'ogType' => 'product',
            'ogTitle' => "{$titleLabel} - {$artistName}",
            'ogDescription' => $description,
            'ogImage' => $imageUrl,
            'jsonLd' => $jsonLd,
        ]);
    }
}
