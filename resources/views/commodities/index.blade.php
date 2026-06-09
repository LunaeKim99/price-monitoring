@extends('layouts.app')

@section('title', 'Komoditas')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-text-primary">Daftar Komoditas</h2>
            <p class="text-sm text-text-muted mt-1">Kelola data komoditas yang dipantau</p>
        </div>
        <a href="{{ route('commodities.create') }}" class="btn-primary inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Komoditas
        </a>
    </div>

    <div class="bg-surface rounded-xl shadow-sm border border-border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border">
                <thead class="bg-surface-secondary">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-muted uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-muted uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-muted uppercase">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-muted uppercase">Satuan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-muted uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-text-muted uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-surface divide-y divide-border">
                    @forelse($commodities as $commodity)
                        <tr class="hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors odd:bg-gray-50/50 dark:odd:bg-gray-700/30">
                            <td class="px-6 py-4 text-sm text-text-muted">{{ $commodity->getId() }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-text-primary">{{ $commodity->getName() }}</td>
                            <td class="px-6 py-4 text-sm text-text-muted">{{ $commodity->getCategory() ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-text-muted">{{ $commodity->getUnit() }}</td>
                            <td class="px-6 py-4">
                                @if($commodity->isActive())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-sm">
                                <a href="{{ route('commodities.edit', $commodity->getId()) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 mr-3">Edit</a>
                                <form method="POST" action="{{ route('commodities.destroy', $commodity->getId()) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="empty-state">
                                    <svg class="w-12 h-12 text-text-muted mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                    <p class="text-text-muted mb-3">Belum ada komoditas.</p>
                                    <a href="{{ route('commodities.create') }}" class="btn-primary inline-flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        Tambah Komoditas
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
