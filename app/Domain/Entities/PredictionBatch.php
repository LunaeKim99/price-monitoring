<?php

namespace App\Domain\Entities;

class PredictionBatch
{
    private ?int $id = null;
    private string $status;
    private int $totalPairs = 0;
    private int $processedPairs = 0;
    private int $totalPredictions = 0;
    private ?string $aiInsight = null;
    private ?\DateTime $aiInsightGeneratedAt = null;
    private ?\DateTime $startedAt = null;
    private ?\DateTime $completedAt = null;
    private ?string $errorMessage = null;
    private ?string $createdAt = null;
    private ?string $updatedAt = null;

    public function __construct(
        string $status = 'pending',
        int $totalPairs = 0,
        int $processedPairs = 0,
        int $totalPredictions = 0,
        ?string $aiInsight = null,
        ?\DateTime $aiInsightGeneratedAt = null,
        ?\DateTime $startedAt = null,
        ?\DateTime $completedAt = null,
        ?string $errorMessage = null
    ) {
        $this->status = $status;
        $this->totalPairs = $totalPairs;
        $this->processedPairs = $processedPairs;
        $this->totalPredictions = $totalPredictions;
        $this->aiInsight = $aiInsight;
        $this->aiInsightGeneratedAt = $aiInsightGeneratedAt;
        $this->startedAt = $startedAt;
        $this->completedAt = $completedAt;
        $this->errorMessage = $errorMessage;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getTotalPairs(): int
    {
        return $this->totalPairs;
    }

    public function setTotalPairs(int $totalPairs): void
    {
        $this->totalPairs = $totalPairs;
    }

    public function getProcessedPairs(): int
    {
        return $this->processedPairs;
    }

    public function setProcessedPairs(int $processedPairs): void
    {
        $this->processedPairs = $processedPairs;
    }

    public function getTotalPredictions(): int
    {
        return $this->totalPredictions;
    }

    public function setTotalPredictions(int $totalPredictions): void
    {
        $this->totalPredictions = $totalPredictions;
    }

    public function getAiInsight(): ?string
    {
        return $this->aiInsight;
    }

    public function setAiInsight(?string $aiInsight): void
    {
        $this->aiInsight = $aiInsight;
    }

    public function getAiInsightGeneratedAt(): ?\DateTime
    {
        return $this->aiInsightGeneratedAt;
    }

    public function setAiInsightGeneratedAt(?\DateTime $aiInsightGeneratedAt): void
    {
        $this->aiInsightGeneratedAt = $aiInsightGeneratedAt;
    }

    public function getStartedAt(): ?\DateTime
    {
        return $this->startedAt;
    }

    public function setStartedAt(?\DateTime $startedAt): void
    {
        $this->startedAt = $startedAt;
    }

    public function getCompletedAt(): ?\DateTime
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?\DateTime $completedAt): void
    {
        $this->completedAt = $completedAt;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function setErrorMessage(?string $errorMessage): void
    {
        $this->errorMessage = $errorMessage;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?string $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
