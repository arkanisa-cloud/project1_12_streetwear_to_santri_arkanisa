@extends('layouts.app')

@section('content')
    {{-- Hero Section: Rasio 4:5 di Mobile, 200vh di Desktop --}}
    <section id="home"
        class="relative w-full aspect-[4/5] md:aspect-auto md:h-[200vh] bg-zinc-950 select-none overflow-hidden">

        {{-- Background Image Container --}}
        <div class="absolute inset-0 z-0 pointer-events-none w-full h-full flex items-center justify-center">

            {{-- Gambar akan menyesuaikan container: 
                 - Di mobile (4:5): crop sangat minimal jika foto asli sudah di-crop portrait.
                 - Di desktop (200vh): sisi kiri-kanan akan terkena crop karena memaksa menutupi area vertikal 200vh. --}}
            <img src="{{ !empty($heroImage) ? asset('storage/' . $heroImage) : asset('images/hero.png') }}"
                class="w-full h-full object-cover object-center animate-fade-in-slow animate-zoom-out">
    </section>

    {{-- Product Section --}}
    <section id="products" class="max-w-7xl mx-auto px-6 py-24 scroll-mt-24">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-black uppercase tracking-tighter">All <span
                    class="text-zinc-300">Collection</span></h2>
            <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mt-4">Koleksi kurasi terbaik yang paling
                diminati saat ini.</p>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-8">
            @foreach ($products as $product)
                <div class="group cursor-pointer" onclick="window.location='{{ route('customer.shop.show', $product) }}'">
                    <div
                        class="relative aspect-[3/4] bg-zinc-100 rounded-2xl overflow-hidden mb-6 shadow-sm group-hover:shadow-xl transition-all duration-500">
                        {{-- Front Image --}}
                        <img src="{{ asset('storage/' . $product->image) }}"
                            class="w-full h-full object-cover transition-all duration-1000 group-hover:scale-110 @if ($product->back_image_url) group-hover:opacity-0 @endif">

                        {{-- Back Image (Optional Hover Swap) --}}
                        @if ($product->back_image)
                            <img src="{{ asset('storage/' . $product->back_image) }}"
                                class="absolute inset-0 w-full h-full object-cover opacity-0 transition-all duration-1000 group-hover:scale-110 group-hover:opacity-100">
                        @endif

                        <div
                            class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                        </div>
                    </div>
                    <h3
                        class="text-xs font-black uppercase text-zinc-950 group-hover:underline underline-offset-4 decoration-2">
                        {{ $product->name }}</h3>
                    <p class="text-[10px] font-bold text-zinc-500 uppercase mt-1 italic">IDR
                        {{ number_format($product->price, 0, ',', '.') }}</p>
                </div>
            @endforeach
        </div>

        <div class="mt-16 text-center px-4">
            <a href="{{ route('customer.shop.index') }}"
                class="inline-block w-auto px-8 py-4 md:px-16 md:py-6 bg-zinc-950 text-white text-[10px] md:text-xs font-black uppercase tracking-[0.3em] md:tracking-[0.4em] rounded-xl md:rounded-2xl shadow-xl md:shadow-2xl hover:bg-zinc-800 hover:-translate-y-1 transition-all duration-300">
                Lihat Semua Produk ➔
            </a>
        </div>
    </section>

    {{-- Superiority Section --}}
    <section id="superiority" class="bg-zinc-950 text-white py-32 scroll-mt-24">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-4xl md:text-6xl font-black uppercase tracking-tighter mb-20 text-center">Standar <span
                    class="text-zinc-700">STS.</span></h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-16">
                <div class="space-y-4 text-center">
                    <span class="text-5xl font-black italic text-zinc-600">01</span>
                    <h3 class="text-xl font-black uppercase">Bahan Premium</h3>
                    <p class="text-xs text-zinc-400 leading-relaxed uppercase tracking-wider">Menggunakan katun
                        heavyweight 20s yang nyaman dipakai sehari hari.</p>
                </div>
                <div class="space-y-4 text-center">
                    <span class="text-5xl font-black italic text-zinc-600">02</span>
                    <h3 class="text-xl font-black uppercase">Sablon Berkualitas</h3>
                    <p class="text-xs text-zinc-400 leading-relaxed uppercase tracking-wider">Dicetak manual dengan tinta
                        plastisol top-tier, memberikan tekstur solid dan tidak mudah pecah.</p>
                </div>
                <div class="space-y-4 text-center">
                    <span class="text-5xl font-black italic text-zinc-600">03</span>
                    <h3 class="text-xl font-black uppercase">Potongan Modern</h3>
                    <p class="text-xs text-zinc-400 leading-relaxed uppercase tracking-wider">Siluet boxy fit yang
                        dirancang
                        khusus menyesuaikan gaya streetwear masa kini.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Contact Section --}}
    <section id="contact" class="max-w-7xl mx-auto px-6 py-32 scroll-mt-24">
        <div class="text-center mb-16">
            <h2 class="text-5xl md:text-6xl font-black uppercase tracking-tighter">Hubungi <span
                    class="text-zinc-300">Kami.</span></h2>
            <p class="text-zinc-500 text-md mt-4s">Ada pertanyaan? Kami siap ngebantu kapan aja!</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <div class="space-y-6">
                <div class="bg-white border border-zinc-100 rounded-[2rem] p-8 shadow-2xl shadow-zinc-200/70 space-y-6">
                    <div class="flex gap-4">
                        <div
                            class="w-12 h-12 rounded-2xl bg-zinc-50 flex items-center justify-center flex-shrink-0 text-zinc-950 border border-zinc-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-[10px] font-black uppercase tracking-widest text-zinc-950 mb-1">Base STS</h4>
                            <p class="text-xs font-bold text-zinc-500 leading-relaxed">Srumbung, Magelang<br>Jawa
                                Tengah</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div
                            class="w-12 h-12 rounded-2xl bg-zinc-50 flex items-center justify-center flex-shrink-0 text-zinc-950 border border-zinc-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-[10px] font-black uppercase tracking-widest text-zinc-950 mb-1">Telepon /
                                WhatsApp</h4>
                            <p class="text-xs font-bold text-zinc-500 leading-relaxed">+62 857 2578 0424</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div
                            class="w-12 h-12 rounded-2xl bg-zinc-50 flex items-center justify-center flex-shrink-0 text-zinc-950 border border-zinc-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-[10px] font-black uppercase tracking-widest text-zinc-950 mb-1">Email Resmi
                            </h4>
                            <p class="text-xs font-bold text-zinc-500 leading-relaxed">support@sts.worldwide</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div
                            class="w-12 h-12 rounded-2xl bg-zinc-50 flex items-center justify-center flex-shrink-0 text-zinc-950 border border-zinc-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-[10px] font-black uppercase tracking-widest text-zinc-950 mb-1">Jam Operasional
                            </h4>
                            <p class="text-xs font-bold text-zinc-500 leading-relaxed">Setiap Hari
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="w-full h-64 md:h-80 bg-zinc-100 rounded-[2rem] overflow-hidden shadow-xl shadow-zinc-200/50 border border-zinc-100 relative group">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3954.218573215286!2d110.31682395!3d-7.59473265!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a895b6a7a13ad%3A0x4027a7658514580!2sSrumbung%2C%20Magelang%20Regency%2C%20Central%20Java!5e0!3m2!1sen!2sid!4v1700000000000!5m2!1sen!2sid"
                        class="w-full h-full border-0 grayscale opacity-90 group-hover:grayscale-0 group-hover:opacity-100 transition-all duration-700"
                        allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>

            <div class="bg-zinc-50 p-8 md:p-12 rounded-[2.5rem] border border-zinc-100 h-fit">
                <h3 class="text-2xl font-black uppercase tracking-tighter mb-8">Kirim <span
                        class="text-zinc-400">Pesan</span></h3>
                <form class="space-y-5">
                    <div>
                        <label class="text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2 block">Nama
                            Lengkap</label>
                        <input type="text" placeholder="Masukkan namamu..."
                            class="w-full p-4 bg-white border-none rounded-2xl text-xs font-bold focus:ring-2 focus:ring-zinc-950 transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2 block">Alamat
                            Email</label>
                        <input type="email" placeholder="nama@email.com..."
                            class="w-full p-4 bg-white border-none rounded-2xl text-xs font-bold focus:ring-2 focus:ring-zinc-950 transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2 block">Pesan
                            Kamu</label>
                        <textarea rows="5" placeholder="Tulisin aja apa yang mau ditanyain..."
                            class="w-full p-4 bg-white border-none rounded-2xl text-xs font-bold focus:ring-2 focus:ring-zinc-950 transition-all resize-none"></textarea>
                    </div>
                    <button type="button"
                        class="w-full py-5 bg-zinc-950 text-white text-[10px] font-black uppercase tracking-[0.4em] rounded-2xl shadow-xl hover:bg-zinc-800 transition-all mt-4">
                        Kirim Sekarang
                    </button>
                </form>
            </div>
        </div>
    </section>
@endsection
