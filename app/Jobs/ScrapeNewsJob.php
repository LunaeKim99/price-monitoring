<?php

namespace App\Jobs;

use App\Application\Services\NewsScraperService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ScrapeNewsJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $uniqueFor = 3600;
    public int $tries     = 2;
    public int $backoff   = 300;

    private string $scrapeDate;

    public function __construct()
    {
        $this->scrapeDate = Carbon::now('Asia/Jakarta')->format('Y-m-d');
    }

    public function uniqueId(): string
    {
        return 'scrape-news-' . $this->scrapeDate;
    }

    public function handle(NewsScraperService $scraper): void
    {
        Log::info('ScrapeNewsJob: Mulai scraping berita.', ['date' => $this->scrapeDate]);

        try {
            $result = $scraper->scrape();

            Log::info('ScrapeNewsJob: Selesai.', [
                'date'    => $this->scrapeDate,
                'sources' => $result['sources'],
                'fetched' => $result['totalFetched'],
                'saved'   => $result['totalSaved'],
                'skipped' => $result['totalSkipped'],
                'errors'  => $result['totalErrors'],
            ]);
        } catch (\Exception $e) {
            Log::error('ScrapeNewsJob: Gagal: {msg}', ['msg' => $e->getMessage()]);
            throw $e;
        }
    }
}
