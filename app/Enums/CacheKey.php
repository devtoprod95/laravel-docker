<?php

namespace App\Enums;

enum CacheKey: string
{
    case VisitorStats               = 'visitor_stats';
    case VisitorTotalUntilYesterday = 'visitor_total_until_yesterday';
}
