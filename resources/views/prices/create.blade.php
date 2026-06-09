@extends('layouts.app')

@section('title', 'Catat Harga')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Catat Harga Baru</h2>
        <p class="text-sm text-gray-500 mt-1">Masukkan data harga komoditas terbaru</p>
    </div>

    <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 max-w-xl">
        <form method="POST" action="{{ route('price-records.store') }}">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="commodity_id" class="block text-sm font-medium text-gray-700 mb-1">Komoditas</label>
                    <select name="commodity_id" id="commodity_id"
                            class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 @error('commodity_id') border-red-500 @enderror"
                            required>
                        <option value="">Pilih Komoditas</option>
                        @foreach($viewModel->commodities as $commodity)
                            <option value="{{ $commodity->getId() }}" {{ old('commodity_id') == $commodity->getId() ? 'selected' : '' }}>
                                {{ $commodity->getName() }}
                            </option>
                        @endforeach
                    </select>
                    @error('commodity_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="region_id" class="block text-sm font-medium text-gray-700 mb-1">Wilayah</label>
                    <select name="region_id" id="region_id"
                            class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 @error('region_id') border-red-500 @enderror"
                            required>
                        <option value="">Pilih Wilayah</option>
                        @foreach($viewModel->regions as $region)
                            <option value="{{ $region->getId() }}" {{ old('region_id') == $region->getId() ? 'selected' : '' }}>
                                {{ $region->getName() }}
                            </option>
                        @endforeach
                    </select>
                    @error('region_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp)</label>
                <input type="number" name="price" id="price" value="{{ old('price') }}"
                       class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 @error('price') border-red-500 @enderror"
                       step="0.01" min="0" required>
                @error('price')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="recorded_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                <input type="date" name="recorded_date" id="recorded_date" value="{{ old('recorded_date', date('Y-m-d')) }}"
                       class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 @error('recorded_date') border-red-500 @enderror"
                       required>
                @error('recorded_date')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="source" class="block text-sm font-medium text-gray-700 mb-1">Sumber</label>
                <input type="text" name="source" id="source" value="{{ old('source') }}"
                       class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Contoh: Pasar Induk, Dinas Perdagangan">
                @error('source')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                <textarea name="notes" id="notes" rows="3"
                          class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Catatan tambahan (opsional)">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                    Simpan
                </button>
                <a href="{{ route('price-records.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection
