@extends('layouts.admin')

@section('content')
    <div class="space-y-8">
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black tracking-tight text-zinc-950 uppercase italic flex items-center gap-3">
                    Dashboard
                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-zinc-950 animate-pulse"></span>
                </h1>
                <p class="text-xs text-zinc-500 font-medium tracking-wide uppercase mt-1">Pantau performa kerajaan streetwear
                    kamu.</p>
            </div>
            <div
                class="bg-white border border-zinc-150 rounded-2xl px-5 py-3 shadow-sm flex items-center gap-4 self-start md:self-auto">
                <div class="w-8 h-8 rounded-xl bg-zinc-50 flex items-center justify-center text-zinc-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z">
                        </path>
                    </svg>
                </div>
                <div>
                    <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Tanggal Sistem</p>
                    <p class="text-xs font-bold text-zinc-950">{{ now()->translatedFormat('d F Y') }}</p>
                </div>
            </div>
        </div>

        {{-- Metrics Grid (Redesigned: Elegannya dapet, Simple Tanpa Span Badge Kotak) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Total Koleksi --}}
            <div
                class="bg-white p-6 rounded-2xl border border-zinc-100 shadow-sm hover:border-zinc-950 transition-all duration-300 flex flex-col justify-between">
                <div>
                    <p class="text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-4">Total Koleksi</p>
                    <h3 class="text-3xl font-black text-zinc-950 italic tracking-tight">{{ $totalProducts }}</h3>
                </div>
                <div
                    class="mt-4 pt-3 border-t border-zinc-100/80 flex items-center justify-between text-[9px] font-bold uppercase tracking-wider">
                    <span class="text-zinc-400">Database aktif</span>
                    <span class="text-zinc-500">Item SKU</span>
                </div>
            </div>

            {{-- Pendapatan Hari Ini --}}
            <div
                class="bg-white p-6 rounded-2xl border border-zinc-100 shadow-sm hover:border-zinc-950 transition-all duration-300 flex flex-col justify-between">
                <div>
                    <p class="text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-4">Pendapatan Hari Ini</p>
                    <div class="min-w-0">
                        <h3 class="text-2xl xl:text-3xl font-black text-zinc-950 italic tracking-tight break-all">
                            Rp {{ number_format($salesToday, 0, ',', '.') }}
                        </h3>
                    </div>
                </div>
                <div
                    class="mt-4 pt-3 border-t border-zinc-100/80 flex items-center justify-between text-[9px] font-bold uppercase tracking-wider">
                    <span class="text-zinc-400">Zona Waktu WIB</span>
                    <span class="text-emerald-600 font-extrabold tracking-widest">● Realtime</span>
                </div>
            </div>

            {{-- Menunggu Diproses --}}
            <div
                class="bg-white p-6 rounded-2xl border border-zinc-100 shadow-sm hover:border-zinc-950 transition-all duration-300 flex flex-col justify-between">
                <div>
                    <p class="text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-4">Menunggu Diproses</p>
                    <h3 class="text-3xl font-black text-zinc-950 italic tracking-tight">{{ $pendingOrders }}</h3>
                </div>
                <div
                    class="mt-4 pt-3 border-t border-zinc-100/80 flex items-center justify-between text-[9px] font-bold uppercase tracking-wider">
                    <span class="text-zinc-400">Antrean Masuk</span>
                    <span
                        class="{{ $pendingOrders > 0 ? 'text-amber-600 font-extrabold animate-pulse' : 'text-zinc-500' }}">
                        {{ $pendingOrders > 0 ? 'Perlu Aksi' : 'Clear' }}
                    </span>
                </div>
            </div>

            {{-- Pelanggan Aktif --}}
            <div
                class="bg-white p-6 rounded-2xl border border-zinc-100 shadow-sm hover:border-zinc-950 transition-all duration-300 flex flex-col justify-between">
                <div>
                    <p class="text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-4">Pelanggan Aktif</p>
                    <h3 class="text-3xl font-black text-zinc-950 italic tracking-tight">{{ $totalCustomers }}</h3>
                </div>
                <div
                    class="mt-4 pt-3 border-t border-zinc-100/80 flex items-center justify-between text-[9px] font-bold uppercase tracking-wider">
                    <span class="text-zinc-400">Sistem Retail</span>
                    <span class="text-zinc-500">User Terdaftar</span>
                </div>
            </div>
        </div>

        {{-- Main Dashboard Grid Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left column: Chart & Recent Orders --}}
            <div class="lg:col-span-2 space-y-8">
                {{-- Chart Card --}}
                <div class="bg-white rounded-3xl border border-zinc-150 shadow-sm p-6 space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h2 class="text-xs font-black uppercase tracking-[0.2em] text-zinc-950 flex items-center gap-2">
                                <span class="w-1.5 h-3 bg-zinc-950 rounded-sm"></span>
                                Grafik Penjualan 7 Hari Terakhir
                            </h2>
                            <p class="text-[10px] text-zinc-400 font-medium uppercase tracking-wider mt-0.5">Pantau
                                fluktuasi pendapatan harian ritel streetwear kamu</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span
                                class="inline-flex items-center gap-1.5 text-[9px] font-black uppercase tracking-wider text-zinc-500 bg-zinc-50 border border-zinc-100 px-2.5 py-1 rounded-lg">
                                <span class="w-1.5 h-1.5 rounded-full bg-zinc-950"></span>
                                Penjualan
                            </span>
                        </div>
                    </div>

                    {{-- Chart Container --}}
                    <div class="h-64 sm:h-72 w-full relative">
                        <canvas id="salesChartCanvas"></canvas>
                    </div>
                </div>

                {{-- Pesanan Terbaru (Polished & Clean Table Layout) --}}
                <div class="bg-white rounded-2xl border border-zinc-100 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-zinc-100 flex items-center justify-between bg-zinc-50/20">
                        <div>
                            <h2 class="text-xs font-black uppercase tracking-[0.2em] text-zinc-950 flex items-center gap-2">
                                <span class="w-1.5 h-3 bg-zinc-950 rounded-sm"></span>
                                Pesanan Terbaru
                            </h2>
                            <p class="text-[10px] text-zinc-400 font-medium uppercase tracking-wider mt-0.5">Transaksi masuk
                                teranyar di platform</p>
                        </div>
                        <a href="{{ route('admin.orders.index') }}"
                            class="text-[9px] font-black uppercase tracking-widest text-zinc-500 hover:text-zinc-950 border border-zinc-200 hover:border-zinc-950 rounded-lg px-3 py-1.5 transition-all duration-200 bg-white">
                            Lihat Semua
                        </a>
                    </div>
                    <div class="p-0">
                        @if ($recentOrders->isEmpty())
                            <div class="text-center text-zinc-400 text-xs italic font-medium py-12">Belum ada pesanan
                                terbaru.</div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs whitespace-nowrap">
                                    <thead>
                                        <tr
                                            class="bg-zinc-50/60 border-b border-zinc-100 text-[9px] font-black uppercase tracking-widest text-zinc-400">
                                            <th class="py-3 px-6">No. Order</th>
                                            <th class="py-3 px-6">Pelanggan</th>
                                            <th class="py-3 px-2">Status</th>
                                            <th class="py-3 px-6 text-right">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-zinc-100 font-medium text-zinc-700">
                                        @foreach ($recentOrders as $order)
                                            <tr class="hover:bg-zinc-50/40 transition-colors">
                                                <td class="py-3.5 px-6 font-bold text-zinc-950 tracking-tight">
                                                    #{{ $order->order_number }}
                                                </td>
                                                <td class="py-3.5 px-6">
                                                    <div class="flex items-center gap-2.5">
                                                        <div
                                                            class="w-5 h-5 rounded bg-zinc-950 flex items-center justify-center font-black text-[9px] text-white uppercase tracking-tighter">
                                                            {{ substr($order->user->name ?? 'G', 0, 1) }}
                                                        </div>
                                                        <span
                                                            class="text-zinc-800 font-semibold">{{ $order->user->name ?? 'Guest' }}</span>
                                                    </div>
                                                </td>
                                                <td class="py-3.5 px-2">
                                                    @if ($order->status == 'pending')
                                                        <span
                                                            class="inline-flex items-center gap-1.5 text-[9px] font-extrabold text-amber-700 tracking-wide uppercase">
                                                            <span
                                                                class="w-1 h-1 rounded-full bg-amber-500"></span>{{ $order->status }}
                                                        </span>
                                                    @elseif($order->status == 'completed' || $order->status == 'selesai')
                                                        <span
                                                            class="inline-flex items-center gap-1.5 text-[9px] font-extrabold text-emerald-700 tracking-wide uppercase">
                                                            <span class="w-1 h-1 rounded-full bg-emerald-500"></span>Selesai
                                                        </span>
                                                    @else
                                                        <span
                                                            class="inline-flex items-center gap-1.5 text-[9px] font-extrabold text-blue-700 tracking-wide uppercase">
                                                            <span
                                                                class="w-1 h-1 rounded-full bg-blue-500"></span>{{ $order->status }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="py-3.5 px-6 text-right font-black italic text-zinc-950 text-sm">
                                                    Rp {{ number_format($order->total, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right column: Stocks & Logistics --}}
            <div class="space-y-8">
                {{-- Peringatan Stok Redesigned --}}
                <div class="bg-white rounded-3xl border border-zinc-150 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-zinc-100 flex items-center justify-between">
                        <div>
                            <h2 class="text-xs font-black uppercase tracking-[0.2em] text-zinc-950 flex items-center gap-2">
                                <span class="w-1.5 h-3 bg-zinc-950 rounded-sm"></span>
                                Peringatan Stok
                            </h2>
                            <p class="text-[10px] text-zinc-400 font-medium uppercase tracking-wider mt-0.5">Kritikalitas
                                ketersediaan inventaris</p>
                        </div>
                    </div>
                    <div class="p-6">
                        @if ($lowStockProducts->isEmpty())
                            <div
                                class="flex items-center gap-3 bg-emerald-50 border border-emerald-100 text-emerald-800 p-4 rounded-2xl">
                                <svg class="w-5 h-5 shrink-0 text-emerald-600" fill="none" stroke="currentColor"
                                    stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-[11px] font-extrabold uppercase tracking-wide">Semua stok produk aman di
                                    atas 5 unit.</p>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach ($lowStockProducts as $product)
                                    <div
                                        class="flex items-center justify-between p-3 rounded-2xl border border-zinc-50 hover:bg-zinc-50/50 transition-colors">
                                        <div class="min-w-0 pr-4">
                                            <h4 class="text-xs font-bold text-zinc-900 truncate">{{ $product->name }}</h4>
                                            <p
                                                class="text-[8px] font-black uppercase tracking-widest text-zinc-400 mt-0.5">
                                                {{ $product->category->name }}</p>
                                        </div>
                                        <div class="shrink-0">
                                            <span
                                                class="inline-block px-2.5 py-1 rounded-lg {{ $product->stock == 0 ? 'bg-red-50 text-red-700 border border-red-100' : 'bg-amber-50 text-amber-700 border border-amber-100' }} text-[9px] font-black uppercase tracking-wider">
                                                {{ $product->stock }} Pcs
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-6 pt-4 border-t border-zinc-100 text-center">
                                <a href="{{ route('admin.products.index') }}"
                                    class="text-[9px] font-black uppercase tracking-widest text-zinc-950 border border-zinc-200 hover:border-zinc-950 rounded-xl px-4 py-2 hover:bg-zinc-50 transition-all inline-block">
                                    Kelola Inventaris →
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Aktivitas/Logistik Hari Ini Redesigned --}}
                <div class="bg-white rounded-3xl border border-zinc-150 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-zinc-100">
                        <h2 class="text-xs font-black uppercase tracking-[0.2em] text-zinc-950 flex items-center gap-2">
                            <span class="w-1.5 h-3 bg-zinc-950 rounded-sm"></span>
                            Logistik Hari Ini
                        </h2>
                        <p class="text-[10px] text-zinc-400 font-medium uppercase tracking-wider mt-0.5">Arus keluar masuk
                            barang hari ini</p>
                    </div>
                    <div class="p-6 grid grid-cols-2 gap-4">
                        <div
                            class="p-4 bg-zinc-50 border border-zinc-100 rounded-2xl flex flex-col justify-between hover:border-zinc-300 transition-colors">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-[8px] font-black uppercase tracking-widest text-zinc-400">Barang Masuk</p>
                                <span
                                    class="w-5 h-5 rounded-lg bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 13.5L12 21m0 0l-7.5-7.5M12 21V3"></path>
                                    </svg>
                                </span>
                            </div>
                            <p class="text-2xl font-black text-zinc-950 italic tracking-tight">+{{ $stockInToday }}</p>
                        </div>
                        <div
                            class="p-4 bg-zinc-50 border border-zinc-100 rounded-2xl flex flex-col justify-between hover:border-zinc-300 transition-colors">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-[8px] font-black uppercase tracking-widest text-zinc-400">Barang Keluar</p>
                                <span
                                    class="w-5 h-5 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4.5 10.5L12 3m0 0l7.5 7.5M12 3v18"></path>
                                    </svg>
                                </span>
                            </div>
                            <p class="text-2xl font-black text-zinc-950 italic tracking-tight">-{{ $stockOutToday }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ChartJS CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chartLabels = {!! json_encode($salesChart->pluck('label')) !!};
            const chartData = {!! json_encode($salesChart->pluck('total_sales')) !!};
            const chartOrders = {!! json_encode($salesChart->pluck('total_orders')) !!};

            const ctx = document.getElementById('salesChartCanvas').getContext('2d');

            const gradient = ctx.createLinearGradient(0, 0, 0, 250);
            gradient.addColorStop(0, 'rgba(9, 9, 11, 0.07)');
            gradient.addColorStop(1, 'rgba(9, 9, 11, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Penjualan',
                        data: chartData,
                        borderColor: '#09090b',
                        borderWidth: 2.5,
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.38,
                        pointBackgroundColor: '#09090b',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointHoverBorderWidth: 2.5,
                        pointHoverBorderColor: '#09090b',
                        pointHoverBackgroundColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#09090b',
                            titleColor: '#ffffff',
                            titleFont: {
                                family: 'Figtree, sans-serif',
                                size: 10,
                                weight: '800'
                            },
                            bodyColor: '#a1a1aa',
                            bodyFont: {
                                family: 'Figtree, sans-serif',
                                size: 10,
                                weight: '600'
                            },
                            padding: 12,
                            cornerRadius: 12,
                            displayColors: false,
                            borderWidth: 1,
                            borderColor: '#27272a',
                            callbacks: {
                                label: function(context) {
                                    const value = context.raw;
                                    const index = context.dataIndex;
                                    const orders = chartOrders[index];
                                    return [
                                        'PENDAPATAN : Rp ' + new Intl.NumberFormat('id-ID').format(
                                            value),
                                        'ORDER MASUK : ' + orders + ' Pesanan'
                                    ];
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    family: 'Figtree, sans-serif',
                                    size: 9,
                                    weight: '700'
                                },
                                color: '#a1a1aa'
                            }
                        },
                        y: {
                            grid: {
                                color: '#f4f4f5',
                                drawTicks: false
                            },
                            ticks: {
                                font: {
                                    family: 'Figtree, sans-serif',
                                    size: 9,
                                    weight: '700'
                                },
                                color: '#a1a1aa',
                                callback: function(value) {
                                    if (value >= 1000000) return 'Rp ' + (value / 1000000) + 'M';
                                    if (value >= 1000) return 'Rp ' + (value / 1000) + 'k';
                                    return 'Rp ' + value;
                                }
                            },
                            border: {
                                dash: [4, 4],
                                color: '#e4e4e7'
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
