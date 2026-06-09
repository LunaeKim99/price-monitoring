<?php

namespace Database\Seeders;

use App\Models\Commodity;
use App\Models\PriceRecord;
use App\Models\Region;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PriceRecordSeeder extends Seeder
{
    public function run(): void
    {
        $commodities = Commodity::all();
        $regions = Region::all();

        if ($commodities->isEmpty() || $regions->isEmpty()) {
            return;
        }

        $priceRanges = [
            'Beras Premium' => [13000, 16000],
            'Beras Medium' => [10000, 13000],
            'Gula Pasir' => [14000, 18000],
            'Minyak Goreng' => [14000, 22000],
            'Tepung Terigu' => [9000, 12000],
            'Daging Sapi' => [120000, 150000],
            'Daging Ayam' => [30000, 40000],
            'Telur Ayam' => [25000, 35000],
            'Susu Kental Manis' => [10000, 15000],
            'Cabai Merah' => [30000, 80000],
            'Bawang Merah' => [20000, 40000],
            'Bawang Putih' => [15000, 35000],
        ];

        $now = Carbon::now();

        foreach ($commodities as $commodity) {
            $range = $priceRanges[$commodity->name] ?? [10000, 50000];

            foreach ($regions as $region) {
                // Create 30 days of price data
                for ($day = 29; $day >= 0; $day--) {
                    $date = $now->copy()->subDays($day);
                    $price = rand($range[0] * 100, $range[1] * 100) / 100;
                    // Add some randomness to make it realistic
                    $dayFactor = sin($day * 0.2) * 2000;
                    $price += $dayFactor;
                    $price = max($range[0], min($range[1], $price));

                    PriceRecord::create([
                        'commodity_id' => $commodity->id,
                        'region_id' => $region->id,
                        'price' => round($price, 2),
                        'recorded_date' => $date->format('Y-m-d'),
                        'source' => 'Pasar Tradisional',
                        'notes' => 'Data otomatis',
                    ]);
                }
            }
        }
    }
}
