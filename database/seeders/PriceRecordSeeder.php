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
        // Idempotency: skip if records already exist
        if (PriceRecord::count() > 0) {
            $this->command->info('Price records already exist. Skipping...');
            return;
        }

        $commodities = Commodity::all();
        $regions = Region::all();

        if ($commodities->isEmpty() || $regions->isEmpty()) {
            $this->command->warn('Commodities or regions table is empty. Skipping PriceRecordSeeder.');
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
        $records = [];

        foreach ($commodities as $commodity) {
            $range = $priceRanges[$commodity->name] ?? [10000, 50000];

            foreach ($regions as $region) {
                // 14 days of historical price data
                for ($day = 13; $day >= 0; $day--) {
                    $date = $now->copy()->subDays($day);
                    $price = rand($range[0] * 100, $range[1] * 100) / 100;
                    // Add realistic fluctuation
                    $dayFactor = sin($day * 0.2) * 2000;
                    $price += $dayFactor;
                    $price = max($range[0], min($range[1], $price));

                    $records[] = [
                        'commodity_id' => $commodity->id,
                        'region_id' => $region->id,
                        'price' => round($price, 2),
                        'recorded_date' => $date->format('Y-m-d'),
                        'source' => 'Pasar Tradisional',
                        'notes' => 'Data otomatis',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        // Batch insert in chunks of 500 for performance
        foreach (array_chunk($records, 500) as $chunk) {
            PriceRecord::insert($chunk);
        }

        $this->command->info('Created ' . count($records) . ' price records across ' . $commodities->count() . ' commodities and ' . $regions->count() . ' regions.');
    }
}
