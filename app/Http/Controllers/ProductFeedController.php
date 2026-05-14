<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductFeedController extends Controller
{
    /**
     * Meta Commerce Manager / Google Merchant uyumlu XML product feed.
     *
     * Şema: RSS 2.0 + Google product extension (g:id, g:title, g:link, ...).
     */
    public function xml(): Response
    {
        $xml = Cache::remember('product_feed_xml_v1', 3600, function () {
            return $this->buildXml();
        });

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * CSV product feed (Meta/Google fallback).
     */
    public function csv(): StreamedResponse
    {
        $rows = Cache::remember('product_feed_rows_v1', 3600, function () {
            return $this->buildRows();
        });

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            // BOM ile UTF-8
            fwrite($handle, "\xEF\xBB\xBF");

            $columns = ['id', 'title', 'description', 'availability', 'condition', 'price', 'link', 'image_link', 'brand'];
            fputcsv($handle, $columns);

            foreach ($rows as $row) {
                fputcsv($handle, array_map(fn($c) => $row[$c] ?? '', $columns));
            }

            fclose($handle);
        }, 'products.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    protected function buildRows(): array
    {
        return Artwork::with('artist')
            ->where('is_active', true)
            ->orderByDesc('updated_at')
            ->get()
            ->map(function (Artwork $a) {
                $price = number_format((float) ($a->price_tl ?? 0), 2, '.', '');
                $availability = $a->is_sold ? 'out of stock' : ($a->is_reserved ? 'preorder' : 'in stock');
                $description = trim(strip_tags($a->description ?? '')) ?: ($a->title . ' - ' . ($a->artist->name ?? ''));

                return [
                    'id' => $a->id,
                    'title' => $this->clean($a->title),
                    'description' => $this->clean($description),
                    'availability' => $availability,
                    'condition' => 'new',
                    'price' => $price . ' TRY',
                    'link' => route('artwork.detail', $a->slug),
                    'image_link' => $a->first_image_url ?? '',
                    'brand' => $this->clean($a->artist->name ?? 'BeArtShare'),
                ];
            })
            ->toArray();
    }

    protected function buildXml(): string
    {
        $rows = $this->buildRows();
        $siteUrl = config('app.url', url('/'));
        $siteName = 'BeArtShare';
        $now = now()->toRssString();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">' . "\n";
        $xml .= "  <channel>\n";
        $xml .= '    <title>' . htmlspecialchars($siteName) . '</title>' . "\n";
        $xml .= '    <link>' . htmlspecialchars($siteUrl) . '</link>' . "\n";
        $xml .= '    <description>BeArtShare ürün feed\'i (Meta Commerce Manager)</description>' . "\n";
        $xml .= '    <pubDate>' . $now . '</pubDate>' . "\n";

        foreach ($rows as $r) {
            $xml .= "    <item>\n";
            $xml .= '      <g:id>' . $r['id'] . '</g:id>' . "\n";
            $xml .= '      <g:title>' . htmlspecialchars($r['title']) . '</g:title>' . "\n";
            $xml .= '      <g:description>' . htmlspecialchars($r['description']) . '</g:description>' . "\n";
            $xml .= '      <g:link>' . htmlspecialchars($r['link']) . '</g:link>' . "\n";
            $xml .= '      <g:image_link>' . htmlspecialchars($r['image_link']) . '</g:image_link>' . "\n";
            $xml .= '      <g:availability>' . $r['availability'] . '</g:availability>' . "\n";
            $xml .= '      <g:condition>' . $r['condition'] . '</g:condition>' . "\n";
            $xml .= '      <g:price>' . $r['price'] . '</g:price>' . "\n";
            $xml .= '      <g:brand>' . htmlspecialchars($r['brand']) . '</g:brand>' . "\n";
            $xml .= "    </item>\n";
        }

        $xml .= "  </channel>\n</rss>\n";

        return $xml;
    }

    protected function clean(string $value): string
    {
        $value = preg_replace('/\s+/', ' ', $value);
        return mb_substr(trim($value), 0, 5000);
    }
}
