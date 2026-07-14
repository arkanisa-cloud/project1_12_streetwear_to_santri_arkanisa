@extends('layouts.customer')

@section('title', 'Shipping Addresses')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-6 sm:py-12">

        {{-- Header Hub --}}
        <div class="mb-10 border-b border-zinc-100 pb-6 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-6">
            <div>
                <span class="text-[9px] font-black uppercase text-zinc-400 tracking-[0.3em] block mb-2">
                    STS Operational / Client Dashboard
                </span>
                <h1 class="text-3xl sm:text-4xl font-black italic tracking-tighter uppercase text-zinc-950">
                    Vault / <span class="text-zinc-400">Addresses</span>
                </h1>
            </div>
            <a href="{{ route('customer.addresses.create') }}"
                class="inline-flex items-center gap-2 px-6 py-3 bg-zinc-950 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-zinc-800 transition-all shadow-md active:scale-95 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Address
            </a>
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
                        class="flex items-center justify-between px-5 py-4 rounded-2xl bg-white text-zinc-600 hover:text-zinc-950 hover:bg-zinc-50 text-[10px] font-bold uppercase tracking-wider transition-all group">
                        <span class="flex items-center gap-3">
                            <span>Pesanan Saya</span>
                        </span>
                        <span
                            class="text-[9px] bg-white/20 px-2 py-0.5 rounded font-mono font-bold">{{ auth()->user()->orders->count() }}</span>
                    </a>

                    <a href="{{ route('customer.addresses.index') }}"
                        class="flex items-center justify-between px-5 py-4 rounded-2xl bg-zinc-950 text-white text-[10px] font-black uppercase tracking-wider italic transition-all group">
                        <span class="flex items-center gap-3">
                            <span>Alamat Saya</span>
                        </span>
                        <span class="opacity-0 group-hover:opacity-100 transition-opacity">&rarr;</span>
                    </a>
                </div>
            </div>

            {{-- KOLOM KONTEN (8/12) --}}
            <div class="lg:col-span-8">
                @if ($addresses->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach ($addresses as $address)
                            <div
                                class="bg-white border border-zinc-100 rounded-3xl p-6 transition-all duration-300 hover:border-zinc-950 hover:shadow-xl hover:shadow-zinc-200/50 group relative flex flex-col">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h4 class="text-sm font-black uppercase tracking-tight text-zinc-950 pr-4">
                                            {{ $address->recipient_name }}
                                        </h4>
                                        <p
                                            class="text-[10px] font-bold uppercase tracking-widest text-zinc-400 mt-1 flex items-center gap-1.5">
                                            <span class="grayscale">📱</span> {{ $address->phone }}
                                        </p>
                                    </div>
                                    <div x-data="{ open: false }" class="relative">
                                        <button @click="open = !open" @click.away="open = false"
                                            class="text-zinc-300 hover:text-zinc-950 transition-colors p-1">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                    d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z">
                                                </path>
                                            </svg>
                                        </button>
                                        <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="transform opacity-0 scale-95"
                                            x-transition:enter-end="transform opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="transform opacity-100 scale-100"
                                            x-transition:leave-end="transform opacity-0 scale-95"
                                            class="absolute right-0 mt-2 w-48 bg-white border border-zinc-100 rounded-2xl shadow-xl z-10 overflow-hidden py-1">
                                            <a href="{{ route('customer.addresses.edit', $address) }}"
                                                class="block px-4 py-3 text-[10px] font-black uppercase tracking-widest text-zinc-950 hover:bg-zinc-50 transition-colors">
                                                Edit Address
                                            </a>
                                            <form action="{{ route('customer.addresses.destroy', $address) }}"
                                                method="POST" onsubmit="return confirm('Are you sure?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="w-full text-left px-4 py-3 text-[10px] font-black uppercase tracking-widest text-rose-600 hover:bg-rose-50 transition-colors">
                                                    Delete Address
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex-1">
                                    <p
                                        class="text-[11px] text-zinc-500 leading-relaxed font-bold uppercase tracking-tight mb-4">
                                        {{ $address->address }}<br>
                                        <span class="text-zinc-950">
                                            @if ($address->subdistrict)
                                                {{ $address->subdistrict }},
                                            @endif {{ $address->city }}, {{ $address->province }}<br>
                                            {{ $address->postal_code }}
                                        </span>
                                    </p>
                                </div>

                                @if ($address->orders()->whereIn('status', ['pending', 'processed', 'shipped'])->count() > 0)
                                    <div class="mt-4 pt-4 border-t border-zinc-50">
                                        <p
                                            class="text-[9px] font-black uppercase tracking-widest text-zinc-400 flex items-center gap-1.5 italic">
                                            <span class="w-1 h-1 bg-amber-500 rounded-full animate-ping"></span>
                                            Active Destination
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-20 border-2 border-dashed border-zinc-200 rounded-3xl bg-zinc-50/50">
                        <svg class="w-16 h-16 mx-auto text-zinc-300 mb-6" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <h3 class="text-sm font-black uppercase tracking-tight text-zinc-950 italic">No Addresses Found
                        </h3>
                        <p class="text-[11px] font-bold text-zinc-400 uppercase tracking-widest mt-1">Sistem kami belum
                            merekam manifest alamat pengiriman anda.</p>
                        <div class="mt-8">
                            <a href="{{ route('customer.addresses.create') }}"
                                class="inline-flex items-center gap-2 px-8 py-4 bg-zinc-950 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-zinc-800 transition-all shadow-md active:scale-95">
                                Add First Address →
                            </a>
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>
@endsection
