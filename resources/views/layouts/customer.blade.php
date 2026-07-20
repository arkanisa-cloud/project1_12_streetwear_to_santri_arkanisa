<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'STS') — Streetwear to Santri</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.bunny.net/css?family=figtree:400,900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="Mid-client-bGLLbrzTBW50U3lL"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-white text-zinc-950 antialiased font-sans">

    {{-- Navbar Customer --}}
    <nav x-data="{ mobileMenuOpen: false, profileMenuOpen: false }"
        class="fixed top-0 w-full z-[50] bg-white/80 backdrop-blur-md border-b border-zinc-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-20 flex items-center justify-between relative">

            {{-- Sisi Kiri: Tombol Kembali ke Beranda --}}
            <div class="flex items-center md:ml-4 z-10">
                <a href="{{ route('home') }}"
                    class="group flex items-center gap-2 p-2.5 md:px-5 md:py-2 border-2 border-zinc-950 rounded-full hover:bg-zinc-950 hover:text-white transition-all duration-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span class="text-[9px] font-black uppercase tracking-widest hidden md:inline">Beranda</span>
                </a>
            </div>

            {{-- Tengah: Logo --}}
            <div class="absolute left-1/2 -translate-x-1/2 md:static md:translate-x-0">
                @if (!empty($siteLogo))
                    <img src="{{ asset('storage/' . $siteLogo) }}" alt="Logo"
                        class="h-11 md:h-14 w-auto object-contain">
                @else
                    <span class="text-2xl font-black italic tracking-tighter uppercase">STS<span
                            class="text-zinc-300">.</span></span>
                @endif
            </div>

            {{-- Sisi Kanan: Cart & Profil --}}
            <div class="flex items-center gap-2 sm:gap-3 z-10">

                {{-- Ikon Keranjang Belanja --}}
                <a href="{{ route('customer.cart.index') }}"
                    class="p-2.5 border-2 border-zinc-100 rounded-full hover:border-zinc-950 hover:scale-105 transition-all relative group
                    {{ request()->routeIs('customer.cart.index') ? 'bg-zinc-950 text-white border-zinc-950' : 'bg-white text-zinc-950' }}">

                    <svg class="w-4 h-4 transition-transform group-hover:-rotate-3" fill="none" stroke="currentColor"
                        stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                    </svg>

                    @auth
                        @php
                            $cartCount = auth()->user()->cart?->cartItems->count() ?? 0;
                        @endphp
                        @if ($cartCount > 0)
                            <span
                                class="absolute -top-1 -right-1 text-white text-[8px] font-black w-4 h-4 flex items-center justify-center rounded-full border shadow-sm animate-pulse
                                {{ request()->routeIs('customer.cart.index') ? 'bg-rose-600 border-zinc-950' : 'bg-zinc-950 border-white' }}">
                                {{ $cartCount }}
                            </span>
                        @endif
                    @endauth
                </a>

                {{-- Profil / Menu Dropdown --}}
                @auth
                    <div class="relative">
                        <button @click="profileMenuOpen = !profileMenuOpen" @click.away="profileMenuOpen = false"
                            class="flex items-center gap-2 {{ auth()->user()->avatar ? 'p-1' : 'p-2.5' }} border-2 border-zinc-100 rounded-full hover:border-zinc-950 transition-all overflow-hidden">
                            @if (auth()->user()->avatar)
                                <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                                    class="w-7 h-7 rounded-full object-cover">
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            @endif
                        </button>
                        <div x-show="profileMenuOpen" x-transition.opacity
                            class="absolute right-0 mt-2 w-48 bg-white border border-zinc-100 rounded-2xl shadow-xl py-2"
                            x-cloak>
                            <a href="{{ route('customer.profile.index') }}"
                                class="block px-4 py-2 text-xs font-bold text-zinc-950 hover:bg-zinc-50">Profil Saya</a>
                            <a href="{{ route('customer.orders.index') }}"
                                class="block px-4 py-2 text-xs font-bold text-zinc-950 hover:bg-zinc-50">Pesanan Saya</a>
                            <a href="{{ route('customer.addresses.index') }}"
                                class="block px-4 py-2 text-xs font-bold text-zinc-950 hover:bg-zinc-50">Alamat
                                Pengiriman</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left block px-4 py-2 text-xs font-bold text-red-600 hover:bg-red-50">Keluar</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}"
                        class="px-3 py-1.5 sm:px-5 sm:py-2 border-2 border-zinc-950 rounded-full text-[8px] sm:text-[9px] font-black uppercase tracking-widest hover:bg-zinc-950 hover:text-white transition-all duration-300">
                        Masuk
                    </a>
                @endauth

            </div>
        </div>
    </nav>

    <main class="pt-20">
        @yield('content')
    </main>



    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000",
        }
        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif
        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                toastr.error("{{ $error }}");
            @endforeach
        @endif
    </script>
</body>

</html>
