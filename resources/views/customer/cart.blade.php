@extends('layouts.customer')

@section('title', 'Keranjang Belanja')

@section('breadcrumb')
    <li class="text-zinc-950 italic underline underline-offset-4">Vault / Cart</li>
@endsection

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 sm:py-12">
        {{-- Header Page --}}
        <div class="mb-6 sm:mb-12 border-b border-zinc-100 pb-4 sm:pb-8">
            <h1 class="text-3xl sm:text-4xl font-black italic tracking-tighter uppercase">Vault / <span
                    class="text-zinc-400">Keranjang</span>
            </h1>
            <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.3em] mt-2">Tinjau dan kelola barang belanjaan
                kamu.</p>
        </div>

        @if ($cart->cartItems->isEmpty())
            {{-- Empty Cart --}}
            <div class="text-center py-40">
                <div class="mb-8">
                    <svg class="w-24 h-24 mx-auto text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-black italic uppercase tracking-tight text-zinc-950 mb-4">Keranjang Kamu Kosong
                </h3>
                <p class="text-zinc-400 text-sm mb-8">Mulai belanja untuk menambahkan barang ke keranjang.</p>
                <a href="{{ route('customer.shop.index') }}"
                    class="inline-flex items-center gap-2 px-8 py-4 bg-zinc-950 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-xl hover:bg-white hover:text-zinc-950 border border-zinc-950 transition-all duration-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    Mulai Belanja
                </a>
            </div>
        @else
            {{-- Cart Items --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-12" x-data="{
                selectedIds: @js($cart->cartItems->where('is_selected', true)->pluck('id')->values()),
                allIds: @js($cart->cartItems->pluck('id')->values()),
                get allSelected() { return this.selectedIds.length === this.allIds.length && this.allIds.length > 0 },
                toggleAll() {
                    if (this.allSelected) {
                        this.selectedIds = [];
                        this.$refs.bulkActionForm.action = '{{ route('customer.cart.bulk-action') }}';
                        this.$refs.actionInput.value = 'deselect_all';
                        this.$refs.bulkActionForm.submit();
                    } else {
                        this.selectedIds = [...this.allIds];
                        this.$refs.bulkActionForm.action = '{{ route('customer.cart.bulk-action') }}';
                        this.$refs.actionInput.value = 'select_all';
                        this.$refs.bulkActionForm.submit();
                    }
                }
            }">
                {{-- Cart Items List --}}
                <div class="lg:col-span-8">
                    <div class="bg-white border border-zinc-100 rounded-[1.5rem] sm:rounded-[2.5rem] p-4 sm:p-8 shadow-sm">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 sm:mb-10 gap-4">
                            <div class="flex items-center gap-4">
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <div class="relative flex items-center">
                                        <input type="checkbox" @change="toggleAll()" :checked="allSelected"
                                            class="w-6 h-6 rounded-lg border-2 border-zinc-200 text-zinc-950 focus:ring-0 focus:ring-offset-0 transition-all cursor-pointer checked:bg-zinc-950 checked:border-zinc-950">
                                        <svg class="absolute w-4 h-4 text-white pointer-events-none opacity-0 transition-opacity"
                                            :class="{ 'opacity-100': allSelected }" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24" style="left: 4px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <span
                                        class="text-xs font-black uppercase tracking-widest text-zinc-950 group-hover:text-zinc-600 transition-colors">Pilih
                                        Semua</span>
                                </label>
                            </div>

                            <div class="flex items-center gap-3">
                                <form x-ref="bulkActionForm" method="POST"
                                    action="{{ route('customer.cart.bulk-action') }}">
                                    @csrf
                                    <input type="hidden" name="action" x-ref="actionInput" value="delete">
                                    <template x-for="id in selectedIds" :key="id">
                                        <input type="hidden" name="ids[]" :value="id">
                                    </template>
                                    <button type="button" x-show="selectedIds.length > 0" x-transition
                                        class="text-[10px] font-black uppercase tracking-widest text-red-500 hover:bg-red-50 px-4 py-2 rounded-xl transition-all"
                                        @click="
                                            Swal.fire({
                                                title: 'Hapus Barang?',
                                                text: 'Barang terpilih akan dihapus dari manifest belanja kamu.',
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonText: 'HAPUS SEKARANG',
                                                cancelButtonText: 'BATAL',
                                                reverseButtons: true
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    $refs.bulkActionForm.submit();
                                                }
                                            })
                                        ">
                                        Hapus Terpilih (<span x-text="selectedIds.length"></span>)
                                    </button>
                                </form>
                                <span
                                    class="text-[10px] font-black uppercase tracking-widest text-zinc-400 bg-zinc-50 px-4 py-2 rounded-full border border-zinc-100">
                                    {{ $cart->count_all }} Barang
                                </span>
                            </div>
                        </div>

                        <div class="space-y-4">
                            @foreach ($cart->cartItems as $item)
                                <div class="flex items-center gap-4 group">
                                    {{-- Checkbox --}}
                                    <form action="{{ route('customer.cart.update', $item) }}" method="POST"
                                        x-ref="form_{{ $item->id }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="relative flex items-center">
                                            <input type="hidden" name="is_selected" value="0">
                                            <input type="checkbox" name="is_selected" value="1"
                                                {{ $item->is_selected ? 'checked' : '' }} @change="$el.form.submit()"
                                                class="w-6 h-6 rounded-lg border-2 border-zinc-100 text-zinc-950 focus:ring-0 focus:ring-offset-0 transition-all cursor-pointer checked:bg-zinc-950 checked:border-zinc-950">
                                            @if ($item->is_selected)
                                                <svg class="absolute w-4 h-4 text-white pointer-events-none" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24" style="left: 4px;">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4"
                                                        d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            @endif
                                        </div>
                                    </form>

                                    <div
                                        class="flex-1 flex gap-4 sm:gap-6 p-4 sm:p-6 bg-zinc-50 rounded-[1.5rem] sm:rounded-[2rem] border border-transparent transition-all duration-300 {{ $item->is_selected ? 'bg-white border-zinc-100 shadow-sm' : 'opacity-60 grayscale-[0.5]' }}">
                                        {{-- Product Image --}}
                                        <div
                                            class="w-20 h-20 sm:w-24 sm:h-24 flex-shrink-0 rounded-2xl overflow-hidden bg-zinc-100 border border-zinc-200/50">
                                            @if ($item->product->image)
                                                <img src="{{ asset('storage/' . $item->product->image) }}"
                                                    alt="{{ $item->product->name }}"
                                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                            @else
                                                <div
                                                    class="w-full h-full flex items-center justify-center text-[8px] font-black uppercase text-zinc-300 italic">
                                                    Tanpa Gambar
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Product Details --}}
                                        <div class="flex-1 min-w-0">
                                            <div class="flex justify-between items-start mb-2">
                                                <div>
                                                    <h3
                                                        class="text-sm font-black uppercase tracking-tight text-zinc-950 mb-1">
                                                        {{ $item->product->name }}</h3>
                                                    <p
                                                        class="text-[9px] font-black uppercase tracking-widest text-zinc-400">
                                                        {{ $item->product->category->name }}</p>
                                                </div>
                                                <form action="{{ route('customer.cart.destroy', $item) }}" method="POST"
                                                    x-ref="deleteForm_{{ $item->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        @click="
                                                            Swal.fire({
                                                                title: 'Hapus Item?',
                                                                text: 'Barang ini akan dikeluarkan dari keranjang.',
                                                                icon: 'question',
                                                                showCancelButton: true,
                                                                confirmButtonText: 'HAPUS',
                                                                cancelButtonText: 'BATAL',
                                                                reverseButtons: true
                                                            }).then((result) => {
                                                                if (result.isConfirmed) {
                                                                    $el.form.submit();
                                                                }
                                                            })
                                                        "
                                                        class="w-8 h-8 flex items-center justify-center rounded-full bg-white text-zinc-300 hover:text-red-500 hover:bg-red-50 border border-zinc-100 transition-all">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>

                                            <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
                                                <div class="flex items-center gap-4">
                                                    {{-- Quantity --}}
                                                    <form action="{{ route('customer.cart.update', $item) }}"
                                                        method="POST"
                                                        class="flex items-center bg-white rounded-xl border border-zinc-100 p-1 shadow-sm">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="button" onclick="changeQty(this, -1)"
                                                            class="w-8 h-8 rounded-lg flex items-center justify-center text-zinc-400 hover:text-zinc-950 hover:bg-zinc-50 transition-all">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M20 12H4"></path>
                                                            </svg>
                                                        </button>
                                                        <input type="number" name="qty" value="{{ $item->qty }}"
                                                            min="1" max="{{ $item->product->stock }}"
                                                            class="w-12 border-none bg-transparent text-center text-xs font-black focus:ring-0"
                                                            onchange="this.form.submit()">
                                                        <button type="button" onclick="changeQty(this, 1)"
                                                            class="w-8 h-8 rounded-lg flex items-center justify-center text-zinc-400 hover:text-zinc-950 hover:bg-zinc-50 transition-all">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                    <span
                                                        class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest">Stok:
                                                        {{ $item->product->stock }}</span>
                                                </div>

                                                <div class="text-left sm:text-right">
                                                    <p class="text-sm font-black italic text-zinc-950">IDR
                                                        {{ number_format($item->product->price, 0, ',', '.') }}</p>
                                                    <p
                                                        class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                                                        Subtotal: IDR
                                                        {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Clear Cart --}}
                        <div class="mt-12 pt-8 border-t border-zinc-100 flex justify-between items-center">
                            <form action="{{ route('customer.cart.clear') }}" method="POST">
                                @csrf
                                <button type="button"
                                    @click="
                                        Swal.fire({
                                            title: 'Kosongkan Keranjang?',
                                            text: 'Semua barang di manifest akan dihapus permanen.',
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonText: 'KOSONGKAN SEKARANG',
                                            cancelButtonText: 'BATAL',
                                            reverseButtons: true
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                $el.form.submit();
                                            }
                                        })
                                    "
                                    class="text-[9px] font-black uppercase tracking-[0.2em] text-zinc-300 hover:text-red-500 transition-colors">
                                    Kosongkan Keranjang
                                </button>
                            </form>
                            <p class="text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400 italic">STS VAULT —
                                2026</p>
                        </div>
                    </div>
                </div>

                {{-- Order Summary --}}
                <div class="lg:col-span-4 space-y-6">
                    <div class="bg-zinc-950 rounded-[1.5rem] sm:rounded-[2.5rem] p-6 sm:p-8 text-white shadow-2xl sticky top-24">
                        <h3 class="text-xl font-black uppercase tracking-tight italic mb-8 border-b border-white/10 pb-4">
                            Ringkasan Pesanan</h3>

                        <div class="space-y-6 mb-10">
                            <div class="flex justify-between items-center">
                                <span class="text-[10px] font-black uppercase tracking-widest text-zinc-500">Barang
                                    Terpilih</span>
                                <span class="text-sm font-black italic"><span x-text="selectedIds.length"></span>
                                    Item</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span
                                    class="text-[10px] font-black uppercase tracking-widest text-zinc-500">Subtotal</span>
                                <span class="text-sm font-black italic">IDR
                                    {{ number_format($cart->total, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex flex-col gap-1">
                                <div class="flex justify-between items-center">
                                    <span class="text-[10px] font-black uppercase tracking-widest text-zinc-500">Ongkos
                                        Kirim</span>
                                    <span class="text-[8px] font-black uppercase tracking-widest text-zinc-600">Dihitung
                                        otomatis</span>
                                </div>
                                <div class="w-full bg-white/5 h-1.5 rounded-full overflow-hidden mt-2">
                                    <div class="bg-zinc-700 h-full w-1/3"></div>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-white/10 pt-8 mb-10">
                            <div class="flex justify-between items-end">
                                <div>
                                    <span
                                        class="text-[10px] font-black uppercase tracking-widest text-zinc-500 block mb-1">Total
                                        Pembayaran</span>
                                    <span class="text-2xl font-black italic tracking-tighter">IDR
                                        {{ number_format($cart->total, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('customer.checkout.index') }}"
                            :class="selectedIds.length === 0 ? 'opacity-50 cursor-not-allowed grayscale' : ''"
                            @click="if(selectedIds.length === 0) { $event.preventDefault(); alert('Pilih minimal satu barang!'); }"
                            class="w-full inline-flex items-center justify-center gap-3 py-5 bg-white text-zinc-950 text-[11px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-zinc-100 transition-all duration-300 shadow-xl active:scale-95">
                            <span>Lanjut ke Checkout</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </a>
                    </div>

                    {{-- Continue Shopping --}}
                    <a href="{{ route('customer.shop.index') }}"
                        class="group block w-full text-center py-5 bg-white border border-zinc-100 rounded-[2rem] hover:bg-zinc-50 transition-all duration-300 shadow-sm">
                        <span
                            class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-950 group-hover:tracking-[0.3em] transition-all">←
                            Lanjut Belanja</span>
                    </a>
                </div>
            </div>
        @endif
    </div>

    <script>
        function changeQty(button, delta) {
            const input = button.parentElement.querySelector('input[type="number"]');
            const newValue = parseInt(input.value) + delta;
            const min = parseInt(input.min);
            const max = parseInt(input.max);

            if (newValue >= min && newValue <= max) {
                input.value = newValue;
                input.form.submit();
            }
        }
    </script>
@endsection
