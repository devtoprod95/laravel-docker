<?php

namespace App\Providers;

use App\Services\DashboardService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(DashboardService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Cache::rememberForever('app_started_at', fn() => now()->toDateTimeString());
    }
}
