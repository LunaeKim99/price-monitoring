<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Prediction;
use Illuminate\Support\Collection;

interface PredictionRepositoryInterface
{
    public function findByCommodityAndRegion(int $commodityId, int $regionId): Collection;

    public function save(Prediction $prediction): Prediction;

    public function delete(int $id): bool;
}
