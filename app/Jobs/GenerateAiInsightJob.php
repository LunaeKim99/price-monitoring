<?php

namespace App\Jobs;

use App\Application\Services\AiInsightService;
use App\Domain\Repositories\CommodityRepositoryInterface;
use App\Domain\Repositories\PredictionBatchRepositoryInterface;
use App\Domain\Repositories\PredictionRepositoryInterface;
use App\Domain\Repositories\RegionRepositoryInterface;
use App\Models\NewsArticle;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateAiInsightJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $maxAttempts = 2;

    /**
     * Backoff in seconds between retry attempts.
     */
    public array $backoff = [30, 60];

    public function __construct(
        private int $batchId
    ) {
    }

    public function handle(
        PredictionBatchRepositoryInterface $batchRepository,
        PredictionRepositoryInterface $predictionRepository,
        CommodityRepositoryInterface $commodityRepository,
        RegionRepositoryInterface $regionRepository,
        AiInsightService $aiInsightService,
    ): void {
        $batch = $batchRepository->findById($this->batchId);

        if (!$batch || $batch->getStatus() !== 'completed') {
            Log::warning('GenerateAiInsightJob: Batch tidak ditemukan atau status bukan completed.', [
                'batch_id' => $this->batchId,
                'status' => $batch?->getStatus(),
            ]);
            return;
        }

        try {
            // Load all predictions for this batch
            $predictions = $predictionRepository->findByBatchId($this->batchId);

            if ($predictions->isEmpty()) {
                $batch->setStatus('completed_with_insight');
                $batch->setAiInsight('Tidak ada data prediksi yang dihasilkan.');
                $batch->setAiInsightGeneratedAt(Carbon::now());
                $batchRepository->save($batch);
                return;
            }

            // Build lookup maps
            $commodityMap = [];
            foreach ($commodityRepository->all() as $c) {
                $commodityMap[$c->getId()] = $c->getName();
            }
            $regionMap = [];
            foreach ($regionRepository->all() as $r) {
                $regionMap[$r->getId()] = $r->getName();
            }

            // Build predictions data array for the insight service
            $predictionsData = [];
            foreach ($predictions as $prediction) {
                $predictionsData[] = [
                    'commodity_id' => $prediction->getCommodityId(),
                    'region_id' => $prediction->getRegionId(),
                    'price' => $prediction->getPredictedPrice(),
                    'date' => $prediction->getPredictedDate()->format('Y-m-d'),
                    'confidence' => $prediction->getConfidence() ?? 0,
                ];
            }

            // Load recent relevant news for context enrichment
            $newsContext = [];
            try {
                $recentNews = NewsArticle::where('is_relevant', true)
                    ->where('published_at', '>=', Carbon::now()->subDays(7))
                    ->orderBy('published_at', 'desc')
                    ->limit(10)
                    ->get();

                foreach ($recentNews as $article) {
                    $newsContext[] = [
                        'title'  => $article->title,
                        'source' => $article->source,
                    ];
                }
            } catch (\Exception $e) {
                Log::warning('GenerateAiInsightJob: Gagal memuat berita.', [
                    'message' => $e->getMessage(),
                ]);
            }

            $insight = $aiInsightService->generateInsight($predictionsData, $commodityMap, $regionMap, $newsContext);

            $batch->setAiInsight($insight);
            $batch->setAiInsightGeneratedAt(Carbon::now());
            $batch->setStatus($insight !== null ? 'completed_with_insight' : 'completed');
            $batchRepository->save($batch);

        } catch (\Exception $e) {
            Log::error('GenerateAiInsightJob: Gagal menghasilkan insight AI.', [
                'batch_id' => $this->batchId,
                'message' => $e->getMessage(),
            ]);

            // Batch stays 'completed' on failure; re-throw so Laravel retry mechanism works
            throw $e;
        }
    }
}
