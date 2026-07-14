@php
    $isHome = request()->routeIs('home');
@endphp

<nav x-data="{
    mobileMenuOpen: false,
    profileMenuOpen: false,
    isScrolled: false,
    isHome: {{ $isHome ? 'true' : 'false' }}
}"
    @scroll.window="isScrolled = (window.pageYOffset > (document.getElementById('home')?.clientHeight - 80 || 20))"
    :class="{
        'bg-white/95 backdrop-blur-md border-zinc-100 shadow-sm': !isHome || isScrolled || mobileMenuOpen,
        'bg-transparent border-transparent': isHome && !isScrolled && !mobileMenuOpen
    }"
    class="fixed top-0 w-full z-[100] border-b transition-all duration-500">
    <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between relative">

        {{-- Left Side: Hamburger (Mobile Only) & Logo (Desktop) --}}
        <div class="flex items-center gap-4">
            {{-- Hamburger Button --}}
            <button @click="mobileMenuOpen = !mobileMenuOpen"
                :class="(!isHome || isScrolled || mobileMenuOpen) ? 'text-zinc-950' : 'text-white'"
                class="md:hidden p-2 -ml-2 focus:outline-none z-[110] transition-colors duration-500">
                <svg class="w-6 h-6 transition-transform duration-300" :class="{ 'rotate-90': mobileMenuOpen }"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 8h16M4 16h10"></path>
                    <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            {{-- Logo: Center on Mobile, Left on Desktop --}}
            <a href="{{ route('home') }}"
                :class="(!isHome || isScrolled || mobileMenuOpen) ? 'text-zinc-950' : 'text-white'"
                class="absolute left-1/2 -translate-x-1/2 md:static md:translate-x-0 md:ml-4 transition-colors duration-500 hover:opacity-70">
                @if (!empty($siteLogo))
                    <img src="{{ asset('storage/' . $siteLogo) }}" alt="Logo" class="h-11 md:h-14 w-auto object-contain">
                @else
                    <span class="text-2xl font-black italic tracking-tighter uppercase">STS<span
                            :class="(!isHome || isScrolled || mobileMenuOpen) ? 'text-zinc-300' : 'text-zinc-400'">.</span></span>
                @endif
            </a>
        </div>

        {{-- Center: Desktop Navigation --}}
        <div class="hidden md:flex items-center gap-10 text-[10px] font-black uppercase tracking-[0.3em]">
            <a href="/#home" data-section="home" class="nav-link">Beranda</a>
            <a href="/#products" data-section="products" class="nav-link">Produk</a>
            <a href="/#superiority" data-section="superiority" class="nav-link">Keunggulan</a>
            <a href="/#contact" data-section="contact" class="nav-link">Kontak</a>
        </div>

        {{-- Right Side: Action Buttons --}}
        <div class="flex items-center gap-3 sm:gap-4">

            {{-- 1. Ikon Keranjang Belanja --}}
            <a href="{{ route('customer.cart.index') }}"
                :class="(!isHome || isScrolled || mobileMenuOpen) ?
                'border-zinc-100 text-zinc-950 bg-white hover:border-zinc-950' :
                'border-white/10 text-white bg-white/5 backdrop-blur-sm hover:bg-white hover:text-zinc-950 hover:border-white'"
                class="p-2.5 border-2 rounded-full hover:scale-105 transition-all duration-300 relative group">

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
                            class="absolute -top-1 -right-1 bg-zinc-950 text-white text-[8px] font-black w-4 h-4 flex items-center justify-center rounded-full border border-white shadow-sm animate-pulse">
                            {{ $cartCount }}
                        </span>
                    @endif
                @endauth
            </a>

            {{-- 2. Auth Profile Button --}}
            @auth
                <div class="relative">
                    <button @click="profileMenuOpen = !profileMenuOpen" @click.away="profileMenuOpen = false"
                        :class="(!isHome || isScrolled || mobileMenuOpen) ? 'border-zinc-100 hover:border-zinc-950' :
                        'border-white/10 hover:border-white text-white'"
                        class="flex items-center gap-2 {{ auth()->user()->avatar ? 'p-1' : 'p-2.5' }} border-2 rounded-full transition-all duration-300 overflow-hidden">
                        @if (auth()->user()->avatar)
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" class="w-7 h-7 rounded-full object-cover">
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
                    :class="(!isHome || isScrolled || mobileMenuOpen) ?
                    'border-zinc-950 hover:bg-zinc-950 hover:text-white text-zinc-950' :
                    'border-white hover:bg-white hover:text-zinc-950 text-white'"
                    class="px-3.5 py-1.5 sm:px-5 sm:py-2 border-2 rounded-full text-[8px] sm:text-[9px] font-black uppercase tracking-widest transition-all duration-300">
                    Masuk
                </a>
            @endauth
        </div>

        {{-- Mobile Simple Dropdown --}}
        <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            class="absolute top-20 left-0 w-full bg-white border-b border-zinc-100 shadow-xl md:hidden px-8 py-8 space-y-6"
            x-cloak @click.away="mobileMenuOpen = false">

            <nav class="flex flex-col gap-6">
                <a href="/#home" @click="mobileMenuOpen = false" data-section="home"
                    class="mobile-nav-link text-sm font-black uppercase tracking-widest text-zinc-900">Beranda</a>
                <a href="/#products" @click="mobileMenuOpen = false" data-section="products"
                    class="mobile-nav-link text-sm font-black uppercase tracking-widest text-zinc-900">Produk</a>
                <a href="/#superiority" @click="mobileMenuOpen = false" data-section="superiority"
                    class="mobile-nav-link text-sm font-black uppercase tracking-widest text-zinc-900">Keunggulan</a>
                <a href="/#contact" @click="mobileMenuOpen = false" data-section="contact"
                    class="mobile-nav-link text-sm font-black uppercase tracking-widest text-zinc-900">Kontak</a>
                
                @guest
                    <div class="pt-4 border-t border-zinc-100">
                        <a href="{{ route('login') }}"
                            class="block w-full text-center py-3 bg-zinc-950 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-zinc-800 transition-colors">
                            Masuk ke Akun
                        </a>
                    </div>
                @endguest
            </nav>
        </div>
    </div>
</nav>

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
</script>
