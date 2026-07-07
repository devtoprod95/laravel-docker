<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('cron:update_site_statistics')->description('사이트 통계 업데이트')->cron('0 0 * * *');
Schedule::command('cron:old_visitors_delete')->description('7일 전 방문자 삭제')->cron('5 0 * * *');
