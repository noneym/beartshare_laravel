<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Artist extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'birth_year',
        'death_year',
        'biography',
        'image',
        'avatar',
        'is_active',
        'old_id',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($artist) {
            if (empty($artist->slug)) {
                $artist->slug = Str::slug($artist->name);
            }
        });
    }

    public function artworks()
    {
        return $this->hasMany(Artwork::class);
    }

    public function getLifeSpanAttribute()
    {
        if ($this->birth_year && $this->death_year) {
            return "({$this->birth_year} - {$this->death_year})";
        } elseif ($this->birth_year) {
            return "({$this->birth_year})";
        }
        return '';
    }

    public function getAvatarUrlAttribute()
    {
        return \App\Support\ImageUrl::make($this->avatar ?: $this->image, 'avatar');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
