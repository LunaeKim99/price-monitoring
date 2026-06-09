<?php

namespace App\Application\DTOs;

class DashboardDTO
{
    public function __construct(
        public readonly int $totalCommodities,
        public readonly int $totalRegions,
        public readonly int $totalPriceRecords,
        public readonly float $averagePrice,
        public readonly array $latestPrices,
        public readonly array $trendingCommodities,
        public readonly array $priceTrendLabels = [],
        public readonly array $priceTrendData = [],
        public readonly array $regionComparisonLabels = [],
        public readonly array $regionComparisonData = [],
    ) {
    }
}
