<?php

namespace App\Services;

use App\Enums\CacheKey;
use App\Models\VisitorLog;
use Illuminate\Support\Facades\Cache;

class VisitorStatsService
{
    private const CACHE_TTL = 300; // 5분

    public function getStats(): array
    {
        return Cache::remember(CacheKey::VisitorStats->value, self::CACHE_TTL, function () {
            return $this->fetchFromDb();
        });
    }

    private function fetchFromDb(): array
    {
        return [
            'total_visitors'     => VisitorLog::count(),
            'today_visitors'     => VisitorLog::today()->count(),
            'yesterday_visitors' => VisitorLog::yesterday()->count(),
        ];
    }

    public function refresh(): array
    {
        Cache::forget(CacheKey::VisitorStats->value);
        return $this->getStats();
    }
}
