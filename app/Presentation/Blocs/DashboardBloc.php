<?php

namespace App\Presentation\Blocs;

use App\Application\Services\AiInsightService;
use App\Application\Services\PriceCalculationService;
use App\Application\UseCases\GetDashboardDataUseCase;
use App\Domain\Repositories\CommodityRepositoryInterface;
use App\Domain\Repositories\RegionRepositoryInterface;
use App\Presentation\ViewModels\DashboardViewModel;
use Illuminate\Support\Facades\Cache;

class DashboardBloc
{
    public function __construct(
        private GetDashboardDataUseCase $getDashboardDataUseCase,
        private PriceCalculationService $priceCalculationService,
        private CommodityRepositoryInterface $commodityRepository,
        private RegionRepositoryInterface $regionRepository,
        private AiInsightService $aiInsightService,
    ) {
    }

    public function getState(): DashboardViewModel
    {
        $dto = $this->getDashboardDataUseCase->execute();

        $prices = array_map(fn($record) => $record->getPrice(), $dto->latestPrices);
        $trend = $this->priceCalculationService->calculateTrend($prices);

        $allCommoditiesCollection = $this->commodityRepository->all();
        $commodityMap = [];
        foreach ($allCommoditiesCollection as $c) {
            $commodityMap[$c->getId()] = $c->getName();
        }
        $regionMap = [];
        foreach ($this->regionRepository->all() as $r) {
            $regionMap[$r->getId()] = $r->getName();
        }

        $latestPricesData = array_map(function ($record) use ($commodityMap, $regionMap) {
            return (object) [
                'id' => $record->getId(),
                'commodity_id' => $record->getCommodityId(),
                'region_id' => $record->getRegionId(),
                'commodity_name' => $commodityMap[$record->getCommodityId()] ?? 'Unknown',
                'region_name' => $regionMap[$record->getRegionId()] ?? 'Unknown',
                'price' => $this->priceCalculationService->formatPrice($record->getPrice()),
                'price_raw' => $record->getPrice(),
                'recorded_date' => $record->getRecordedDate()->format('Y-m-d'),
                'source' => $record->getSource(),
            ];
        }, $dto->latestPrices);

        $trendingData = array_map(function ($commodity) {
            return (object) [
                'id' => $commodity->getId(),
                'name' => $commodity->getName(),
                'category' => $commodity->getCategory(),
                'unit' => $commodity->getUnit(),
            ];
        }, $dto->trendingCommodities);

        // --- AI Market Insight (cache-first, fallback jika tidak ada cache) ---
        $trendingNames = array_map(fn($c) => $c->getName(), $dto->trendingCommodities);

        $insight = null;
        $generatedAt = null;
        $aiStatus = 'ok';

        $cached = Cache::get('dashboard_ai_insight');
        if ($cached) {
            $insight = $cached['insight'];
            $generatedAt = $cached['generated_at'];
            $aiStatus = 'cached';
        } elseif (empty(config('services.groq.api_key', ''))) {
            $aiStatus = 'no_key';
        } else {
            try {
                $fresh = $this->aiInsightService->generateDashboardInsight([
                    'total_commodities' => $dto->totalCommodities,
                    'total_regions' => $dto->totalRegions,
                    'total_price_records' => $dto->totalPriceRecords,
                    'average_price' => number_format($dto->averagePrice, 0, ',', '.'),
                    'trend_direction' => $trend,
                    'trending_commodities' => $trendingNames,
                    'price_trend_labels' => $dto->priceTrendLabels,
                    'price_trend_data' => $dto->priceTrendData,
                    'region_comparison_labels' => $dto->regionComparisonLabels,
                    'region_comparison_data' => $dto->regionComparisonData,
                    'latest_prices' => array_map(fn($r) => [
                        'commodity' => $commodityMap[$r->getCommodityId()] ?? 'Unknown',
                        'region' => $regionMap[$r->getRegionId()] ?? 'Unknown',
                        'price' => $r->getPrice(),
                    ], $dto->latestPrices),
                ]);

                if ($fresh !== null) {
                    $insight = $fresh;
                    $generatedAt = now()->format('Y-m-d H:i:s');
                    Cache::put('dashboard_ai_insight', ['insight' => $insight, 'generated_at' => $generatedAt], 3600);
                    $aiStatus = 'ok';
                } else {
                    $aiStatus = 'failed';
                }
            } catch (\Throwable $e) {
                $aiStatus = 'failed';
            }
        }

        return DashboardViewModel::fromArray([
            'total_commodities' => $dto->totalCommodities,
            'total_regions' => $dto->totalRegions,
            'total_price_records' => $dto->totalPriceRecords,
            'average_price' => $this->priceCalculationService->formatPrice($dto->averagePrice),
            'latest_prices' => $latestPricesData,
            'trending_commodities' => $trendingData,
            'trend_direction' => $trend,
            'last_updated' => now()->format('Y-m-d H:i:s'),
            'price_trend_labels' => $dto->priceTrendLabels,
            'price_trend_data' => $dto->priceTrendData,
            'region_comparison_labels' => $dto->regionComparisonLabels,
            'region_comparison_data' => $dto->regionComparisonData,
            'ai_insight' => $insight,
            'ai_insight_generated_at' => $generatedAt,
            'ai_status' => $aiStatus,
            'all_commodities' => $allCommoditiesCollection->map(fn($c) => [
                'id' => $c->getId(),
                'name' => $c->getName(),
            ])->values()->toArray(),
        ]);
    }
}
