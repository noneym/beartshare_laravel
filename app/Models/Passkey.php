<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Passkey extends Model
{
    protected $fillable = ['user_id', 'name', 'credential_id', 'public_key', 'sign_count', 'rp_id', 'aaguid', 'backed_up', 'last_used_at'];

    protected $hidden = ['public_key'];

    protected $casts = [
        'sign_count' => 'integer',
        'backed_up' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
