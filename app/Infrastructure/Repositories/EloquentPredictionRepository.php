<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Prediction as DomainPrediction;
use App\Domain\Repositories\PredictionRepositoryInterface;
use App\Models\Prediction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentPredictionRepository implements PredictionRepositoryInterface
{
    public function all(): Collection
    {
        return Prediction::orderBy('created_at', 'desc')
            ->get()
            ->map(fn(Prediction $model) => $this->toDomain($model));
    }

    public function paginate(int $perPage = 15, ?int $commodityId = null, ?int $regionId = null): LengthAwarePaginator
    {
        $query = Prediction::orderBy('created_at', 'desc');

        if ($commodityId !== null) {
            $query->where('commodity_id', $commodityId);
        }
        if ($regionId !== null) {
            $query->where('region_id', $regionId);
        }

        $paginator = $query->paginate($perPage);
        $paginator->getCollection()->transform(fn(Prediction $model) => $this->toDomain($model));

        return $paginator;
    }

    public function findByCommodityAndRegion(int $commodityId, int $regionId): Collection
    {
        return Prediction::where('commodity_id', $commodityId)
            ->where('region_id', $regionId)
            ->orderBy('predicted_date', 'desc')
            ->get()
            ->map(fn(Prediction $model) => $this->toDomain($model));
    }

    public function findByBatchId(int $batchId): Collection
    {
        return Prediction::where('prediction_batch_id', $batchId)
            ->orderBy('predicted_date', 'desc')
            ->get()
            ->map(fn(Prediction $model) => $this->toDomain($model));
    }

    public function deleteByCommodityAndRegion(int $commodityId, int $regionId): void
    {
        Prediction::where('commodity_id', $commodityId)
            ->where('region_id', $regionId)
            ->whereNull('prediction_batch_id')
            ->delete();
    }

    public function deleteAllExceptBatch(int $keepBatchId): int
    {
        return Prediction::where('prediction_batch_id', '!=', $keepBatchId)
            ->whereNotNull('prediction_batch_id')
            ->delete();
    }

    public function save(DomainPrediction $prediction): DomainPrediction
    {
        $data = [
            'commodity_id' => $prediction->getCommodityId(),
            'region_id' => $prediction->getRegionId(),
            'predicted_price' => $prediction->getPredictedPrice(),
            'predicted_date' => $prediction->getPredictedDate()->format('Y-m-d'),
            'confidence' => $prediction->getConfidence(),
            'model_name' => $prediction->getModelName(),
            'prediction_batch_id' => $prediction->getPredictionBatchId(),
        ];

        if ($prediction->getId()) {
            $model = Prediction::findOrFail($prediction->getId());
            $model->update($data);
        } else {
            $model = Prediction::create($data);
        }

        return $this->toDomain($model->fresh());
    }

    public function delete(int $id): bool
    {
        return Prediction::destroy($id) > 0;
    }

    private function toDomain(Prediction $model): DomainPrediction
    {
        $entity = new DomainPrediction(
            commodityId: $model->commodity_id,
            regionId: $model->region_id,
            predictedPrice: (float) $model->predicted_price,
            predictedDate: new \DateTime($model->predicted_date),
            confidence: $model->confidence !== null ? (float) $model->confidence : null,
            modelName: $model->model_name,
        );
        $entity->setId($model->id);
        $entity->setPredictionBatchId($model->prediction_batch_id);
        $entity->setCreatedAt($model->created_at);
        $entity->setUpdatedAt($model->updated_at);

        return $entity;
    }
}
