@extends('layouts.app')

@section('title', 'Tambah Wilayah')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Tambah Wilayah</h2>
        <p class="text-sm text-gray-500 mt-1">Tambahkan wilayah pemantauan baru</p>
    </div>

    <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 max-w-lg">
        <form method="POST" action="{{ route('regions.store') }}">
            @csrf

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Wilayah</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}"
                       class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                       required>
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Tipe Wilayah</label>
                <select name="type" id="type"
                        class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 @error('type') border-red-500 @enderror"
                        required>
                    <option value="">Pilih Tipe</option>
                    <option value="province" {{ old('type') === 'province' ? 'selected' : '' }}>Provinsi</option>
                    <option value="city" {{ old('type') === 'city' ? 'selected' : '' }}>Kota</option>
                    <option value="district" {{ old('type') === 'district' ? 'selected' : '' }}>Kecamatan</option>
                </select>
                @error('type')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-1">Wilayah Induk</label>
                <select name="parent_id" id="parent_id"
                        class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tidak Ada (Provinsi)</option>
                    @foreach($provinces as $province)
                        <option value="{{ $province->getId() }}" {{ old('parent_id') == $province->getId() ? 'selected' : '' }}>
                            {{ $province->getName() }}
                        </option>
                    @endforeach
                </select>
                @error('parent_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                    Simpan
                </button>
                <a href="{{ route('regions.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection
