<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    protected $fillable = ['key', 'sms_body', 'email_subject', 'email_body', 'sms_enabled', 'email_enabled'];

    protected $casts = [
        'sms_enabled' => 'boolean',
        'email_enabled' => 'boolean',
    ];
}
