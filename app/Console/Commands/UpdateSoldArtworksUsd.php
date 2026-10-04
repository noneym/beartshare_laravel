<?php

namespace App\Console\Commands;

use App\Models\Artwork;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Satılmış eserlerin USD fiyatını, TL fiyatı ve satış tarihindeki USD/TRY kuru ile yeniden hesaplar.
 * Kur kaynağı: api.frankfurter.dev (ECB referans kurları). Hafta sonu / tatilde bir önceki iş günü kullanılır.
 */
class UpdateSoldArtworksUsd extends Command
{
    protected $signature = 'artworks:sold-usd
        {--dry-run : Değişiklikleri yalnızca göster, kaydetme}';

    protected $description = 'Satılmış eserlerin USD fiyatını satış tarihindeki kura göre günceller (Frankfurter)';

    protected const API = 'https://api.frankfurter.dev/v1';

    public function handle(): int
    {
        $artworks = Artwork::where('is_sold', true)->whereNotNull('sold_at')->where('price_tl', '>', 0)
            ->orderBy('sold_at')->get(['id', 'title', 'price_tl', 'price_usd', 'sold_at']);

        $skipped = Artwork::where('is_sold', true)->whereNull('sold_at')->pluck('id');
        if ($artworks->isEmpty()) {
            $this->warn('Satış tarihi olan satılmış eser yok.');
            return self::SUCCESS;
        }

        // Tek istekte tüm dönem; başlangıçtan önceki birkaç gün, hafta sonuna denk gelen ilk satış için
        $from = $artworks->first()->sold_at->copy()->subDays(7)->toDateString();
        $to = now()->toDateString();
        $res = Http::timeout(30)->retry(2, 1000)->get(self::API . "/{$from}..{$to}", ['base' => 'USD', 'symbols' => 'TRY']);
        $rates = collect($res->json('rates') ?? [])->map(fn ($r) => (float) ($r['TRY'] ?? 0))->filter()->sortKeys();

        if (!$res->successful() || $rates->isEmpty()) {
            $this->error('Kur verisi alınamadı (HTTP ' . $res->status() . ').');
            return self::FAILURE;
        }
        $this->info("Kur verisi: {$rates->keys()->first()} .. {$rates->keys()->last()} ({$rates->count()} iş günü)");

        $rows = [];
        $changed = 0;
        foreach ($artworks as $a) {
            $day = $a->sold_at->copy()->setTimezone(config('app.timezone'))->toDateString();
            // O gün ya da öncesindeki en son iş günü kuru
            $rateDate = $rates->keys()->filter(fn ($d) => $d <= $day)->last();
            if (!$rateDate) {
                $rows[] = [$a->id, $day, '-', '-', $a->price_usd, 'kur yok'];
                continue;
            }
            $rate = $rates[$rateDate];
            $usd = round((float) $a->price_tl / $rate, 2);
            $diff = round($usd - (float) $a->price_usd, 2);

            if (abs($diff) >= 0.01) {
                $changed++;
                if (!$this->option('dry-run')) {
                    // saving olayını (sold_at) tetiklemeden yalnızca USD fiyatı
                    Artwork::whereKey($a->id)->update(['price_usd' => $usd]);
                }
            }
            $rows[] = [$a->id, $day, $rateDate, number_format($rate, 4, ',', '.'), number_format((float) $a->price_usd, 2, ',', '.') . ' → ' . number_format($usd, 2, ',', '.'), $diff >= 0 ? "+{$diff}" : (string) $diff];
        }

        $this->table(['Eser', 'Satış', 'Kur tarihi', 'USD/TRY', 'USD fiyatı', 'Fark'], $rows);
        $this->info(($this->option('dry-run') ? '[DRY RUN] ' : '') . "{$changed} / {$artworks->count()} eserin USD fiyatı " . ($this->option('dry-run') ? 'değişecek.' : 'güncellendi.'));
        if ($skipped->isNotEmpty()) {
            $this->warn('Satış tarihi olmadığı için atlanan: #' . $skipped->implode(', #'));
        }

        return self::SUCCESS;
    }
}
