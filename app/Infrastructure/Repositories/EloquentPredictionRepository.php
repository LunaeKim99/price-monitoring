<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Prediction as DomainPrediction;
use App\Domain\Repositories\PredictionRepositoryInterface;
use App\Models\Prediction;
use Illuminate\Support\Collection;

class EloquentPredictionRepository implements PredictionRepositoryInterface
{
    public function findByCommodityAndRegion(int $commodityId, int $regionId): Collection
    {
        return Prediction::where('commodity_id', $commodityId)
            ->where('region_id', $regionId)
            ->orderBy('predicted_date', 'desc')
            ->get()
            ->map(fn(Prediction $model) => $this->toDomain($model));
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
        $entity->setCreatedAt($model->created_at);
        $entity->setUpdatedAt($model->updated_at);

        return $entity;
    }
}
