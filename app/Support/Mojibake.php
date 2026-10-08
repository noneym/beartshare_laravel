<?php

namespace App\Support;

/**
 * Bir kez yanlışlıkla Latin-1 / Windows-1252 olarak okunup UTF-8 kaydedilmiş metni onarır
 * (ör. "Pepperâ\u{80}\u{99}s" → "Pepper’s", "â€œ" → "“"). Yalnızca bozuk dizileri değiştirir;
 * aynı metindeki doğru Türkçe karakterlere dokunmaz.
 */
class Mojibake
{
    /** Windows-1252'nin 0x80-0x9F aralığındaki karakterleri → bayt */
    private const CP1252 = [
        0x20AC => 0x80, 0x201A => 0x82, 0x0192 => 0x83, 0x201E => 0x84, 0x2026 => 0x85, 0x2020 => 0x86,
        0x2021 => 0x87, 0x02C6 => 0x88, 0x2030 => 0x89, 0x0160 => 0x8A, 0x2039 => 0x8B, 0x0152 => 0x8C,
        0x017D => 0x8E, 0x2018 => 0x91, 0x2019 => 0x92, 0x201C => 0x93, 0x201D => 0x94, 0x2022 => 0x95,
        0x2013 => 0x96, 0x2014 => 0x97, 0x02DC => 0x98, 0x2122 => 0x99, 0x0161 => 0x9A, 0x203A => 0x9B,
        0x0153 => 0x9C, 0x017E => 0x9E, 0x0178 => 0x9F,
    ];

    public static function fix(?string $text): ?string
    {
        if ($text === null || $text === '' || !preg_match('/[\x{00C2}-\x{00F4}]/u', $text)) {
            return $text;
        }

        $cont = '[\x{0080}-\x{00BF}\x{0152}\x{0153}\x{0160}\x{0161}\x{0178}\x{017D}\x{017E}\x{0192}\x{02C6}\x{02DC}\x{2013}\x{2014}\x{2018}-\x{201E}\x{2020}-\x{2022}\x{2026}\x{2030}\x{2039}\x{203A}\x{20AC}\x{2122}]';

        return preg_replace_callback('/[\x{00C2}-\x{00F4}]' . $cont . '{1,3}/u', function ($m) {
            $bytes = '';
            foreach (mb_str_split($m[0]) as $ch) {
                $cp = mb_ord($ch);
                if ($cp <= 0xFF) $bytes .= chr($cp);
                elseif (isset(self::CP1252[$cp])) $bytes .= chr(self::CP1252[$cp]);
                else return $m[0];
            }
            // Geçerli tek bir UTF-8 karakteri çıkıyorsa onarılmış demektir
            return mb_check_encoding($bytes, 'UTF-8') && mb_strlen($bytes, 'UTF-8') === 1 ? $bytes : $m[0];
        }, $text);
    }
}
