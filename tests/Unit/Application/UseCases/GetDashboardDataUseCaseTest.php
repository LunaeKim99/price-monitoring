<?php

namespace Tests\Unit\Application\UseCases;

use App\Application\UseCases\GetDashboardDataUseCase;
use App\Domain\Entities\Commodity;
use App\Domain\Entities\PriceRecord;
use App\Domain\Entities\Region;
use App\Domain\Repositories\CommodityRepositoryInterface;
use App\Domain\Repositories\PriceRecordRepositoryInterface;
use App\Domain\Repositories\RegionRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GetDashboardDataUseCaseTest extends TestCase
{
    private CommodityRepositoryInterface&MockObject $commodityRepository;
    private RegionRepositoryInterface&MockObject $regionRepository;
    private PriceRecordRepositoryInterface&MockObject $priceRecordRepository;
    private GetDashboardDataUseCase $useCase;

    protected function setUp(): void
    {
        $this->commodityRepository = $this->createMock(CommodityRepositoryInterface::class);
        $this->regionRepository = $this->createMock(RegionRepositoryInterface::class);
        $this->priceRecordRepository = $this->createMock(PriceRecordRepositoryInterface::class);

        $this->useCase = new GetDashboardDataUseCase(
            $this->commodityRepository,
            $this->regionRepository,
            $this->priceRecordRepository,
        );
    }

    public function test_execute_returns_dashboard_dto_with_correct_counts(): void
    {
        $commodities = collect([
            (new Commodity('Beras'))->setId(1),
            (new Commodity('Gula'))->setId(2),
        ]);
        $regions = collect([
            (new Region('Jakarta', 'province'))->setId(1),
        ]);

        $this->commodityRepository->method('all')->willReturn($commodities);
        $this->regionRepository->method('all')->willReturn($regions);
        $this->priceRecordRepository->method('count')->willReturn(2);

        $this->priceRecordRepository->method('getLatestByCommodity')
            ->willReturnCallback(function (int $commodityId, int $limit = 10) use ($commodities) {
                if ($commodityId === 1) {
                    return collect([
                        new PriceRecord(1, 1, 16000, new \DateTime('2024-01-02')),
                    ]);
                }
                return collect([
                    new PriceRecord(2, 1, 14000, new \DateTime('2024-01-02')),
                ]);
            });

        $this->priceRecordRepository->method('getLatest')->willReturn(collect([
            new PriceRecord(1, 1, 16000, new \DateTime('2024-01-02')),
            new PriceRecord(2, 1, 14000, new \DateTime('2024-01-02')),
        ]));

        $this->priceRecordRepository->method('countByCommodity')->willReturn([1 => 10, 2 => 5]);
        $this->commodityRepository->method('findById')->willReturnCallback(function (int $id) use ($commodities) {
            return $commodities->first(fn($c) => $c->getId() === $id);
        });

        $dto = $this->useCase->execute();

        $this->assertEquals(2, $dto->totalCommodities);
        $this->assertEquals(1, $dto->totalRegions);
        $this->assertEquals(2, $dto->totalPriceRecords);
        $this->assertEquals(15000, $dto->averagePrice);
    }

    public function test_execute_with_no_data(): void
    {
        $this->commodityRepository->method('all')->willReturn(collect());
        $this->regionRepository->method('all')->willReturn(collect());
        $this->priceRecordRepository->method('count')->willReturn(0);
        $this->priceRecordRepository->method('getLatestByCommodity')->willReturn(collect());
        $this->priceRecordRepository->method('getLatest')->willReturn(collect());
        $this->priceRecordRepository->method('countByCommodity')->willReturn([]);

        $dto = $this->useCase->execute();

        $this->assertEquals(0, $dto->totalCommodities);
        $this->assertEquals(0, $dto->totalRegions);
        $this->assertEquals(0, $dto->totalPriceRecords);
        $this->assertEquals(0, $dto->averagePrice);
    }
}
