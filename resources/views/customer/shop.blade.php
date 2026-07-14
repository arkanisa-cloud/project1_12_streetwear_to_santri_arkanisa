@extends('layouts.customer')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6">

        {{-- Header Section --}}
        <div class="py-12 border-b border-zinc-100 bg-white flex flex-col md:flex-row md:items-end md:justify-between gap-6">
            <div>
                <h1 class="text-4xl font-black italic tracking-tighter uppercase">
                    @if (request('search'))
                        CARI / <span class="text-zinc-400">"{{ strtoupper(request('search')) }}"</span>
                    @else
                        PRODUK / <span class="text-zinc-400">{{ $selectedCategory->name ?? 'SEMUA' }}</span>
                    @endif
                </h1>
                <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.3em] mt-2">
                    @if (request('search'))
                        Menemukan {{ $products->total() }} artikel yang cocok dengan pencarian Anda.
                    @else
                        Koleksi artikel pilihan yang dikurasi secara eksklusif.
                    @endif
                </p>
            </div>

            {{-- Search Bar --}}
            <div class="w-full md:w-72">
                <form action="{{ route('customer.shop.index') }}" method="GET" class="relative group">
                    @if (request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Produk..."
                        class="w-full bg-zinc-50 border-2 border-zinc-100 rounded-2xl px-5 py-3 text-[10px] font-black uppercase tracking-widest focus:border-zinc-950 focus:ring-0 transition-all placeholder:text-zinc-300">
                    <button type="submit"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-zinc-400 group-hover:text-zinc-950 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        {{-- Kategori & Filter Urutkan --}}
        <div class="py-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-zinc-50">
            {{-- Kategori Pilihan (Horizontal Bar sebelum Card Produk) --}}
            <div class="flex flex-wrap gap-2 items-center">
                <a href="{{ route('customer.shop.index', array_merge(request()->query(), ['category' => null, 'page' => null])) }}"
                    class="px-5 py-2.5 text-[9px] font-black uppercase tracking-widest rounded-full transition-all italic
                          {{ !request('category') ? 'bg-zinc-950 text-white shadow-lg shadow-zinc-950/20' : 'bg-zinc-50 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-950' }}">
                    Semua Koleksi
                </a>
                @foreach ($categories as $category)
                    <a href="{{ route('customer.shop.index', array_merge(request()->query(), ['category' => $category->id, 'page' => null])) }}"
                        class="px-5 py-2.5 text-[9px] font-black uppercase tracking-widest rounded-full transition-all italic
                              {{ request('category') == $category->id ? 'bg-zinc-950 text-white shadow-lg shadow-zinc-950/20' : 'bg-zinc-50 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-950' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>

            {{-- Sorting --}}
            <div class="flex items-center gap-2">
                <span class="text-[9px] font-black uppercase tracking-widest text-zinc-400">URUTKAN:</span>
                <select onchange="location = this.value;"
                    class="bg-zinc-50 border-2 border-zinc-100 rounded-xl px-4 py-2 text-[9px] font-black uppercase tracking-widest focus:border-zinc-950 focus:ring-0 transition-all text-zinc-950 cursor-pointer">
                    <option
                        value="{{ route('customer.shop.index', array_merge(request()->query(), ['sort' => 'latest', 'page' => null])) }}"
                        {{ request('sort') == 'latest' || !request('sort') ? 'selected' : '' }}>
                        Terbaru
                    </option>
                    <option
                        value="{{ route('customer.shop.index', array_merge(request()->query(), ['sort' => 'price_low', 'page' => null])) }}"
                        {{ request('sort') == 'price_low' ? 'selected' : '' }}>
                        Harga: Terendah
                    </option>
                    <option
                        value="{{ route('customer.shop.index', array_merge(request()->query(), ['sort' => 'price_high', 'page' => null])) }}"
                        {{ request('sort') == 'price_high' ? 'selected' : '' }}>
                        Harga: Tertinggi
                    </option>
                </select>
            </div>
        </div>

        {{-- Product Grid --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 py-12">
            @forelse ($products as $product)
                {{-- Card Utama: Efek hover shadow & border tetap aktif untuk semua produk --}}
                <div
                    class="bg-white group relative flex flex-col p-4 border border-zinc-100 rounded-2xl transition-all duration-300 hover:shadow-2xl hover:shadow-zinc-200/60 hover:border-zinc-200
                    {{ $product->stock == 0 ? 'bg-zinc-50/40' : '' }}">

                    {{-- Image Wrapper: Tetap bisa diklik walau stok habis --}}
                    <div class="relative aspect-[3/4] overflow-hidden rounded-xl bg-zinc-50 mb-5 cursor-pointer"
                        onclick="window.location='{{ route('customer.shop.show', $product) }}'">

                        {{-- Gambar Produk dengan Hover Swap --}}
                        @if ($product->image)
                            {{-- Front Image --}}
                            <img src="{{ asset('storage/' . $product->image) }}"
                                class="w-full h-full object-cover transition-all duration-700 group-hover:scale-105 @if ($product->back_image_url) group-hover:opacity-0 @endif {{ $product->stock == 0 ? 'opacity-50 grayscale-[20%]' : '' }}">

                            {{-- Back Image (Optional Hover Swap) --}}
                            @if ($product->back_image)
                                <img src="{{ asset('storage/' . $product->back_image) }}"
                                    class="absolute inset-0 w-full h-full object-cover opacity-0 transition-all duration-700 group-hover:scale-105 group-hover:opacity-100 {{ $product->stock == 0 ? 'opacity-50 grayscale-[20%]' : '' }}">
                            @endif
                        @else
                            <div
                                class="w-full h-full flex items-center justify-center text-[10px] font-black text-zinc-300">
                                TANPA GAMBAR
                            </div>
                        @endif

                        {{-- Overlay & Label Status Stok --}}
                        @if ($product->stock == 0)
                            {{-- Overlay pudar tipis dengan tulisan HABIS TERSISA --}}
                            <div
                                class="absolute inset-0 bg-zinc-950/5 flex items-center justify-center backdrop-blur-[0.5px]">
                                <span
                                    class="bg-zinc-950 text-white text-[9px] font-black uppercase tracking-[0.2em] px-4 py-2.5 rounded-lg shadow-xl italic border border-zinc-800">
                                    SOLD OUT
                                </span>
                            </div>
                        @elseif ($product->stock <= 5 && $product->stock > 0)
                            {{-- Label low stock --}}
                            <div class="absolute top-3 left-3 z-10">
                                <span
                                    class="bg-rose-600 text-white text-[8px] font-black uppercase tracking-widest px-2.5 py-1 rounded shadow-sm italic">
                                    SISA {{ $product->stock }} Pcs!
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- Info & Detail Produk --}}
                    <div class="flex-1 flex flex-col justify-between">
                        <div>
                            <span class="text-[9px] font-black uppercase text-zinc-400 tracking-widest block mb-1">
                                {{ $product->category->name ?? 'Koleksi STS' }}
                            </span>
                            {{-- Judul: Tetap menggunakan link dan hover color yang sama --}}
                            <a href="{{ route('customer.shop.show', $product) }}" class="block">
                                <h2
                                    class="text-xs sm:text-sm font-black uppercase tracking-tight leading-tight group-hover:text-zinc-600 transition-colors
                                   {{ $product->stock == 0 ? 'text-zinc-500' : 'text-zinc-950' }}">
                                    {{ $product->name }}
                                </h2>
                            </a>
                        </div>

                        {{-- Pembatas & Harga Terpisah Jelas --}}
                        <div class="mt-4 pt-3 border-t border-zinc-50 flex items-center justify-between">
                            <p
                                class="text-xs sm:text-sm font-black italic {{ $product->stock == 0 ? 'text-zinc-400' : 'text-zinc-950' }}">
                                IDR {{ number_format($product->price, 0, ',', '.') }}
                            </p>

                            {{-- Tombol Navigasi Mikro: Tetap menampilkan 'LIHAT ➔' dengan efek translasi geser saat hover --}}
                            <a href="{{ route('customer.shop.show', $product) }}"
                                class="text-[9px] font-black uppercase tracking-widest group-hover:translate-x-1 transition-all flex items-center gap-1
                              {{ $product->stock == 0 ? 'text-zinc-400 group-hover:text-zinc-600' : 'text-zinc-400 group-hover:text-zinc-950' }}">
                                @if ($product->stock == 0)
                                    <span
                                        class="text-[8px] tracking-normal font-medium px-1.5 py-0.5 bg-zinc-100 text-zinc-500 rounded mr-1">SOLD</span>
                                @endif
                                LIHAT ➔
                            </a>
                        </div>
                    </div>

                </div>
            @empty
                <div class="col-span-full py-32 text-center bg-zinc-50 rounded-3xl border border-dashed border-zinc-200">
                    <p class="text-xs font-black uppercase tracking-[0.3em] text-zinc-400 italic">
                        Belum ada artikel resmi yang dirilis untuk kategori ini.
                    </p>
                </div>
            @endforelse
        </div>

        {{-- Custom Pagination --}}
        @if ($products->hasPages())
            <div
                class="pb-20 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-zinc-100 pt-8 mt-8">
                {{-- Info (Desktop Only) --}}
                <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.2em] order-2 sm:order-1">
                    Menampilkan {{ $products->firstItem() }} – {{ $products->lastItem() }} dari {{ $products->total() }}
                    Artikel
                </div>

                {{-- Page Links (Responsive) --}}
                <div class="flex items-center gap-1.5 order-1 sm:order-2">
                    {{-- Previous Page --}}
                    @if ($products->onFirstPage())
                        <span
                            class="px-3.5 py-2.5 text-[9px] font-black uppercase tracking-widest bg-zinc-50 text-zinc-300 rounded-xl cursor-not-allowed italic">
                            ← Prev
                        </span>
                    @else
                        <a href="{{ $products->previousPageUrl() }}"
                            class="px-3.5 py-2.5 text-[9px] font-black uppercase tracking-widest bg-zinc-50 text-zinc-950 border border-zinc-100 rounded-xl hover:bg-zinc-950 hover:text-white hover:border-zinc-950 transition-all italic active:scale-95">
                            ← Prev
                        </a>
                    @endif

                    {{-- Page Numbers (Hidden on Mobile for neat layout, except current and surrounding) --}}
                    <div class="hidden sm:flex items-center gap-1.5">
                        @foreach ($products->getUrlRange(max(1, $products->currentPage() - 2), min($products->lastPage(), $products->currentPage() + 2)) as $page => $url)
                            @if ($page == $products->currentPage())
                                <span
                                    class="px-3.5 py-2.5 text-[9px] font-black bg-zinc-950 text-white rounded-xl shadow-lg shadow-zinc-950/10 italic">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}"
                                    class="px-3.5 py-2.5 text-[9px] font-black text-zinc-500 bg-zinc-50 border border-zinc-100 hover:text-zinc-950 hover:bg-zinc-100 hover:border-zinc-200 rounded-xl transition-all italic active:scale-95">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    </div>

                    {{-- Mobile Page Indicator --}}
                    <span class="sm:hidden px-3.5 py-2.5 text-[9px] font-black bg-zinc-950 text-white rounded-xl italic">
                        {{ $products->currentPage() }} / {{ $products->lastPage() }}
                    </span>

                    {{-- Next Page --}}
                    @if ($products->hasMorePages())
                        <a href="{{ $products->nextPageUrl() }}"
                            class="px-3.5 py-2.5 text-[9px] font-black uppercase tracking-widest bg-zinc-50 text-zinc-950 border border-zinc-100 rounded-xl hover:bg-zinc-950 hover:text-white hover:border-zinc-950 transition-all italic active:scale-95">
                            Next ➔
                        </a>
                    @else
                        <span
                            class="px-3.5 py-2.5 text-[9px] font-black uppercase tracking-widest bg-zinc-50 text-zinc-300 rounded-xl cursor-not-allowed italic">
                            Next ➔
                        </span>
                    @endif
                </div>
            </div>
        @endif

    </div>


@endsection
