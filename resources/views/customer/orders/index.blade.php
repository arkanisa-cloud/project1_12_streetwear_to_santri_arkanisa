@extends('layouts.customer')

@section('title', 'Pesanan Saya')

@push('styles')
    {{-- DataTables Core & Custom Tailwind Bridge Styling --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <style>
        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #e4e4e7 !important;
            background-color: #fafafa !important;
            border-radius: 12px !important;
            padding: 6px 24px 6px 12px !important;
            font-size: 11px !important;
            font-weight: 700 !important;
        }

        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #e4e4e7 !important;
            background-color: #fafafa !important;
            border-radius: 14px !important;
            padding: 8px 14px !important;
            font-size: 11px !important;
            font-weight: 700 !important;
            margin-left: 8px !important;
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #09090b !important;
            outline: none !important;
            background-color: #ffffff !important;
        }

        table.dataTable {
            border-collapse: collapse !important;
            border-spacing: 0 !important;
            margin-top: 16px !important;
            margin-bottom: 16px !important;
        }

        table.dataTable thead th {
            border-bottom: 2px solid #09090b !important;
            padding: 12px 16px !important;
        }

        table.dataTable tbody td {
            border-bottom: 1px solid #f4f4f5 !important;
            padding: 16px !important;
        }

        .dataTables_wrapper .dataTables_info {
            font-size: 10px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.1em !important;
            color: #a1a1aa !important;
            padding-top: 16px !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #09090b !important;
            color: #ffffff !important;
            border: 1px solid #09090b !important;
            border-radius: 10px !important;
            font-size: 11px !important;
            font-weight: 900 !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #f4f4f5 !important;
            color: #09090b !important;
            border: 1px solid #e4e4e7 !important;
            border-radius: 10px !important;
        }
    </style>
