<?php

namespace App\Providers;

use App\Http\Controllers\Admin\QueueController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Cloudflare/proxy arkasında istek uygulamaya HTTP olarak gelebiliyor; APP_URL https ise
        // üretilen tüm adresler https olsun (aksi halde fetch istekleri "Mixed Content" ile engellenir)
        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        // Carbon Türkçe locale ayarı
        Carbon::setLocale('tr');
        setlocale(LC_TIME, 'tr_TR.UTF-8', 'tr_TR', 'turkish', 'tr');

        // Worker canlılık sinyali (Admin > Kuyruk)
        Queue::looping(function () {
            Cache::put(QueueController::HEARTBEAT_KEY, now()->timestamp, 600);
        });
    }
}
