<?php

namespace App\Http\Controllers;

use App\Domain\Entities\Commodity;
use App\Domain\Repositories\CommodityRepositoryInterface;
use App\Http\Requests\StoreCommodityRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CommodityController extends Controller
{
    public function __construct(
        private CommodityRepositoryInterface $commodityRepository,
    ) {
    }

    public function index(): View
    {
        $commodities = $this->commodityRepository->all();

        return view('commodities.index', compact('commodities'));
    }

    public function create(): View
    {
        return view('commodities.create');
    }

    public function store(StoreCommodityRequest $request): RedirectResponse
    {
        $commodity = new Commodity(
            name: $request->validated()['name'],
            category: $request->validated()['category'] ?? null,
            unit: $request->validated()['unit'],
        );

        $this->commodityRepository->save($commodity);

        return redirect()->route('commodities.index')->with('success', 'Komoditas berhasil ditambahkan.');
    }

    public function edit(int $id): View
    {
        $commodity = $this->commodityRepository->findById($id);

        return view('commodities.edit', compact('commodity'));
    }

    public function update(StoreCommodityRequest $request, int $id): RedirectResponse
    {
        $commodity = $this->commodityRepository->findById($id);
        $commodity->setName($request->validated()['name']);
        $commodity->setCategory($request->validated()['category'] ?? null);
        $commodity->setUnit($request->validated()['unit']);

        $this->commodityRepository->update($commodity);

        return redirect()->route('commodities.index')->with('success', 'Komoditas berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->commodityRepository->delete($id);

        return redirect()->route('commodities.index')->with('success', 'Komoditas berhasil dihapus.');
    }
}
