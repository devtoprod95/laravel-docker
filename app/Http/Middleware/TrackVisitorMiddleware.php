<?php

namespace App\Http\Middleware;

use App\Enums\CacheKey;
use App\Models\VisitorLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TrackVisitorMiddleware
{
    private array $botKeywords = [
        'bot', 'crawler', 'spider', 'curl', 'wget',
        'slurp', 'bingbot', 'googlebot', 'yandex',
    ];

    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('GET')) {
            if (!$request->is('api/*', '*.css', '*.js', '*.ico', '*.map',
                '_debugbar*', '.well-known*') &&
                !$request->expectsJson() &&
                $request->header('X-Requested-With') !== 'XMLHttpRequest') {
                if (!$this->isBot($request)) {
                    $this->recordVisit($request);
                }
            }
        }

        return $next($request);
    }

    private function recordVisit(Request $request): void
    {
        $ip       = $this->getClientIp($request);
        $today    = now()->toDateString();
        $cacheKey = "visitor_ip:{$today}:{$ip}";

        if (Cache::has($cacheKey)) {
            return;
        }

        VisitorLog::create([
            'ip'         => $ip,
            'user_agent' => $request->userAgent(),
            'visited_at' => $today,
        ]);

        Cache::put($cacheKey, true, now()->endOfDay());
        $stats = Cache::get(CacheKey::VisitorStats->value);
        if ($stats) {
            $stats['total_visitors']++;
            $stats['today_visitors']++;
            Cache::put(CacheKey::VisitorStats->value, $stats, now()->endOfDay());
        }
    }

    private function getClientIp(Request $request): string
    {
        $forwardedFor = $request->header('X-Forwarded-For');
        if ($forwardedFor) {
            $ip = trim(explode(',', $forwardedFor)[0] ?? '');
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }

        return $request->ip();
    }

    private function isBot(Request $request): bool
    {
        $ua = strtolower($request->userAgent() ?? '');

        foreach ($this->botKeywords as $keyword) {
            if (str_contains($ua, $keyword)) {
                return true;
            }
        }

        return false;
    }
}
