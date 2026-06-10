<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        $provinces = [
            'Aceh',
            'Sumatera Utara',
            'Sumatera Barat',
            'Riau',
            'Kepulauan Riau',
            'Jambi',
            'Sumatera Selatan',
            'Kepulauan Bangka Belitung',
            'Bengkulu',
            'Lampung',
            'DKI Jakarta',
            'Jawa Barat',
            'Banten',
            'Jawa Tengah',
            'DI Yogyakarta',
            'Jawa Timur',
            'Bali',
            'Nusa Tenggara Barat',
            'Nusa Tenggara Timur',
            'Kalimantan Barat',
            'Kalimantan Tengah',
            'Kalimantan Selatan',
            'Kalimantan Timur',
            'Kalimantan Utara',
            'Sulawesi Utara',
            'Gorontalo',
            'Sulawesi Tengah',
            'Sulawesi Barat',
            'Sulawesi Selatan',
            'Sulawesi Tenggara',
            'Maluku',
            'Maluku Utara',
            'Papua Barat',
            'Papua',
        ];

        foreach ($provinces as $name) {
            Region::firstOrCreate(
                ['name' => $name],
                ['type' => 'province']
            );
        }

        $this->command->info('Created/verified ' . count($provinces) . ' provinces.');
    }
}
