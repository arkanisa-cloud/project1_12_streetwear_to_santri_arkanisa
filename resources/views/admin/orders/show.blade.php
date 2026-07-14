@extends('layouts.admin')

@section('title', 'Detail Pesanan ' . $order->order_number)

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 sm:py-10" x-data="{ rejectModal: false, proofModal: false }">

        {{-- Top Action & Header --}}
        <div class="mb-8 border-b border-zinc-100 pb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <span class="text-[9px] font-black uppercase text-zinc-400 tracking-[0.3em] block mb-1">
                    Panel Manajemen / Pesanan Pelanggan
                </span>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tighter uppercase italic text-zinc-950">
                    #{{ $order->order_number }}
                </h1>
            </div>

            {{-- Form Update Status Cepat & Badge Status --}}
            <div class="flex flex-wrap items-center gap-3">
                <div class="text-right hidden sm:block">
                    <span class="text-[9px] font-black uppercase text-zinc-400 tracking-wider block mb-1">Status Saat
                        Ini</span>
                    @if ($order->status === 'pending')
                        <span
                            class="bg-zinc-100 text-zinc-600 border border-zinc-200 text-[10px] font-black uppercase tracking-wider px-3 py-1 rounded italic">Menunggu
                            Diproses</span>
                    @elseif($order->status === 'processed')
                        <span
                            class="bg-zinc-950 text-white border border-zinc-950 text-[10px] font-black uppercase tracking-wider px-3 py-1 rounded italic">Sedang
                            Diproses</span>
                    @elseif($order->status === 'shipped')
                        <span
                            class="bg-blue-50 text-blue-600 border border-blue-100 text-[10px] font-black uppercase tracking-wider px-3 py-1 rounded italic">Sedang
                            Dikirim</span>
                    @elseif($order->status === 'completed')
                        <span
                            class="bg-emerald-50 text-emerald-600 border border-emerald-100 text-[10px] font-black uppercase tracking-wider px-3 py-1 rounded italic">Selesai</span>
                    @elseif($order->status === 'cancelled')
                        <span
                            class="bg-rose-50 text-rose-600 border border-rose-100 text-[10px] font-black uppercase tracking-wider px-3 py-1 rounded italic">Dibatalkan</span>
                    @endif
                </div>

                @if ($order->status !== 'completed' && $order->status !== 'cancelled')
                    <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST"
                        class="flex items-center gap-2 bg-zinc-50 p-2 border border-zinc-200/60 rounded-xl">
                        @csrf
                        <select name="status"
                            class="bg-white border border-zinc-200 text-xs font-bold rounded-lg px-3 py-2 focus:border-zinc-950 focus:ring-0 text-zinc-800">
                            @if ($order->status === 'pending')
                                <option value="processed">Proses Pesanan</option>
                            @endif
                            @if ($order->status === 'processed')
                                <option value="shipped">Kirim Pesanan (Kurir)</option>
                            @endif
                            @if ($order->status === 'shipped')
                                <option value="completed">Selesaikan Pesanan</option>
                            @endif
                            <option value="cancelled">Batalkan Pesanan</option>
                        </select>
                        <button type="submit"
                            class="px-4 py-2 bg-zinc-950 text-white text-[10px] font-black uppercase tracking-widest hover:bg-zinc-800 transition-all rounded-lg shadow-sm">
                            Update
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Main Column Grid System --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            {{-- KOLOM KIRI: Data Profil, Alamat & Item Pembelian (Column 8 dari 12) --}}
            <div class="lg:col-span-8 space-y-6">

                {{-- Box 1: Informasi Dasar Profil Pelanggan --}}
                <div class="bg-white border border-zinc-100 rounded-2xl p-5 sm:p-6 shadow-sm">
                    <h2
                        class="text-xs font-black uppercase tracking-[0.2em] text-zinc-950 flex items-center gap-3 border-b border-zinc-50 pb-4 mb-4">
                        <span class="w-1.5 h-3 bg-zinc-950 block"></span> Informasi Customer
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs font-medium text-zinc-600">
                        <div class="space-y-1">
                            <span class="text-[9px] font-black uppercase tracking-wider text-zinc-400 block">Akun
                                Pengguna</span>
                            <p class="text-zinc-950 font-bold text-sm">{{ $order->user->name }}</p>
                            <p class="text-zinc-400 font-normal">{{ $order->user->email }}</p>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[9px] font-black uppercase tracking-wider text-zinc-400 block">Tujuan
                                Pengiriman</span>
                            <p class="text-zinc-950 font-bold">{{ $order->shippingAddress->recipient_name }}</p>
                            <p class="text-zinc-800 font-bold italic">{{ $order->shippingAddress->phone }}</p>
                            <p class="mt-1 leading-relaxed max-w-sm">{{ $order->shippingAddress->full_address }}</p>
                        </div>
                    </div>
                </div>

                {{-- Box 2: Tabel Detail Produk / Artikel Ritel --}}
                <div class="bg-white border border-zinc-100 rounded-2xl p-5 sm:p-6 shadow-sm">
                    <h2
                        class="text-xs font-black uppercase tracking-[0.2em] text-zinc-950 flex items-center gap-3 border-b border-zinc-50 pb-4 mb-4">
                        <span class="w-1.5 h-3 bg-zinc-950 block"></span> Rincian Item Pakaian
                    </h2>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="border-b border-zinc-100 text-[9px] font-black uppercase tracking-widest text-zinc-400">
                                    <th class="pb-3">Artikel Produk</th>
                                    <th class="pb-3 text-center" width="80">Qty</th>
                                    <th class="pb-3 text-right" width="130">Harga</th>
                                    <th class="pb-3 text-right" width="130">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-50 text-xs font-bold text-zinc-800">
                                @foreach ($order->orderItems as $item)
                                    <tr class="align-middle">
                                        <td class="py-4 flex items-center gap-3">
                                            @if ($item->product->image)
                                                <div
                                                    class="w-10 h-14 bg-zinc-100 rounded-lg overflow-hidden flex-shrink-0 border border-zinc-100">
                                                    <img src="{{ asset('storage/' . $item->product->image) }}"
                                                        alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                                </div>
                                            @endif
                                            <span
                                                class="uppercase tracking-tight text-zinc-950 font-black">{{ $item->product->name }}</span>
                                        </td>
                                        <td class="py-4 text-center text-zinc-400 font-medium">{{ $item->qty }}</td>
                                        <td class="py-4 text-right italic font-medium text-zinc-500">
                                            {{ $item->formatted_price }}</td>
                                        <td class="py-4 text-right italic font-black text-zinc-950">
                                            {{ $item->formatted_subtotal }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="border-t border-zinc-100 text-xs font-bold text-zinc-800">
                                <tr>
                                    <td colspan="3" class="pt-4 text-right text-[9px] font-black uppercase tracking-widest text-zinc-400">
                                        Subtotal Produk:
                                    </td>
                                    <td class="pt-4 text-right italic font-medium text-zinc-700">
                                        IDR {{ number_format($order->orderItems->sum('subtotal'), 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="py-2 text-right text-[9px] font-black uppercase tracking-widest text-zinc-400">
                                        Ongkos Kirim ({{ strtoupper($order->shipping_courier) }} - {{ $order->shipping_service }}):
                                    </td>
                                    <td class="py-2 text-right italic font-medium text-zinc-700">
                                        IDR {{ number_format($order->shipping_cost, 0, ',', '.') }}
                                    </td>
                                </tr>
                                 <tr>
                                     <td colspan="3" class="py-2 text-right text-[9px] font-black uppercase tracking-widest text-zinc-400">
                                         Metode Pembayaran:
                                     </td>
                                     <td class="py-2 text-right text-xs font-black uppercase text-zinc-950 italic">
                                         {{ $order->payment->payment_method_label ?? '-' }}
                                     </td>
                                 </tr>
                                 <tr class="border-t border-zinc-950">
                                     <td colspan="3" class="pt-4 text-right text-[10px] font-black uppercase tracking-widest text-zinc-950">
                                         Grand Total Dana:
                                     </td>
                                     <td class="pt-4 text-right text-sm font-black italic text-zinc-950">
                                         {{ $order->formatted_total }}
                                     </td>
                                 </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </div>

            {{-- KOLOM KANAN: Kontrol Audit Pembayaran & Histori Log (Column 4 dari 12) --}}
            <div class="lg:col-span-4 space-y-6">

                {{-- Box 3: Panel Verifikasi Pembayaran / Finance Audit --}}
                <div class="bg-white border border-zinc-100 rounded-2xl p-5 sm:p-6 shadow-sm space-y-4">
                    <h2
                        class="text-xs font-black uppercase tracking-[0.2em] text-zinc-950 flex items-center gap-3 border-b border-zinc-50 pb-4">
                        <span class="w-1.5 h-3 bg-zinc-950 block"></span> Status Keuangan
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
                                * Pembayaran COD akan diterima tunai oleh kurir saat pengiriman selesai.
                            </p>
                        @else
                            <div
                                class="p-3.5 bg-amber-50/80 border border-amber-100 rounded-xl text-xs font-bold text-amber-700 italic flex items-center gap-2">
                                <span>⚠ Belum Ada Data Transfer</span>
                            </div>
                        @endif
                    @elseif($order->payment_status === 'pending')
                        <div
                            class="p-3 bg-zinc-950 text-white text-center text-xs font-black uppercase tracking-wider italic rounded-xl animate-pulse">
                            Audit Diperlukan
                        </div>

                        {{-- Tombol Lihat Bukti --}}
                        @if ($order->payment->proof)
                            <button type="button" @click="proofModal = true"
                                class="w-full py-2.5 bg-zinc-100 text-zinc-950 hover:bg-zinc-200 transition-all rounded-xl text-xs font-black uppercase tracking-widest text-center border border-zinc-200">
                                Lihat Gambar Bukti 👁
                            </button>
                        @endif

                        {{-- Tombol Aksi Persetujuan / Penolakan --}}
                        <div class="grid grid-cols-2 gap-2 pt-2 border-t border-zinc-50">
                            <form action="{{ route('admin.orders.verifyPayment', $order) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="verified">
                                <button type="submit"
                                    class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-black uppercase tracking-wider rounded-lg transition-all shadow-sm">
                                    Terima (Paid)
                                </button>
                            </form>
                            <button type="button" @click="rejectModal = true"
                                class="w-full py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-600 text-[10px] font-black uppercase tracking-wider rounded-lg transition-all border border-rose-100">
                                Tolak Berkas
                            </button>
                        </div>
                    @elseif($order->payment_status === 'paid')
                        <div
                            class="p-3.5 bg-emerald-50 border border-emerald-100 rounded-xl text-xs font-bold text-emerald-700 italic flex items-center gap-2">
                            <span>✓ Lunas Terverifikasi</span>
                        </div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 space-y-2 pt-1">
                            <div class="flex justify-between border-b border-zinc-50 pb-2">
                                <span>Metode:</span>
                                <span
                                    class="text-zinc-950 font-black italic">{{ $order->payment->payment_method_label ?? 'Transfer Bank' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Waktu Verifikasi:</span>
                                <span class="text-zinc-950 font-black italic">
                                    {{ $order->payment->verified_at ? $order->payment->verified_at->translatedFormat('d M Y H:i') : '-' }}
                                </span>
                            </div>
                        </div>
                    @elseif($order->payment_status === 'rejected')
                        <div
                            class="p-3.5 bg-rose-50 border border-rose-100 rounded-xl text-xs font-bold text-rose-700 italic flex items-center gap-2">
                            <span>✕ Pembayaran Ditolak</span>
                        </div>
                        @if ($order->payment->admin_notes)
                            <div
                                class="p-3.5 bg-zinc-50 border border-zinc-100 rounded-xl text-xs text-zinc-600 leading-relaxed">
                                <strong class="text-[9px] font-black uppercase text-rose-600 block mb-0.5">Catatan Penolakan
                                    Admin:</strong>
                                {{ $order->payment->admin_notes }}
                            </div>
                        @endif
                    @endif
                </div>

                {{-- Box 4: Alur Histori Status Pesanan --}}
                <div class="bg-white border border-zinc-100 rounded-2xl p-5 sm:p-6 shadow-sm space-y-4">
                    <h2
                        class="text-xs font-black uppercase tracking-[0.2em] text-zinc-950 flex items-center gap-3 border-b border-zinc-50 pb-4">
                        <span class="w-1.5 h-3 bg-zinc-950 block"></span> Alur Logistik
                    </h2>

                    <div
                        class="relative pl-6 space-y-5 before:content-[''] before:absolute before:left-2 before:top-1.5 before:bottom-1.5 before:w-[1.5px] before:bg-zinc-100">

                        <div class="relative">
                            <span
                                class="absolute -left-[21px] top-1 w-2.5 h-2.5 rounded-full border-2 border-white bg-zinc-950"></span>
                            <div class="text-xs">
                                <h4 class="font-black text-zinc-950 uppercase tracking-tight">Order Masuk</h4>
                                <p class="text-[9px] font-bold text-zinc-400 italic mt-0.5">
                                    {{ $order->created_at->translatedFormat('d M Y H:i') }}</p>
                            </div>
                        </div>

                        @if ($order->status !== 'pending')
                            <div class="relative">
                                <span
                                    class="absolute -left-[21px] top-1 w-2.5 h-2.5 rounded-full border-2 border-white bg-zinc-950"></span>
                                <div class="text-xs">
                                    <h4 class="font-black text-zinc-950 uppercase tracking-tight">Diproses Admin</h4>
                                </div>
                            </div>
                        @endif

                        @if ($order->status === 'shipped' || $order->status === 'completed')
                            <div class="relative">
                                <span
                                    class="absolute -left-[21px] top-1 w-2.5 h-2.5 rounded-full border-2 border-white bg-zinc-950"></span>
                                <div class="text-xs">
                                    <h4 class="font-black text-zinc-950 uppercase tracking-tight">Dikirim</h4>
                                </div>
                            </div>
                        @endif

                        @if ($order->status === 'completed')
                            <div class="relative">
                                <span
                                    class="absolute -left-[21px] top-1 w-2.5 h-2.5 rounded-full border-2 border-white bg-emerald-500"></span>
                                <div class="text-xs">
                                    <h4 class="font-black text-emerald-600 uppercase tracking-tight">Selesai</h4>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>

        {{-- Bottom Action Back Button --}}
        <div class="mt-8 pt-6 border-t border-zinc-100">
            <a href="{{ route('admin.orders.index') }}"
                class="inline-flex items-center gap-2.5 px-5 py-2.5 border-2 border-zinc-950 rounded-full text-[9px] font-black uppercase tracking-widest hover:bg-zinc-950 hover:text-white transition-all">
                ← Kembali ke List Utama
            </a>
        </div>

        {{-- MODAL ALPINE 1: Input Alasan Penolakan Pembayaran --}}
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-950/40 backdrop-blur-sm"
            x-show="rejectModal" x-transition x-cloak>
            <div class="bg-white border border-zinc-100 rounded-2xl w-full max-w-md p-6 shadow-xl"
                @click.away="rejectModal = false">
                <h3 class="text-sm font-black uppercase tracking-[0.1em] text-zinc-950 mb-2">Alasan Penolakan Berkas</h3>
                <p class="text-[11px] font-medium text-zinc-400 mb-4 leading-relaxed">Berikan penjelasan detail mengapa
                    bukti transfer ini tidak sah, agar pelanggan dapat membaca alasannya di dasbor mereka.</p>

                <form action="{{ route('admin.orders.verifyPayment', $order) }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="status" value="rejected">
                    <textarea name="admin_notes" rows="4" required
                        placeholder="Contoh: Gambar buram, Nominal transfer tidak sesuai, atau Rekening koran tidak terdaftar..."
                        class="w-full p-4 bg-zinc-50 border-none rounded-xl text-xs font-bold focus:ring-1 focus:ring-zinc-950 text-zinc-800 placeholder-zinc-300"></textarea>

                    <div class="flex justify-end gap-2 text-[10px] font-black uppercase tracking-widest">
                        <button type="button" @click="rejectModal = false"
                            class="px-4 py-2.5 border border-zinc-200 rounded-lg text-zinc-500 hover:bg-zinc-50">Batal</button>
                        <button type="submit"
                            class="px-5 py-2.5 bg-rose-600 text-white rounded-lg hover:bg-rose-700">Tolak Sekarang</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL ALPINE 2: Lightbox View Bukti Pembayaran --}}
        @if ($order->payment_status === 'pending' && $order->payment && $order->payment->proof)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-950/80 backdrop-blur-sm"
                x-show="proofModal" x-transition x-cloak>
                <div class="relative max-w-3xl w-full bg-white p-2 rounded-2xl shadow-2xl"
                    @click.away="proofModal = false">
                    <button type="button" @click="proofModal = false"
                        class="absolute -top-10 right-0 text-white font-black uppercase tracking-widest text-xs hover:underline">
                        Tutup [✕]
                    </button>
                    <div class="overflow-hidden rounded-xl bg-zinc-50 max-h-[75vh]">
                        <img src="{{ $order->payment->proof_url }}" alt="Bukti Pembayaran Audit"
                            class="w-full h-auto object-contain mx-auto">
                    </div>
                </div>
            </div>
        @endif

    </div>
@endsection
