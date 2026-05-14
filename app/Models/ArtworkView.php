<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArtworkView extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'artwork_id',
        'user_id',
        'session_id',
        'viewed_at',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    public function artwork()
    {
        return $this->belongsTo(Artwork::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
