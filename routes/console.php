<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Step 0 — Scrape berita komoditas (setiap hari 01:00 WIB)
Schedule::command('news:scrape')
    ->dailyAt('01:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping(3600)
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/news-scrape.log'));

// Step 1 — Scrape harga terbaru (Senin 02:00 WIB)
Schedule::command('prices:scrape')
    ->weeklyOn(1, '02:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping(7200)
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/prices-scrape.log'));

// Step 2 — Generate prediksi mingguan (Senin 02:30 WIB)
Schedule::command('predictions:generate-weekly')
    ->weeklyOn(1, '02:30')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping(600)
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/predictions-weekly.log'));
