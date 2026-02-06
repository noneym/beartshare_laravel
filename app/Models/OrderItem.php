<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id',
        'artwork_id',
        'artwork_title',
        'artist_name',
        'quantity',
        'price_tl',
        'price_usd',
    ];

    protected $casts = [
        'price_tl' => 'decimal:2',
        'price_usd' => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function artwork()
    {
        return $this->belongsTo(Artwork::class);
    }

    public function getArtworkTitleAttribute($value)
    {
        return $value ?? ($this->artwork ? $this->artwork->title : 'Bilinmeyen Eser');
    }

    public function getArtistNameAttribute($value)
    {
        return $value ?? ($this->artwork && $this->artwork->artist ? $this->artwork->artist->name : 'Bilinmeyen Sanatci');
    }

    public function getQuantityAttribute($value)
    {
        return $value ?? 1;
    }
}
