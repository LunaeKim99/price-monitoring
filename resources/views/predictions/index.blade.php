@extends('layouts.app')

@section('title', 'Prediksi Harga')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Prediksi Harga Komoditas</h2>
            <p class="text-sm text-gray-500 mt-1">Hasil prediksi harga menggunakan metode Moving Average</p>
        </div>
        <a href="{{ route('predictions.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
            + Generate Prediksi
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Komoditas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Wilayah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga Prediksi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Confidence</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Metode</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($predictions as $prediction)
                        <tr class="hover:bg-blue-50 transition-colors odd:bg-gray-50/50">
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $prediction->getId() }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                {{ $commodityMap[$prediction->getCommodityId()] ?? 'ID: '.$prediction->getCommodityId() }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-800">
                                {{ $regionMap[$prediction->getRegionId()] ?? 'ID: '.$prediction->getRegionId() }}
                            </td>
                            <td class="px-6 py-4">
                                <x-price-badge :amount="$prediction->getPredictedPrice()" />
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $prediction->getPredictedDate()->format('Y-m-d') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $prediction->getConfidence() !== null ? number_format($prediction->getConfidence() * 100, 1).'%' : '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $prediction->getModelName() ?? '-' }}</td>
                            <td class="px-6 py-4 text-right text-sm">
                                <form method="POST" action="{{ route('predictions.destroy', $prediction->getId()) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                Belum ada data prediksi.
                                <a href="{{ route('predictions.create') }}" class="text-blue-600 hover:underline">Buat prediksi baru</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
