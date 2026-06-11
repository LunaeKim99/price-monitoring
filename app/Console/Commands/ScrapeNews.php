<?php

namespace App\Console\Commands;

use App\Application\Services\NewsScraperService;
use App\Jobs\ScrapeNewsJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ScrapeNews extends Command
{
    protected $signature = 'news:scrape
                            {--dry-run : Tampilkan jumlah item tanpa menyimpan}';

    protected $description = 'Scrape berita komoditas dari RSS feed';

    public function handle(NewsScraperService $scraper): int
    {
        $lock = Cache::lock('news:scraping', 3600);

        if (!$lock->get()) {
            $this->warn('Proses scraping berita sedang berjalan.');
            return Command::FAILURE;
        }

        try {
            if ($this->option('dry-run')) {
                $this->info('[DRY-RUN] Simulasi scraping berita...');
                $result = $scraper->scrape();
                $this->line("Item ditemukan: {$result['totalFetched']}");
                $this->line("Akan disimpan: {$result['totalSaved']} (setelah filter relevansi + duplikasi)");
                $this->line("Akan diskip: {$result['totalSkipped']}");
                return Command::SUCCESS;
            }

            ScrapeNewsJob::dispatch();
            $this->info('ScrapeNewsJob didispatch untuk ' . now('Asia/Jakarta')->format('d M Y'));

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return Command::FAILURE;
        } finally {
            $lock->release();
        }
    }
}
