<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Prediction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface PredictionRepositoryInterface
{
    public function all(): Collection;

    public function paginate(int $perPage = 15, ?int $commodityId = null, ?int $regionId = null): LengthAwarePaginator;

    public function findByCommodityAndRegion(int $commodityId, int $regionId): Collection;

    public function save(Prediction $prediction): Prediction;

    public function delete(int $id): bool;

    public function findByBatchId(int $batchId): Collection;

    public function deleteByCommodityAndRegion(int $commodityId, int $regionId): void;
}
