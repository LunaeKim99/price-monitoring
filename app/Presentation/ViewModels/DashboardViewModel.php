<?php

namespace App\Presentation\ViewModels;

class DashboardViewModel
{
    public function __construct(
        public readonly int $totalCommodities,
        public readonly int $totalRegions,
        public readonly int $totalPriceRecords,
        public readonly string $averagePrice,
        public readonly array $latestPrices,
        public readonly array $trendingCommodities,
        public readonly string $trendDirection,
        public readonly string $lastUpdated,
        public readonly array $priceTrendLabels = [],
        public readonly array $priceTrendData = [],
        public readonly array $regionComparisonLabels = [],
        public readonly array $regionComparisonData = [],
        public readonly ?string $aiInsight = null,
        public readonly ?string $aiInsightGeneratedAt = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            totalCommodities: $data['total_commodities'],
            totalRegions: $data['total_regions'],
            totalPriceRecords: $data['total_price_records'],
            averagePrice: $data['average_price'],
            latestPrices: $data['latest_prices'],
            trendingCommodities: $data['trending_commodities'],
            trendDirection: $data['trend_direction'] ?? 'stable',
            lastUpdated: $data['last_updated'] ?? now()->format('Y-m-d H:i:s'),
            priceTrendLabels: $data['price_trend_labels'] ?? [],
            priceTrendData: $data['price_trend_data'] ?? [],
            regionComparisonLabels: $data['region_comparison_labels'] ?? [],
            regionComparisonData: $data['region_comparison_data'] ?? [],
            aiInsight: $data['ai_insight'] ?? null,
            aiInsightGeneratedAt: $data['ai_insight_generated_at'] ?? null,
        );
    }
}
