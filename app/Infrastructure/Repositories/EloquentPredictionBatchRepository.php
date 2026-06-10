<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\PredictionBatch as DomainPredictionBatch;
use App\Domain\Repositories\PredictionBatchRepositoryInterface;
use App\Models\PredictionBatch;
use Illuminate\Support\Collection;

class EloquentPredictionBatchRepository implements PredictionBatchRepositoryInterface
{
    public function all(): Collection
    {
        return PredictionBatch::orderBy('created_at', 'desc')
            ->get()
            ->map(fn(PredictionBatch $model) => $this->toDomain($model));
    }

    public function findById(int $id): ?DomainPredictionBatch
    {
        $model = PredictionBatch::find($id);

        return $model ? $this->toDomain($model) : null;
    }

    public function findLatest(): ?DomainPredictionBatch
    {
        $model = PredictionBatch::orderBy('created_at', 'desc')->first();

        return $model ? $this->toDomain($model) : null;
    }

    public function save(DomainPredictionBatch $batch): DomainPredictionBatch
    {
        $data = [
            'status' => $batch->getStatus(),
            'total_pairs' => $batch->getTotalPairs(),
            'processed_pairs' => $batch->getProcessedPairs(),
            'total_predictions' => $batch->getTotalPredictions(),
            'ai_insight' => $batch->getAiInsight(),
            'ai_insight_generated_at' => $batch->getAiInsightGeneratedAt(),
            'started_at' => $batch->getStartedAt(),
            'completed_at' => $batch->getCompletedAt(),
            'error_message' => $batch->getErrorMessage(),
        ];

        if ($batch->getId()) {
            $model = PredictionBatch::findOrFail($batch->getId());
            $model->update($data);
        } else {
            $model = PredictionBatch::create($data);
        }

        return $this->toDomain($model->fresh());
    }

    public function findByStatus(string $status): Collection
    {
        return PredictionBatch::where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn(PredictionBatch $model) => $this->toDomain($model));
    }

    private function toDomain(PredictionBatch $model): DomainPredictionBatch
    {
        $entity = new DomainPredictionBatch(
            status: $model->status,
            totalPairs: (int) $model->total_pairs,
            processedPairs: (int) $model->processed_pairs,
            totalPredictions: (int) $model->total_predictions,
            aiInsight: $model->ai_insight,
            aiInsightGeneratedAt: $model->ai_insight_generated_at,
            startedAt: $model->started_at,
            completedAt: $model->completed_at,
            errorMessage: $model->error_message,
        );
        $entity->setId($model->id);
        $entity->setCreatedAt($model->created_at);
        $entity->setUpdatedAt($model->updated_at);

        return $entity;
    }
}
