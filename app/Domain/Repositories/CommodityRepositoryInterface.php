<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Commodity;
use Illuminate\Support\Collection;

interface CommodityRepositoryInterface
{
    public function all(): Collection;

    public function findById(int $id): ?Commodity;

    public function findByCategory(string $category): Collection;

    public function save(Commodity $commodity): Commodity;

    public function update(Commodity $commodity): Commodity;

    public function delete(int $id): bool;
}
