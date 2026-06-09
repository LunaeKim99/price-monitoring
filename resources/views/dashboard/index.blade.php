@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-surface rounded-xl shadow-sm p-6 border border-border hover:shadow-lg transition-shadow border-b-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-text-muted font-medium">Total Komoditas</p>
                    <p class="text-3xl font-bold text-text-primary mt-1">{{ $viewModel->totalCommodities }}</p>
                </div>
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-3 rounded-full">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-surface rounded-xl shadow-sm p-6 border border-border hover:shadow-lg transition-shadow border-b-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-text-muted font-medium">Total Wilayah</p>
                    <p class="text-3xl font-bold text-text-primary mt-1">{{ $viewModel->totalRegions }}</p>
                </div>
                <div class="bg-gradient-to-br from-green-500 to-green-600 p-3 rounded-full">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-surface rounded-xl shadow-sm p-6 border border-border hover:shadow-lg transition-shadow border-b-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-text-muted font-medium">Total Data Harga</p>
                    <p class="text-3xl font-bold text-text-primary mt-1">{{ $viewModel->totalPriceRecords }}</p>
                </div>
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 p-3 rounded-full">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-surface rounded-xl shadow-sm p-6 border border-border hover:shadow-lg transition-shadow border-b-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-text-muted font-medium">Rata-rata Harga</p>
                    <p class="text-2xl font-bold text-text-primary mt-1">{{ $viewModel->averagePrice }}</p>
                </div>
                <div class="bg-gradient-to-br from-orange-500 to-orange-600 p-3 rounded-full">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-surface rounded-xl shadow-sm p-6 border border-border">
            <h3 class="text-lg font-semibold text-text-primary mb-4">Tren Harga Komoditas (30 Hari)</h3>
            <div class="relative h-[300px] w-full">
                <canvas id="priceTrendChart" class="w-full h-full"></canvas>
            </div>
        </div>

        <div class="bg-surface rounded-xl shadow-sm p-6 border border-border">
            <h3 class="text-lg font-semibold text-text-primary mb-4">Perbandingan Harga per Wilayah (30 Hari)</h3>
            <div class="relative h-[300px] w-full">
                <canvas id="regionComparisonChart" class="w-full h-full"></canvas>
            </div>
        </div>
    </div>

    <!-- Trending Commodities -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-surface rounded-xl shadow-sm p-6 border border-border">
            <h3 class="text-lg font-semibold text-text-primary mb-4">Komoditas Terpopuler</h3>
            <div class="space-y-3">
                @forelse($viewModel->trendingCommodities as $commodity)
                    <div class="flex items-center justify-between p-3 bg-surface-secondary rounded-lg">
                        <div>
                            <p class="font-medium text-text-primary">{{ $commodity->name }}</p>
                            <p class="text-sm text-text-muted">{{ $commodity->category ?? 'Umum' }} - {{ $commodity->unit }}</p>
                        </div>
                        @php
                            $cat = $commodity->category ?? '';
                            $badgeColor = match(true) {
                                str_contains($cat, 'Sembako') || str_contains($cat, 'Beras') => 'blue',
                                str_contains($cat, 'Protein') || str_contains($cat, 'Telur') || str_contains($cat, 'Daging') || str_contains($cat, 'Ikan') => 'red',
                                str_contains($cat, 'Bumbu') || str_contains($cat, 'Cabai') || str_contains($cat, 'Bawang') => 'yellow',
                                default => 'gray',
                            };
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $badgeColor === 'blue' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300' : '' }}
                            {{ $badgeColor === 'red' ? 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300' : '' }}
                            {{ $badgeColor === 'yellow' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300' : '' }}
                            {{ $badgeColor === 'gray' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' : '' }}">
                            {{ $cat ?: 'Umum' }}
                        </span>
                    </div>
                @empty
                    <div class="empty-state">
                        <svg class="w-12 h-12 text-text-muted mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <p class="text-text-muted">Belum ada data komoditas.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="bg-surface rounded-xl shadow-sm p-6 border border-border">
            <h3 class="text-lg font-semibold text-text-primary mb-4">Informasi</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-surface-secondary rounded-lg">
                    <span class="text-text-secondary">Arah Tren</span>
                    <span class="font-medium {{ $viewModel->trendDirection === 'up' ? 'text-red-600 dark:text-red-400' : ($viewModel->trendDirection === 'down' ? 'text-green-600 dark:text-green-400' : 'text-text-primary') }}">
                        @if($viewModel->trendDirection === 'up')
                            ↑ Meningkat
                        @elseif($viewModel->trendDirection === 'down')
                            ↓ Menurun
                        @else
                            → Stabil
                        @endif
                    </span>
                </div>
                <div class="flex items-center justify-between p-3 bg-surface-secondary rounded-lg">
                    <span class="text-text-secondary">Terakhir Diperbarui</span>
                    <span class="font-medium text-text-primary">{{ $viewModel->lastUpdated }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Latest Prices Table -->
    <div class="bg-surface rounded-xl shadow-sm border border-border overflow-hidden">
        <div class="px-6 py-4 border-b border-border">
            <h3 class="text-lg font-semibold text-text-primary">Harga Terbaru</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border">
                <thead class="bg-surface-secondary">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-muted uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-muted uppercase">Komoditas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-muted uppercase">Wilayah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-muted uppercase">Harga</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-muted uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-muted uppercase">Sumber</th>
                    </tr>
                </thead>
                <tbody class="bg-surface divide-y divide-border">
                    @forelse($viewModel->latestPrices as $price)
                        <tr class="hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors odd:bg-gray-50/50 dark:odd:bg-gray-700/30">
                            <td class="px-6 py-4 text-sm text-text-muted">{{ $price->id }}</td>
                            <td class="px-6 py-4 text-sm text-text-primary">{{ $price->commodity_name }}</td>
                            <td class="px-6 py-4 text-sm text-text-primary">{{ $price->region_name }}</td>
                            <td class="px-6 py-4">
                                <x-price-badge :amount="$price->price_raw" />
                            </td>
                            <td class="px-6 py-4 text-sm text-text-muted">{{ $price->recorded_date }}</td>
                            <td class="px-6 py-4 text-sm text-text-muted">{{ $price->source ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="empty-state">
                                    <svg class="w-12 h-12 text-text-muted mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <p class="text-text-muted mb-3">Belum ada data harga.</p>
                                    <a href="{{ route('price-records.create') }}" class="btn-primary inline-flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        Catat Harga Baru
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(count($viewModel->latestPrices) > 0)
            <div class="px-6 py-3 border-t border-border text-right">
                <a href="{{ route('price-records.index') }}" class="text-sm text-brand-600 hover:text-brand-700 font-medium inline-flex items-center gap-1">
                    Lihat Semua
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const trendLabels = @json($viewModel->priceTrendLabels);
    const trendData = @json($viewModel->priceTrendData);
    const regionLabels = @json($viewModel->regionComparisonLabels);
    const regionData = @json($viewModel->regionComparisonData);

    let trendChart = null;
    let regionChart = null;

    function createCharts() {
        if (trendChart) trendChart.destroy();
        if (regionChart) regionChart.destroy();

        if (!window.Chart) return;

        const trendEl = document.getElementById('priceTrendChart');
        const regionEl = document.getElementById('regionComparisonChart');

        if (!trendEl || !regionEl) return;

        const isDark = document.documentElement.classList.contains('dark');
        const lineColor = isDark ? '#60a5fa' : '#3b82f6';
        const barColor = isDark ? 'rgba(96, 165, 250, 0.7)' : 'rgba(59, 130, 246, 0.7)';
        const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.06)';
        const tickColor = isDark ? '#9ca3af' : '#6b7280';

        if (trendLabels.length > 0) {
            trendChart = new Chart(trendEl.getContext('2d'), {
                type: 'line',
                data: {
                    labels: trendLabels,
                    datasets: [{
                        label: 'Rata-rata Harga',
                        data: trendData,
                        borderColor: lineColor,
                        backgroundColor: lineColor + '1a',
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
                        x: {
                            ticks: { color: tickColor },
                            grid: { color: gridColor }
                        },
                        y: {
                            beginAtZero: false,
                            ticks: {
                                color: tickColor,
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            },
                            grid: { color: gridColor }
                        }
                    }
                }
            });
        }

        if (regionLabels.length > 0) {
            regionChart = new Chart(regionEl.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: regionLabels,
                    datasets: [{
                        label: 'Rata-rata Harga',
                        data: regionData,
                        backgroundColor: barColor,
                        borderColor: lineColor,
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
                        x: {
                            ticks: { color: tickColor },
                            grid: { color: gridColor }
                        },
                        y: {
                            beginAtZero: false,
                            ticks: {
                                color: tickColor,
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            },
                            grid: { color: gridColor }
                        }
                    }
                }
            });
        }
    }

    if (trendLabels.length > 0 || regionLabels.length > 0) {
        createCharts();
    }

    window.addEventListener('theme-changed', function() {
        if (trendLabels.length > 0 || regionLabels.length > 0) {
            createCharts();
        }
    });
});
</script>
@endpush
