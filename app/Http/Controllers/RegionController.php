<?php

namespace App\Http\Controllers;

use App\Domain\Entities\Region;
use App\Domain\Repositories\RegionRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegionController extends Controller
{
    public function __construct(
        private RegionRepositoryInterface $regionRepository,
    ) {
    }

    public function index(): View
    {
        $regions = $this->regionRepository->all();

        $regionMap = [];
        foreach ($regions as $r) {
            $regionMap[$r->getId()] = $r->getName();
        }

        return view('regions.index', compact('regions', 'regionMap'));
    }

    public function create(): View
    {
        $provinces = $this->regionRepository->findByType('province');

        return view('regions.create', compact('provinces'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:province,city,district',
            'parent_id' => 'nullable|exists:regions,id',
        ]);

        $region = new Region(
            name: $validated['name'],
            type: $validated['type'],
            parentId: $validated['parent_id'] ?? null,
        );

        $this->regionRepository->save($region);

        return redirect()->route('regions.index')->with('success', 'Wilayah berhasil ditambahkan.');
    }
}
