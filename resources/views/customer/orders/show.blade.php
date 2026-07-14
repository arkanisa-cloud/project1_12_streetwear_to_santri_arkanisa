@extends('layouts.customer')

@section('title', 'Detail Pesanan #' . $order->order_number)

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 sm:py-12" x-data="{ uploadOpen: false }">

        {{-- Header Area: Status & Nomor Invoice --}}
        <div
            class="mb-8 sm:mb-12 border-b border-zinc-100 pb-6 sm:pb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-[0.3em] block mb-1">
                    Arsip Transaksi / Detail Pesanan
                </span>
                <h1 class="text-2xl sm:text-4xl font-black tracking-tighter uppercase italic text-zinc-950">
                    #{{ $order->order_number }}
                </h1>
            </div>

            {{-- Badge Status Utama ala Streetwear Theme --}}
            <div>
                @if ($order->status === 'pending')
                    <span
                        class="bg-zinc-100 text-zinc-600 border border-zinc-200 text-[10px] font-black uppercase tracking-[0.2em] px-4 py-2 rounded-md italic">Menunggu
                        Diproses</span>
                @elseif($order->status === 'processed')
                    <span
                        class="bg-zinc-950 text-white border border-zinc-950 text-[10px] font-black uppercase tracking-[0.2em] px-4 py-2 rounded-md italic">Sedang
                        Diproses</span>
                @elseif($order->status === 'shipped')
                    <span
                        class="bg-blue-50 text-blue-600 border border-blue-100 text-[10px] font-black uppercase tracking-[0.2em] px-4 py-2 rounded-md italic">Sedang
                        Dikirim</span>
                @elseif($order->status === 'completed')
                    <span
                        class="bg-emerald-50 text-emerald-600 border border-emerald-100 text-[10px] font-black uppercase tracking-[0.2em] px-4 py-2 rounded-md italic">Selesai</span>
                @elseif($order->status === 'cancelled')
                    <span
                        class="bg-rose-50 text-rose-600 border border-rose-100 text-[10px] font-black uppercase tracking-[0.2em] px-4 py-2 rounded-md italic">Dibatalkan</span>
                @endif
            </div>
        </div>

        {{-- Main Layout Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 sm:gap-12 items-start">

            {{-- KOLOM KIRI: Daftar Item & Alamat Tujuan (Column 8 dari 12) --}}
            <div class="lg:col-span-8 space-y-6 sm:space-y-8">

                {{-- Box 1: Item Pesanan Pakaian --}}
                <div class="bg-white border border-zinc-100 rounded-2xl p-5 sm:p-8 shadow-sm">
                    <h2
                        class="text-xs sm:text-sm font-black uppercase tracking-[0.2em] text-zinc-950 flex items-center gap-3 border-b border-zinc-50 pb-4 mb-4">
                        <span class="w-1.5 h-3 bg-zinc-950 block"></span> Item Pesanan
                    </h2>

                    {{-- Desktop & Tablet View Table --}}
                    <div class="hidden sm:block overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="border-b border-zinc-100 text-[9px] font-black uppercase tracking-widest text-zinc-400">
                                    <th class="pb-3">Produk / Artikel</th>
                                    <th class="pb-3 text-center" width="80">Qty</th>
                                    <th class="pb-3 text-right" width="140">Harga</th>
                                    <th class="pb-3 text-right" width="140">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-50 text-xs font-bold text-zinc-800">
                                @foreach ($order->orderItems as $item)
                                    <tr class="align-middle">
                                        <td class="py-4 flex items-center gap-4">
                                            @if ($item->product->image)
                                                <div
                                                    class="w-12 h-16 bg-zinc-50 rounded-lg overflow-hidden flex-shrink-0 border border-zinc-100">
                                                    <img src="{{ asset('storage/' . $item->product->image) }}"
                                                        alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                                </div>
                                            @endif
                                            <span
                                                class="uppercase tracking-tight text-zinc-950 font-black">{{ $item->product->name }}</span>
                                        </td>
                                        <td class="py-4 text-center text-zinc-500 font-medium">{{ $item->qty }}</td>
                                        <td class="py-4 text-right italic font-medium">{{ $item->formatted_price }}</td>
                                        <td class="py-4 text-right italic font-black text-zinc-950">
                                            {{ $item->formatted_subtotal }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="border-t border-zinc-100 text-xs font-bold text-zinc-800">
                                <tr>
                                    <td colspan="3"
                                        class="pt-4 text-right text-[9px] font-black uppercase tracking-widest text-zinc-400">
                                        Subtotal Produk:
                                    </td>
                                    <td class="pt-4 text-right italic font-medium text-zinc-700">
                                        IDR {{ number_format($order->orderItems->sum('subtotal'), 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3"
                                        class="py-2 text-right text-[9px] font-black uppercase tracking-widest text-zinc-400">
                                        Ongkos Kirim ({{ strtoupper($order->shipping_courier) }} -
                                        {{ $order->shipping_service }}):
                                    </td>
                                    <td class="py-2 text-right italic font-medium text-zinc-700">
                                        IDR {{ number_format($order->shipping_cost, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3"
                                        class="py-2 text-right text-[9px] font-black uppercase tracking-widest text-zinc-400">
                                        Metode Pembayaran:
                                    </td>
                                    <td class="py-2 text-right text-xs font-black uppercase text-zinc-950 italic">
                                        {{ $order->payment->payment_method_label ?? '-' }}
                                    </td>
                                </tr>
                                <tr class="border-t-2 border-zinc-950">
                                    <td colspan="3"
                                        class="pt-4 text-right text-[10px] font-black uppercase tracking-widest text-zinc-950">
                                        Total Pembayaran:
                                    </td>
                                    <td class="pt-4 text-right text-base font-black italic text-zinc-950">
                                        {{ $order->formatted_total }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    {{-- Mobile View Layout (Stack Card) --}}
                    <div class="sm:hidden space-y-4">
                        @foreach ($order->orderItems as $item)
                            <div class="flex items-start gap-4 pb-4 border-b border-zinc-50 last:border-none last:pb-0">
                                @if ($item->product->image)
                                    <div
                                        class="w-14 h-20 bg-zinc-50 rounded-lg overflow-hidden flex-shrink-0 border border-zinc-100">
                                        <img src="{{ asset('storage/' . $item->product->image) }}"
                                            alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-[11px] font-black uppercase tracking-tight text-zinc-950 truncate">
                                        {{ $item->product->name }}</h4>
                                    <p class="text-[10px] text-zinc-400 font-medium mt-0.5">{{ $item->qty }} x
                                        {{ $item->formatted_price }}</p>
                                    <p class="text-xs font-black italic text-zinc-950 mt-1.5">
                                        {{ $item->formatted_subtotal }}</p>
                                </div>
                            </div>
                        @endforeach

                        <div class="pt-4 border-t border-zinc-100 space-y-2 text-xs font-bold text-zinc-800">
                            <div class="flex justify-between items-center">
                                <span class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Subtotal
                                    Produk</span>
                                <span class="italic font-medium text-zinc-700">IDR
                                    {{ number_format($order->orderItems->sum('subtotal'), 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Ongkos Kirim
                                    ({{ strtoupper($order->shipping_courier) }} - {{ $order->shipping_service }})</span>
                                <span class="italic font-medium text-zinc-700">IDR
                                    {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Metode Pembayaran</span>
                                <span class="text-xs font-black uppercase text-zinc-950 italic">{{ $order->payment->payment_method_label ?? '-' }}</span>
                            </div>
                            <div class="pt-3 border-t border-zinc-950 flex justify-between items-baseline text-zinc-950">
                                <span class="text-[9px] font-black uppercase tracking-widest text-zinc-950">Total
                                    Pembayaran</span>
                                <span
                                    class="text-base font-black italic tracking-tight">{{ $order->formatted_total }}</span>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Box 2: Alamat Pengiriman --}}
                <div class="bg-white border border-zinc-100 rounded-2xl p-5 sm:p-8 shadow-sm">
                    <h2
                        class="text-xs sm:text-sm font-black uppercase tracking-[0.2em] text-zinc-950 flex items-center gap-3 border-b border-zinc-50 pb-4 mb-4">
                        <span class="w-1.5 h-3 bg-zinc-950 block"></span> Alamat Pengiriman
                    </h2>
                    <div class="text-xs font-medium text-zinc-600 space-y-1.5 leading-relaxed">
                        <p class="text-sm font-black uppercase tracking-tight text-zinc-950">
                            {{ $order->shippingAddress->recipient_name }}</p>
                        <p class="font-bold text-zinc-800 italic">{{ $order->shippingAddress->phone }}</p>
                        <p class="max-w-xl">{{ $order->shippingAddress->full_address }}</p>
                    </div>
                </div>

            </div>

            {{-- KOLOM KANAN: Status Pembayaran & Histori Timeline (Column 4 dari 12) --}}
            <div class="lg:col-span-4 space-y-6">

                {{-- Box 3: Status Finansial / Pembayaran --}}
                <div class="bg-white border border-zinc-100 rounded-2xl p-5 sm:p-8 shadow-sm space-y-4">
                    <h2
                        class="text-xs sm:text-sm font-black uppercase tracking-[0.2em] text-zinc-950 flex items-center gap-3 border-b border-zinc-50 pb-4">
                        <span class="w-1.5 h-3 bg-zinc-950 block"></span> Status Pembayaran
                    </h2>

                    <div class="flex justify-between items-center text-[10px] font-black uppercase tracking-wider text-zinc-400 border-b border-zinc-50 pb-3">
                        <span>Metode Pembayaran:</span>
                        <span class="text-zinc-950 italic">{{ $order->payment->payment_method_label ?? '-' }}</span>
                    </div>

                    @if ($order->payment_status === 'unpaid')
                        @if ($order->payment && $order->payment->payment_method === 'cod')
                            <div
                                class="p-3.5 bg-zinc-100 border border-zinc-200 rounded-xl text-xs font-bold text-zinc-700 italic flex items-center gap-2">
                                <span>✓ Bayar di Tempat (COD)</span>
                            </div>
                            <p class="text-[9px] font-medium leading-relaxed text-zinc-400 uppercase tracking-wider italic">
                                * Pembayaran akan dilakukan secara tunai langsung kepada kurir saat pesanan tiba di alamat Anda.
                            </p>
                        @else
                            <div
                                class="p-3.5 bg-amber-50/70 border border-amber-100 rounded-xl text-xs font-bold text-amber-700 italic flex items-center gap-2">
                                <span>⚠ Belum Ada Transaksi Pembayaran</span>
                            </div>

                            <button type="button" @click="uploadOpen = !uploadOpen"
                                class="w-full py-3 bg-zinc-950 text-white text-[10px] font-black uppercase tracking-[0.2em] hover:bg-zinc-800 transition-all rounded-xl text-center shadow-md">
                                Upload Bukti Pembayaran
                            </button>

                            {{-- Panel Dropdown Form Upload via Alpine.js --}}
                            <div x-show="uploadOpen" x-transition x-cloak class="pt-2">
                                <form action="{{ route('customer.orders.payment', $order) }}" method="POST"
                                    enctype="multipart/form-data"
                                    class="space-y-3.5 p-4 bg-zinc-50 border border-zinc-100 rounded-xl">
                                    @csrf
                                    <div class="space-y-1.5">
                                        <label class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Berkas
                                            Bukti Transfer</label>
                                        <input type="file" name="proof" required accept="image/jpeg,image/png,image/jpg"
                                            class="w-full text-xs text-zinc-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-[9px] file:font-black file:uppercase file:tracking-wider file:bg-zinc-950 file:text-white file:cursor-pointer hover:file:bg-zinc-800">
                                        <p class="text-[8px] font-medium text-zinc-400 uppercase tracking-wider italic">*
                                            Format: JPEG/PNG, Maksimal 2MB</p>
                                    </div>
                                    <button type="submit"
                                        class="w-full py-2.5 bg-emerald-600 text-white text-[9px] font-black uppercase tracking-widest hover:bg-emerald-700 transition-all rounded-lg text-center">
                                        Kirim Bukti Konfirmasi
                                    </button>
                                </form>
                            </div>
                        @endif
                    @elseif($order->payment_status === 'pending')
                        <div
                            class="p-3.5 bg-zinc-950 text-white border border-zinc-950 rounded-xl text-xs font-bold italic flex items-center gap-2 animate-pulse">
                            <span>⏱ Menunggu Verifikasi Admin</span>
                        </div>

                        @if ($order->payment->proof)
                            <div class="border border-zinc-100 rounded-xl overflow-hidden shadow-sm bg-zinc-50 p-2">
                                <img src="{{ $order->payment->proof_url }}" alt="Bukti Pembayaran"
                                    class="w-full h-auto rounded-lg max-h-64 object-contain mx-auto">
                            </div>
                        @endif

                        <p class="text-[9px] font-medium leading-relaxed text-zinc-400 uppercase tracking-wider italic">
                            * Berkas transaksi Anda sudah aman dan sedang dalam antrean audit tim finance STS.
                        </p>
                    @elseif($order->payment_status === 'paid')
                        <div
                            class="p-3.5 bg-emerald-50 border border-emerald-100 rounded-xl text-xs font-bold text-emerald-700 italic flex items-center gap-2">
                            <span>✓ Pembayaran Terverifikasi</span>
                        </div>

                        <div class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 space-y-2 pt-1">
                            <div class="flex justify-between border-b border-zinc-50 pb-2">
                                <span>Metode:</span>
                                <span
                                    class="text-zinc-950 font-black italic">{{ $order->payment->payment_method_label }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Tanggal:</span>
                                <span class="text-zinc-950 font-black italic">
                                    {{ $order->payment->verified_at ? $order->payment->verified_at->translatedFormat('d M Y H:i') : '-' }}
                                </span>
                            </div>
                        </div>
                    @elseif($order->payment_status === 'rejected')
                        <div
                            class="p-3.5 bg-rose-50 border border-rose-100 rounded-xl text-xs font-bold text-rose-700 italic flex items-center gap-2">
                            <span>✕ Verifikasi Pembayaran Ditolak</span>
                        </div>

                        @if ($order->payment->admin_notes)
                            <div
                                class="p-4 bg-zinc-50 border border-zinc-100 rounded-xl text-xs font-medium text-zinc-600">
                                <strong class="text-[9px] font-black uppercase text-rose-600 block mb-1">Alasan
                                    Penolakan:</strong>
                                <p class="leading-relaxed">{{ $order->payment->admin_notes }}</p>
                            </div>
                        @endif

                        <button type="button" @click="uploadOpen = !uploadOpen"
                            class="w-full py-3 bg-zinc-950 text-white text-[10px] font-black uppercase tracking-[0.2em] hover:bg-zinc-800 transition-all rounded-xl text-center shadow-md">
                            Upload Ulang Bukti
                        </button>

                        <div x-show="uploadOpen" x-transition x-cloak class="pt-2">
                            <form action="{{ route('customer.orders.payment', $order) }}" method="POST"
                                enctype="multipart/form-data"
                                class="space-y-3.5 p-4 bg-zinc-50 border border-zinc-100 rounded-xl">
                                @csrf
                                <div class="space-y-1.5">
                                    <label class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Bukti
                                        Transfer Baru</label>
                                    <input type="file" name="proof" required accept="image/jpeg,image/png,image/jpg"
                                        class="w-full text-xs text-zinc-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-[9px] file:font-black file:uppercase file:tracking-wider file:bg-zinc-950 file:text-white file:cursor-pointer hover:file:bg-zinc-800">
                                </div>
                                <button type="submit"
                                    class="w-full py-2.5 bg-emerald-600 text-white text-[9px] font-black uppercase tracking-widest hover:bg-emerald-700 transition-all rounded-lg text-center">
                                    Upload Ulang
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

                {{-- Box 4: Timeline Status Logistik --}}
                <div class="bg-white border border-zinc-100 rounded-2xl p-5 sm:p-8 shadow-sm space-y-5">
                    <h2
                        class="text-xs sm:text-sm font-black uppercase tracking-[0.2em] text-zinc-950 flex items-center gap-3 border-b border-zinc-50 pb-4">
                        <span class="w-1.5 h-3 bg-zinc-950 block"></span> Log Alur Pesanan
                    </h2>

                    {{-- Vertical Timeline List --}}
                    <div
                        class="relative pl-6 space-y-6 before:content-[''] before:absolute before:left-2 before:top-1.5 before:bottom-1.5 before:w-[1.5px] before:bg-zinc-100">

                        {{-- Log 1: Order Created --}}
                        <div class="relative">
                            <span
                                class="absolute -left-[21px] top-1 w-2.5 h-2.5 rounded-full border-2 border-white bg-zinc-950 shadow-sm"></span>
                            <div class="text-xs">
                                <h4 class="font-black text-zinc-950 uppercase tracking-tight">Order Berhasil Dibuat</h4>
                                <p class="text-[10px] font-bold text-zinc-400 italic mt-0.5">
                                    {{ $order->created_at->translatedFormat('d M Y H:i') }}
                                </p>
                            </div>
                        </div>

                        {{-- Log 2: Processed --}}
                        @if ($order->status !== 'pending')
                            <div class="relative">
                                <span
                                    class="absolute -left-[21px] top-1 w-2.5 h-2.5 rounded-full border-2 border-white bg-zinc-950 shadow-sm"></span>
                                <div class="text-xs">
                                    <h4 class="font-black text-zinc-950 uppercase tracking-tight">Pesanan Diproses</h4>
                                    <p class="text-[10px] font-medium text-zinc-400 mt-0.5">Produk sedang disiapkan di
                                        gudang STS.</p>
                                </div>
                            </div>
                        @endif

                        {{-- Log 3: Shipped --}}
                        @if ($order->status === 'shipped' || $order->status === 'completed')
                            <div class="relative">
                                <span
                                    class="absolute -left-[21px] top-1 w-2.5 h-2.5 rounded-full border-2 border-white bg-zinc-950 shadow-sm"></span>
                                <div class="text-xs">
                                    <h4 class="font-black text-zinc-950 uppercase tracking-tight">Dalam Pengiriman</h4>
                                    <p class="text-[10px] font-medium text-zinc-400 mt-0.5">Paket diserahkan ke pihak
                                        ekspedisi.</p>
                                </div>
                            </div>
                        @endif

                        {{-- Log 4: Completed --}}
                        @if ($order->status === 'completed')
                            <div class="relative">
                                <span
                                    class="absolute -left-[21px] top-1 w-2.5 h-2.5 rounded-full border-2 border-white bg-emerald-500 shadow-sm animate-pulse"></span>
                                <div class="text-xs">
                                    <h4 class="font-black text-emerald-600 uppercase tracking-tight">Paket Selesai</h4>
                                    <p class="text-[10px] font-medium text-zinc-400 mt-0.5">Pesanan telah diterima oleh
                                        pembeli.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>

        {{-- Bottom Action: Kembali ke Daftar Pesanan --}}
        <div class="mt-8 sm:mt-12 pt-6 sm:pt-8 border-t border-zinc-100">
            <a href="{{ route('customer.orders.index') }}"
                class="inline-flex items-center gap-3 px-6 py-3 border-2 border-zinc-950 rounded-full text-[10px] font-black uppercase tracking-widest hover:bg-zinc-950 hover:text-white transition-all active:scale-95">
                ← Kembali ke List Vault Anda
            </a>
        </div>

    </div>
@endsection
