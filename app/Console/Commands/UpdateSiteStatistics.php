<?php

namespace App\Console\Commands;

use App\Models\SiteStatistic;
use App\Models\VisitorLog;
use Illuminate\Console\Command;

class UpdateSiteStatistics extends Command
{
    protected $signature   = 'cron:update_site_statistics';
    protected $description = '사이트 통계 업데이트';

    public function handle()
    {
        $yesterday = now()->subDay()->toDateString();
        $weekAgo   = now()->subDays(7)->toDateString();
        $today     = now()->toDateString();

        $yesterdayCount = VisitorLog::whereDate('visited_at', $yesterday)
            ->distinct('ip')
            ->count('ip');

        $weeklyCount = VisitorLog::whereDate('visited_at', '>=', $weekAgo)
            ->whereDate('visited_at', '<', $today)
            ->distinct('ip')
            ->count('ip');

        $stat = SiteStatistic::firstOrCreate();
        $stat->increment('total_visitors', $yesterdayCount);
        $stat->update([
            'weekly_visitors'    => $weeklyCount,
            'yesterday_visitors' => $yesterdayCount,
        ]);
    }
}
