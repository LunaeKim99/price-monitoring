<?php

namespace App\Application\Services;

use App\Domain\Repositories\PriceRecordRepositoryInterface;

class TrendAnalysisService
{
    public function __construct(
        private PriceRecordRepositoryInterface $priceRecordRepository,
    ) {
    }

    public function analyzeByCommodity(int $commodityId, int $days = 30): array
    {
        $records = $this->priceRecordRepository->getLatestByCommodity($commodityId, $days);

        if ($records->isEmpty()) {
            return [
                'min' => 0,
                'max' => 0,
                'avg' => 0,
                'trend' => 'stable',
                'count' => 0,
            ];
        }

        $prices = $records->map(fn($r) => $r->getPrice())->toArray();

        $calculationService = new PriceCalculationService();
        $trend = $calculationService->calculateTrend($prices);

        return [
            'min' => min($prices),
            'max' => max($prices),
            'avg' => $calculationService->calculateAverage($prices),
            'trend' => $trend,
            'count' => count($prices),
        ];
    }
}
