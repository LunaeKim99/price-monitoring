<?php

namespace App\Application\UseCases;

use App\Domain\Repositories\PriceRecordRepositoryInterface;
use App\Domain\ValueObjects\PriceFilter;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FilterPricesUseCase
{
    public function __construct(
        private PriceRecordRepositoryInterface $priceRecordRepository,
    ) {
    }

    public function execute(PriceFilter $filter): LengthAwarePaginator
    {
        return $this->priceRecordRepository->findByFilter($filter);
    }
}
