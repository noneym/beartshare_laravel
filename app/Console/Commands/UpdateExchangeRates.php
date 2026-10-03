<?php

namespace App\Console\Commands;

use App\Models\Artwork;
use App\Models\ExchangeRate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/**
 * TCMB'den güncel USD kurunu çeker ve tüm eserlerin USD fiyatını TL fiyatından yeniden hesaplar.
 * Saatlik çalıştırılır (cronxo): php artisan rates:update
 */
class UpdateExchangeRates extends Command
{
    protected $signature = 'rates:update
        {--rate= : Kuru elle ver (TCMB\'ye gitmeden)}
        {--dry-run : Sadece kuru göster, kaydetme}';

    protected $description = 'TCMB USD kurunu çeker, eserlerin USD fiyatlarını TL fiyatı üzerinden günceller';

    protected const TCMB_URL = 'https://www.tcmb.gov.tr/kurlar/today.xml';

    public function handle(): int
    {
        [$rate, $date] = $this->option('rate')
            ? [(float) str_replace(',', '.', $this->option('rate')), now()->toDateString()]
            : $this->fetchTcmb();

        if (!$rate || $rate <= 0) {
            $this->error('Kur alınamadı.');
            return self::FAILURE;
        }

        // Hatalı veriye karşı: önceki kurdan %20'den fazla sapma varsa uygulama
        $previous = ExchangeRate::latestRate('USD');
        if ($previous && abs($rate - $previous) / $previous > 0.20 && !$this->option('rate')) {
            $this->error("Kur önceki değerden çok farklı ({$previous} → {$rate}), uygulanmadı. Gerekirse --rate ile elle verin.");
            return self::FAILURE;
        }

        $this->info("USD/TRY: {$rate} ({$date})");
        if ($this->option('dry-run')) {
            return self::SUCCESS;
        }

        $updated = DB::transaction(function () use ($rate) {
            return Artwork::query()->where('price_tl', '>', 0)->update([
                'price_usd' => DB::raw('ROUND(price_tl / ' . (float) $rate . ', 2)'),
            ]);
        });

        ExchangeRate::create([
            'currency' => 'USD',
            'rate' => $rate,
            'source' => $this->option('rate') ? 'manual' : 'tcmb',
            'rate_date' => $date,
            'artworks_updated' => $updated,
        ]);

        $this->info("{$updated} eserin USD fiyatı güncellendi.");
        return self::SUCCESS;
    }

    /** @return array{0: ?float, 1: ?string} */
    protected function fetchTcmb(): array
    {
        try {
            $res = Http::timeout(20)->retry(2, 1000)->get(self::TCMB_URL);
        } catch (\Throwable $e) {
            $this->error('TCMB isteği başarısız: ' . $e->getMessage());
            return [null, null];
        }
        if (!$res->successful()) {
            $this->error('TCMB yanıtı: HTTP ' . $res->status());
            return [null, null];
        }

        $xml = @simplexml_load_string($res->body());
        if (!$xml) {
            $this->error('TCMB XML okunamadı.');
            return [null, null];
        }

        foreach ($xml->Currency as $c) {
            if ((string) $c['CurrencyCode'] === 'USD') {
                $rate = (float) str_replace(',', '.', (string) $c->ForexSelling);
                $date = \DateTime::createFromFormat('m/d/Y', (string) $xml['Date']);
                return [$rate ?: null, $date ? $date->format('Y-m-d') : now()->toDateString()];
            }
        }

        $this->error('TCMB verisinde USD bulunamadı.');
        return [null, null];
    }
}
