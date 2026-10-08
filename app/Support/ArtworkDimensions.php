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
    /**
     * Boyut alanına karışmış açıklamayı ayırır: "35 x 25 cm İlgili gravür..." → ["35 x 25 cm", "İlgili gravür..."].
     * Parantezli ölçü açıklamaları ("(9,4 in × 13 in)", "(iç), 60x50 cm (çerçeveli)") boyutta kalır.
     *
     * @return array{0: ?string, 1: ?string} [boyut, ek not]
     */
    public static function splitNote(?string $text): array
    {
        $text = trim(preg_replace('/\s+/u', ' ', (string) $text));
        if ($text === '') {
            return [null, null];
        }

        $num = '\d+(?:[.,]\d+)?';
        $unit = '(?:\s*cm(?![a-zçğıöşü])\.?)?';
        $sep = $unit . '\s*[x×X\*]\s*';
        if (!preg_match("/^({$num}{$sep}{$num}(?:{$sep}{$num})?{$unit})(.*)$/u", $text, $m)) {
            return [$text, null];
        }

        [$dims, $rest] = [trim($m[1]), trim($m[2])];
        if ($rest === '' || str_starts_with($rest, '(')) {
            return [$text, null];
        }

        if (!preg_match('/cm\.?$/u', $dims)) {
            $dims .= ' cm';
        }
        $note = trim(preg_replace('/^[\s,.;:\-–—]+/u', '', $rest));
        $first = mb_substr($note, 0, 1);
        $note = $note === '' ? null : ($first === 'i' ? 'İ' : mb_strtoupper($first)) . mb_substr($note, 1);

        return [rtrim($dims, '.'), $note];
    }

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
