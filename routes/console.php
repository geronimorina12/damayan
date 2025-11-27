<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('daily:task', function () {
    $this->call(\App\Console\Commands\DailyTask::class);
});

Artisan::command('is:active', function () {
    $this->call(\App\Console\Commands\IsActive::class);
});

// ⬇⬇ The new schedule for Laravel 11
Schedule::command('daily:task')->daily();
Schedule::command('is:active')->daily();
