<?php

namespace App\Http\Controllers;

use App\Presentation\Blocs\DashboardBloc;

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
}
