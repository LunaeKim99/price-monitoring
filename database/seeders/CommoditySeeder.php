<?php

namespace Database\Seeders;

use App\Models\Commodity;
use Illuminate\Database\Seeder;

class CommoditySeeder extends Seeder
{
    public function run(): void
    {
        $commodities = [
            ['name' => 'Beras Premium', 'category' => 'Sembako', 'unit' => 'kg'],
            ['name' => 'Beras Medium', 'category' => 'Sembako', 'unit' => 'kg'],
            ['name' => 'Gula Pasir', 'category' => 'Sembako', 'unit' => 'kg'],
            ['name' => 'Minyak Goreng', 'category' => 'Sembako', 'unit' => 'liter'],
            ['name' => 'Tepung Terigu', 'category' => 'Sembako', 'unit' => 'kg'],
            ['name' => 'Daging Sapi', 'category' => 'Protein', 'unit' => 'kg'],
            ['name' => 'Daging Ayam', 'category' => 'Protein', 'unit' => 'kg'],
            ['name' => 'Telur Ayam', 'category' => 'Protein', 'unit' => 'kg'],
            ['name' => 'Susu Kental Manis', 'category' => 'Sembako', 'unit' => 'kaleng'],
            ['name' => 'Cabai Merah', 'category' => 'Bumbu', 'unit' => 'kg'],
            ['name' => 'Bawang Merah', 'category' => 'Bumbu', 'unit' => 'kg'],
            ['name' => 'Bawang Putih', 'category' => 'Bumbu', 'unit' => 'kg'],
        ];

        foreach ($commodities as $commodity) {
            Commodity::create($commodity);
        }
    }
}
