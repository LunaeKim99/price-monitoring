@extends('layouts.app')

@section('title', 'Wilayah')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Daftar Wilayah</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola data wilayah pemantauan</p>
        </div>
        <a href="{{ route('regions.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
            + Tambah Wilayah
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Induk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dibuat</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($regions as $region)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $region->getId() }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $region->getName() }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $region->getType() === 'province' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $region->getType() === 'city' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $region->getType() === 'district' ? 'bg-green-100 text-green-800' : '' }}">
                                    {{ ucfirst($region->getType()) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $region->getParentId() ? 'ID: '.$region->getParentId() : '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $region->getCreatedAt() ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                Belum ada wilayah.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
