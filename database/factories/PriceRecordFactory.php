<?php

namespace Database\Factories;

use App\Models\Commodity;
use App\Models\PriceRecord;
use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PriceRecord>
 */
class PriceRecordFactory extends Factory
{
    protected $model = PriceRecord::class;

    public function definition(): array
    {
        return [
            'commodity_id' => Commodity::factory(),
            'region_id' => Region::factory(),
            'price' => fake()->randomFloat(2, 1000, 200000),
            'recorded_date' => fake()->date(),
            'source' => fake()->randomElement(['Pasar Induk', 'Dinas Perdagangan', 'BPS']),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
