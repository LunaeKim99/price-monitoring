<?php

namespace App\Http\Controllers;

use App\Domain\Repositories\CommodityRepositoryInterface;
use App\Domain\Repositories\PriceRecordRepositoryInterface;
use App\Presentation\Blocs\DashboardBloc;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardBloc $dashboardBloc,
        private PriceRecordRepositoryInterface $priceRecordRepository,
        private CommodityRepositoryInterface $commodityRepository,
    ) {
    }

    public function index()
    {
        $viewModel = $this->dashboardBloc->getState();

        return view('dashboard.index', compact('viewModel'));
    }

    public function refreshInsight(): \Illuminate\Http\JsonResponse
    {
        Cache::forget('dashboard_ai_insight');

        return response()->json([
            'status' => 'ok',
            'message' => 'Cache dihapus',
        ]);
    }

    public function chartData(Request $request): JsonResponse
    {
        $commodityId = $request->integer('commodity_id', 0) ?: null;

        $from = (new \DateTime())->modify('-30 days');
        $to   = new \DateTime();

        $records = $this->priceRecordRepository
            ->getRecordsBetweenDatesByCommodity($from, $to, $commodityId);

        // Nama komoditas untuk label chart
        $commodityName = 'Semua Komoditas';
        if ($commodityId) {
            $commodity = $this->commodityRepository->findById($commodityId);
            $commodityName = $commodity?->getName() ?? 'Komoditas #' . $commodityId;
        }

        // Group by date → rata-rata harga per hari
        $dateGroups = [];
        foreach ($records as $record) {
            $date = $record->getRecordedDate()->format('Y-m-d');
            $dateGroups[$date][] = $record->getPrice();
        }
        ksort($dateGroups);

        $labels = [];
        $data   = [];
        foreach ($dateGroups as $date => $prices) {
            $labels[] = $date;
            $data[]   = round(array_sum($prices) / count($prices), 2);
        }

        return response()->json([
            'labels'        => $labels,
            'data'          => $data,
            'commodityName' => $commodityName,
        ]);
    }
}
