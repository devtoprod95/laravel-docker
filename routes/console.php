<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('inspire')->description('명언 배치 04시')->dailyAt('04:00');
Schedule::command('inspire')->description('명언 배치 01시')->dailyAt('01:00');
Schedule::command('inspire')->description('명언 배치 06시')->dailyAt('06:00');
Schedule::command('inspire')->description('명언 배치 02시')->dailyAt('02:00');
Schedule::command('inspire')->description('명언 배치 05시')->dailyAt('05:00');
Schedule::command('inspire')->description('명언 배치 03시')->dailyAt('03:00');
Schedule::command('inspire')->description('명언 배치 5분마다 실행')->everyFiveMinutes();
