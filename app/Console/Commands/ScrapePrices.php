<?php

namespace App\Console\Commands;

use App\Jobs\ScrapePricesJob;
use App\Models\ScrapeLog;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ScrapePrices extends Command
{
    protected $signature = 'prices:scrape
                           {--date= : Tanggal target (Y-m-d). Default: hari ini}
                           {--source=auto : Sumber: auto|kemendag|csv}
                           {--dry-run : Simulasi tanpa menyimpan}';

    protected $description = 'Scrape harga komoditas terbaru dari Kemendag/CSV';

    public function handle(): int
    {
        $date     = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::today();
        $source   = $this->option('source') ?? 'auto';
        $isDryRun = (bool) $this->option('dry-run');
        $dateStr  = $date->format('Y-m-d');

        $lock = Cache::lock('prices:scrape:' . $dateStr, 7200);

        if (!$lock->get()) {
            $this->warn("Proses scrape untuk {$dateStr} sedang berjalan.");
            return Command::FAILURE;
        }

        try {
            $existing = ScrapeLog::where('scrape_date', $dateStr)
                ->where('status', 'completed')
                ->first();

            if ($existing) {
                $this->warn("Data {$dateStr} sudah di-scrape (Log #{$existing->id}).");
                return Command::SUCCESS;
            }

            $log = ScrapeLog::create([
                'source'      => $source,
                'status'      => 'pending',
                'scrape_date' => $dateStr,
            ]);

            if ($isDryRun) {
                $this->info("[DRY-RUN] Akan scrape {$dateStr} dari source: {$source}");
                $log->delete();
                return Command::SUCCESS;
            }

            ScrapePricesJob::dispatch(
                date: $dateStr,
                source: $source,
                scrapeLogId: $log->id,
            );

            $this->info("Job ScrapePricesJob didispatch (Log #{$log->id}).");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return Command::FAILURE;
        } finally {
            $lock->release();
        }
    }
}
