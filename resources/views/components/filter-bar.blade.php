@props([
    'commodities' => collect(),
    'regions' => collect(),
    'filters' => null,
])

<div class="bg-white rounded-lg shadow-md p-4 mb-6 border border-gray-200">
    <form method="GET" action="{{ url()->current() }}" class="flex flex-wrap items-end gap-4">
        @if($commodities->isNotEmpty())
            <div class="flex-1 min-w-[150px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Komoditas</label>
                <select name="commodity_id" class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Komoditas</option>
                    @foreach($commodities as $commodity)
                        <option value="{{ $commodity->getId() }}" {{ $filters && $filters->getCommodityId() == $commodity->getId() ? 'selected' : '' }}>
                            {{ $commodity->getName() }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif

        @if($regions->isNotEmpty())
            <div class="flex-1 min-w-[150px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Wilayah</label>
                <select name="region_id" class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Wilayah</option>
                    @foreach($regions as $region)
                        <option value="{{ $region->getId() }}" {{ $filters && $filters->getRegionId() == $region->getId() ? 'selected' : '' }}>
                            {{ $region->getName() }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif

        <div class="flex-1 min-w-[130px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
            <input type="date" name="date_from" value="{{ $filters ? $filters->getDateFrom() : '' }}"
                   class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="flex-1 min-w-[130px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
            <input type="date" name="date_to" value="{{ $filters ? $filters->getDateTo() : '' }}"
                   class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="flex items-center gap-2 pb-0.5">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                Filter
            </button>
            <a href="{{ url()->current() }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition-colors">
                Reset
            </a>
        </div>
    </form>
</div>
