@extends('layouts.app')

@section('title', 'Data Harga')

@section('content')
    @php
        $commodityMap = [];
        foreach ($viewModel->commodities as $c) {
            $commodityMap[$c->getId()] = $c->getName();
        }
        $regionMap = [];
        foreach ($viewModel->regions as $r) {
            $regionMap[$r->getId()] = $r->getName();
        }
    @endphp

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-text-primary">Data Harga Komoditas</h2>
            <p class="text-sm text-text-muted mt-1">Total: {{ $viewModel->totalCount }} record</p>
        </div>
        <a href="{{ route('price-records.create') }}" class="btn-primary inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Catat Harga
        </a>
    </div>

    <!-- Filter Bar -->
    @include('components.filter-bar', [
        'commodities' => $viewModel->commodities,
        'regions' => $viewModel->regions,
        'filters' => $viewModel->filters,
    ])

    <!-- Prices Table -->
    <div class="bg-surface rounded-xl shadow-sm border border-border overflow-hidden">
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-muted uppercase">Catatan</th>
                    </tr>
                </thead>
                <tbody class="bg-surface divide-y divide-border">
                    @forelse($viewModel->prices as $record)
                        <tr class="hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors odd:bg-gray-50/50 dark:odd:bg-gray-700/30">
                            <td class="px-6 py-4 text-sm text-text-muted">{{ $record->getId() }}</td>
                            <td class="px-6 py-4 text-sm text-text-primary">{{ $commodityMap[$record->getCommodityId()] ?? 'ID: '.$record->getCommodityId() }}</td>
                            <td class="px-6 py-4 text-sm text-text-primary">{{ $regionMap[$record->getRegionId()] ?? 'ID: '.$record->getRegionId() }}</td>
                            <td class="px-6 py-4">
                                <x-price-badge :amount="$record->getPrice()" />
                            </td>
                            <td class="px-6 py-4 text-sm text-text-muted">{{ $record->getRecordedDate()->format('Y-m-d') }}</td>
                            <td class="px-6 py-4 text-sm text-text-muted">{{ $record->getSource() ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-text-muted max-w-[200px] truncate">{{ $record->getNotes() ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
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

        @if($viewModel->prices->hasPages())
            <div class="px-6 py-4 border-t border-border">
                {{ $viewModel->prices->links() }}
            </div>
        @endif
    </div>
@endsection
