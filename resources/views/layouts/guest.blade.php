<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>STS — {{ $title ?? 'Access Vault' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.bunny.net/css?family=figtree:400,900&display=swap" rel="stylesheet" />
    <style>
        .auth-gradient {
            background: radial-gradient(circle at top right, #f4f4f5 0%, #ffffff 100%);
        }
    </style>
</head>

<body class="auth-gradient text-zinc-950 antialiased font-sans">
    <div class="min-h-screen flex flex-col justify-center items-center px-6 py-12">

        {{-- Brand Identity --}}
        <div class="mb-12 text-center">
            <a href="/" class="text-4xl font-black italic tracking-tighter uppercase group">
                STS<span class="text-zinc-300 group-hover:text-zinc-950 transition-colors">.</span>
            </a>
            <p class="text-[9px] font-black uppercase tracking-[0.5em] text-zinc-400 mt-2">Streetwear to Santri
                Worldwide</p>
        </div>

        {{-- Auth Card --}}
        <div
            class="w-full sm:max-w-md bg-white border border-zinc-100 rounded-[2rem] p-8 md:p-12 shadow-[0_32px_64px_-15px_rgba(0,0,0,0.05)]">
            {{ $slot }}
        </div>

        {{-- Footer --}}
        <div class="mt-12 text-[8px] font-black uppercase tracking-[0.4em] text-zinc-300">
            Secure Access — STS Archive 2026
        </div>
    </div>
</body>

</html>
