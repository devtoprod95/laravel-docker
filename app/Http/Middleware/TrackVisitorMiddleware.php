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
        // API, 기타 페이지 제외
        if ($request->is('api/*', '*.css', '*.js', '*.ico')) {
            return $next($request);
        }

        if (!$this->isBot($request)) {
            $this->recordVisit($request);
        }

        return $next($request);
    }

    private function recordVisit(Request $request): void
    {
        $ip    = $this->getClientIp($request);
        $today = now()->toDateString();

        $cacheKey = "visitor_ip:{$today}:{$ip}";

        if (Cache::has($cacheKey)) {
            return;
        }

        Cache::put($cacheKey, true, now()->endOfDay());

        VisitorLog::create([
            'ip'         => $ip,
            'user_agent' => $request->userAgent(),
            'visited_at' => $today,
        ]);

        Cache::forget(CacheKey::VisitorStats->value);
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
