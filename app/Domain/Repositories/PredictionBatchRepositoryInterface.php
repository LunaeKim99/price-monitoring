<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\PredictionBatch;
use Illuminate\Support\Collection;

interface PredictionBatchRepositoryInterface
{
    public function all(): Collection;

    public function findById(int $id): ?PredictionBatch;

    public function findLatest(): ?PredictionBatch;

    public function save(PredictionBatch $batch): PredictionBatch;

    public function findByStatus(string $status): Collection;
}
