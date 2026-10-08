<?php

namespace App\Models;

use App\Support\ImageUrl;
use Illuminate\Database\Eloquent\Model;

class ArtTerm extends Model
{
    protected $fillable = ['title', 'slug', 'title_tr', 'description', 'description_tr', 'image', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /** Sayfada gösterilen başlık: Türkçesi varsa o, yoksa orijinal (İngilizce) terim */
    public function getDisplayTitleAttribute(): string
    {
        return $this->title_tr ?: $this->title;
    }

    public function getTextAttribute(): ?string
    {
        return $this->description_tr ?: $this->description;
    }

    public function imageUrl(int|string $size = 'card'): ?string
    {
        return $this->image ? ImageUrl::web($this->image, $size) : null;
    }

    /**
     * Eski sitenin (Nuxt) slugify fonksiyonuyla birebir: aksanlar atılır, a-z0-9 dışı silinir,
     * boşluklar tireye çevrilir. Eski /art-terms/{slug} adreslerinin eşleşmesi için.
     */
    public static function legacySlug(string $title): string
    {
        $s = class_exists(\Normalizer::class) ? \Normalizer::normalize($title, \Normalizer::FORM_KD) : $title;
        $s = preg_replace('/[\x{0300}-\x{036f}]/u', '', $s);
        $s = mb_strtolower(trim($s));
        $s = preg_replace('/[^a-z0-9 -]/', '', $s);
        $s = preg_replace('/\s+/', '-', $s);
        return preg_replace('/-+/', '-', $s);
    }
}
