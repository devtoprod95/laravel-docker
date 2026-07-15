<?php

namespace App\Providers;

use App\Enums\Role;
use App\Models\Admin;
use App\Services\DashboardService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

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
        Paginator::useBootstrapFive();
        Cache::rememberForever('app_started_at', fn() => now()->toDateTimeString());

        Gate::define('viewLogViewer', function (Admin $admin) {
            $request = request();

            // 원격 프록시(로컬 서버)에서 온 토큰 요청은 허용
            if ($request->bearerToken() && hash_equals(env('LOG_VIEWER_TOKEN', ''), $request->bearerToken())) {
                return true;
            }

            $flag = $admin && $admin->hasRole(Role::SuperAdmin->value);
            return $flag;
        });
    }
}
