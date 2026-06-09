@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Total Komoditas</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $viewModel->totalCommodities }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Total Wilayah</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $viewModel->totalRegions }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Total Data Harga</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $viewModel->totalPriceRecords }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Rata-rata Harga</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $viewModel->averagePrice }}</p>
                </div>
                <div class="bg-orange-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Harga Komoditas (30 Hari)</h3>
            <div class="relative h-[300px] w-full">
                <canvas id="priceTrendChart" class="w-full h-full"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Perbandingan Harga per Wilayah (30 Hari)</h3>
            <div class="relative h-[300px] w-full">
                <canvas id="regionComparisonChart" class="w-full h-full"></canvas>
            </div>
        </div>
    </div>

    <!-- Trending Commodities -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Komoditas Terpopuler</h3>
            <div class="space-y-3">
                @forelse($viewModel->trendingCommodities as $commodity)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-800">{{ $commodity->name }}</p>
                            <p class="text-sm text-gray-500">{{ $commodity->category ?? 'Umum' }} - {{ $commodity->unit }}</p>
                        </div>
                        <span class="text-xs text-gray-400">ID: {{ $commodity->id }}</span>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">Belum ada data komoditas.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-gray-600">Arah Tren</span>
                    <span class="font-medium {{ $viewModel->trendDirection === 'up' ? 'text-red-600' : ($viewModel->trendDirection === 'down' ? 'text-green-600' : 'text-gray-600') }}">
                        @if($viewModel->trendDirection === 'up')
                            ↑ Meningkat
                        @elseif($viewModel->trendDirection === 'down')
                            ↓ Menurun
                        @else
                            → Stabil
                        @endif
                    </span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-gray-600">Terakhir Diperbarui</span>
                    <span class="font-medium text-gray-800">{{ $viewModel->lastUpdated }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Latest Prices Table -->
    <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Harga Terbaru</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Komoditas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Wilayah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sumber</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($viewModel->latestPrices as $price)
                        <tr class="hover:bg-blue-50 transition-colors odd:bg-gray-50/50">
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $price->id }}</td>
                            <td class="px-6 py-4 text-sm text-gray-800">{{ $price->commodity_name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-800">{{ $price->region_name }}</td>
                            <td class="px-6 py-4">
                                <x-price-badge :amount="$price->price_raw" />
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $price->recorded_date }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $price->source ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                Belum ada data harga.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const trendLabels = @json($viewModel->priceTrendLabels);
    const trendData = @json($viewModel->priceTrendData);
    const regionLabels = @json($viewModel->regionComparisonLabels);
    const regionData = @json($viewModel->regionComparisonData);

    if (window.Chart && trendLabels.length > 0) {
        const trendCtx = document.getElementById('priceTrendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [{
                    label: 'Rata-rata Harga',
                    data: trendData,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });

        const regionCtx = document.getElementById('regionComparisonChart').getContext('2d');
        new Chart(regionCtx, {
            type: 'bar',
            data: {
                labels: regionLabels,
                datasets: [{
                    label: 'Rata-rata Harga',
                    data: regionData,
                    backgroundColor: 'rgba(59, 130, 246, 0.7)',
                    borderColor: '#3b82f6',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
