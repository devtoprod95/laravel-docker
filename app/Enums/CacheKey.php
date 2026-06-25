<?php

namespace App\Enums;

enum CacheKey: string
{
    case VisitorStats               = 'visitor_stats';
    case VisitorTotalUntilYesterday = 'visitor_total_until_yesterday';
    case HTTP_STATUS_COUNTS         = 'http_status_counts';

     public function forToday(): string
    {
        return $this->value . ':' . now()->toDateString();
    }
}
