<?php

namespace App\Support;

class ArtworkDimensions
{
    /**
     * "53x65 cm", "183 x 75,5 cm", "21 x 17 x 7 cm", "349 cm × 776 cm" gibi serbest
     * metinden ölçüleri santimetre olarak çıkarır.
     *
     * Hangi sayının en, hangisinin boy olduğu girişlerde tutarlı değil; bu yüzden
     * sadece [a, b, derinlik] döner, yönelim görselin oranına göre belirlenir.
     *
     * @return array{0: float, 1: float, 2: float|null}|null
     */
    public static function parse(?string $text): ?array
    {
        if (!$text) {
            return null;
        }

        $num = '(\d+(?:[.,]\d+)?)';
        $sep = '\s*(?:cm)?\s*[x×X\*]\s*';

        if (!preg_match("/{$num}{$sep}{$num}(?:{$sep}{$num})?/u", $text, $m)) {
            return null;
        }

        $values = array_map(
            fn ($v) => (float) str_replace(',', '.', $v),
            array_slice(array_filter($m, fn ($v) => $v !== ''), 1)
        );

        // "mm" yazılmışsa santimetreye çevir
        if (preg_match('/\bmm\b/i', $text)) {
            $values = array_map(fn ($v) => $v / 10, $values);
        }

        [$a, $b] = $values;
        if ($a <= 0 || $b <= 0 || $a > 2000 || $b > 2000) {
            return null;
        }

        return [$a, $b, $values[2] ?? null];
    }
}
