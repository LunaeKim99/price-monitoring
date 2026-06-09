<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\PriceRecord;
use App\Domain\ValueObjects\PriceFilter;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface PriceRecordRepositoryInterface
{
    public function all(): Collection;

    public function findById(int $id): ?PriceRecord;

    public function findByFilter(PriceFilter $filter): LengthAwarePaginator;

    public function getLatestByCommodity(int $commodityId, int $limit = 10): Collection;

    public function getLatest(int $limit = 10): Collection;

    public function count(): int;

    /**
     * @return array<int, int> commodityId => count
     */
    public function countByCommodity(int $limit = 5): array;

    public function save(PriceRecord $record): PriceRecord;

    public function update(PriceRecord $record): PriceRecord;

    public function delete(int $id): bool;

    public function getAggregateStats(PriceFilter $filter): array;

    public function getRecordsBetweenDates(\DateTime $from, \DateTime $to): Collection;

    public function getLatestByCommodityAndRegion(int $commodityId, int $regionId, int $limit = 60): Collection;
}
