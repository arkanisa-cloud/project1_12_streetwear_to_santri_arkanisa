<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - LOST IN THE STREETS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-zinc-950 font-sans antialiased h-screen flex items-center justify-center p-8">
    <div class="max-w-xl w-full text-center">
        <h1 class="text-[12rem] font-black leading-none tracking-tighter italic border-b-8 border-zinc-950 inline-block mb-8">404</h1>
        <div class="space-y-4">
            <h2 class="text-3xl font-black uppercase tracking-tighter">Lost in the streets?</h2>
            <p class="text-zinc-500 font-medium italic">The collection or page you're looking for has been archived or never existed.</p>
        </div>
        
        <div class="mt-12">
            <a href="{{ url('/') }}" class="inline-block px-12 py-4 bg-zinc-950 text-white text-xs font-black uppercase tracking-[0.3em] border-2 border-zinc-950 shadow-[8px_8px_0px_0px_rgba(161,161,170,1)] hover:shadow-none hover:translate-x-2 hover:translate-y-2 transition-all">
                Back to Base
            </a>
        </div>
        
        <div class="mt-20 opacity-10">
            <span class="text-xs font-black uppercase tracking-[1em]">Zenly Warehouse System v1.0</span>
        </div>
    </div>
</body>
</html>
