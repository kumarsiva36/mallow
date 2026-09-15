<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Scheduled Queue Jobs (Every day at 12:00 AM / Midnight)
|--------------------------------------------------------------------------
|
| - Aggregate customer daily usage events into daily rollups via queue.
| - Process cycle-end billing and invoice generation for due subscriptions via queue.
|
*/

Schedule::command('usage:aggregate-daily --date=yesterday --queue')
    ->dailyAt('00:00')
    ->name('aggregate-customer-daily-usage')
    ->withoutOverlapping();

Schedule::command('billing:process-cycle --queue')
    ->dailyAt('00:00')
    ->name('process-cycle-billing')
    ->withoutOverlapping();

