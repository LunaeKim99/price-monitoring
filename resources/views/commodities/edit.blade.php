@extends('layouts.app')

@section('title', 'Edit Komoditas')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Edit Komoditas</h2>
        <p class="text-sm text-gray-500 mt-1">Ubah data komoditas</p>
    </div>

    <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 max-w-lg">
        <form method="POST" action="{{ route('commodities.update', $commodity->getId()) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Komoditas</label>
                <input type="text" name="name" id="name" value="{{ old('name', $commodity->getName()) }}"
                       class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                       required>
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <input type="text" name="category" id="category" value="{{ old('category', $commodity->getCategory()) }}"
                       class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                @error('category')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="unit" class="block text-sm font-medium text-gray-700 mb-1">Satuan</label>
                <select name="unit" id="unit"
                        class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 @error('unit') border-red-500 @enderror"
                        required>
                    <option value="kg" {{ old('unit', $commodity->getUnit()) === 'kg' ? 'selected' : '' }}>Kilogram (kg)</option>
                    <option value="liter" {{ old('unit', $commodity->getUnit()) === 'liter' ? 'selected' : '' }}>Liter</option>
                    <option value="gram" {{ old('unit', $commodity->getUnit()) === 'gram' ? 'selected' : '' }}>Gram</option>
                    <option value="butir" {{ old('unit', $commodity->getUnit()) === 'butir' ? 'selected' : '' }}>Butir</option>
                    <option value="ekor" {{ old('unit', $commodity->getUnit()) === 'ekor' ? 'selected' : '' }}>Ekor</option>
                </select>
                @error('unit')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                    Update
                </button>
                <a href="{{ route('commodities.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection
