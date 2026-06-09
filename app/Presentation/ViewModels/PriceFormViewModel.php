<?php

namespace App\Presentation\ViewModels;

use Illuminate\Support\Collection;

class PriceFormViewModel
{
    public function __construct(
        public readonly Collection $commodities,
        public readonly Collection $regions,
        public readonly mixed $priceRecord = null,
    ) {
    }
}
