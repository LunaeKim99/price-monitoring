<?php

namespace App\Http\Controllers;

use App\Presentation\Blocs\DashboardBloc;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardBloc $dashboardBloc,
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
}
