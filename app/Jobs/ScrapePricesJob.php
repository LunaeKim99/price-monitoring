<?php

namespace App\Jobs;

use App\Application\Services\PriceScraperService;
use App\Models\ScrapeLog;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ScrapePricesJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $uniqueFor = 7200;

    public int $tries = 2;

    public int $backoff = 300;

    private string $date;
    private string $source;
    private int $scrapeLogId;

    public function __construct(string $date, string $source, int $scrapeLogId)
    {
        $this->date = $date;
        $this->source = $source;
        $this->scrapeLogId = $scrapeLogId;
    }

    public function uniqueId(): string
    {
        return 'scrape-prices-' . $this->date . '-' . $this->source;
    }

    public function handle(PriceScraperService $scraper): void
    {
        $log = ScrapeLog::find($this->scrapeLogId);

        if (!$log) {
            Log::error('ScrapePricesJob: ScrapeLog #{id} not found.', [
                'id' => $this->scrapeLogId,
            ]);
            return;
        }

        $log->update([
            'status'     => 'running',
            'started_at' => Carbon::now(),
        ]);

        try {
            $result = $scraper->scrape(
                date: Carbon::parse($this->date),
                source: $this->source,
            );

            $log->update([
                'status'            => 'completed',
                'completed_at'      => Carbon::now(),
                'records_imported'  => $result['imported'],
                'records_skipped'   => $result['skipped'],
                'records_failed'    => $result['errors'],
            ]);

            Log::info('ScrapePricesJob: Completed.', [
                'log_id'   => $this->scrapeLogId,
                'result'   => $result,
            ]);
        } catch (\Exception $e) {
            $log->update([
                'status'        => 'failed',
                'completed_at'  => Carbon::now(),
                'error_message' => $e->getMessage(),
            ]);

            Log::error('ScrapePricesJob: Failed: {msg}', [
                'log_id' => $this->scrapeLogId,
                'msg'    => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
