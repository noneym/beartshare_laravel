<?php

namespace App\Support;

use App\Models\User;

/**
 * Toplu SMS / e-posta içeriğindeki değişkenleri kullanıcı bilgileriyle doldurur.
 */
class MessageTemplate
{
    public static function render(string $content, User $user): string
    {
        $firstName = explode(' ', trim($user->name))[0];

        return str_replace([
            '{isim}',
            '{ad}',
            '{email}',
            '{telefon}',
            '{artpuan}',
            '{referans_kodu}',
            '{referans_linki}',
            '{id}',
        ], [
            $user->name,
            $firstName,
            $user->email ?? '',
            $user->phone ?? '',
            number_format($user->art_puan, 2, ',', '.'),
            $user->referral_code ?? '',
            $user->referral_link ?? '',
            $user->id,
        ], $content);
    }
}
