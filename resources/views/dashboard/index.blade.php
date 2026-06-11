@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
{{-- Page Header --}}
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-text-primary">Dashboard</h1>
        <p class="text-sm text-text-muted mt-0.5">Monitor harga komoditas pangan secara real-time</p>
    </div>
    <a href="{{ route('price-records.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Catat Harga
    </a>
</div>

{{-- ZONA 1: Stats --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    @php
    $stats = [
        ['label' => 'Total Komoditas',  'value' => $viewModel->totalCommodities,  'color' => 'blue',
         'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
        ['label' => 'Total Wilayah',    'value' => $viewModel->totalRegions,       'color' => 'green',
         'icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z'],
        ['label' => 'Total Data Harga', 'value' => $viewModel->totalPriceRecords,  'color' => 'purple',
         'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
        ['label' => 'Rata-rata Harga',  'value' => 'Rp ' . $viewModel->averagePrice, 'color' => 'orange',
         'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
    ];
    $colorMap = [
        'blue'   => ['border' => 'border-blue-500',   'bg' => 'from-blue-500 to-blue-600'],
        'green'  => ['border' => 'border-green-500',  'bg' => 'from-green-500 to-green-600'],
        'purple' => ['border' => 'border-purple-500', 'bg' => 'from-purple-500 to-purple-600'],
        'orange' => ['border' => 'border-orange-500', 'bg' => 'from-orange-500 to-orange-600'],
    ];
    @endphp
    @foreach($stats as $stat)
    @php $c = $colorMap[$stat['color']]; @endphp
    <div class="bg-surface rounded-xl shadow-sm p-5 border border-border hover:shadow-md transition-shadow border-b-4 {{ $c['border'] }}">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-text-muted font-medium uppercase tracking-wide">{{ $stat['label'] }}</p>
                <p class="text-2xl font-bold text-text-primary mt-1">{{ $stat['value'] }}</p>
            </div>
            <div class="bg-gradient-to-br {{ $c['bg'] }} p-3 rounded-full shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}"/>
                </svg>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- ZONA 2: AI Insight Panel --}}
<div class="mb-8" x-data="{ refreshing: false }">
    @if($viewModel->aiInsight)
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl shadow-sm p-6 border border-indigo-400/30 relative overflow-hidden">
        <div class="absolute right-4 top-4 opacity-10">
            <svg class="w-24 h-24 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                    d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
            </svg>
        </div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-200" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    <h3 class="font-semibold text-white text-lg">Ringkasan Pasar (AI)</h3>
                    @if($viewModel->aiStatus === 'cached')
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-400/20 text-amber-200">
                        Dari Cache
                    </span>
                    @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-400/20 text-green-200">
                        Live
                    </span>
                    @endif
                </div>
                {{-- Tombol Refresh --}}
                <button
                    type="button"
                    @click="
                        refreshing = true;
                        fetch('{{ route('dashboard.refresh-insight') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json'
                            }
                        })
                        .then(() => window.location.reload())
                        .catch(() => { refreshing = false; })
                    "
                    :disabled="refreshing"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium
                           bg-white/10 hover:bg-white/20 text-white transition-colors
                           disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-3.5 h-3.5" :class="{ 'animate-spin': refreshing }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span x-text="refreshing ? 'Memuat...' : 'Refresh'"></span>
                </button>
            </div>
            <p class="text-white/90 text-sm leading-relaxed whitespace-pre-line">
                {{ $viewModel->aiInsight }}
            </p>
            @if($viewModel->aiInsightGeneratedAt)
            <p class="text-indigo-200 text-xs mt-3">
                Dihasilkan: {{ \Carbon\Carbon::parse($viewModel->aiInsightGeneratedAt)->format('d M Y, H:i') }} WIB
            </p>
            @endif
        </div>
    </div>

    @elseif($viewModel->aiStatus === 'failed')
    <div class="bg-surface rounded-xl border border-border p-5 flex items-start justify-between gap-4">
        <div class="flex items-start gap-3">
            <div class="bg-gray-100 dark:bg-gray-700 rounded-full p-2 shrink-0">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-text-primary">Ringkasan AI tidak tersedia</p>
                <p class="text-xs text-text-muted mt-0.5">Layanan AI sedang tidak dapat dihubungi. Coba lagi dalam beberapa saat.</p>
            </div>
        </div>
        <button onclick="window.location.reload()"
            class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium
                   border border-border hover:bg-surface-secondary text-text-secondary transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Coba Lagi
        </button>
    </div>

    @elseif($viewModel->aiStatus === 'no_key')
    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-xl p-4 flex items-start gap-3">
        <svg class="w-5 h-5 text-amber-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
        </svg>
        <div>
            <p class="text-sm font-medium text-amber-800 dark:text-amber-200">Wawasan AI belum aktif</p>
            <p class="text-xs text-amber-600 dark:text-amber-400 mt-1">
                Konfigurasikan <code class="bg-amber-100 dark:bg-amber-800 px-1 rounded font-mono">GROQ_API_KEY</code>
                di file <code class="bg-amber-100 dark:bg-amber-800 px-1 rounded font-mono">.env</code>
                untuk mengaktifkan ringkasan pasar berbasis AI.
            </p>
        </div>
    </div>
    @endif
</div>

{{-- ZONA 3: Charts --}}
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-8">
    <div class="lg:col-span-7 bg-surface rounded-xl shadow-sm p-6 border border-border">
        <h3 class="text-base font-semibold text-text-primary mb-4">
            Tren Harga Rata-rata (30 Hari Terakhir)
        </h3>
        @if(count($viewModel->priceTrendLabels) > 0)
        <div class="relative h-[320px]">
            <canvas id="priceTrendChart"></canvas>
        </div>
        @else
        <x-chart-placeholder message="Belum ada data tren harga." />
        @endif
    </div>
    <div class="lg:col-span-5 bg-surface rounded-xl shadow-sm p-6 border border-border">
        <h3 class="text-base font-semibold text-text-primary mb-4">
            Rata-rata Harga per Wilayah
        </h3>
        @if(count($viewModel->regionComparisonLabels) > 0)
        <div class="relative h-[320px]">
            <canvas id="regionComparisonChart"></canvas>
        </div>
        @else
        <x-chart-placeholder message="Belum ada data perbandingan wilayah." />
        @endif
    </div>
</div>

{{-- ZONA 4: Tabel + Sidebar Info --}}
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-8">

    {{-- Tabel Harga Terbaru (kiri) --}}
    <div class="lg:col-span-7 bg-surface rounded-xl shadow-sm border border-border overflow-hidden">
        <div class="px-6 py-4 border-b border-border flex items-center justify-between">
            <h3 class="text-base font-semibold text-text-primary">Harga Terbaru</h3>
            <a href="{{ route('price-records.index') }}"
               class="text-xs text-brand-600 hover:text-brand-700 font-medium inline-flex items-center gap-1">
                Lihat Semua
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border text-sm">
                <thead class="bg-surface-secondary">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-medium text-text-muted uppercase tracking-wide">Komoditas</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-text-muted uppercase tracking-wide">Wilayah</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-text-muted uppercase tracking-wide">Harga</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-text-muted uppercase tracking-wide">Tanggal</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-text-muted uppercase tracking-wide">Sumber</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($viewModel->latestPrices as $price)
                    <tr class="hover:bg-surface-secondary transition-colors">
                        <td class="px-5 py-3 font-medium text-text-primary">{{ $price->commodity_name }}</td>
                        <td class="px-5 py-3 text-text-secondary">{{ $price->region_name }}</td>
                        <td class="px-5 py-3"><x-price-badge :amount="$price->price_raw" /></td>
                        <td class="px-5 py-3 text-text-muted">{{ $price->recorded_date }}</td>
                        <td class="px-5 py-3 text-text-muted">{{ $price->source ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-text-muted text-sm">
                            Belum ada data harga.
                            <a href="{{ route('price-records.create') }}" class="text-brand-600 hover:underline ml-1">Catat sekarang →</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Sidebar Info (kanan) --}}
    <div class="lg:col-span-5 flex flex-col gap-4">

        {{-- Komoditas Terpopuler --}}
        <div class="bg-surface rounded-xl shadow-sm border border-border p-5 flex-1">
            <h3 class="text-base font-semibold text-text-primary mb-3">Komoditas Terpopuler</h3>
            <div class="space-y-2">
                @forelse($viewModel->trendingCommodities as $commodity)
                <div class="flex items-center justify-between py-2 px-3 rounded-lg bg-surface-secondary">
                    <div>
                        <p class="text-sm font-medium text-text-primary">{{ $commodity->name }}</p>
                        <p class="text-xs text-text-muted">{{ $commodity->unit }}</p>
                    </div>
                    @php
                        $cat = $commodity->category ?? '';
                        $badge = match(true) {
                            str_contains($cat, 'Sembako') || str_contains($cat, 'Beras') => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
                            str_contains($cat, 'Protein') || str_contains($cat, 'Daging') || str_contains($cat, 'Telur') => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
                            str_contains($cat, 'Bumbu') || str_contains($cat, 'Cabai') || str_contains($cat, 'Bawang') => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300',
                            default => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
                        };
                    @endphp
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $badge }}">
                        {{ $cat ?: 'Umum' }}
                    </span>
                </div>
                @empty
                <p class="text-sm text-text-muted py-4 text-center">Belum ada data komoditas.</p>
                @endforelse
            </div>
        </div>

        {{-- Status Pasar --}}
        <div class="bg-surface rounded-xl shadow-sm border border-border p-5">
            <h3 class="text-base font-semibold text-text-primary mb-3">Status Pasar</h3>
            <div class="space-y-2">
                <div class="flex items-center justify-between py-2 px-3 rounded-lg bg-surface-secondary">
                    <span class="text-sm text-text-secondary">Arah Tren</span>
                    <span class="text-sm font-semibold
                        {{ $viewModel->trendDirection === 'up' ? 'text-red-600 dark:text-red-400' :
                           ($viewModel->trendDirection === 'down' ? 'text-green-600 dark:text-green-400' :
                           'text-text-primary') }}">
                        @if($viewModel->trendDirection === 'up') ↑ Meningkat
                        @elseif($viewModel->trendDirection === 'down') ↓ Menurun
                        @else → Stabil
                        @endif
                    </span>
                </div>
                <div class="flex items-center justify-between py-2 px-3 rounded-lg bg-surface-secondary">
                    <span class="text-sm text-text-secondary">Terakhir Diperbarui</span>
                    <span class="text-sm font-medium text-text-primary">{{ $viewModel->lastUpdated }}</span>
                </div>
                <div class="flex items-center justify-between py-2 px-3 rounded-lg bg-surface-secondary">
                    <span class="text-sm text-text-secondary">Total Komoditas Aktif</span>
                    <span class="text-sm font-medium text-text-primary">{{ $viewModel->totalCommodities }}</span>
                </div>
            </div>
        </div>
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
                    indexAxis: 'y',
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            beginAtZero: false,
                            ticks: {
                                color: tickColor,
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            },
                            grid: { color: gridColor }
                        },
                        y: {
                            ticks: { color: tickColor },
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
