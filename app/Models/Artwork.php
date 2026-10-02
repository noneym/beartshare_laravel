<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Artwork extends Model
{
    use HasFactory;

    protected $fillable = [
        'artist_id',
        'category_id',
        'title',
        'slug',
        'description',
        'tags',
        'technique',
        'dimensions',
        'year',
        'price_tl',
        'price_usd',
        'is_sold',
        'is_active',
        'is_featured',
        'is_reserved',
        'allow_credit_card',
        'owner_name',
        'admin_notes',
        'type',
        'sort_order',
        'old_id',
        'images',
    ];

    protected $casts = [
        'is_sold' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_reserved' => 'boolean',
        'allow_credit_card' => 'boolean',
        'images' => 'array',
        'price_tl' => 'decimal:2',
        'price_usd' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($artwork) {
            if (empty($artwork->slug)) {
                $artwork->slug = Str::slug($artwork->title . '-' . uniqid());
            }
        });
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
     * Get the first image as a full URL (handles both external URLs and local storage paths).
     */
    public function getFirstImageUrlAttribute()
    {
        return \App\Support\ImageUrl::make($this->first_image, 'card');
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
        return array_map(fn ($image) => \App\Support\ImageUrl::make($image, 'detail'), $this->images);
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
