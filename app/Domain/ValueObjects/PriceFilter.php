<?php

namespace App\Domain\ValueObjects;

class PriceFilter
{
    private ?int $commodityId;
    private ?int $regionId;
    private ?string $dateFrom;
    private ?string $dateTo;

    public function __construct(
        ?int $commodityId = null,
        ?int $regionId = null,
        ?string $dateFrom = null,
        ?string $dateTo = null
    ) {
        $this->commodityId = $commodityId;
        $this->regionId = $regionId;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function getCommodityId(): ?int
    {
        return $this->commodityId;
    }

    public function getRegionId(): ?int
    {
        return $this->regionId;
    }

    public function getDateFrom(): ?string
    {
        return $this->dateFrom;
    }

    public function getDateTo(): ?string
    {
        return $this->dateTo;
    }
}
