<?php

namespace App\Jobs;

use App\Application\Services\PredictionService;
use App\Domain\Repositories\CommodityRepositoryInterface;
use App\Domain\Repositories\PredictionBatchRepositoryInterface;
use App\Domain\Repositories\PredictionRepositoryInterface;
use App\Domain\Repositories\RegionRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessWeeklyPredictionsJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $uniqueFor = 3600;

    public int $maxAttempts = 1;

    public function __construct(
        private int $batchId
    ) {
    }

    public function uniqueId(): string
    {
        return 'weekly-predictions-' . $this->batchId;
    }

    public function handle(
        PredictionBatchRepositoryInterface $batchRepository,
        CommodityRepositoryInterface $commodityRepository,
        RegionRepositoryInterface $regionRepository,
        PredictionService $predictionService,
        PredictionRepositoryInterface $predictionRepository,
    ): void {
        $batch = $batchRepository->findById($this->batchId);

        if (!$batch) {
            Log::error('ProcessWeeklyPredictionsJob: Batch tidak ditemukan.', ['batch_id' => $this->batchId]);
            return;
        }

        // Mark as processing
        $batch->setStatus('processing');
        $batch->setStartedAt(Carbon::now());
        $batchRepository->save($batch);

        try {
            $commodities = $commodityRepository->all();
            $regions = $regionRepository->all();

            $totalPairs = $commodities->count() * $regions->count();
            $batch->setTotalPairs($totalPairs);
            $batchRepository->save($batch);

            $totalPredictions = 0;

            foreach ($commodities as $commodity) {
                foreach ($regions as $region) {
                    try {
                        $predictions = $predictionService->generatePredictions(
                            commodityId: $commodity->getId(),
                            regionId: $region->getId(),
                            days: 7,
                            predictionBatchId: $this->batchId
                        );

                        $totalPredictions += count($predictions);
                    } catch (\RuntimeException $e) {
                        Log::warning('ProcessWeeklyPredictionsJob: Gagal generate prediksi.', [
                            'commodity_id' => $commodity->getId(),
                            'region_id' => $region->getId(),
                            'message' => $e->getMessage(),
                        ]);
                    }

                    $batch->setProcessedPairs($batch->getProcessedPairs() + 1);
                    \App\Models\PredictionBatch::where('id', $this->batchId)->increment('processed_pairs');
                }
            }

            $batch->refresh();

            // Mark as completed
            $batch->setTotalPredictions($totalPredictions);
            $batch->setCompletedAt(Carbon::now());
            $batch->setStatus('completed');
            $batchRepository->save($batch);

            // Dispatch AI insight generation
            GenerateAiInsightJob::dispatch($this->batchId);

            // Purge predictions dari batch lama (hemat storage)
            $deleted = $predictionRepository->deleteAllExceptBatch($this->batchId);
            Log::info('ProcessWeeklyPredictionsJob: Purged old predictions.', [
                'kept_batch_id' => $this->batchId,
                'deleted_rows'  => $deleted,
            ]);

        } catch (\Exception $e) {
            Log::error('ProcessWeeklyPredictionsJob: Gagal memproses batch.', [
                'batch_id' => $this->batchId,
                'message' => $e->getMessage(),
            ]);

            $batch->setStatus('failed');
            $batch->setErrorMessage($e->getMessage());
            $batchRepository->save($batch);
        }
    }
}
