<?php

namespace App\Application\UseCases;

use App\Domain\Repositories\PriceRecordRepositoryInterface;
use Illuminate\Support\Collection;

class GetLatestPricesUseCase
{
    public function __construct(
        private PriceRecordRepositoryInterface $priceRecordRepository,
    ) {
    }

    public function execute(int $limit = 10): Collection
    {
        return $this->priceRecordRepository->getLatest($limit);
    }
}
