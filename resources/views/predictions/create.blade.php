@extends('layouts.app')

@section('title', 'Generate Prediksi')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-text-primary">Generate Prediksi Harga</h2>
        <p class="text-sm text-text-muted mt-1">Pilih komoditas, wilayah, dan periode prediksi</p>
    </div>

    <div class="bg-surface rounded-xl shadow-sm border border-border p-6 max-w-xl">
        <form method="POST" action="{{ route('predictions.generate') }}" x-data="{ loading: false }" x-on:submit="loading = true">
            @csrf

            <div class="mb-4">
                <label for="commodity_id" class="block text-sm font-medium text-text-secondary dark:text-gray-300 mb-1">Komoditas</label>
                <select name="commodity_id" id="commodity_id"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 border px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 @error('commodity_id') border-red-500 dark:border-red-400 @enderror"
                        required>
                    <option value="">Pilih Komoditas</option>
                    @foreach($commodities as $commodity)
                        <option value="{{ $commodity->getId() }}" {{ old('commodity_id') == $commodity->getId() ? 'selected' : '' }}>
                            {{ $commodity->getName() }}
                        </option>
                    @endforeach
                </select>
                @error('commodity_id')
                    <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="region_id" class="block text-sm font-medium text-text-secondary dark:text-gray-300 mb-1">Wilayah</label>
                <select name="region_id" id="region_id"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 border px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 @error('region_id') border-red-500 dark:border-red-400 @enderror"
                        required>
                    <option value="">Pilih Wilayah</option>
                    @foreach($regions as $region)
                        <option value="{{ $region->getId() }}" {{ old('region_id') == $region->getId() ? 'selected' : '' }}>
                            {{ $region->getName() }}
                        </option>
                    @endforeach
                </select>
                @error('region_id')
                    <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Segmented Period Selector -->
            <div x-data="{ days: '{{ old('days', '7') }}' }" class="mb-6">
                <label class="block text-sm font-medium text-text-secondary dark:text-gray-300 mb-3">Periode Prediksi</label>
                <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-1 inline-flex" role="group">
                    <label class="px-4 py-2 rounded-md text-sm font-medium cursor-pointer transition-colors"
                           :class="days === '7' ? 'bg-white dark:bg-gray-700 shadow-sm text-brand-600' : 'text-text-secondary'">
                        <input type="radio" name="days" value="7" x-model="days" class="sr-only"> 7 Hari
                    </label>
                    <label class="px-4 py-2 rounded-md text-sm font-medium cursor-pointer transition-colors"
                           :class="days === '14' ? 'bg-white dark:bg-gray-700 shadow-sm text-brand-600' : 'text-text-secondary'">
                        <input type="radio" name="days" value="14" x-model="days" class="sr-only"> 14 Hari
                    </label>
                    <label class="px-4 py-2 rounded-md text-sm font-medium cursor-pointer transition-colors"
                           :class="days === '30' ? 'bg-white dark:bg-gray-700 shadow-sm text-brand-600' : 'text-text-secondary'">
                        <input type="radio" name="days" value="30" x-model="days" class="sr-only"> 30 Hari
                    </label>
                </div>
                @error('days')
                    <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Info Box -->
            <div class="rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 p-4 mb-6">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-blue-800 dark:text-blue-300">Metode SMA (Simple Moving Average)</p>
                        <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">Prediksi dihitung berdasarkan rata-rata harga dari 7 data terakhir dengan koreksi tren linear. Semakin panjang periode, semakin halus kurva prediksi.</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="btn-primary inline-flex items-center gap-2" :disabled="loading">
                    <span x-show="!loading">Generate Prediksi</span>
                    <span x-show="loading" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Memproses...
                    </span>
                </button>
                <a href="{{ route('predictions.index') }}" class="btn-secondary">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection
