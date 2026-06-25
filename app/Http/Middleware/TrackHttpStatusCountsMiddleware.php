<?php

namespace App\Http\Middleware;

use App\Enums\CacheKey;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\Response;

class TrackHttpStatusCountsMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        if ($this->shouldSkip($request)) {
            return;
        }

        $key    = CacheKey::HTTP_STATUS_COUNTS->forToday();
        $status = (string) $response->getStatusCode();
        Redis::hincrby($key, $status, 1);

        // 자정 넘으면 자동 소멸 (오늘 자정까지 남은 초)
        $ttl = now()->secondsUntilEndOfDay();
        Redis::expire($key, $ttl);
    }

    private function shouldSkip(Request $request): bool
    {
        $excludeExtensions = [
            'ico', 'png', 'jpg', 'jpeg', 'gif', 'svg',
            'css', 'js', 'map', 'woff', 'woff2', 'ttf', 'otf',
            'txt', 'xml'
        ];

        $path = $request->path();

        if (str_starts_with($path, '_debugbar')) {
            return true;
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION);
        if (in_array(strtolower($extension), $excludeExtensions)) {
            return true;
        }

        if (str_contains($path, 'favicon.ico')) {
            return true;
        }

        if (!$request->route()) {
            return true;
        }

        return false;
    }

}
