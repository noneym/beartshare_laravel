<?php

namespace App\Console\Commands;

use App\Support\ImageUrlMap;
use App\Support\LegacyArtTerms;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportLegacyArtTerms extends Command
{
    protected $signature = 'legacy:art-terms {--images : Ardından görselleri R2\'ye taşı (images:migrate --only=art-terms)}';

    protected $description = 'Eski sistemdeki sanat terimlerini (art-terms) yeni sisteme aktarır';

    public function handle(): int
    {
        $count = LegacyArtTerms::import(DB::connection('legacy'), fn ($url) => $url ? (ImageUrlMap::get($url) ?? $url) : null,
            DB::table('art_terms')->pluck('image', 'id')->all());
        $this->info("Sanat terimleri: {$count}");

        if ($this->option('images')) {
            $this->call('images:migrate', ['--only' => 'art-terms']);
        }

        return self::SUCCESS;
    }
}
