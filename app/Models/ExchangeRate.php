<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    protected $fillable = ['currency', 'rate', 'source', 'rate_date', 'artworks_updated'];

    protected $casts = [
        'rate' => 'decimal:4',
        'rate_date' => 'date',
    ];

    public static function latestRate(string $currency = 'USD'): ?float
    {
        $rate = static::where('currency', $currency)->latest('id')->value('rate');
        return $rate ? (float) $rate : null;
    }
}
