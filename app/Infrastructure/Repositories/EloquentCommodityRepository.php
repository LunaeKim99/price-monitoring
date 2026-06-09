<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Commodity as DomainCommodity;
use App\Domain\Repositories\CommodityRepositoryInterface;
use App\Models\Commodity;
use Illuminate\Support\Collection;

class EloquentCommodityRepository implements CommodityRepositoryInterface
{
    public function all(): Collection
    {
        return Commodity::all()->map(fn(Commodity $model) => $this->toDomain($model));
    }

    public function findById(int $id): ?DomainCommodity
    {
        $model = Commodity::find($id);
        return $model ? $this->toDomain($model) : null;
    }

    public function findByCategory(string $category): Collection
    {
        return Commodity::where('category', $category)->get()->map(fn(Commodity $model) => $this->toDomain($model));
    }

    public function save(DomainCommodity $commodity): DomainCommodity
    {
        $data = [
            'name' => $commodity->getName(),
            'category' => $commodity->getCategory(),
            'unit' => $commodity->getUnit(),
            'is_active' => $commodity->isActive(),
        ];

        if ($commodity->getId()) {
            $model = Commodity::findOrFail($commodity->getId());
            $model->update($data);
        } else {
            $model = Commodity::create($data);
        }

        return $this->toDomain($model->fresh());
    }

    public function update(DomainCommodity $commodity): DomainCommodity
    {
        if (!$commodity->getId()) {
            throw new \LogicException('Cannot update a commodity without an existing ID.');
        }
        return $this->save($commodity);
    }

    public function delete(int $id): bool
    {
        return Commodity::destroy($id) > 0;
    }

    private function toDomain(Commodity $model): DomainCommodity
    {
        $entity = new DomainCommodity(
            name: $model->name,
            category: $model->category,
            unit: $model->unit,
            isActive: $model->is_active,
        );
        $entity->setId($model->id);
        $entity->setCreatedAt($model->created_at);
        $entity->setUpdatedAt($model->updated_at);

        return $entity;
    }
}
