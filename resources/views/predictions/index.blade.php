@extends('layouts.app')

@section('title', 'Prediksi Harga')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-text-primary">Prediksi Harga Komoditas</h2>
            <p class="text-sm text-text-muted mt-1">Hasil prediksi harga menggunakan metode Moving Average</p>
        </div>
        <a href="{{ route('predictions.create') }}" class="btn-primary inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Generate Prediksi
        </a>
    </div>

    <!-- Batch Status Card -->
    @if($latestBatch)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white text-lg">
                        Batch Prediksi #{{ $latestBatch->getId() }}
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Dibuat: {{ $latestBatch->getCreatedAt() ? \Carbon\Carbon::parse($latestBatch->getCreatedAt())->format('d M Y H:i') : '-' }}
                        @if($latestBatch->getCompletedAt())
                            | Selesai: {{ \Carbon\Carbon::parse($latestBatch->getCompletedAt())->format('d M Y H:i') }}
                        @endif
                    </p>
                </div>
                <x-prediction-batch-status :status="$latestBatch->getStatus()" />
            </div>

            @if($latestBatch->getAiInsight())
                <div class="mt-4 p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg border border-indigo-200 dark:border-indigo-700/50">
                    <p class="text-sm font-medium text-indigo-800 dark:text-indigo-300 mb-2">
                        Ringkasan AI
                    </p>
                    <p class="text-sm text-indigo-700 dark:text-indigo-400 whitespace-pre-line leading-relaxed">
                        {{ $latestBatch->getAiInsight() }}
                    </p>
                    @if($latestBatch->getAiInsightGeneratedAt())
                        <p class="text-xs text-indigo-500 dark:text-indigo-500 mt-2">
                            Dihasilkan: {{ \Carbon\Carbon::parse($latestBatch->getAiInsightGeneratedAt())->format('d M Y H:i') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6 mb-6">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Belum ada batch prediksi otomatis. Jalankan <code class="bg-gray-100 dark:bg-gray-700 px-1 py-0.5 rounded text-xs">php artisan predictions:generate-weekly</code> atau buat prediksi manual melalui tombol "Generate Prediksi".
            </p>
        </div>
    @endif

    <!-- Summary Cards -->
    @php
        $displayPredictions = $batchPredictions->isNotEmpty() ? $batchPredictions : $predictions;
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="bg-surface rounded-xl shadow-sm p-4 border border-border">
            <p class="text-sm text-text-muted">Total Prediksi</p>
            <p class="text-2xl font-bold text-text-primary">{{ collect($displayPredictions)->count() }}</p>
        </div>
        <div class="bg-surface rounded-xl shadow-sm p-4 border border-border">
            <p class="text-sm text-text-muted">Rata-rata Confidence</p>
            <p class="text-2xl font-bold text-text-primary">
                @php
                    $confidences = collect($displayPredictions)->map(fn($p) => $p->getConfidence() ?? 0);
                    $avgConf = $confidences->count() > 0 ? $confidences->avg() : 0;
                @endphp
                {{ number_format($avgConf * 100, 1) }}%
            </p>
        </div>
    </div>

    <div class="bg-surface rounded-xl shadow-sm border border-border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border">
                <thead class="bg-surface-secondary">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-muted uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-muted uppercase">Komoditas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-muted uppercase">Wilayah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-muted uppercase">Harga Prediksi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-muted uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-muted uppercase">Confidence</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-muted uppercase">Metode</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-text-muted uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-surface divide-y divide-border">
                    @forelse($displayPredictions as $prediction)
                        <tr class="hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors odd:bg-gray-50/50 dark:odd:bg-gray-700/30">
                            <td class="px-6 py-4 text-sm text-text-muted">{{ $prediction->getId() }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-text-primary">
                                {{ $commodityMap[$prediction->getCommodityId()] ?? 'ID: '.$prediction->getCommodityId() }}
                            </td>
                            <td class="px-6 py-4 text-sm text-text-primary">
                                {{ $regionMap[$prediction->getRegionId()] ?? 'ID: '.$prediction->getRegionId() }}
                            </td>
                            <td class="px-6 py-4">
                                <x-price-badge :amount="$prediction->getPredictedPrice()" />
                            </td>
                            <td class="px-6 py-4 text-sm text-text-muted">{{ $prediction->getPredictedDate()->format('Y-m-d') }}</td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full">
                                        <div class="h-1.5 rounded-full" style="width: {{ $prediction->getConfidence() * 100 }}%; background-color: {{ $prediction->getConfidence() > 0.7 ? '#22c55e' : ($prediction->getConfidence() > 0.4 ? '#eab308' : '#ef4444') }}"></div>
                                    </div>
                                    <span class="text-xs text-text-muted">{{ number_format($prediction->getConfidence() * 100, 1) }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-text-muted">{{ $prediction->getModelName() ?? '-' }}</td>
                            <td class="px-6 py-4 text-right text-sm">
                                <form method="POST" action="{{ route('predictions.destroy', $prediction->getId()) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="empty-state">
                                    <svg class="w-12 h-12 text-text-muted mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                    </svg>
                                    <p class="text-text-muted mb-3">Belum ada data prediksi.</p>
                                    <a href="{{ route('predictions.create') }}" class="btn-primary inline-flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        Buat Prediksi Baru
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
