@extends('layouts.admin')

@section('title', 'Laporan Stok')

@section('content')
    <div class="space-y-6">
        <div
            class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <div>
                <h1 class="text-2xl font-black tracking-tighter text-zinc-950 uppercase italic">Laporan Stok</h1>
                <p class="text-sm text-zinc-500">Laporan status inventaris dan valuasi stok secara menyeluruh.</p>
            </div>
        </div>

        {{-- Filter --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <form action="{{ route('admin.reports.stock') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-600 mb-2">Kategori</label>
                    <select name="category_id"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-zinc-500 text-sm">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-600 mb-2">Status Stok</label>
                    <select name="status"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-zinc-500 text-sm">
                        <option value="">Semua</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>
                            Tersedia (stok &gt; 0)
                        </option>
                        <option value="low" {{ request('status') == 'low' ? 'selected' : '' }}>
                            Menipis (stok ≤ 5)
                        </option>
                        <option value="out" {{ request('status') == 'out' ? 'selected' : '' }}>
                            Habis (stok = 0)
                        </option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="flex-1 bg-zinc-950 hover:bg-zinc-800 text-white text-xs font-bold uppercase tracking-widest px-4 py-2 rounded-lg transition-all">
                        Terapkan Filter
                    </button>
                    <a href="{{ route('admin.reports.stock') }}"
                        class="bg-zinc-100 hover:bg-zinc-200 text-zinc-700 text-xs font-bold uppercase tracking-widest px-4 py-2 rounded-lg transition-all">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white border border-zinc-100 rounded-2xl shadow-sm p-6 text-center">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-2">Total Produk</p>
                <h3 class="text-3xl font-black italic text-zinc-950">{{ $products->count() }}</h3>
                <p class="text-[10px] text-zinc-400 font-medium mt-1 uppercase tracking-wider">Item SKU</p>
            </div>
            <div class="bg-white border border-zinc-100 rounded-2xl shadow-sm p-6 text-center">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-2">Total Nilai Stok</p>
                <h3 class="text-xl font-black italic text-emerald-600">Rp {{ number_format($products->sum(function ($p) { return $p->price * $p->stock; }), 0, ',', '.') }}</h3>
                <p class="text-[10px] text-zinc-400 font-medium mt-1 uppercase tracking-wider">Valuasi</p>
            </div>
            <div class="bg-white border border-amber-100 rounded-2xl shadow-sm p-6 text-center">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-amber-500 mb-2">Stok Menipis</p>
                <h3 class="text-3xl font-black italic text-amber-600">{{ $products->where('stock', '<=', 5)->where('stock', '>', 0)->count() }}</h3>
                <p class="text-[10px] text-zinc-400 font-medium mt-1 uppercase tracking-wider">≤ 5 item</p>
            </div>
            <div class="bg-white border border-red-100 rounded-2xl shadow-sm p-6 text-center">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-red-500 mb-2">Stok Habis</p>
                <h3 class="text-3xl font-black italic text-red-600">{{ $products->where('stock', 0)->count() }}</h3>
                <p class="text-[10px] text-zinc-400 font-medium mt-1 uppercase tracking-wider">= 0 item</p>
            </div>
        </div>

        {{-- Products Table --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse datatable">
                    <thead>
                        <tr class="bg-zinc-50 border-b border-gray-100">
                            <th class="px-4 py-4 text-xs font-black uppercase tracking-widest text-zinc-600 w-16">ID</th>
                            <th class="px-4 py-4 text-xs font-black uppercase tracking-widest text-zinc-600">Produk</th>
                            <th class="px-4 py-4 text-xs font-black uppercase tracking-widest text-zinc-600">Kategori</th>
                            <th class="px-4 py-4 text-xs font-black uppercase tracking-widest text-zinc-600">Harga</th>
                            <th class="px-4 py-4 text-xs font-black uppercase tracking-widest text-zinc-600 text-center">Stok</th>
                            <th class="px-4 py-4 text-xs font-black uppercase tracking-widest text-zinc-600 text-right">Total Nilai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($products as $product)
                            <tr class="hover:bg-zinc-50/50 transition-colors group">
                                <td class="px-4 py-4">
                                    <span class="text-sm font-bold text-zinc-950">#{{ $product->id }}</span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-sm font-bold text-zinc-950">{{ $product->name }}</div>
                                    <div class="text-[10px] text-zinc-400 font-medium italic">ID: #PROD-{{ $product->id }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-xs font-bold text-zinc-500 uppercase tracking-tight bg-zinc-100 px-2 py-1 rounded">
                                        {{ $product->category->name }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-sm font-black text-zinc-900">{{ $product->formatted_price }}</td>
                                <td class="px-4 py-4 text-center">
                                    @if ($product->stock == 0)
                                        <span class="inline-block bg-red-100 text-red-800 px-2 py-1 rounded text-[10px] font-black uppercase">
                                            Habis
                                        </span>
                                    @elseif($product->stock <= 5)
                                        <span class="inline-block bg-amber-100 text-amber-800 px-2 py-1 rounded text-[10px] font-black uppercase">
                                            {{ $product->stock }} — Menipis
                                        </span>
                                    @else
                                        <span class="inline-block bg-emerald-100 text-emerald-800 px-2 py-1 rounded text-[10px] font-black uppercase">
                                            {{ $product->stock }} Tersedia
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-right text-sm font-black text-zinc-900">
                                    Rp {{ number_format($product->price * $product->stock, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-zinc-50 border-t-2 border-zinc-200">
                            <td colspan="5"
                                class="px-4 py-4 text-right text-xs font-black text-zinc-700 uppercase tracking-widest">
                                Grand Total:</td>
                            <td class="px-4 py-4 text-right text-base font-black italic text-zinc-950">
                                Rp {{ number_format($products->sum(function ($p) { return $p->price * $p->stock; }), 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Export Buttons --}}
        <div class="flex flex-wrap gap-3">
            <button
                class="inline-flex items-center justify-center px-6 py-3 bg-emerald-600 text-white text-xs font-bold uppercase tracking-widest rounded-lg opacity-50 cursor-not-allowed"
                disabled>
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                Export Excel (Segera Hadir)
            </button>
            <button
                class="inline-flex items-center justify-center px-6 py-3 bg-red-600 text-white text-xs font-bold uppercase tracking-widest rounded-lg opacity-50 cursor-not-allowed"
                disabled>
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                Export PDF (Segera Hadir)
            </button>
        </div>
    </div>
@endsection
