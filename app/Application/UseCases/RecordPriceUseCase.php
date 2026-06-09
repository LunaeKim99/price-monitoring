<?php

namespace App\Application\UseCases;

use App\Application\DTOs\PriceRecordDTO;
use App\Domain\Entities\PriceRecord;
use App\Domain\Repositories\PriceRecordRepositoryInterface;

class RecordPriceUseCase
{
    public function __construct(
        private PriceRecordRepositoryInterface $priceRecordRepository,
    ) {
    }

    public function execute(PriceRecordDTO $dto): PriceRecord
    {
        $record = new PriceRecord(
            commodityId: $dto->commodityId,
            regionId: $dto->regionId,
            price: $dto->price,
            recordedDate: new \DateTime($dto->recordedDate),
            source: $dto->source,
            notes: $dto->notes,
        );

        return $this->priceRecordRepository->save($record);
    }
}
