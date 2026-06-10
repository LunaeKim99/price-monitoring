<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('predictions:generate-weekly')
    ->weekly()
    ->mondays()
    ->at('02:00')
    ->withoutOverlapping(600)
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/predictions-weekly.log'));
