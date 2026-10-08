<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasSlugRedirects;
use App\Support\Slugger;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    use HasFactory, HasSlugRedirects;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'blog_category_id',
        'image',
        'user_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = static::generateSlug($post->title);
            }
        });
    }

    /** Başlıktan slug; doluysa -2, -3 ... */
    public static function generateSlug(?string $title, ?int $ignoreId = null, array $reserved = []): string
    {
        return Slugger::unique(
            Slugger::base($title) ?: 'yazi',
            fn (string $s) => in_array($s, $reserved, true) || static::slugTaken($s, $ignoreId),
        );
    }

    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getImageUrlAttribute()
    {
        return \App\Support\ImageUrl::web($this->image, 'blog') ?? asset('images/og-default.jpg');
    }

    /**
     * İçerikteki R2/Thumbor görselleri güncel anahtarla imzalanmış halde döner.
     */
    public function getRenderedContentAttribute(): ?string
    {
        return \App\Support\ImageUrl::resignHtml($this->content, 'blog');
    }

    public function getExcerptAttribute()
    {
        return Str::limit(html_entity_decode(strip_tags($this->content), ENT_QUOTES, 'UTF-8'), 150);
    }

    public function getReadTimeAttribute()
    {
        $wordCount = str_word_count(strip_tags($this->content));
        $minutes = max(1, ceil($wordCount / 200));
        return $minutes . ' dk okuma';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
