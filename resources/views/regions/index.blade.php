@extends('layouts.app')

@section('title', 'Wilayah')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-text-primary">Daftar Wilayah</h2>
            <p class="text-sm text-text-muted mt-1">Kelola data wilayah pemantauan</p>
        </div>
        <a href="{{ route('regions.create') }}" class="btn-primary inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Wilayah
        </a>
    </div>

    <div class="bg-surface rounded-xl shadow-sm border border-border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border">
                <thead class="bg-surface-secondary">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-muted uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-muted uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-muted uppercase">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-muted uppercase">Induk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-muted uppercase">Dibuat</th>
                    </tr>
                </thead>
                <tbody class="bg-surface divide-y divide-border">
                    @forelse($regions as $region)
                        <tr class="hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors odd:bg-gray-50/50 dark:odd:bg-gray-700/30">
                            <td class="px-6 py-4 text-sm text-text-muted">{{ $region->getId() }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-text-primary">{{ $region->getName() }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $region->getType() === 'province' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300' : '' }}
                                    {{ $region->getType() === 'city' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300' : '' }}
                                    {{ $region->getType() === 'district' ? 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300' : '' }}">
                                    {{ ucfirst($region->getType()) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-text-muted">
                                {{ $region->getParentId() ? ($regionMap[$region->getParentId()] ?? 'ID: '.$region->getParentId()) : '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-text-muted">{{ $region->getCreatedAt() ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="empty-state">
                                    <svg class="w-12 h-12 text-text-muted mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <p class="text-text-muted mb-3">Belum ada wilayah.</p>
                                    <a href="{{ route('regions.create') }}" class="btn-primary inline-flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        Tambah Wilayah
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
