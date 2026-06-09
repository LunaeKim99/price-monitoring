<?php

namespace App\Domain\Entities;

class Prediction
{
    private ?int $id = null;
    private int $commodityId;
    private int $regionId;
    private float $predictedPrice;
    private \DateTime $predictedDate;
    private ?float $confidence = null;
    private ?string $modelName = null;
    private ?string $createdAt = null;
    private ?string $updatedAt = null;

    public function __construct(
        int $commodityId,
        int $regionId,
        float $predictedPrice,
        \DateTime $predictedDate,
        ?float $confidence = null,
        ?string $modelName = null
    ) {
        $this->commodityId = $commodityId;
        $this->regionId = $regionId;
        $this->predictedPrice = $predictedPrice;
        $this->predictedDate = $predictedDate;
        $this->confidence = $confidence;
        $this->modelName = $modelName;
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

    public function getCommodityId(): int
    {
        return $this->commodityId;
    }

    public function getRegionId(): int
    {
        return $this->regionId;
    }

    public function getPredictedPrice(): float
    {
        return $this->predictedPrice;
    }

    public function setPredictedPrice(float $predictedPrice): void
    {
        $this->predictedPrice = $predictedPrice;
    }

    public function getPredictedDate(): \DateTime
    {
        return $this->predictedDate;
    }

    public function setPredictedDate(\DateTime $predictedDate): void
    {
        $this->predictedDate = $predictedDate;
    }

    public function getConfidence(): ?float
    {
        return $this->confidence;
    }

    public function setConfidence(?float $confidence): void
    {
        $this->confidence = $confidence;
    }

    public function getModelName(): ?string
    {
        return $this->modelName;
    }

    public function setModelName(?string $modelName): void
    {
        $this->modelName = $modelName;
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
