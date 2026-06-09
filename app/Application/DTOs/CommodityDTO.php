<?php

namespace App\Application\DTOs;

class CommodityDTO
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $category = null,
        public readonly string $unit = 'kg',
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            category: $data['category'] ?? null,
            unit: $data['unit'] ?? 'kg',
        );
    }
}
