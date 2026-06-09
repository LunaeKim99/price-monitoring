<?php

namespace App\Application\UseCases;

use App\Application\DTOs\DashboardDTO;
use App\Domain\Repositories\CommodityRepositoryInterface;
use App\Domain\Repositories\PriceRecordRepositoryInterface;
use App\Domain\Repositories\RegionRepositoryInterface;

class GetDashboardDataUseCase
{
    public function __construct(
        private CommodityRepositoryInterface $commodityRepository,
        private RegionRepositoryInterface $regionRepository,
        private PriceRecordRepositoryInterface $priceRecordRepository,
    ) {
    }

    public function execute(): DashboardDTO
    {
        $commodities = $this->commodityRepository->all();
        $regions = $this->regionRepository->all();

        $totalPriceRecords = $this->priceRecordRepository->count();

        // Calculate average price from latest records of each commodity
        $latestPrices = [];
        foreach ($commodities as $commodity) {
            $records = $this->priceRecordRepository->getLatestByCommodity($commodity->getId(), 1);
            if ($records->isNotEmpty()) {
                $latestPrices[] = $records->first();
            }
        }

        $averagePrice = 0;
        if (count($latestPrices) > 0) {
            $averagePrice = array_sum(array_map(fn($r) => $r->getPrice(), $latestPrices)) / count($latestPrices);
        }

        // Get all latest records (last 10 overall)
        $recentRecords = $this->priceRecordRepository->getLatest(10)->toArray();

        // Trending commodities: those with most data points
        $topCommodityIds = $this->priceRecordRepository->countByCommodity(5);
        $trending = [];
        foreach ($topCommodityIds as $commodityId => $count) {
            $commodity = $this->commodityRepository->findById($commodityId);
            if ($commodity) {
                $trending[] = $commodity;
            }
        }

        return new DashboardDTO(
            totalCommodities: $commodities->count(),
            totalRegions: $regions->count(),
            totalPriceRecords: $totalPriceRecords,
            averagePrice: $averagePrice,
            latestPrices: $recentRecords,
            trendingCommodities: $trending,
        );
    }
}
