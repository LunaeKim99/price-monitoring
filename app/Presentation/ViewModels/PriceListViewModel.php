<?php

namespace App\Presentation\ViewModels;

use App\Domain\ValueObjects\PriceFilter;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PriceListViewModel
{
    public function __construct(
        public readonly LengthAwarePaginator $prices,
        public readonly Collection $commodities,
        public readonly Collection $regions,
        public readonly PriceFilter $filters,
        public readonly int $totalCount,
    ) {
    }
}
