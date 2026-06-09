<?php

namespace App\Application\DTOs;

class PriceRecordDTO
{
    public function __construct(
        public readonly int $commodityId,
        public readonly int $regionId,
        public readonly float $price,
        public readonly string $recordedDate,
        public readonly ?string $source = null,
        public readonly ?string $notes = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['commodity_id'], $data['region_id'], $data['price'], $data['recorded_date'])) {
            throw new \InvalidArgumentException(
                'Missing required fields: commodity_id, region_id, price, recorded_date'
            );
        }

        return new self(
            commodityId: (int) $data['commodity_id'],
            regionId: (int) $data['region_id'],
            price: (float) $data['price'],
            recordedDate: $data['recorded_date'],
            source: $data['source'] ?? null,
            notes: $data['notes'] ?? null,
        );
    }
}
