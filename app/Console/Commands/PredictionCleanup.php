<?php

namespace App\Console\Commands;

use App\Domain\Repositories\PredictionBatchRepositoryInterface;
use App\Domain\Repositories\PredictionRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PredictionCleanup extends Command
{
    protected $signature = 'predictions:cleanup
                            {--dry-run : Tampilkan jumlah row yang akan dihapus tanpa benar-benar menghapus}';

    protected $description = 'Hapus data predictions lama, pertahankan hanya dari batch terbaru. Metadata prediction_batches tidak tersentuh.';

    public function handle(
        PredictionBatchRepositoryInterface $batchRepository,
        PredictionRepositoryInterface $predictionRepository,
    ): int {
        $latestBatch = $batchRepository->findLatest();

        if (!$latestBatch) {
            $this->warn('Tidak ada batch prediksi yang ditemukan.');
            return Command::SUCCESS;
        }

        $keepBatchId = $latestBatch->getId();

        $this->info("Batch terbaru: #{$keepBatchId} (status: {$latestBatch->getStatus()})");

        if ($this->option('dry-run')) {
            $count = \App\Models\Prediction::where('prediction_batch_id', '!=', $keepBatchId)
                ->whereNotNull('prediction_batch_id')
                ->count();
            $this->line("  [dry-run] {$count} row predictions dari batch lama akan dihapus.");
            $this->line("  [dry-run] Tidak ada perubahan dilakukan.");
            return Command::SUCCESS;
        }

        $deleted = $predictionRepository->deleteAllExceptBatch($keepBatchId);

        $this->info("✓ {$deleted} row predictions dari batch lama berhasil dihapus.");
        $this->info("✓ Metadata prediction_batches tidak tersentuh.");

        Log::info('predictions:cleanup: Purged old predictions.', [
            'kept_batch_id' => $keepBatchId,
            'deleted_rows'  => $deleted,
        ]);

        return Command::SUCCESS;
    }
}
