@extends('layouts.admin')

@section('content')
    <div class="space-y-8">
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black tracking-tight text-zinc-950 uppercase italic flex items-center gap-3">
                    Edit Website
                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                </h1>
                <p class="text-xs text-zinc-500 font-medium tracking-wide uppercase mt-1">Kelola tampilan logo & hero
                    section website kamu.</p>
            </div>
        </div>

        <form action="{{ route('admin.site-settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')

            {{-- Logo Section --}}
            <div class="bg-white border border-zinc-100 rounded-[2rem] p-8 md:p-10 shadow-sm space-y-6">
                <div class="flex items-center gap-4 pb-6 border-b border-zinc-100">
                    <div
                        class="w-12 h-12 rounded-2xl bg-zinc-950 flex items-center justify-center text-white flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.764m3.42 3.42a6.776 6.776 0 00-3.42-3.42" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-black uppercase tracking-tight text-zinc-950">Logo Website</h2>
                        <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Ganti logo Streetwear to
                            Santri!</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
                    {{-- Current Logo Preview --}}
                    <div class="space-y-4">
                        <label class="text-[10px] font-black uppercase tracking-widest text-zinc-400 block">Logo Saat
                            Ini</label>
                        <div class="bg-zinc-50 rounded-2xl p-8 flex items-center justify-center min-h-[160px] border border-zinc-100"
                            id="logo-preview-container">
                            @if ($siteLogo)
                                <img src="{{ asset('storage/' . $siteLogo) }}" alt="Current Logo"
                                    class="max-h-20 max-w-full object-contain" id="logo-preview-img">
                            @else
                                <div class="text-center" id="logo-preview-placeholder">
                                    <span class="text-3xl font-black italic tracking-tighter text-zinc-950">STS<span
                                            class="text-zinc-300">.</span></span>
                                    <p class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest mt-2">Logo
                                        Default (Text)</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Upload New Logo --}}
                    <div class="space-y-4">
                        <label class="text-[10px] font-black uppercase tracking-widest text-zinc-400 block">Upload Logo
                            Baru</label>
                        <div class="relative" x-data="{ logoFileName: '' }">
                            <label for="site_logo"
                                class="flex flex-col items-center justify-center min-h-[160px] border-2 border-dashed border-zinc-200 rounded-2xl cursor-pointer hover:border-zinc-950 hover:bg-zinc-50 transition-all duration-300 group">
                                <div class="text-center p-6">
                                    <svg class="w-8 h-8 mx-auto text-zinc-300 group-hover:text-zinc-950 transition-colors mb-3"
                                        fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                    </svg>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400 group-hover:text-zinc-950 transition-colors"
                                        x-text="logoFileName || 'Klik untuk pilih file'"></p>
                                    <p class="text-[9px] font-bold text-zinc-300 uppercase tracking-widest mt-1">PNG, JPG,
                                        SVG, WEBP • Max 2MB</p>
                                </div>
                            </label>
                            <input id="site_logo" name="site_logo" type="file" accept="image/*" class="hidden"
                                @change="logoFileName = $event.target.files[0]?.name || ''; previewLogo($event)">
                        </div>

                        {{-- New Logo Preview --}}
                        <div id="new-logo-preview"
                            class="hidden bg-emerald-50 border border-emerald-200 rounded-2xl p-6 text-center">
                            <p class="text-[9px] font-black uppercase tracking-widest text-emerald-600 mb-3">Preview Logo
                                Baru</p>
                            <img id="new-logo-img" class="max-h-16 max-w-full object-contain mx-auto">
                        </div>

                        @error('site_logo')
                            <p class="text-[10px] font-bold text-red-500 uppercase tracking-wider">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Hero Section --}}
            <div class="bg-white border border-zinc-100 rounded-[2rem] p-8 md:p-10 shadow-sm space-y-6">
                <div class="flex items-center gap-4 pb-6 border-b border-zinc-100">
                    <div
                        class="w-12 h-12 rounded-2xl bg-zinc-950 flex items-center justify-center text-white flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5a2.25 2.25 0 002.25-2.25V6.75a2.25 2.25 0 00-2.25-2.25H3.75A2.25 2.25 0 001.5 6.75v11.25c0 1.242 1.008 2.25 2.25 2.25z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-black uppercase tracking-tight text-zinc-950">Hero Section</h2>
                        <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Ganti foto utama yang
                            tampil di halaman beranda</p>
                    </div>
                </div>

                <div class="space-y-6">
                    {{-- Current Hero Preview --}}
                    <div class="space-y-4">
                        <label class="text-[10px] font-black uppercase tracking-widest text-zinc-400 block">Hero
                            Image Saat Ini</label>
                        <div class="relative rounded-2xl overflow-hidden shadow-lg border border-zinc-100">
                            <div class="aspect-[21/9] bg-zinc-100">
                                @if ($heroImage)
                                    <img src="{{ asset('storage/' . $heroImage) }}" alt="Current Hero"
                                        class="w-full h-full object-cover" id="hero-preview-img">
                                @else
                                    <img src="{{ asset('images/hero.png') }}" alt="Default Hero"
                                        class="w-full h-full object-cover" id="hero-preview-img">
                                @endif
                            </div>
                            <div
                                class="absolute bottom-4 left-4 bg-zinc-950/80 backdrop-blur-sm rounded-xl px-4 py-2 text-[9px] font-black uppercase tracking-widest text-white">
                                @if ($heroImage)
                                    Custom Hero Image
                                @else
                                    Default Hero Image
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Upload New Hero --}}
                    <div class="space-y-4">
                        <label class="text-[10px] font-black uppercase tracking-widest text-zinc-400 block">Upload
                            Hero Image Baru</label>
                        <div class="relative" x-data="{ heroFileName: '' }">
                            <label for="hero_image"
                                class="flex flex-col items-center justify-center min-h-[200px] border-2 border-dashed border-zinc-200 rounded-2xl cursor-pointer hover:border-zinc-950 hover:bg-zinc-50 transition-all duration-300 group">
                                <div class="text-center p-6">
                                    <svg class="w-10 h-10 mx-auto text-zinc-300 group-hover:text-zinc-950 transition-colors mb-3"
                                        fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                    </svg>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400 group-hover:text-zinc-950 transition-colors"
                                        x-text="heroFileName || 'Klik untuk pilih file hero'"></p>
                                    <p class="text-[9px] font-bold text-zinc-300 uppercase tracking-widest mt-1">PNG, JPG,
                                        WEBP • Max 5MB • Rekomendasi: 1920x1080px</p>
                                </div>
                            </label>
                            <input id="hero_image" name="hero_image" type="file" accept="image/*" class="hidden"
                                @change="heroFileName = $event.target.files[0]?.name || ''; previewHero($event)">
                        </div>

                        {{-- New Hero Preview --}}
                        <div id="new-hero-preview"
                            class="hidden bg-emerald-50 border border-emerald-200 rounded-2xl overflow-hidden">
                            <p class="text-[9px] font-black uppercase tracking-widest text-emerald-600 px-6 pt-4 pb-2">
                                Preview Hero Baru</p>
                            <div class="aspect-[21/9]">
                                <img id="new-hero-img" class="w-full h-full object-cover">
                            </div>
                        </div>

                        @error('hero_image')
                            <p class="text-[10px] font-bold text-red-500 uppercase tracking-wider">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="flex justify-end">
                <button type="submit"
                    class="px-10 py-4 bg-zinc-950 text-white text-[10px] font-black uppercase tracking-[0.3em] rounded-2xl shadow-xl hover:bg-zinc-800 hover:-translate-y-0.5 transition-all duration-300 flex items-center gap-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <script>
        function previewLogo(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('new-logo-preview').classList.remove('hidden');
                    document.getElementById('new-logo-img').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        }

        function previewHero(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('new-hero-preview').classList.remove('hidden');
                    document.getElementById('new-hero-img').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        }
    </script>
@endsection
