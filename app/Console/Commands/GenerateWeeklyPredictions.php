<?php

namespace App\Console\Commands;

use App\Domain\Entities\PredictionBatch;
use App\Domain\Repositories\PredictionBatchRepositoryInterface;
use App\Jobs\ProcessWeeklyPredictionsJob;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class GenerateWeeklyPredictions extends Command
{
    protected $signature = 'predictions:generate-weekly';

    protected $description = 'Generate weekly predictions for all commodity-region pairs';

    public function handle(PredictionBatchRepositoryInterface $batchRepository): int
    {
        $lock = Cache::lock('predictions:weekly:running', 600);

        if (!$lock->get()) {
            $this->warn('Proses prediksi mingguan sedang berjalan. Coba lagi nanti.');
            return Command::FAILURE;
        }

        try {
            // Check if a completed batch already exists from the last 7 days
            $latestBatch = $batchRepository->findLatest();

            if ($latestBatch && $latestBatch->getCreatedAt()) {
                $createdAt = Carbon::parse($latestBatch->getCreatedAt());
                if ($createdAt->diffInDays(Carbon::now()) < 7 && in_array($latestBatch->getStatus(), ['completed', 'completed_with_insight'])) {
                    $this->warn('Batch prediksi mingguan sudah tersedia dalam 7 hari terakhir. Batch #' . $latestBatch->getId() . ' dengan status: ' . $latestBatch->getStatus());
                    return Command::SUCCESS;
                }
            }

            // Create new batch
            $batch = new PredictionBatch(status: 'pending');
            $batch = $batchRepository->save($batch);

            $this->info('Batch prediksi #' . $batch->getId() . ' dibuat. Memproses...');

            // Dispatch the job
            ProcessWeeklyPredictionsJob::dispatch($batch->getId());

            $this->info('Job ProcessWeeklyPredictionsJob didispatch untuk batch #' . $batch->getId() . '.');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Gagal membuat batch prediksi: ' . $e->getMessage());

            return Command::FAILURE;
        } finally {
            $lock->release();
        }
    }
}
