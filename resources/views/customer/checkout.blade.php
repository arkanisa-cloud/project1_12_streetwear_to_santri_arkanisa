@extends('layouts.customer')

@section('title', 'Checkout')

@section('breadcrumb')
    <li class="text-zinc-950 italic underline underline-offset-4">Vault / Checkout</li>
@endsection

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 sm:py-12" x-data="checkoutForm()">
        {{-- Header Page --}}
        <div class="mb-6 sm:mb-12 border-b border-zinc-100 pb-4 sm:pb-8">
            <h1 class="text-3xl sm:text-4xl font-black italic tracking-tighter uppercase">Vault / <span
                    class="text-zinc-400">Checkout</span></h1>
            <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.3em] mt-2">Selesaikan pembelian kamu dengan
                aman.
            </p>
        </div>

        {{-- Checkout Progress --}}
        <div class="mb-12">
            <div class="bg-white border border-zinc-100 rounded-[1.5rem] sm:rounded-3xl p-4 sm:p-6">
                <div class="flex items-center justify-between max-w-md mx-auto">
                    <div class="flex flex-col items-center">
                        <div
                            class="w-8 h-8 bg-zinc-950 text-white rounded-full flex items-center justify-center text-[10px] font-black mb-2">
                            ✓
                        </div>
                        <span class="text-[8px] font-black uppercase tracking-widest text-zinc-950">Keranjang</span>
                    </div>
                    <div class="flex-1 h-[1px] bg-zinc-200 mx-4"></div>
                    <div class="flex flex-col items-center">
                        <div
                            class="w-8 h-8 bg-zinc-950 text-white rounded-full flex items-center justify-center text-[10px] font-black mb-2">
                            2
                        </div>
                        <span class="text-[8px] font-black uppercase tracking-widest text-zinc-950">Checkout</span>
                    </div>
                    <div class="flex-1 h-[1px] bg-zinc-200 mx-4"></div>
                    <div class="flex flex-col items-center">
                        <div
                            class="w-8 h-8 bg-zinc-400 text-zinc-100 rounded-full flex items-center justify-center text-[10px] font-black mb-2">
                            3
                        </div>
                        <span class="text-[8px] font-black uppercase tracking-widest text-zinc-400">Pembayaran</span>
                    </div>
                    <div class="flex-1 h-[1px] bg-zinc-200 mx-4"></div>
                    <div class="flex flex-col items-center">
                        <div
                            class="w-8 h-8 bg-zinc-400 text-zinc-100 rounded-full flex items-center justify-center text-[10px] font-black mb-2">
                            4
                        </div>
                        <span class="text-[8px] font-black uppercase tracking-widest text-zinc-400">Selesai</span>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('customer.checkout.store') }}" method="POST" @submit="submitForm($event)">
            @csrf

            <input type="hidden" name="shipping_courier" :value="selectedCourier">
            <input type="hidden" name="shipping_service"
                :value="shippingServices.length > 0 && selectedServiceIdx !== null ? shippingServices[selectedServiceIdx]
                    .service : ''">
            <input type="hidden" name="shipping_cost" :value="shippingCost">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-12">
                {{-- Shipping Address --}}
                <div class="lg:col-span-7">
                    <div class="bg-white border border-zinc-100 rounded-[1.5rem] sm:rounded-3xl p-4 sm:p-8">
                        <h3 class="text-lg font-black uppercase tracking-tight mb-6 flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Alamat Pengiriman
                        </h3>

                        {{-- Error message untuk alamat tidak ada --}}
                        @if ($addresses->count() === 0)
                            <div class="bg-red-50 border border-red-200 rounded-2xl p-4 mb-6">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <h4 class="text-sm font-bold text-red-900">Alamat Pengiriman Diperlukan</h4>
                                        <p class="text-sm text-red-700 mt-1">Silakan tambahkan alamat pengiriman terlebih
                                            dahulu sebelum melanjutkan checkout.</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($addresses->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                @foreach ($addresses as $address)
                                    <label class="cursor-pointer">
                                        <input class="sr-only address-radio" type="radio" name="shipping_address_id"
                                            value="{{ $address->id }}"
                                            {{ old('shipping_address_id', $addresses->first()->id) == $address->id ? 'checked' : '' }}
                                            @change="selectedAddress = $event.target.value; calculateShipping(); clearValidation()"
                                            required>
                                        <div class="border-2 rounded-xl sm:rounded-2xl p-3 sm:p-4 transition-all duration-300 hover:border-zinc-950 cursor-pointer"
                                            :class="selectedAddress === '{{ $address->id }}' ? 'border-zinc-950 bg-zinc-50' :
                                                'border-zinc-200'">
                                            <div class="flex items-start gap-3">
                                                <div class="w-5 h-5 border-2 border-zinc-300 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 transition-all"
                                                    :class="selectedAddress === '{{ $address->id }}' ? 'border-zinc-950' : ''">
                                                    <div class="w-2.5 h-2.5 bg-zinc-950 rounded-full"
                                                        :class="selectedAddress === '{{ $address->id }}' ? 'block' : 'hidden'">
                                                    </div>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex justify-between items-center mb-1">
                                                        <h4
                                                            class="text-sm font-bold uppercase tracking-tight text-zinc-950 pr-4">
                                                            {{ $address->recipient_name }}
                                                        </h4>
                                                        <a href="{{ route('customer.addresses.edit', ['shippingAddress' => $address->id, 'redirect' => 'checkout']) }}"
                                                            class="text-[9px] font-black uppercase tracking-widest text-zinc-400 hover:text-zinc-950 transition-colors duration-200 flex items-center gap-1"
                                                            @click.stop>
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                                                </path>
                                                            </svg>
                                                            Ubah
                                                        </a>
                                                    </div>
                                                    <p
                                                        class="text-[10px] font-bold uppercase tracking-widest text-zinc-500 mb-2">
                                                        📱 {{ $address->phone }}
                                                    </p>
                                                    <p class="text-xs text-zinc-600 leading-relaxed font-medium">
                                                        {{ $address->address }}<br>
                                                        @if ($address->subdistrict)
                                                            {{ $address->subdistrict }},
                                                        @endif {{ $address->city }},
                                                        {{ $address->province }}
                                                        {{ $address->postal_code }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                            <a href="{{ route('customer.addresses.create', ['redirect' => 'checkout']) }}"
                                class="inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-zinc-400 hover:text-zinc-950 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                Tambah Alamat Baru
                            </a>
                        @else
                            <div class="text-center py-12 border-2 border-dashed border-zinc-200 rounded-3xl">
                                <svg class="w-12 h-12 mx-auto text-zinc-300 mb-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <h4 class="text-sm font-black uppercase tracking-tight text-zinc-950 mb-2">Belum Ada Alamat
                                    Pengiriman</h4>
                                <p class="text-zinc-400 text-sm mb-4">Tambahkan alamat pengiriman untuk melanjutkan
                                    pesanan.
                                </p>
                                <a href="{{ route('customer.addresses.create', ['redirect' => 'checkout']) }}"
                                    class="inline-flex items-center gap-2 px-6 py-3 bg-zinc-950 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-xl hover:bg-white hover:text-zinc-950 border border-zinc-950 transition-all duration-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Tambah Alamat
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Order Summary & Payment --}}
                <div class="lg:col-span-5 space-y-6">
                    {{-- Order Summary --}}
                    <div class="bg-white border border-zinc-100 rounded-[1.5rem] sm:rounded-3xl p-4 sm:p-8">
                        <h3 class="text-lg font-black uppercase tracking-tight mb-6 flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                </path>
                            </svg>
                            Ringkasan Pesanan
                        </h3>

                        <div class="space-y-3 mb-6">
                            @foreach ($cart->cartItems as $item)
                                <div class="flex justify-between items-center py-2 border-b border-zinc-50">
                                    <div class="flex-1">
                                        <span
                                            class="text-sm font-bold uppercase tracking-tight text-zinc-950">{{ $item->qty }}x
                                            {{ $item->product->name }}</span>
                                    </div>
                                    <span class="text-sm font-black italic text-zinc-950">IDR
                                        {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>

                        {{-- Shipping Cost Calculator --}}
                        <div class="mb-6 pt-4 border-t border-zinc-100">
                            <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-950 mb-3">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4">
                                    </path>
                                </svg>
                                Pilih Ekspedisi
                            </label>
                            <div class="relative">
                                <select name="shipping_courier" id="shipping_courier" x-model="selectedCourier"
                                    @change="calculateShipping(); clearValidation()"
                                    class="w-full bg-zinc-50 border border-zinc-200 rounded-2xl px-4 py-3.5 text-xs font-medium focus:outline-none focus:ring-2 focus:ring-zinc-950 focus:bg-white transition-all">
                                    <option value="">-- Pilih Ekspedisi Pengiriman --</option>
                                    <option value="jne">JNE</option>
                                    <option value="jnt">J&T Express</option>
                                    <option value="pos">POS Indonesia</option>
                                    <option value="tiki">TIKI</option>
                                    <option value="lion">Lion Parcel</option>
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-zinc-950">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>

                            {{-- Shipping Services Options --}}
                            <div class="mt-4 space-y-2" x-show="shippingServices.length > 0">
                                <label
                                    class="block text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2">Layanan
                                    yang Tersedia</label>

                                <input type="hidden" name="shipping_service"
                                    :value="selectedServiceIdx !== null ? shippingServices[selectedServiceIdx].service : ''">

                                <template x-for="(service, idx) in shippingServices" :key="idx">
                                    <div @click="selectService(idx); clearValidation()"
                                        :class="selectedServiceIdx === idx ? 'border-zinc-950 bg-zinc-50' :
                                            'border-zinc-100 hover:border-zinc-300'"
                                        class="border rounded-xl sm:rounded-2xl p-3 sm:p-4 flex items-center justify-between cursor-pointer transition-all gap-2">
                                        <div class="flex items-center gap-4">
                                            <div class="w-4 h-4 rounded-full border flex items-center justify-center"
                                                :class="selectedServiceIdx === idx ? 'border-zinc-950' : 'border-zinc-300'">
                                                <div class="w-2 h-2 rounded-full bg-zinc-950"
                                                    x-show="selectedServiceIdx === idx"></div>
                                            </div>
                                            <div>
                                                <span class="text-xs font-black uppercase tracking-tight"
                                                    x-text="service.service"></span>
                                                <p class="text-[10px] text-zinc-400 font-medium"
                                                    x-text="service.description"></p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-xs font-black"
                                                x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(service.cost)"></span>
                                            <p class="text-[9px] text-zinc-400 font-bold uppercase tracking-wider"
                                                x-text="'Est: ' + service.etd"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <div class="form-text mt-4 flex items-center gap-2" x-show="loadingShipping">
                                <span
                                    class="inline-block w-4 h-4 border-2 border-zinc-950 border-t-transparent rounded-full animate-spin"></span>
                                <span class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Menghitung
                                    ongkos kirim...</span>
                            </div>
                            <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-xl flex items-start gap-3"
                                x-show="shippingError" x-cloak>
                                <svg class="w-4 h-4 text-red-600 flex-shrink-0 mt-0.5" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-xs font-bold text-red-700" x-text="shippingError"></span>
                            </div>
                        </div>

                        <div class="space-y-3 mb-6 pt-4 border-t border-zinc-100">
                            <div class="flex justify-between items-center">
                                <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Subtotal
                                    Barang</span>
                                <span class="text-sm font-bold text-zinc-950">IDR
                                    {{ number_format($cart->total, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Ongkos
                                    Kirim</span>
                                <span class="text-sm font-bold text-zinc-950"
                                    x-text="shippingCost > 0 ? 'IDR ' + new Intl.NumberFormat('id-ID').format(shippingCost) : 'Pilih Kurir'"></span>
                            </div>
                        </div>

                        <div class="flex justify-between items-center mb-8 pt-4 border-t border-zinc-100">
                            <span class="text-sm font-black uppercase tracking-tight">Total</span>
                            <span class="text-xl font-black italic text-zinc-950"
                                x-text="'IDR ' + new Intl.NumberFormat('id-ID').format({{ $cart->total }} + shippingCost)"></span>
                        </div>

                        {{-- Payment Method --}}
                        <div class="mb-6">
                            <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-950 mb-3">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                    </path>
                                </svg>
                                Metode Pembayaran
                            </label>
                            <div class="relative">
                                <select name="payment_method" x-model="paymentMethod"
                                    class="w-full bg-zinc-50 border border-zinc-200 rounded-xl p-4 text-sm font-bold uppercase tracking-wide focus:ring-1 focus:ring-zinc-950 focus:border-zinc-950 appearance-none cursor-pointer transition-all duration-300 hover:border-zinc-300"
                                    required @change="clearValidation()">
                                    <option value="">-- Pilih Metode Pembayaran --</option>
                                    <option value="transfer">Bank Transfer</option>
                                    <option value="ewallet">E-Wallet (GoPay/OVO/Dana/QRIS)</option>
                                    <option value="cod">Cash on Delivery (COD)</option>
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-zinc-950">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Validation Alert --}}
                        <div x-show="showValidationAlert && validationMessage" x-cloak
                            class="bg-red-50 border-2 border-red-400 rounded-2xl p-6 mb-6 animate-pulse">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5 animate-bounce" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <h4 class="text-sm font-bold text-red-900">⚠️ Silakan Perbaiki</h4>
                                    <p class="text-sm text-red-700 mt-1 font-medium" x-text="validationMessage"></p>
                                </div>
                            </div>
                        </div>

                        {{-- Place Order Button --}}
                        <button type="submit"
                            :disabled="isSubmitting || addresses.length === 0 || loadingShipping || selectedServiceIdx === null ||
                                !paymentMethod"
                            :class="{
                                'opacity-50 cursor-not-allowed': isSubmitting || addresses.length === 0 ||
                                    loadingShipping ||
                                    selectedServiceIdx === null || !paymentMethod
                            }"
                            class="w-full inline-flex items-center justify-center gap-2 py-4 bg-zinc-950 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-xl hover:bg-white hover:text-zinc-950 border border-zinc-950 transition-all duration-300 disabled:hover:bg-zinc-950 disabled:hover:text-white">
                            <svg x-show="!isSubmitting && !loadingShipping" class="w-4 h-4" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <svg x-show="isSubmitting || loadingShipping" x-cloak class="w-4 h-4 animate-spin"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                                </path>
                            </svg>
                            <span
                                x-text="isSubmitting ? 'Memproses...' : (loadingShipping ? 'Menghitung Ongkos Kirim...' : 'Buat Pesanan')"></span>
                        </button>

                        {{-- Back to Cart --}}
                        <a href="{{ route('customer.cart.index') }}"
                            class="block w-full text-center mt-4 py-3 text-[10px] font-black uppercase tracking-widest text-zinc-400 hover:text-zinc-950 transition-colors">
                            ← Kembali ke Keranjang
                        </a>
                    </div>

                    {{-- Info Card --}}
                    <div class="bg-zinc-50 border border-zinc-100 rounded-[1.5rem] sm:rounded-3xl p-4 sm:p-6">
                        <h4 class="text-sm font-black uppercase tracking-tight text-zinc-950 mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Informasi Pesanan
                        </h4>
                        <ul class="space-y-2 text-xs text-zinc-600 font-medium">
                            <li class="flex items-start gap-2">
                                <span class="text-zinc-950 mt-0.5">✓</span>
                                <span>Pesanan akan diproses setelah pembayaran dikonfirmasi</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-zinc-950 mt-0.5">✓</span>
                                <span>Waktu pengiriman bervariasi sesuai dengan kurir yang dipilih</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-zinc-950 mt-0.5">✓</span>
                                <span>Kamu dapat mengunggah bukti pembayaran setelah pesanan dibuat</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function checkoutForm() {
            const addressData = @json($addresses->map(fn($a) => ['id' => $a->id, 'district_id' => $a->subdistrict_id]));

            @php
                $cartItemsData = $cart->cartItems
                    ->map(
                        fn($item) => [
                            'product_id' => $item->product_id,
                            'qty' => $item->qty,
                            'weight' => $item->product->weight ?? 0,
                        ],
                    )
                    ->toArray();
            @endphp
            const cartItems = @json($cartItemsData);

            return {
                selectedAddress: '{{ old('shipping_address_id', $addresses->first()->id ?? '') }}',
                paymentMethod: '{{ old('payment_method', '') }}',
                isSubmitting: false,
                selectedCourier: '',
                shippingServices: [],
                selectedServiceIdx: null,
                shippingCost: 0,
                loadingShipping: false,
                shippingError: '',
                showValidationAlert: false,
                validationMessage: '',
                totalWeight: cartItems.reduce((sum, item) => sum + (parseInt(item.weight || 0) * parseInt(item.qty || 1)),
                    0) || 1000,
                addresses: @json($addresses),

                clearValidation() {
                    this.showValidationAlert = false;
                    this.validationMessage = '';
                },

                getSelectedDistrictId() {
                    const addr = addressData.find(a => a.id == this.selectedAddress);
                    return addr ? addr.district_id : null;
                },

                async calculateShipping() {
                    this.shippingServices = [];
                    this.selectedServiceIdx = null;
                    this.shippingCost = 0;
                    this.shippingError = '';

                    const districtId = this.getSelectedDistrictId();
                    if (!districtId || !this.selectedCourier) return;

                    this.loadingShipping = true;
                    try {
                        const token = document.querySelector('meta[name="csrf-token"]').content;
                        const res = await fetch('{{ route('customer.api.rajaongkir.cost') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token
                            },
                            body: JSON.stringify({
                                origin: {{ config('services.rajaongkir.origin_district_id', 261) }},
                                destination: districtId,
                                weight: this.totalWeight > 0 ? this.totalWeight : 1000,
                                courier: this.selectedCourier
                            })
                        });

                        const data = await res.json();

                        if (data.success && Array.isArray(data.data)) {
                            this.shippingServices = data.data;
                            if (this.shippingServices.length === 0) {
                                this.shippingError = 'Tidak ada layanan pengiriman yang tersedia untuk rute ini.';
                            }
                        } else {
                            this.shippingError = 'Gagal mendapatkan opsi ongkos kirim.';
                        }
                    } catch (e) {
                        console.error(e);
                        this.shippingError = 'Terjadi gangguan koneksi internet.';
                    } finally {
                        this.loadingShipping = false;
                    }
                },

                selectService(idx) {
                    this.selectedServiceIdx = idx;
                    if (this.shippingServices[idx] && typeof this.shippingServices[idx].cost !== 'undefined') {
                        this.shippingCost = this.shippingServices[idx].cost;
                    } else {
                        this.shippingCost = 0;
                    }
                },

                submitForm(e) {
                    e.preventDefault();
                    this.showValidationAlert = false;
                    this.validationMessage = '';

                    // VALIDASI 1: Alamat pengiriman harus dipilih
                    if (!this.selectedAddress) {
                        this.showValidationAlert = true;
                        this.validationMessage = 'Silakan pilih alamat pengiriman.';
                        this.scrollToAlert();
                        return;
                    }

                    // VALIDASI 2: Kurir harus dipilih
                    if (!this.selectedCourier) {
                        this.showValidationAlert = true;
                        this.validationMessage = 'Silakan pilih kurir pengiriman.';
                        this.scrollToAlert();
                        return;
                    }

                    // VALIDASI 3: CRITICAL - Tunggu sampai shipping services sudah di-load
                    // INI ADALAH FIX UNTUK RACE CONDITION BUG!
                    if (this.loadingShipping) {
                        this.showValidationAlert = true;
                        this.validationMessage = 'Mohon tunggu, sedang menghitung ongkos kirim...';
                        this.scrollToAlert();
                        return;
                    }

                    // VALIDASI 4: Harus ada shipping services yang tersedia
                    if (this.shippingServices.length === 0) {
                        this.showValidationAlert = true;
                        this.validationMessage = 'Tidak ada layanan pengiriman yang tersedia. Silakan pilih kurir lain.';
                        this.scrollToAlert();
                        return;
                    }

                    // VALIDASI 5: Layanan pengiriman harus dipilih
                    if (this.selectedServiceIdx === null || this.selectedServiceIdx === '') {
                        this.showValidationAlert = true;
                        this.validationMessage = 'Silakan pilih layanan pengiriman.';
                        this.scrollToAlert();
                        return;
                    }

                    // VALIDASI 6: Metode pembayaran harus dipilih
                    if (!this.paymentMethod) {
                        this.showValidationAlert = true;
                        this.validationMessage = 'Silakan pilih metode pembayaran.';
                        this.scrollToAlert();
                        return;
                    }

                    // SEMUA VALIDASI BERHASIL - LANJUTKAN SUBMIT
                    this.isSubmitting = true;
                    e.target.submit();
                },

                scrollToAlert() {
                    // Scroll ke validation alert untuk user lihat error
                    this.$nextTick(() => {
                        const alertEl = this.$el.querySelector(
                            '[x-show="showValidationAlert && validationMessage"]');
                        if (alertEl) {
                            alertEl.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                        }
                    });
                }
            }
        }
    </script>
@endsection
