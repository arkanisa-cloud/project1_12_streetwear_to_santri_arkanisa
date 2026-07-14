@extends('layouts.admin')

@section('title', 'Manajemen Pesanan')

@section('content')
    <div class="space-y-6">
        <div
            class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <div>
                <h1 class="text-2xl font-black tracking-tighter text-zinc-950 uppercase italic">Manajemen Pesanan</h1>
                <p class="text-sm text-zinc-500">Pantau dan kelola semua pesanan pelanggan dengan efisien.</p>
            </div>
        </div>

        {{-- Filter --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <form action="{{ route('admin.orders.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-600 mb-2">Status
                        Pesanan</label>
                    <select name="status"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-zinc-500 text-sm">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processed" {{ request('status') == 'processed' ? 'selected' : '' }}>Diproses</option>
                        <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Dikirim</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan
                        </option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-600 mb-2">Status
                        Pembayaran</label>
                    <select name="payment_status"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-zinc-500 text-sm">
                        <option value="">Semua Status</option>
                        <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Belum Bayar
                        </option>
                        <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Menunggu
                            Verifikasi</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Lunas</option>
                        <option value="rejected" {{ request('payment_status') == 'rejected' ? 'selected' : '' }}>Ditolak
                        </option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-600 mb-2">Cari</label>
                    <input type="text" name="search"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-zinc-500 text-sm"
                        placeholder="No. order / nama pelanggan..." value="{{ request('search') }}">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="flex-1 bg-zinc-950 hover:bg-zinc-800 text-white text-xs font-bold uppercase tracking-widest px-4 py-2 rounded-lg transition-all">
                        Filter
                    </button>
                    <a href="{{ route('admin.orders.index') }}"
                        class="bg-zinc-100 hover:bg-zinc-200 text-zinc-700 text-xs font-bold uppercase tracking-widest px-4 py-2 rounded-lg transition-all">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Orders Table --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse datatable">
                    <thead>
                        <tr class="bg-zinc-50 border-b border-gray-100">
                            <th class="px-4 py-4 text-xs font-black uppercase tracking-widest text-zinc-600">No. Pesanan
                            </th>
                            <th class="px-4 py-4 text-xs font-black uppercase tracking-widest text-zinc-600">Tanggal</th>
                            <th class="px-4 py-4 text-xs font-black uppercase tracking-widest text-zinc-600">Pelanggan</th>
                            <th class="px-4 py-4 text-xs font-black uppercase tracking-widest text-zinc-600">Total</th>
                            <th class="px-4 py-4 text-xs font-black uppercase tracking-widest text-zinc-600">Status</th>
                            <th class="px-4 py-4 text-xs font-black uppercase tracking-widest text-zinc-600">Pembayaran</th>
                            <th class="px-4 py-4 text-xs font-black uppercase tracking-widest text-zinc-600 text-right">Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($orders as $order)
                            <tr class="hover:bg-zinc-50/50 transition-colors group">
                                <td class="px-4 py-4">
                                    <div class="text-sm font-bold text-zinc-950">{{ $order->order_number }}</div>
                                    <div class="text-[10px] text-zinc-400 font-medium italic">#ORDER-{{ $order->id }}
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-sm text-zinc-600">
                                    {{ $order->created_at->translatedFormat('d M Y') }}
                                </td>
                                <td class="px-4 py-4 text-sm font-medium text-zinc-900">{{ $order->user->name }}</td>
                                <td class="px-4 py-4 text-sm font-black text-zinc-900">{{ $order->formatted_total }}</td>
                                <td class="px-4 py-4">
                                    @php
                                        $statusMap = [
                                            'pending' => [
                                                'class' => 'bg-amber-100 text-amber-800',
                                                'label' => 'Pending',
                                            ],
                                            'processed' => [
                                                'class' => 'bg-blue-100 text-blue-800',
                                                'label' => 'Diproses',
                                            ],
                                            'shipped' => ['class' => 'bg-cyan-100 text-cyan-800', 'label' => 'Dikirim'],
                                            'completed' => [
                                                'class' => 'bg-emerald-100 text-emerald-800',
                                                'label' => 'Selesai',
                                            ],
                                            'cancelled' => [
                                                'class' => 'bg-red-100 text-red-800',
                                                'label' => 'Dibatalkan',
                                            ],
                                        ];
                                        $sMap = $statusMap[$order->status] ?? [
                                            'class' => 'bg-zinc-100 text-zinc-600',
                                            'label' => $order->status,
                                        ];
                                    @endphp
                                    <span
                                        class="inline-block {{ $sMap['class'] }} px-2 py-1 rounded text-[10px] font-black uppercase tracking-wider">
                                        {{ $sMap['label'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    @php
                                        $payMap = [
                                            'paid' => [
                                                'class' => 'bg-emerald-100 text-emerald-800',
                                                'label' => 'Lunas',
                                            ],
                                            'pending' => [
                                                'class' => 'bg-amber-100 text-amber-800',
                                                'label' => 'Menunggu',
                                            ],
                                            'rejected' => ['class' => 'bg-red-100 text-red-800', 'label' => 'Ditolak'],
                                            'unpaid' => [
                                                'class' => 'bg-zinc-100 text-zinc-600',
                                                'label' => 'Belum Bayar',
                                            ],
                                        ];
                                        $pMap = $payMap[$order->payment_status] ?? [
                                            'class' => 'bg-zinc-100 text-zinc-600',
                                            'label' => $order->payment_status,
                                        ];
                                    @endphp
                                    <span
                                        class="inline-block {{ $pMap['class'] }} px-2 py-1 rounded text-[10px] font-black uppercase tracking-wider">
                                        {{ $pMap['label'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <a href="{{ route('admin.orders.show', $order) }}"
                                        class="inline-flex p-2 text-zinc-400 hover:text-zinc-950 transition-colors"
                                        title="Lihat Detail">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                            </path>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
