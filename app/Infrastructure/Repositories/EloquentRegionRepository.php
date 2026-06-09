<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Region as DomainRegion;
use App\Domain\Repositories\RegionRepositoryInterface;
use App\Models\Region;
use Illuminate\Support\Collection;

class EloquentRegionRepository implements RegionRepositoryInterface
{
    public function all(): Collection
    {
        return Region::all()->map(fn(Region $model) => $this->toDomain($model));
    }

    public function findById(int $id): ?DomainRegion
    {
        $model = Region::find($id);
        return $model ? $this->toDomain($model) : null;
    }

    public function findByType(string $type): Collection
    {
        return Region::where('type', $type)->get()->map(fn(Region $model) => $this->toDomain($model));
    }

    public function save(DomainRegion $region): DomainRegion
    {
        $data = [
            'name' => $region->getName(),
            'type' => $region->getType(),
            'parent_id' => $region->getParentId(),
        ];

        if ($region->getId()) {
            $model = Region::findOrFail($region->getId());
            $model->update($data);
        } else {
            $model = Region::create($data);
        }

        return $this->toDomain($model->fresh());
    }

    public function update(DomainRegion $region): DomainRegion
    {
        if (!$region->getId()) {
            throw new \LogicException('Cannot update a region without an existing ID.');
        }
        return $this->save($region);
    }

    public function delete(int $id): bool
    {
        return Region::destroy($id) > 0;
    }

    private function toDomain(Region $model): DomainRegion
    {
        $entity = new DomainRegion(
            name: $model->name,
            type: $model->type,
            parentId: $model->parent_id,
        );
        $entity->setId($model->id);
        $entity->setCreatedAt($model->created_at);
        $entity->setUpdatedAt($model->updated_at);

        return $entity;
    }
}
