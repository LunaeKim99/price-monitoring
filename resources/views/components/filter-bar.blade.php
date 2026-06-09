@props([
    'commodities' => collect(),
    'regions' => collect(),
    'filters' => null,
])

<div class="bg-surface rounded-xl shadow-sm p-4 mb-6 border border-border">
    <form method="GET" action="{{ url()->current() }}" class="flex flex-wrap items-end gap-4">
        @if($commodities->isNotEmpty())
            <div class="flex-1 min-w-[150px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Komoditas</label>
                <select name="commodity_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 border px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
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
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Wilayah</label>
                <select name="region_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 border px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
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
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Dari Tanggal</label>
            <input type="date" name="date_from" value="{{ $filters ? $filters->getDateFrom() : '' }}"
                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 border px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="flex-1 min-w-[130px]">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sampai Tanggal</label>
            <input type="date" name="date_to" value="{{ $filters ? $filters->getDateTo() : '' }}"
                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 border px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="flex items-center gap-2 pb-0.5">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                Filter
            </button>
            <a href="{{ url()->current() }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                Reset
            </a>
        </div>
    </form>
</div>
