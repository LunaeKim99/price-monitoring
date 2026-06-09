<?php

namespace App\Presentation\Blocs;

use App\Application\UseCases\FilterPricesUseCase;
use App\Domain\Repositories\CommodityRepositoryInterface;
use App\Domain\Repositories\RegionRepositoryInterface;
use App\Domain\ValueObjects\PriceFilter;
use App\Presentation\ViewModels\PriceListViewModel;

class PriceFilterBloc
{
    public function __construct(
        private FilterPricesUseCase $filterPricesUseCase,
        private CommodityRepositoryInterface $commodityRepository,
        private RegionRepositoryInterface $regionRepository,
    ) {
    }

    public function getFilteredState(PriceFilter $filter): PriceListViewModel
    {
        $paginator = $this->filterPricesUseCase->execute($filter);
        $commodities = $this->commodityRepository->all();
        $regions = $this->regionRepository->all();

        return new PriceListViewModel(
            prices: $paginator,
            commodities: $commodities,
            regions: $regions,
            filters: $filter,
            totalCount: $paginator->total(),
        );
    }
}
