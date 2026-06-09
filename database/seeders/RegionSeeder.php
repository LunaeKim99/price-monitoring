<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        $provinces = [
            ['name' => 'DKI Jakarta', 'type' => 'province'],
            ['name' => 'Jawa Barat', 'type' => 'province'],
            ['name' => 'Jawa Tengah', 'type' => 'province'],
            ['name' => 'Jawa Timur', 'type' => 'province'],
            ['name' => 'Sumatera Utara', 'type' => 'province'],
            ['name' => 'Sulawesi Selatan', 'type' => 'province'],
            ['name' => 'Bali', 'type' => 'province'],
            ['name' => 'Kalimantan Timur', 'type' => 'province'],
        ];

        foreach ($provinces as $province) {
            Region::create($province);
        }
    }
}
