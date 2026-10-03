<?php

namespace App\Providers;

use App\Http\Controllers\Admin\QueueController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
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
        // Carbon Türkçe locale ayarı
        Carbon::setLocale('tr');
        setlocale(LC_TIME, 'tr_TR.UTF-8', 'tr_TR', 'turkish', 'tr');

        // Worker canlılık sinyali (Admin > Kuyruk)
        Queue::looping(function () {
            Cache::put(QueueController::HEARTBEAT_KEY, now()->timestamp, 600);
        });
    }
}