@endpush

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-6 sm:py-12">

        {{-- Header Hub --}}
        <div class="mb-10 border-b border-zinc-100 pb-6">
            <span class="text-[9px] font-black uppercase text-zinc-400 tracking-[0.3em] block mb-2">
                STS Operational / Client Dashboard
            </span>
            <h1 class="text-3xl sm:text-4xl font-black italic tracking-tighter uppercase text-zinc-950">
                Pesanan / <span class="text-zinc-400">Saya</span>
            </h1>
        </div>

        {{-- Workspace Grid System --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            {{-- KOLOM NAVIGASI (4/12): Desktop Only --}}
            <div class="hidden lg:block lg:col-span-4 space-y-6">

                {{-- Modern Identity Card --}}
                <div
                    class="bg-zinc-950 rounded-[2rem] p-8 text-white relative overflow-hidden shadow-2xl group transition-all duration-500 hover:scale-[1.02]">
                    {{-- Decorative elements --}}
                    <div
                        class="absolute -right-4 -top-4 w-24 h-24 bg-white/5 rounded-full blur-2xl group-hover:bg-white/10 transition-colors">
                    </div>
                    <div
                        class="absolute -left-4 -bottom-4 w-32 h-32 bg-white/5 rounded-full blur-3xl group-hover:bg-white/10 transition-colors">
                    </div>

                    <div
                        class="absolute inset-0 opacity-[0.03] italic font-black text-[10vw] flex items-center justify-center select-none pointer-events-none group-hover:opacity-[0.05] transition-opacity">
                        STS
                    </div>

                    <div class="relative z-10 flex flex-col items-center text-center">
                        <div class="relative mb-6">
                            <div
                                class="w-24 h-24 bg-gradient-to-tr from-zinc-800 to-zinc-700 rounded-3xl flex items-center justify-center border border-white/10 overflow-hidden shadow-inner group-hover:rotate-3 transition-transform duration-500">
                                @if (auth()->user()->avatar)
                                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <span class="text-3xl font-black uppercase italic tracking-tighter text-white/20">
                                        {{ auth()->user()->name ? strtoupper(substr(auth()->user()->name, 0, 2)) : 'ST' }}
                                    </span>
                                @endif
                            </div>
                            <div class="absolute -bottom-2 -right-2 bg-emerald-500 w-6 h-6 rounded-full border-4 border-zinc-950 flex items-center justify-center shadow-lg"
                                title="Status Online">
                                <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                            </div>
                        </div>

                        <h3 class="text-xl font-black uppercase tracking-tight italic text-white mb-1">
                            {{ auth()->user()->name }}</h3>
                        <div
                            class="inline-flex items-center gap-2 px-3 py-1 bg-white/5 rounded-full border border-white/10">
                            <span class="w-1 h-1 bg-zinc-500 rounded-full"></span>
                            <span class="text-[8px] font-black uppercase tracking-[0.2em] text-zinc-400">
                                Klien Terverifikasi
                            </span>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-white/5 grid grid-cols-2 gap-4 relative z-10">
                        <div class="text-center">
                            <p class="text-[10px] font-black uppercase text-zinc-500 tracking-widest mb-1">Pesanan</p>
                            <p class="text-sm font-black italic text-white">{{ auth()->user()->orders->count() }}</p>
                        </div>
                        <div class="text-center border-l border-white/5">
                            <p class="text-[10px] font-black uppercase text-zinc-500 tracking-widest mb-1">Status</p>
                            <p class="text-[10px] font-black uppercase italic text-emerald-400">Aktif</p>
                        </div>
                    </div>
                </div>

                {{-- Account Side Navigation Bar --}}
                <div class="bg-white border border-zinc-100 rounded-[2rem] p-3 shadow-sm space-y-1">
                    <span class="text-[8px] font-black uppercase tracking-[0.2em] text-zinc-400 block px-5 pt-4 pb-2">
                        Navigasi Akun
                    </span>

                    <a href="{{ route('customer.profile.index') }}"
                        class="flex items-center justify-between px-5 py-4 rounded-2xl bg-white text-zinc-600 hover:text-zinc-950 hover:bg-zinc-50 text-[10px] font-bold uppercase tracking-wider transition-all group">
                        <span class="flex items-center gap-3">
                            <span>Profile</span>
                        </span>
                        <span class="opacity-0 group-hover:opacity-100 transition-opacity">&rarr;</span>
                    </a>

                    <a href="{{ route('customer.orders.index') }}"
                        class="flex items-center justify-between px-5 py-4 rounded-2xl bg-zinc-950 text-white text-[10px] font-black uppercase tracking-wider italic transition-all group">
                        <span class="flex items-center gap-3">
                            <span>Pesanan Saya</span>
                        </span>
                        <span
                            class="text-[9px] bg-white/20 px-2 py-0.5 rounded font-mono font-bold">{{ auth()->user()->orders->count() }}</span>
                    </a>

                    <a href="{{ route('customer.addresses.index') }}"
                        class="flex items-center justify-between px-5 py-4 rounded-2xl bg-white text-zinc-600 hover:text-zinc-950 hover:bg-zinc-50 text-[10px] font-bold uppercase tracking-wider transition-all group">
                        <span class="flex items-center gap-3">
                            <span>Alamat Saya</span>
                        </span>
                        <span class="opacity-0 group-hover:opacity-100 transition-opacity">&rarr;</span>
                    </a>
                </div>
            </div>

            {{-- KOLOM KONTEN (8/12) --}}
            <div class="lg:col-span-8">

                @if (auth()->user()->orders->count() > 0)
                    <div class="bg-white border border-zinc-100 rounded-3xl p-4 sm:p-8 shadow-sm">

                        {{-- Order Cards for Mobile --}}
                        <div class="lg:hidden space-y-4">
                            @foreach (auth()->user()->orders->sortByDesc('created_at') as $order)
                                <div class="border border-zinc-100 rounded-2xl p-5 space-y-4">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <span
                                                class="text-[10px] font-black uppercase tracking-tighter text-zinc-950 block">#{{ $order->order_number }}</span>
                                            <span
                                                class="text-[9px] font-bold text-zinc-400 uppercase">{{ $order->created_at->translatedFormat('d M Y') }}</span>
                                        </div>
                                        @if ($order->status === 'pending')
                                            <span
                                                class="px-2.5 py-1 bg-zinc-100 text-zinc-500 text-[8px] font-black uppercase tracking-wider rounded italic">Pending</span>
                                        @elseif($order->status === 'processed')
                                            <span
                                                class="px-2.5 py-1 bg-zinc-950 text-white text-[8px] font-black uppercase tracking-wider rounded italic">Processed</span>
                                        @elseif($order->status === 'shipped')
                                            <span
                                                class="px-2.5 py-1 bg-blue-50 text-blue-600 text-[8px] font-black uppercase tracking-wider rounded border border-blue-100 italic">Shipped</span>
                                        @elseif($order->status === 'completed')
                                            <span
                                                class="px-2.5 py-1 bg-emerald-50 text-emerald-700 text-[8px] font-black uppercase tracking-wider rounded border border-emerald-100 italic">Selesai</span>
                                        @elseif($order->status === 'cancelled')
                                            <span
                                                class="px-2.5 py-1 bg-rose-50 text-rose-600 text-[8px] font-black uppercase tracking-wider rounded border border-rose-100 italic">Void</span>
                                        @endif
                                    </div>
                                    <div class="flex justify-between items-center pt-3 border-t border-zinc-50">
                                        <p class="text-xs font-black text-zinc-950 italic">{{ $order->formatted_total }}
                                        </p>
                                        <a href="{{ route('customer.orders.show', $order) }}"
                                            class="px-4 py-2 bg-zinc-950 text-white rounded-xl text-[9px] font-black uppercase tracking-widest shadow-sm">
                                            Audit 👁
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Desktop Table Manifest --}}
                        <div class="hidden lg:block overflow-x-auto">
                            <table id="orderManifestTable" class="w-full text-left display">
                                <thead>
                                    <tr
                                        class="text-[9px] font-black uppercase tracking-widest text-zinc-400 border-b border-zinc-950">
                                        <th>No. Order</th>
                                        <th>Tanggal</th>
                                        <th>Total Aggregate</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Finansial</th>
                                        <th class="text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="text-xs font-bold text-zinc-800 divide-y divide-zinc-50">
                                    @foreach (auth()->user()->orders->sortByDesc('created_at') as $order)
                                        <tr class="hover:bg-zinc-50/50 transition-colors">
                                            <td class="py-4">
                                                <span
                                                    class="font-black text-zinc-950 uppercase tracking-tight block">#{{ $order->order_number }}</span>
                                            </td>
                                            <td class="py-4 font-medium text-zinc-500">
                                                {{ $order->created_at->translatedFormat('d M Y') }}
                                            </td>
                                            <td class="py-4 font-black text-zinc-950 italic">
                                                {{ $order->formatted_total }}
                                            </td>
                                            <td class="py-4 text-center">
                                                @if ($order->status === 'pending')
                                                    <span
                                                        class="inline-block px-3 py-1 bg-zinc-100 text-zinc-500 text-[9px] font-black uppercase tracking-wider rounded-lg italic">Pending</span>
                                                @elseif($order->status === 'processed')
                                                    <span
                                                        class="inline-block px-3 py-1 bg-zinc-950 text-white text-[9px] font-black uppercase tracking-wider rounded-lg italic">Processed</span>
                                                @elseif($order->status === 'shipped')
                                                    <span
                                                        class="inline-block px-3 py-1 bg-blue-50 text-blue-600 text-[9px] font-black uppercase tracking-wider rounded-lg border border-blue-100 italic">Shipped</span>
                                                @elseif($order->status === 'completed')
                                                    <span
                                                        class="inline-block px-3 py-1 bg-emerald-50 text-emerald-700 text-[9px] font-black uppercase tracking-wider rounded-lg border border-emerald-100 italic">Selesai</span>
                                                @elseif($order->status === 'cancelled')
                                                    <span
                                                        class="inline-block px-3 py-1 bg-rose-50 text-rose-600 text-[9px] font-black uppercase tracking-wider rounded-lg border border-rose-100 italic">Void</span>
                                                @endif
                                            </td>
                                            <td class="py-4 text-center">
                                                @if ($order->payment_status === 'unpaid')
                                                    <span
                                                        class="text-[10px] font-black uppercase tracking-wider text-zinc-300 italic">Unpaid</span>
                                                @elseif($order->payment_status === 'pending')
                                                    <span
                                                        class="text-[10px] font-black uppercase tracking-wider text-amber-500 italic animate-pulse">Review</span>
                                                @elseif($order->payment_status === 'paid')
                                                    <span
                                                        class="text-[10px] font-black uppercase tracking-wider text-emerald-600 italic">Settled</span>
                                                @elseif($order->payment_status === 'rejected')
                                                    <span
                                                        class="text-[10px] font-black uppercase tracking-wider text-rose-600 italic">Declined</span>
                                                @endif
                                            </td>
                                            <td class="py-4 text-right">
                                                <a href="{{ route('customer.orders.show', $order) }}"
                                                    class="inline-block px-4 py-2 bg-zinc-50 hover:bg-zinc-950 text-zinc-950 hover:text-white border border-zinc-200/60 hover:border-zinc-950 rounded-xl text-[10px] font-black uppercase tracking-widest text-center transition-all shadow-sm active:scale-95">
                                                    Audit 👁
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                @else
                    <div class="bg-white border border-zinc-100 rounded-3xl p-12 text-center shadow-sm">
                        <div
                            class="w-16 h-16 bg-zinc-50 rounded-2xl flex items-center justify-center text-zinc-400 text-2xl mx-auto mb-4 border border-zinc-100">
                            🗎
                        </div>
                        <h3 class="text-sm font-black uppercase tracking-tight text-zinc-950 italic">Belum Ada Transaksi
                        </h3>
                        <p class="text-[11px] font-bold text-zinc-400 uppercase tracking-widest mt-1">Kamu belum mengamankan
                            koleksi produk apapun dari STS Vault.</p>
                        <div class="mt-6">
                            <a href="{{ route('customer.shop.index') }}"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-zinc-950 hover:bg-zinc-800 text-white text-[10px] font-black uppercase tracking-widest rounded-full transition-all shadow-md active:scale-95">
                                Mulai Menjelajah Shop →
                            </a>
                        </div>
                    </div>
                @endif

            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#orderManifestTable').DataTable({
                "responsive": true,
                "ordering": true,
                "order": [
                    [1, "desc"]
                ],
                "pageLength": 10,
                "language": {
                    "search": "CARI MANIFEST:",
                    "lengthMenu": "TAMPIL _MENU_ BARIS",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ arsip",
                    "infoEmpty": "Tidak ada data manifest ditemukan",
                    "infoFiltered": "(disaring dari _MAX_ total arsip)",
                    "zeroRecords": "Tidak ada transaksi matching",
                    "paginate": {
                        "first": "◄◄",
                        "last": "►►",
                        "next": "►",
                        "previous": "◄"
                    }
                }
            });
        });
    </script>
@endpush
