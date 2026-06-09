<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Region;
use Illuminate\Support\Collection;

interface RegionRepositoryInterface
{
    public function all(): Collection;

    public function findById(int $id): ?Region;

    public function findByType(string $type): Collection;

    public function save(Region $region): Region;

    public function update(Region $region): Region;

    public function delete(int $id): bool;
}
