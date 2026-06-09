<?php

namespace Database\Factories;

use App\Models\Commodity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Commodity>
 */
class CommodityFactory extends Factory
{
    protected $model = Commodity::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word() . ' ' . fake()->word(),
            'category' => fake()->randomElement(['Sembako', 'Protein', 'Bumbu']),
            'unit' => fake()->randomElement(['kg', 'liter', 'butir']),
            'is_active' => true,
        ];
    }
}
