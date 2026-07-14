@extends('layouts.admin')

@section('title', 'Laporan Penjualan')

@section('content')
    <div class="space-y-6">
        <div
            class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <div>
                <h1 class="text-2xl font-black tracking-tighter text-zinc-950 uppercase italic">Laporan Penjualan</h1>
                <p class="text-sm text-zinc-500">Analisis performa penjualan dan pendapatan secara komprehensif.</p>
            </div>
        </div>

        {{-- Date Filter --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <form action="{{ route('admin.reports.sales') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-600 mb-2">Tanggal Mulai</label>
                    <input type="date" name="start_date"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-zinc-500 text-sm"
                        value="{{ $startDate }}" required>
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-600 mb-2">Tanggal Akhir</label>
                    <input type="date" name="end_date"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-zinc-500 text-sm"
                        value="{{ $endDate }}" required>
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-600 mb-2">Status Order</label>
                    <select name="status"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-zinc-500 text-sm">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processed" {{ request('status') == 'processed' ? 'selected' : '' }}>Diproses</option>
                        <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Dikirim</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="flex-1 bg-zinc-950 hover:bg-zinc-800 text-white text-xs font-bold uppercase tracking-widest px-4 py-2 rounded-lg transition-all">
                        Terapkan Filter
                    </button>
                    <a href="{{ route('admin.reports.sales') }}"
                        class="bg-zinc-100 hover:bg-zinc-200 text-zinc-700 text-xs font-bold uppercase tracking-widest px-4 py-2 rounded-lg transition-all">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white border border-zinc-100 rounded-2xl shadow-sm p-6 text-center">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-2">Total Pesanan</p>
                <h3 class="text-3xl font-black italic text-zinc-950">{{ $totalOrders }}</h3>
                <p class="text-[10px] text-zinc-400 font-medium mt-1 uppercase tracking-wider">Order Masuk</p>
            </div>
            <div class="bg-white border border-emerald-100 rounded-2xl shadow-sm p-6 text-center">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-emerald-500 mb-2">Pesanan Lunas</p>
                <h3 class="text-3xl font-black italic text-emerald-600">{{ $paidOrders }}</h3>
                <p class="text-[10px] text-zinc-400 font-medium mt-1 uppercase tracking-wider">Terbayar</p>
            </div>
            <div class="bg-white border border-zinc-100 rounded-2xl shadow-sm p-6 text-center">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-2">Total Pendapatan</p>
                <h3 class="text-lg font-black italic text-zinc-950">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                <p class="text-[10px] text-zinc-400 font-medium mt-1 uppercase tracking-wider">Pendapatan Bersih</p>
            </div>
            <div class="bg-white border border-zinc-100 rounded-2xl shadow-sm p-6 text-center">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-2">Rata-rata / Order</p>
                <h3 class="text-lg font-black italic text-zinc-950">
                    @if ($paidOrders > 0)
                        Rp {{ number_format($totalRevenue / $paidOrders, 0, ',', '.') }}
                    @else
                        Rp 0
                    @endif
                </h3>
                <p class="text-[10px] text-zinc-400 font-medium mt-1 uppercase tracking-wider">Per Transaksi</p>
            </div>
        </div>

        {{-- Main Grid: Transaksi + Produk Terlaris --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Daftar Transaksi --}}
            <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-zinc-100 flex items-center gap-3">
                    <span class="w-1.5 h-4 bg-zinc-950 block rounded"></span>
                    <h5 class="font-black text-sm uppercase tracking-[0.15em] text-zinc-950">Daftar Transaksi</h5>
                </div>
                <div class="p-6 overflow-x-auto">
                    <table class="w-full text-left border-collapse datatable">
                        <thead>
                            <tr class="bg-zinc-50 border-b border-gray-100">
                                <th class="px-4 py-3 text-xs font-black uppercase tracking-widest text-zinc-600">No. Order</th>
                                <th class="px-4 py-3 text-xs font-black uppercase tracking-widest text-zinc-600">Tanggal</th>
                                <th class="px-4 py-3 text-xs font-black uppercase tracking-widest text-zinc-600">Pelanggan</th>
                                <th class="px-4 py-3 text-xs font-black uppercase tracking-widest text-zinc-600">Total</th>
                                <th class="px-4 py-3 text-xs font-black uppercase tracking-widest text-zinc-600">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($orders as $order)
                                <tr class="hover:bg-zinc-50/50 transition-colors">
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-bold text-zinc-950">{{ $order->order_number }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-zinc-600">
                                        {{ $order->created_at->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm font-medium text-zinc-900">{{ $order->user->name }}</td>
                                    <td class="px-4 py-3 text-sm font-black text-zinc-900">
                                        @if ($order->payment_status === 'paid')
                                            <span class="text-emerald-600">{{ $order->formatted_total }}</span>
                                        @else
                                            {{ $order->formatted_total }}
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($order->status === 'completed')
                                            <span class="inline-block bg-emerald-100 text-emerald-800 px-2 py-1 rounded text-[10px] font-black uppercase tracking-wider">
                                                Selesai
                                            </span>
                                        @elseif($order->status === 'cancelled')
                                            <span class="inline-block bg-red-100 text-red-800 px-2 py-1 rounded text-[10px] font-black uppercase tracking-wider">
                                                Dibatalkan
                                            </span>
                                        @else
                                            <span class="inline-block bg-zinc-100 text-zinc-600 px-2 py-1 rounded text-[10px] font-black uppercase tracking-wider">
                                                {{ $order->status_label }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Produk Terlaris --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-zinc-100 flex items-center gap-3">
                    <span class="w-1.5 h-4 bg-zinc-950 block rounded"></span>
                    <h5 class="font-black text-sm uppercase tracking-[0.15em] text-zinc-950">Produk Terlaris</h5>
                </div>
                <div class="p-6">
                    @if ($topProducts->count() > 0)
                        <div class="space-y-4">
                            @foreach ($topProducts as $index => $top)
                                <div class="flex items-center justify-between p-3 bg-zinc-50 rounded-xl hover:bg-zinc-100 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-zinc-950 text-white rounded-full flex items-center justify-center text-xs font-black italic">
                                            {{ $index + 1 }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-zinc-950 leading-tight">{{ $top->product->name }}</div>
                                            <div class="text-[10px] text-zinc-400 uppercase tracking-wider">#PROD-{{ $top->product->id }}</div>
                                        </div>
                                    </div>
                                    <div class="text-right flex-shrink-0">
                                        <div class="text-lg font-black italic text-zinc-950">{{ $top->total_sold }}</div>
                                        <div class="text-[10px] text-zinc-400 uppercase tracking-widest">Terjual</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="w-10 h-10 mx-auto text-zinc-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                            <p class="text-sm font-bold text-zinc-400 uppercase tracking-widest">Belum ada data penjualan</p>
                        </div>
                    @endif
                </div>
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
