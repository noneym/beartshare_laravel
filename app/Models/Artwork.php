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
        'type',
        'sort_order',
        'old_id',
        'images',
    ];

    protected $casts = [
        'is_sold' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
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
        $image = $this->first_image;
        if (!$image) {
            return null;
        }
        if (str_starts_with($image, 'http')) {
            return $image;
        }
        return asset('storage/' . $image);
    }

    /**
     * Get all images as full URLs.
     */
    public function getImageUrlsAttribute()
    {
        if (!$this->images || count($this->images) === 0) {
            return [];
        }
        return array_map(function ($image) {
            if (str_starts_with($image, 'http')) {
                return $image;
            }
            return asset('storage/' . $image);
        }, $this->images);
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
        return $query->where('is_sold', false)->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
