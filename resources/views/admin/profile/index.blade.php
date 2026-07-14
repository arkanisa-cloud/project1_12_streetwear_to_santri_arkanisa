@extends('layouts.admin')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-6 sm:py-12" x-data="{ isEditing: false }" x-cloak>

        {{-- Header Hub --}}
        <div class="mb-8 sm:mb-12 border-b border-zinc-100 pb-6 sm:pb-8">
            <span class="text-[9px] font-black uppercase text-zinc-400 tracking-[0.3em] block mb-2">
                STS Admin / Security Hub
            </span>
            <h1 class="text-3xl sm:text-4xl font-black italic tracking-tighter uppercase text-zinc-950">
                Admin / <span class="text-zinc-400">Profile Settings</span>
            </h1>
        </div>

        {{-- Master Responsive Workspace --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">

            {{-- KOLOM NAVIGASI (4/12): Desktop Only (lg:block), Hidden on Mobile --}}
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
                                @if ($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-3xl font-black uppercase italic tracking-tighter text-white/20">
                                        {{ $user->name ? strtoupper(substr($user->name, 0, 2)) : 'ST' }}
                                    </span>
                                @endif
                            </div>
                            <div class="absolute -bottom-2 -right-2 bg-rose-500 w-6 h-6 rounded-full border-4 border-zinc-950 flex items-center justify-center shadow-lg"
                                title="Status Online">
                                <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                            </div>
                        </div>

                        <h3 class="text-xl font-black uppercase tracking-tight italic text-white mb-1">{{ $user->name }}
                        </h3>
                        <div
                            class="inline-flex items-center gap-2 px-3 py-1 bg-white/5 rounded-full border border-white/10">
                            <span class="w-1 h-1 bg-zinc-500 rounded-full"></span>
                            <span class="text-[8px] font-black uppercase tracking-[0.2em] text-zinc-400">
                                Administrator System
                            </span>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-white/5 grid grid-cols-2 gap-4 relative z-10">
                        <div class="text-center">
                            <p class="text-[10px] font-black uppercase text-zinc-500 tracking-widest mb-1">Peran</p>
                            <p class="text-[10px] font-black uppercase italic text-white">Full Access</p>
                        </div>
                        <div class="text-center border-l border-white/5">
                            <p class="text-[10px] font-black uppercase text-zinc-500 tracking-widest mb-1">Status</p>
                            <p class="text-[10px] font-black uppercase italic text-emerald-400">Aktif</p>
                        </div>
                    </div>
                </div>

                {{-- Dashboard Navigation Hub --}}
                <div class="bg-white border border-zinc-100 rounded-[2rem] p-3 shadow-sm space-y-1">
                    <span class="text-[8px] font-black uppercase tracking-[0.2em] text-zinc-400 block px-5 pt-4 pb-2">
                        Navigasi Admin
                    </span>

                    <a href="{{ route('admin.dashboard') }}"
                        class="flex items-center justify-between px-5 py-4 rounded-2xl bg-white text-zinc-600 hover:text-zinc-950 hover:bg-zinc-50 text-[10px] font-bold uppercase tracking-wider transition-all group">
                        <span class="flex items-center gap-3">
                            <span>Main Dashboard</span>
                        </span>
                        <span class="opacity-0 group-hover:opacity-100 transition-opacity">→</span>
                    </a>

                    <a href="{{ route('admin.profile.index') }}"
                        class="flex items-center justify-between px-5 py-4 rounded-2xl bg-zinc-950 text-white text-[10px] font-black uppercase tracking-wider italic transition-all group">
                        <span class="flex items-center gap-3">
                            <span>Admin Profile</span>
                        </span>
                        <span class="opacity-0 group-hover:opacity-100 transition-opacity">→</span>
                    </a>
                </div>

            </div>

            {{-- KOLOM KONTEN (8/12) --}}
            <div class="lg:col-span-8">

                {{-- DISPLAY MODE: READ-ONLY VAULT --}}
                <div class="bg-white border border-zinc-100 rounded-3xl p-6 sm:p-8 shadow-sm relative" x-show="!isEditing"
                    x-transition:enter="transition ease-out duration-200">

                    <button type="button" @click="isEditing = true"
                        class="absolute top-6 right-6 p-2.5 bg-zinc-50 hover:bg-zinc-950 text-zinc-500 hover:text-white rounded-xl border border-zinc-200/60 transition-all group shadow-sm active:scale-95"
                        title="Edit Profile">
                        <span
                            class="text-[10px] font-black uppercase tracking-wider px-1 hidden sm:inline group-hover:inline-block">Edit</span>
                        <span class="text-xs">✎</span>
                    </button>

                    <h2
                        class="text-xs font-black uppercase tracking-[0.2em] text-zinc-950 flex items-center gap-3 border-b border-zinc-50 pb-4 mb-6">
                        <span class="w-1.5 h-3 bg-zinc-950 block"></span> Admin Identity Details
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                        <div class="space-y-1">
                            <span class="text-[9px] font-black uppercase tracking-wider text-zinc-400 block">Nama
                                Lengkap</span>
                            <p class="text-sm font-black text-zinc-950 uppercase tracking-tight">{{ $user->name }}</p>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[9px] font-black uppercase tracking-wider text-zinc-400 block">Alamat
                                Email</span>
                            <p class="text-sm font-mono font-bold text-zinc-800 break-all">{{ $user->email }}</p>
                        </div>
                    </div>
                </div>

                {{-- INTERACTIVE MODE: EDIT FORM --}}
                <div class="bg-white border border-zinc-950 rounded-3xl p-6 sm:p-8 shadow-lg space-y-6" x-show="isEditing"
                    x-transition:enter="transition ease-out duration-200">

                    <div class="flex items-center justify-between border-b border-zinc-100 pb-4 mb-2">
                        <h2 class="text-xs font-black uppercase tracking-[0.2em] text-zinc-950 flex items-center gap-3">
                            <span class="w-1.5 h-3 bg-zinc-950 block"></span> Form Update Admin
                        </h2>
                        <button type="button" @click="isEditing = false"
                            class="text-[10px] font-black uppercase tracking-widest text-zinc-400 hover:text-zinc-950 transition-colors">
                            Batal [✕]
                        </button>
                    </div>

                    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-6">
                        @csrf
                        @method('PUT')

                        {{-- Avatar Upload Field --}}
                        <div class="space-y-2 pb-6 border-b border-zinc-50">
                            <label class="text-[9px] font-black uppercase tracking-widest text-zinc-400 block">Avatar
                                Profil Admin</label>
                            <div class="flex items-center gap-6">
                                <div class="relative group">
                                    @if ($user->avatar)
                                        <img src="{{ asset('storage/' . $user->avatar) }}"
                                            class="w-20 h-20 rounded-2xl object-cover border-2 border-zinc-100 shadow-md">
                                    @else
                                        <div
                                            class="w-20 h-20 bg-zinc-50 rounded-2xl border-2 border-zinc-100 flex items-center justify-center shadow-sm">
                                            <span class="text-[10px] font-black text-zinc-300 uppercase">Empty</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <input type="file" name="avatar"
                                        class="w-full text-[10px] text-zinc-400 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-[9px] file:font-black file:uppercase file:tracking-widest file:bg-zinc-950 file:text-white hover:file:bg-zinc-800 file:transition-all">
                                    <p class="text-[8px] text-zinc-400 mt-2 font-bold uppercase tracking-widest italic">
                                        Format: JPG, PNG, WEBP (Max 2MB)</p>
                                </div>
                            </div>
                            @error('avatar')
                                <span class="text-[10px] font-bold text-rose-600 block mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[9px] font-black uppercase tracking-widest text-zinc-400 block">Nama
                                    Lengkap</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                    class="w-full p-4 bg-zinc-50 border border-zinc-100 rounded-2xl text-xs font-black uppercase tracking-tight focus:bg-white focus:border-zinc-950 focus:ring-0 text-zinc-950 transition-all">
                                @error('name')
                                    <span class="text-[10px] font-bold text-rose-600 block mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-[9px] font-black uppercase tracking-widest text-zinc-400 block">Alamat
                                    Email</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                    class="w-full p-4 bg-zinc-50 border border-zinc-100 rounded-2xl text-xs font-mono font-bold focus:bg-white focus:border-zinc-950 focus:ring-0 text-zinc-800 transition-all">
                                @error('email')
                                    <span class="text-[10px] font-bold text-rose-600 block mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Security --}}
                        <div class="pt-6 border-t border-zinc-50 space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div class="space-y-2 sm:col-span-2">
                                    <label
                                        class="text-[9px] font-black uppercase tracking-widest text-zinc-400 block">Password
                                        Saat Ini</label>
                                    <input type="password" name="current_password" placeholder="KONFIRMASI PASSWORD"
                                        class="w-full p-4 bg-zinc-50 border border-zinc-100 rounded-2xl text-xs font-mono focus:bg-white focus:border-zinc-950 focus:ring-0 text-zinc-950 transition-all">
                                    @error('current_password')
                                        <span
                                            class="text-[10px] font-bold text-rose-600 block mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label
                                        class="text-[9px] font-black uppercase tracking-widest text-zinc-400 block">Password
                                        Baru</label>
                                    <input type="password" name="password" placeholder="••••••••"
                                        class="w-full p-4 bg-zinc-50 border border-zinc-100 rounded-2xl text-xs font-mono focus:bg-white focus:border-zinc-950 focus:ring-0 text-zinc-950 transition-all">
                                    @error('password')
                                        <span
                                            class="text-[10px] font-bold text-rose-600 block mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label
                                        class="text-[9px] font-black uppercase tracking-widest text-zinc-400 block">Konfirmasi
                                        Password</label>
                                    <input type="password" name="password_confirmation" placeholder="••••••••"
                                        class="w-full p-4 bg-zinc-50 border border-zinc-100 rounded-2xl text-xs font-mono focus:bg-white focus:border-zinc-950 focus:ring-0 text-zinc-950 transition-all">
                                </div>
                            </div>
                        </div>

                        {{-- Controls --}}
                        <div class="pt-6 border-t border-zinc-100 flex justify-end gap-3">
                            <button type="button" @click="isEditing = false"
                                class="px-5 py-3 border border-zinc-200 rounded-xl text-[10px] font-black uppercase tracking-widest text-zinc-400 hover:bg-zinc-50 transition-all">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-6 py-3 bg-zinc-950 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-zinc-900 transition-all shadow-md active:scale-95">
                                Simpan Perubahan 🗸
                            </button>
                        </div>
                    </form>
                </div>

            </div>

        </div>
    </div>
@endsection
