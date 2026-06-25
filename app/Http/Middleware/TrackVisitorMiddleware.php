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
        $ip    = $request->ip();
        $today = now()->toDateString();

        // Redis에 오늘 이 IP가 방문했는지 체크
        $cacheKey = "visitor_ip:{$today}:{$ip}";

        if (Cache::has($cacheKey)) {
            return; // 오늘 이미 방문한 IP → 스킵
        }

        // Redis에 기록 (자정까지 유지)
        Cache::put($cacheKey, true, now()->endOfDay());

        // DB에 저장
        VisitorLog::create([
            'ip'         => $ip,
            'user_agent' => $request->userAgent(),
            'visited_at' => $today,
        ]);

        // 통계 캐시 무효화 (다음 조회 시 갱신되도록)
        Cache::forget(CacheKey::VisitorStats->value);
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
