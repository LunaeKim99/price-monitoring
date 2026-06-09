<?php

namespace App\Presentation\Blocs;

use App\Application\Services\PriceCalculationService;
use App\Application\UseCases\GetDashboardDataUseCase;
use App\Presentation\ViewModels\DashboardViewModel;

class DashboardBloc
{
    public function __construct(
        private GetDashboardDataUseCase $getDashboardDataUseCase,
        private PriceCalculationService $priceCalculationService,
    ) {
    }

    public function getState(): DashboardViewModel
    {
        $dto = $this->getDashboardDataUseCase->execute();

        $prices = array_map(fn($record) => $record->getPrice(), $dto->latestPrices);
        $trend = $this->priceCalculationService->calculateTrend($prices);

        $latestPricesData = array_map(function ($record) {
            return (object) [
                'id' => $record->getId(),
                'commodity_id' => $record->getCommodityId(),
                'region_id' => $record->getRegionId(),
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

        return DashboardViewModel::fromArray([
            'total_commodities' => $dto->totalCommodities,
            'total_regions' => $dto->totalRegions,
            'total_price_records' => $dto->totalPriceRecords,
            'average_price' => $this->priceCalculationService->formatPrice($dto->averagePrice),
            'latest_prices' => $latestPricesData,
            'trending_commodities' => $trendingData,
            'trend_direction' => $trend,
            'last_updated' => now()->format('Y-m-d H:i:s'),
        ]);
    }
}
