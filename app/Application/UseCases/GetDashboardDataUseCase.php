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

        // Chart data: 30-day price trend (average across all commodities per day)
        $thirtyDaysAgo = (new \DateTime())->modify('-30 days');
        $now = new \DateTime();
        $allRecentRecords = $this->priceRecordRepository->getRecordsBetweenDates($thirtyDaysAgo, $now);

        // Group by date for price trend
        $dateGroups = [];
        foreach ($allRecentRecords as $record) {
            $date = $record->getRecordedDate()->format('Y-m-d');
            if (!isset($dateGroups[$date])) {
                $dateGroups[$date] = [];
            }
            $dateGroups[$date][] = $record->getPrice();
        }

        $priceTrendLabels = [];
        $priceTrendData = [];
        ksort($dateGroups);
        foreach ($dateGroups as $date => $prices) {
            $priceTrendLabels[] = $date;
            $priceTrendData[] = round(array_sum($prices) / count($prices), 2);
        }

        // Region comparison: average price per region
        $regionGroups = [];
        foreach ($allRecentRecords as $record) {
            $rid = $record->getRegionId();
            if (!isset($regionGroups[$rid])) {
                $regionGroups[$rid] = [];
            }
            $regionGroups[$rid][] = $record->getPrice();
        }

        $regionComparisonLabels = [];
        $regionComparisonData = [];
        foreach ($regionGroups as $rid => $prices) {
            $region = $this->regionRepository->findById($rid);
            $regionComparisonLabels[] = $region ? $region->getName() : 'Region ' . $rid;
            $regionComparisonData[] = round(array_sum($prices) / count($prices), 2);
        }

        return new DashboardDTO(
            totalCommodities: $commodities->count(),
            totalRegions: $regions->count(),
            totalPriceRecords: $totalPriceRecords,
            averagePrice: $averagePrice,
            latestPrices: $recentRecords,
            trendingCommodities: $trending,
            priceTrendLabels: $priceTrendLabels,
            priceTrendData: $priceTrendData,
            regionComparisonLabels: $regionComparisonLabels,
            regionComparisonData: $regionComparisonData,
        );
    }
}
