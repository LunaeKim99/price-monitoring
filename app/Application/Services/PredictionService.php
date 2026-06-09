<?php

namespace App\Application\Services;

use App\Domain\Entities\Prediction;
use App\Domain\Repositories\PredictionRepositoryInterface;
use App\Domain\Repositories\PriceRecordRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PredictionService
{
    public function __construct(
        private PriceRecordRepositoryInterface $priceRecordRepository,
        private PredictionRepositoryInterface $predictionRepository,
    ) {
    }

    /**
     * Generate predictions using Simple Moving Average + Linear Regression.
     *
     * @param int $commodityId
     * @param int $regionId
     * @param int $days Number of days to predict (7, 14, or 30)
     * @return Prediction[]
     * @throws \RuntimeException When insufficient data
     */
    public function generatePredictions(int $commodityId, int $regionId, int $days = 7): array
    {
        return DB::transaction(function () use ($commodityId, $regionId, $days) {
            $records = $this->priceRecordRepository->getLatestByCommodityAndRegion($commodityId, $regionId, 30);

            if ($records->count() < 7) {
                throw new \RuntimeException(
                    'Data harga tidak mencukupi. Minimal 7 data harga diperlukan untuk prediksi.'
                );
            }

            $prices = [];
            foreach ($records as $record) {
                $prices[] = $record->getPrice();
            }
            $prices = array_reverse($prices); // chronological order

            $n = count($prices);

            // Simple Moving Average (last 7 data points)
            $smaPeriod = min(7, $n);
            $sma = array_sum(array_slice($prices, -$smaPeriod)) / $smaPeriod;

            // Linear Regression for trend
            $regression = $this->linearRegression($prices);

            // Standard deviation for confidence (sample std dev)
            $mean = array_sum($prices) / $n;
            $variance = 0;
            foreach ($prices as $p) {
                $variance += ($p - $mean) ** 2;
            }
            $stdDev = $n > 1 ? sqrt($variance / ($n - 1)) : 0;
            $confidence = $mean > 0 ? max(0, min(1, 1 - ($stdDev / $mean))) : 0.5;

            $predictions = [];
            $lastDate = $this->getLastRecordDate($records);

            // Delete old predictions for this commodity+region
            $oldPredictions = $this->predictionRepository->findByCommodityAndRegion($commodityId, $regionId);
            foreach ($oldPredictions as $old) {
                $this->predictionRepository->delete($old->getId());
            }

            for ($day = 1; $day <= $days; $day++) {
                $predictedDate = (clone $lastDate)->modify("+{$day} days");
                $predictedPrice = $sma + ($regression['slope'] * ($n + $day));

                if ($predictedPrice < 0) {
                    $predictedPrice = $sma * 0.5; // floor at 50% of SMA
                }

                $prediction = new Prediction(
                    commodityId: $commodityId,
                    regionId: $regionId,
                    predictedPrice: round($predictedPrice, 2),
                    predictedDate: $predictedDate,
                    confidence: round($confidence, 2),
                    modelName: 'SMA-LinearRegression'
                );

                $stored = $this->predictionRepository->save($prediction);
                $predictions[] = $stored;
            }

            return $predictions;
        });
    }

    private function linearRegression(array $prices): array
    {
        $n = count($prices);
        $xMean = ($n - 1) / 2;
        $yMean = array_sum($prices) / $n;

        $numerator = 0;
        $denominator = 0;

        foreach ($prices as $x => $y) {
            $xDiff = $x - $xMean;
            $yDiff = $y - $yMean;
            $numerator += $xDiff * $yDiff;
            $denominator += $xDiff * $xDiff;
        }

        $slope = $denominator > 0 ? $numerator / $denominator : 0;
        $intercept = $yMean - ($slope * $xMean);

        return ['slope' => $slope, 'intercept' => $intercept];
    }

    private function getLastRecordDate(Collection $records): \DateTime
    {
        $latest = null;
        foreach ($records as $record) {
            $date = $record->getRecordedDate();
            if ($latest === null || $date > $latest) {
                $latest = $date;
            }
        }
        // $records is guaranteed non-empty by caller
        return $latest;
    }
}
