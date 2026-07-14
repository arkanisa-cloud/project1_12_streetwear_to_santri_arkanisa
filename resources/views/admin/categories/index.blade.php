@extends('layouts.admin')

@section('content')
    <div class="space-y-6">
        {{-- Header Section --}}
        <div
            class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <div>
                <h1 class="text-2xl font-black tracking-tighter text-zinc-950 uppercase italic">Manajemen Kategori</h1>
                <p class="text-sm text-zinc-500 font-medium">Kelola semua kategori koleksi streetwear kamu.</p>
            </div>
            <a href="{{ route('admin.categories.create') }}"
                class="inline-flex items-center justify-center px-6 py-3 bg-zinc-950 hover:bg-zinc-800 text-white text-xs font-bold uppercase tracking-widest transition-all rounded-lg">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Kategori
            </a>
        </div>

        {{-- Table Container --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse datatable">
                    <thead>
                        <tr class="bg-zinc-50 border-b border-gray-100">
                            <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-zinc-600">Nama Kategori
                            </th>
                            <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-zinc-600">Deskripsi
                            </th>
                            <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-zinc-600 text-center">
                                Item</th>
                            <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-zinc-600 text-center">
                                Status</th>
                            <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-zinc-600 text-right">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($categories as $category)
                            <tr class="hover:bg-zinc-50/50 transition-colors group">
                                <td class="px-6 py-4">
                                    <div
                                        class="text-sm font-bold text-zinc-950 uppercase tracking-tight group-hover:italic transition-all">
                                        {{ $category->name }}
                                    </div>
                                    <div class="text-[10px] text-zinc-400 font-medium italic">UUID: #CAT-{{ $category->id }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-zinc-500 italic line-clamp-1">
                                        {{ $category->description ?? 'Tidak ada deskripsi.' }}
                                    </p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-sm font-black text-zinc-950 italic">
                                        {{ $category->products->count() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($category->status === 'active')
                                        <span
                                            class="text-[10px] font-black uppercase px-2 py-1 bg-zinc-100 text-zinc-600 rounded">Aktif</span>
                                    @else
                                        <span
                                            class="text-[10px] font-black uppercase px-2 py-1 bg-red-50 text-red-600 rounded">Inaktif</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('admin.categories.edit', $category) }}"
                                        class="inline-flex p-2 text-zinc-400 hover:text-zinc-950 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                                        class="inline delete-form">
                                        @csrf @method('DELETE')
                                        <button type="button"
                                            class="p-2 text-zinc-300 hover:text-red-600 transition-colors"
                                            onclick="
                                                Swal.fire({
                                                    title: 'Hapus Kategori?',
                                                    text: 'Data kategori ini akan dihapus permanen dari sistem.',
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonText: 'YA, HAPUS',
                                                    cancelButtonText: 'BATAL',
                                                    reverseButtons: true
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        this.closest('form').submit();
                                                    }
                                                })
                                            ">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
