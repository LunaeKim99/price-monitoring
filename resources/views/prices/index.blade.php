@extends('layouts.app')

@section('title', 'Data Harga')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Data Harga Komoditas</h2>
            <p class="text-sm text-gray-500 mt-1">Total: {{ $viewModel->totalCount }} record</p>
        </div>
        <a href="{{ route('price-records.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
            + Catat Harga
        </a>
    </div>

    <!-- Filter Bar -->
    @include('components.filter-bar', [
        'commodities' => $viewModel->commodities,
        'regions' => $viewModel->regions,
        'filters' => $viewModel->filters,
    ])

    <!-- Prices Table -->
    <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catatan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($viewModel->prices as $record)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $record->getId() }}</td>
                            <td class="px-6 py-4 text-sm text-gray-800">{{ $record->getCommodityId() }}</td>
                            <td class="px-6 py-4 text-sm text-gray-800">{{ $record->getRegionId() }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Rp {{ number_format($record->getPrice(), 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $record->getRecordedDate()->format('Y-m-d') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $record->getSource() ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500 max-w-[200px] truncate">{{ $record->getNotes() ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                Tidak ada data harga.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($viewModel->prices->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $viewModel->prices->links() }}
            </div>
        @endif
    </div>
@endsection
