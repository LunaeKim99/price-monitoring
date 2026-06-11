<?php

namespace App\Application\Services;

use App\Domain\Repositories\CommodityRepositoryInterface;
use App\Domain\Repositories\PriceRecordRepositoryInterface;
use App\Domain\Repositories\RegionRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PriceScraperService
{
    private const COMMODITY_MAP = [
        'beras medium'          => 'Beras Medium',
        'beras premium'         => 'Beras Premium',
        'cabai rawit merah'     => 'Cabai Rawit',
        'bawang merah'          => 'Bawang Merah',
        'bawang putih impor'    => 'Bawang Putih',
        'minyak goreng kemasan' => 'Minyak Goreng',
        'gula pasir lokal'      => 'Gula Pasir',
        'tepung terigu'         => 'Tepung Terigu',
        'daging ayam ras'       => 'Daging Ayam',
        'daging sapi murni'     => 'Daging Sapi',
        'telur ayam ras'        => 'Telur Ayam',
    ];

    private const KEMENDAG_API_URL = 'https://dev-panelharga.badanpangan.go.id/api/v1/prices';

    private CommodityRepositoryInterface $commodityRepository;
    private PriceRecordRepositoryInterface $priceRecordRepository;
    private RegionRepositoryInterface $regionRepository;

    /** @var array<string, int>|null */
    private ?array $commodityMap = null;

    /** @var array<string, int>|null */
    private ?array $regionMap = null;

    public function __construct(
        CommodityRepositoryInterface $commodityRepository,
        PriceRecordRepositoryInterface $priceRecordRepository,
        RegionRepositoryInterface $regionRepository,
    ) {
        $this->commodityRepository = $commodityRepository;
        $this->priceRecordRepository = $priceRecordRepository;
        $this->regionRepository = $regionRepository;
    }

    public function scrape(Carbon $date, string $source = 'auto'): array
    {
        $this->buildLookupMaps();

        $rows = match ($source) {
            'kemendag' => $this->fetchFromKemendag($date),
            'csv'      => $this->fetchFromCsv(),
            default    => $this->fetchWithFallback($date),
        };

        if (empty($rows)) {
            Log::warning('PriceScraperService: No data fetched.', [
                'source' => $source,
                'date'   => $date->toDateString(),
            ]);
            return ['imported' => 0, 'skipped' => 0, 'errors' => 0, 'source' => 'none'];
        }

        $normalized = [];
        foreach ($rows as $raw) {
            $row = $this->normalizeRow($raw, $date);
            if ($row !== null) {
                $normalized[] = $row;
            }
        }

        if (empty($normalized)) {
            return ['imported' => 0, 'skipped' => 0, 'errors' => 0, 'source' => 'none'];
        }

        return $this->saveToDatabase($normalized);
    }

    private function fetchWithFallback(Carbon $date): array
    {
        $rows = $this->fetchFromKemendag($date);

        if (!empty($rows)) {
            return $rows;
        }

        Log::info('PriceScraperService: Kemendag API returned empty, falling back to CSV.');

        return $this->fetchFromCsv();
    }

    private function fetchFromKemendag(Carbon $date): array
    {
        try {
            $response = Http::timeout(15)
                ->retry(2, 1000)
                ->withHeaders([
                    'Accept'     => 'application/json',
                    'User-Agent' => 'PriceMonitorBot/1.0',
                ])
                ->get(self::KEMENDAG_API_URL, [
                    'date'  => $date->format('Y-m-d'),
                    'limit' => 200,
                ]);

            if (!$response->successful()) {
                Log::warning('PriceScraperService: Kemendag API status {status}', [
                    'status' => $response->status(),
                ]);
                return [];
            }

            $body = $response->json();
            $items = $body['data'] ?? $body ?? [];

            if (!is_array($items) || empty($items)) {
                return [];
            }

            Log::info('PriceScraperService: Fetched {count} rows from Kemendag.', [
                'count' => count($items),
            ]);

            return $items;
        } catch (\Exception $e) {
            Log::warning('PriceScraperService: Kemendag exception: {msg}', [
                'msg' => $e->getMessage(),
            ]);
            return [];
        }
    }

    private function fetchFromCsv(): array
    {
        $contents = null;

        // Try storage path first (runtime path, not tracked by git)
        if (Storage::exists('prices/weekly_update.csv')) {
            $contents = Storage::get('prices/weekly_update.csv');
        } elseif (file_exists(base_path('database/data/weekly_update.csv'))) {
            // Fallback to tracked template
            $contents = file_get_contents(base_path('database/data/weekly_update.csv'));
        }

        if ($contents === null || empty(trim($contents))) {
            Log::info('PriceScraperService: CSV not found at any expected path.');
            return [];
        }

        $lines = explode("\n", trim($contents));
        $header = str_getcsv(array_shift($lines));

        $rows = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            $data = str_getcsv($line);
            $row = [];
            foreach ($header as $i => $col) {
                $row[$col] = $data[$i] ?? '';
            }
            $rows[] = $row;
        }

        Log::info('PriceScraperService: Read {count} rows from CSV.', ['count' => count($rows)]);

        return $rows;
    }

    private function normalizeRow(array $raw, Carbon $defaultDate): ?array
    {
        $rawCommodity = strtolower(trim(
            $raw['commodity'] ?? $raw['komoditas'] ?? $raw['nama_komoditas'] ?? ''
        ));

        $dbCommodityName = null;
        foreach (self::COMMODITY_MAP as $keyword => $dbName) {
            if (str_contains($rawCommodity, $keyword) || $rawCommodity === $keyword) {
                $dbCommodityName = strtolower($dbName);
                break;
            }
        }

        if ($dbCommodityName === null) {
            return null;
        }

        $commodityId = $this->commodityMap[$dbCommodityName] ?? null;
        if ($commodityId === null) {
            Log::warning('PriceScraperService: Commodity "{name}" not found in DB.', [
                'name' => $dbCommodityName,
            ]);
            return null;
        }

        $rawRegion = trim(
            $raw['region'] ?? $raw['wilayah'] ?? $raw['nama_wilayah'] ?? $raw['kota'] ?? ''
        );
        $regionId = $this->regionMap[strtolower($rawRegion)] ?? null;

        if ($regionId === null) {
            Log::debug('PriceScraperService: Unknown region "{name}".', ['name' => $rawRegion]);
            return null;
        }

        $price = $raw['price'] ?? $raw['harga'] ?? $raw['nilai'] ?? null;
        if ($price === null || !is_numeric($price)) {
            return null;
        }

        $rawDate = $raw['date'] ?? $raw['tanggal'] ?? $raw['recorded_date'] ?? null;
        $recordedDate = $rawDate ? Carbon::parse($rawDate) : $defaultDate;

        return [
            'commodity_id'  => $commodityId,
            'region_id'     => $regionId,
            'price'         => (float) $price,
            'recorded_date' => $recordedDate,
            'source'        => $raw['source'] ?? 'kemendag',
            'notes'         => $raw['notes'] ?? null,
        ];
    }

    private function saveToDatabase(array $rows): array
    {
        $imported = 0;
        $skipped  = 0;
        $errors   = 0;
        $sourceUsed = '';

        foreach ($rows as $row) {
            try {
                if ($this->priceRecordRepository->existsForDate(
                    $row['commodity_id'],
                    $row['region_id'],
                    $row['recorded_date']->toDateTime(),
                )) {
                    $skipped++;
                    continue;
                }

                $entity = new \App\Domain\Entities\PriceRecord(
                    commodityId:  $row['commodity_id'],
                    regionId:     $row['region_id'],
                    price:        $row['price'],
                    recordedDate: $row['recorded_date']->toDateTime(),
                    source:       $row['source'],
                    notes:        $row['notes'],
                );

                $this->priceRecordRepository->save($entity);
                $imported++;
                $sourceUsed = $row['source'];
            } catch (\Exception $e) {
                Log::error('PriceScraperService: Failed to save row.', [
                    'row'   => $row,
                    'error' => $e->getMessage(),
                ]);
                $errors++;
            }
        }

        Log::info('PriceScraperService: Save complete.', [
            'imported' => $imported,
            'skipped'  => $skipped,
            'errors'   => $errors,
        ]);

        return compact('imported', 'skipped', 'errors') + ['source' => $sourceUsed];
    }

    private function buildLookupMaps(): void
    {
        if ($this->commodityMap !== null && $this->regionMap !== null) {
            return;
        }

        $this->commodityMap = [];
        foreach ($this->commodityRepository->all() as $commodity) {
            $this->commodityMap[strtolower($commodity->getName())] = $commodity->getId();
        }

        $this->regionMap = [];
        foreach ($this->regionRepository->all() as $region) {
            $this->regionMap[strtolower($region->getName())] = $region->getId();
        }
    }
}
