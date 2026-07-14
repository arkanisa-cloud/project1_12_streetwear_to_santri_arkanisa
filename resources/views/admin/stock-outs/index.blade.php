@extends('layouts.admin')

@section('title', 'Stok Keluar')

@section('content')
    <div class="space-y-6">
        <div
            class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <div>
                <h1 class="text-2xl font-black tracking-tighter text-zinc-950 uppercase italic">Catatan Stok Keluar</h1>
                <p class="text-sm text-zinc-500">Pantau pengurangan inventaris dan penyesuaian stok.</p>
            </div>
            <a href="{{ route('admin.stock-outs.create') }}"
                class="inline-flex items-center justify-center px-6 py-3 bg-zinc-950 hover:bg-zinc-800 text-white text-xs font-bold uppercase tracking-widest transition-all rounded-lg">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                </svg>
                Tambah Stok Keluar
            </a>
        </div>

        {{-- Stock Outs Table --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse datatable">
                    <thead>
                        <tr class="bg-zinc-50 border-b border-gray-100">
                            <th class="px-4 py-4 text-xs font-black uppercase tracking-widest text-zinc-600 w-16">ID</th>
                            <th class="px-4 py-4 text-xs font-black uppercase tracking-widest text-zinc-600">Tanggal</th>
                            <th class="px-4 py-4 text-xs font-black uppercase tracking-widest text-zinc-600">Produk</th>
                            <th class="px-4 py-4 text-xs font-black uppercase tracking-widest text-zinc-600">Alasan</th>
                            <th class="px-4 py-4 text-xs font-black uppercase tracking-widest text-zinc-600 text-center">Qty Keluar</th>
                            <th class="px-4 py-4 text-xs font-black uppercase tracking-widest text-zinc-600">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($stockOuts as $stockOut)
                            <tr class="hover:bg-zinc-50/50 transition-colors group">
                                <td class="px-4 py-4">
                                    <span class="text-sm font-bold text-zinc-950">#{{ $stockOut->id }}</span>
                                </td>
                                <td class="px-4 py-4 text-sm text-zinc-600">
                                    {{ $stockOut->tanggal_keluar->translatedFormat('d M Y') }}
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-sm font-bold text-zinc-950">{{ $stockOut->product->name }}</div>
                                    <div class="text-[10px] text-zinc-400 font-medium italic">ID: #PROD-{{ $stockOut->product->id }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    @php
                                        $alasanMap = [
                                            'rusak'      => 'bg-red-100 text-red-800',
                                            'hilang'     => 'bg-amber-100 text-amber-800',
                                            'kadaluarsa' => 'bg-cyan-100 text-cyan-800',
                                            'lainnya'    => 'bg-zinc-100 text-zinc-600',
                                        ];
                                        $alasanClass = $alasanMap[$stockOut->alasan] ?? 'bg-zinc-100 text-zinc-600';
                                    @endphp
                                    <span class="inline-block {{ $alasanClass }} px-2 py-1 rounded text-[10px] font-black uppercase tracking-wider">
                                        {{ $stockOut->alasan_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="inline-block bg-red-100 text-red-800 px-3 py-1 rounded text-xs font-black">
                                        -{{ $stockOut->qty }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-sm text-zinc-600 italic">{{ $stockOut->keterangan ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
