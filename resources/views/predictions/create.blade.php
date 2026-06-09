@extends('layouts.app')

@section('title', 'Generate Prediksi')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Generate Prediksi Harga</h2>
        <p class="text-sm text-gray-500 mt-1">Pilih komoditas, wilayah, dan periode prediksi</p>
    </div>

    <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 max-w-xl">
        <form method="POST" action="{{ route('predictions.generate') }}">
            @csrf

            <div class="mb-4">
                <label for="commodity_id" class="block text-sm font-medium text-gray-700 mb-1">Komoditas</label>
                <select name="commodity_id" id="commodity_id"
                        class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 @error('commodity_id') border-red-500 @enderror"
                        required>
                    <option value="">Pilih Komoditas</option>
                    @foreach($commodities as $commodity)
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
                    @foreach($regions as $region)
                        <option value="{{ $region->getId() }}" {{ old('region_id') == $region->getId() ? 'selected' : '' }}>
                            {{ $region->getName() }}
                        </option>
                    @endforeach
                </select>
                @error('region_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Periode Prediksi</label>
                <div class="flex gap-4">
                    <label class="flex items-center">
                        <input type="radio" name="days" value="7" {{ old('days', '7') == '7' ? 'checked' : '' }} class="text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">7 Hari</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="days" value="14" {{ old('days', '7') == '14' ? 'checked' : '' }} class="text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">14 Hari</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="days" value="30" {{ old('days', '7') == '30' ? 'checked' : '' }} class="text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">30 Hari</span>
                    </label>
                </div>
                @error('days')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                    Generate Prediksi
                </button>
                <a href="{{ route('predictions.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection
