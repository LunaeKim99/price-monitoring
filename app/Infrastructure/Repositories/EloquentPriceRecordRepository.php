<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\PriceRecord as DomainPriceRecord;
use App\Domain\Repositories\PriceRecordRepositoryInterface;
use App\Domain\ValueObjects\PriceFilter;
use App\Models\PriceRecord;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentPriceRecordRepository implements PriceRecordRepositoryInterface
{
    public function all(): Collection
    {
        return PriceRecord::all()->map(fn(PriceRecord $model) => $this->toDomain($model));
    }

    public function findById(int $id): ?DomainPriceRecord
    {
        $model = PriceRecord::find($id);
        return $model ? $this->toDomain($model) : null;
    }

    public function findByFilter(PriceFilter $filter): LengthAwarePaginator
    {
        $query = PriceRecord::query();

        if ($filter->getCommodityId()) {
            $query->where('commodity_id', $filter->getCommodityId());
        }

        if ($filter->getRegionId()) {
            $query->where('region_id', $filter->getRegionId());
        }

        if ($filter->getDateFrom()) {
            $query->whereDate('recorded_date', '>=', $filter->getDateFrom());
        }

        if ($filter->getDateTo()) {
            $query->whereDate('recorded_date', '<=', $filter->getDateTo());
        }

        $query->orderBy('recorded_date', 'desc');

        $paginator = $query->paginate(15);
        $paginator->getCollection()->transform(fn(PriceRecord $model) => $this->toDomain($model));

        return $paginator;
    }

    public function getLatestByCommodity(int $commodityId, int $limit = 10): Collection
    {
        return PriceRecord::where('commodity_id', $commodityId)
            ->orderBy('recorded_date', 'desc')
            ->limit($limit)
            ->get()
            ->map(fn(PriceRecord $model) => $this->toDomain($model));
    }

    public function getLatest(int $limit = 10): Collection
    {
        return PriceRecord::orderBy('recorded_date', 'desc')
            ->limit($limit)
            ->get()
            ->map(fn(PriceRecord $model) => $this->toDomain($model));
    }

    public function count(): int
    {
        return PriceRecord::count();
    }

    public function countByCommodity(int $limit = 5): array
    {
        return PriceRecord::selectRaw('commodity_id, COUNT(*) as total')
            ->groupBy('commodity_id')
            ->orderBy('total', 'desc')
            ->limit($limit)
            ->pluck('total', 'commodity_id')
            ->toArray();
    }

    public function save(DomainPriceRecord $record): DomainPriceRecord
    {
        $data = [
            'commodity_id' => $record->getCommodityId(),
            'region_id' => $record->getRegionId(),
            'price' => $record->getPrice(),
            'recorded_date' => $record->getRecordedDate()->format('Y-m-d'),
            'source' => $record->getSource(),
            'notes' => $record->getNotes(),
        ];

        if ($record->getId()) {
            $model = PriceRecord::findOrFail($record->getId());
            $model->update($data);
        } else {
            $model = PriceRecord::create($data);
        }

        return $this->toDomain($model->fresh());
    }

    public function update(DomainPriceRecord $record): DomainPriceRecord
    {
        if (!$record->getId()) {
            throw new \LogicException('Cannot update a price record without an existing ID.');
        }
        return $this->save($record);
    }

    public function delete(int $id): bool
    {
        return PriceRecord::destroy($id) > 0;
    }

    public function getRecordsBetweenDates(\DateTime $from, \DateTime $to): Collection
    {
        return PriceRecord::whereBetween('recorded_date', [$from->format('Y-m-d'), $to->format('Y-m-d')])
            ->orderBy('recorded_date', 'asc')
            ->get()
            ->map(fn(PriceRecord $model) => $this->toDomain($model));
    }

    public function getLatestByCommodityAndRegion(int $commodityId, int $regionId, int $limit = 60): Collection
    {
        return PriceRecord::where('commodity_id', $commodityId)
            ->where('region_id', $regionId)
            ->orderBy('recorded_date', 'desc')
            ->limit($limit)
            ->get()
            ->map(fn(PriceRecord $model) => $this->toDomain($model));
    }

    public function getAggregateStats(PriceFilter $filter): array
    {
        $query = PriceRecord::query();

        if ($filter->getCommodityId()) {
            $query->where('commodity_id', $filter->getCommodityId());
        }

        if ($filter->getRegionId()) {
            $query->where('region_id', $filter->getRegionId());
        }

        if ($filter->getDateFrom()) {
            $query->whereDate('recorded_date', '>=', $filter->getDateFrom());
        }

        if ($filter->getDateTo()) {
            $query->whereDate('recorded_date', '<=', $filter->getDateTo());
        }

        return [
            'avg' => (float) $query->avg('price') ?? 0,
            'count' => $query->count(),
            'min' => (float) $query->min('price') ?? 0,
            'max' => (float) $query->max('price') ?? 0,
        ];
    }

    private function toDomain(PriceRecord $model): DomainPriceRecord
    {
        $entity = new DomainPriceRecord(
            commodityId: $model->commodity_id,
            regionId: $model->region_id,
            price: (float) $model->price,
            recordedDate: new \DateTime($model->recorded_date),
            source: $model->source,
            notes: $model->notes,
        );
        $entity->setId($model->id);
        $entity->setCreatedAt($model->created_at);
        $entity->setUpdatedAt($model->updated_at);

        return $entity;
    }
}
