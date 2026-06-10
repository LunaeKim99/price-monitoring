<?php

namespace App\Http\Controllers;

use App\Application\Services\PredictionService;
use App\Domain\Repositories\CommodityRepositoryInterface;
use App\Domain\Repositories\PredictionBatchRepositoryInterface;
use App\Domain\Repositories\PredictionRepositoryInterface;
use App\Domain\Repositories\RegionRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PredictionController extends Controller
{
    public function __construct(
        private PredictionService $predictionService,
        private PredictionRepositoryInterface $predictionRepository,
        private CommodityRepositoryInterface $commodityRepository,
        private RegionRepositoryInterface $regionRepository,
        private PredictionBatchRepositoryInterface $predictionBatchRepository,
    ) {
    }

    public function index(Request $request): View
    {
        $commodityId = $request->query('commodity_id');
        $regionId = $request->query('region_id');

        $predictions = $this->predictionRepository->paginate(
            perPage: 20,
            commodityId: $commodityId ? (int) $commodityId : null,
            regionId: $regionId ? (int) $regionId : null,
        );

        // Load latest batch info
        $latestBatch = $this->predictionBatchRepository->findLatest();
        $batchPredictions = $latestBatch
            ? $this->predictionRepository->findByBatchId($latestBatch->getId())
            : collect();

        $commodities = $this->commodityRepository->all();
        $regions = $this->regionRepository->all();

        // Build lookup maps for names
        $commodityMap = [];
        foreach ($commodities as $c) {
            $commodityMap[$c->getId()] = $c->getName();
        }
        $regionMap = [];
        foreach ($regions as $r) {
            $regionMap[$r->getId()] = $r->getName();
        }

        $displayPredictions = $batchPredictions->isNotEmpty() ? $batchPredictions : $predictions;

        return view('predictions.index', compact(
            'predictions',
            'commodityMap',
            'regionMap',
            'latestBatch',
            'batchPredictions',
            'displayPredictions',
            'commodities',
            'regions',
        ));
    }

    public function create(): View
    {
        $commodities = $this->commodityRepository->all();
        $regions = $this->regionRepository->all();

        return view('predictions.create', compact('commodities', 'regions'));
    }

    public function generate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'commodity_id' => 'required|exists:commodities,id',
            'region_id' => 'required|exists:regions,id',
            'days' => 'required|in:7,14,30',
        ]);

        try {
            $this->predictionService->generatePredictions(
                (int) $validated['commodity_id'],
                (int) $validated['region_id'],
                (int) $validated['days']
            );

            return redirect()->route('predictions.index')
                ->with('success', 'Prediksi berhasil dibuat untuk ' . $validated['days'] . ' hari ke depan.');
        } catch (\RuntimeException $e) {
            return redirect()->route('predictions.index')
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        $deleted = $this->predictionRepository->delete($id);

        if (!$deleted) {
            return redirect()->route('predictions.index')
                ->with('error', 'Data prediksi tidak ditemukan.');
        }

        return redirect()->route('predictions.index')
            ->with('success', 'Data prediksi berhasil dihapus.');
    }
}
