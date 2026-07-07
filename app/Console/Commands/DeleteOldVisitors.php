<?php

namespace App\Console\Commands;

use App\Models\VisitorLog;
use Illuminate\Console\Command;

class DeleteOldVisitors extends Command
{
    protected $signature   = 'cron:old_visitors_delete';
    protected $description = '7일 전 방문자 삭제';

    public function handle()
    {
        VisitorLog::where('visited_at', '<', now()->subDays(7)->toDateString())->delete();
    }
}
