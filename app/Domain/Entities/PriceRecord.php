<?php

namespace App\Domain\Entities;

class PriceRecord
{
    private ?int $id = null;
    private int $commodityId;
    private int $regionId;
    private float $price;
    private \DateTime $recordedDate;
    private ?string $source = null;
    private ?string $notes = null;
    private ?string $createdAt = null;
    private ?string $updatedAt = null;

    public function __construct(
        int $commodityId,
        int $regionId,
        float $price,
        \DateTime $recordedDate,
        ?string $source = null,
        ?string $notes = null
    ) {
        $this->commodityId = $commodityId;
        $this->regionId = $regionId;
        $this->price = $price;
        $this->recordedDate = $recordedDate;
        $this->source = $source;
        $this->notes = $notes;
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

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function getRecordedDate(): \DateTime
    {
        return $this->recordedDate;
    }

    public function setRecordedDate(\DateTime $recordedDate): void
    {
        $this->recordedDate = $recordedDate;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): void
    {
        $this->source = $source;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): void
    {
        $this->notes = $notes;
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
