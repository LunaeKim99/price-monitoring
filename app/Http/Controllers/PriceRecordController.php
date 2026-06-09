<?php

namespace App\Http\Controllers;

use App\Application\DTOs\PriceRecordDTO;
use App\Application\UseCases\RecordPriceUseCase;
use App\Domain\Repositories\CommodityRepositoryInterface;
use App\Domain\Repositories\RegionRepositoryInterface;
use App\Domain\ValueObjects\PriceFilter;
use App\Http\Requests\FilterPriceRequest;
use App\Http\Requests\StorePriceRecordRequest;
use App\Presentation\Blocs\PriceFilterBloc;
use App\Presentation\ViewModels\PriceFormViewModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PriceRecordController extends Controller
{
    public function __construct(
        private PriceFilterBloc $priceFilterBloc,
        private RecordPriceUseCase $recordPriceUseCase,
        private CommodityRepositoryInterface $commodityRepository,
        private RegionRepositoryInterface $regionRepository,
    ) {
    }

    public function index(FilterPriceRequest $request): View
    {
        $filter = new PriceFilter(
            commodityId: $request->validated()['commodity_id'] ?? null,
            regionId: $request->validated()['region_id'] ?? null,
            dateFrom: $request->validated()['date_from'] ?? null,
            dateTo: $request->validated()['date_to'] ?? null,
        );

        $viewModel = $this->priceFilterBloc->getFilteredState($filter);

        return view('prices.index', compact('viewModel'));
    }

    public function create(): View
    {
        $commodities = $this->commodityRepository->all();
        $regions = $this->regionRepository->all();
        $viewModel = new PriceFormViewModel($commodities, $regions);

        return view('prices.create', compact('viewModel'));
    }

    public function store(StorePriceRecordRequest $request): RedirectResponse
    {
        $dto = PriceRecordDTO::fromArray($request->validated());
        $this->recordPriceUseCase->execute($dto);

        return redirect()->route('price-records.index')->with('success', 'Harga berhasil dicatat.');
    }
}
