<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - {{ config('app.name', 'STS') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:300,400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] {
            display: none !important;
        }

        .dataTables_wrapper .dataTables_length select {
            padding-right: 2rem;
        }

        /* Custom DataTables Pagination Styling - Elegant Streetwear Theme */
        .dataTables_wrapper .dataTables_paginate {
            display: flex !important;
            align-items: center;
            justify-content: flex-end;
            gap: 0.25rem;
            margin-top: 1.5rem !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border: 1px solid #e4e4e7 !important;
            /* border-zinc-200 */
            background: #ffffff !important;
            color: #09090b !important;
            /* text-zinc-950 */
            border-radius: 0.5rem !important;
            /* rounded-lg */
            padding: 0.5rem 0.75rem !important;
            font-size: 0.75rem !important;
            /* text-xs */
            font-weight: 700 !important;
            /* font-bold */
            transition: all 0.2s ease-in-out !important;
            cursor: pointer !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
            margin: 0 !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            border-color: #09090b !important;
            background: #09090b !important;
            color: #ffffff !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            border-color: #09090b !important;
            background: #09090b !important;
            color: #ffffff !important;
            font-weight: 900 !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover,
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:active {
            border-color: #f4f4f5 !important;
            /* border-zinc-100 */
            background: #f4f4f5 !important;
            color: #a1a1aa !important;
            /* text-zinc-400 */
            cursor: not-allowed !important;
            opacity: 0.6;
        }

        .dataTables_wrapper .dataTables_info {
            color: #71717a !important;
            /* text-zinc-500 */
            font-size: 0.75rem !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            margin-top: 1.5rem !important;
        }

        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #e4e4e7 !important;
            border-radius: 0.75rem !important;
            padding: 0.5rem 1rem !important;
            font-size: 0.75rem !important;
            background: #fafafa !important;
            color: #09090b !important;
            outline: none !important;
            transition: all 0.2s;
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #09090b !important;
            background: #ffffff !important;
            box-shadow: 0 0 0 1px #09090b !important;
        }

        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #e4e4e7 !important;
            border-radius: 0.75rem !important;
            padding: 0.5rem 2rem 0.5rem 1rem !important;
            font-size: 0.75rem !important;
            background: #fafafa !important;
            outline: none !important;
            transition: all 0.2s;
        }

        .dataTables_wrapper .dataTables_length select:focus {
            border-color: #09090b !important;
            background: #ffffff !important;
        }
    </style>
</head>

<body class="font-sans antialiased bg-white text-zinc-950">
    <div x-data="{ sidebarOpen: false }" class="min-h-screen flex flex-col md:flex-row relative">

        {{-- Mobile Header & Hamburger Menu --}}
        <div class="md:hidden bg-zinc-950 h-16 flex items-center justify-center px-6 z-20 relative">
            <button @click="sidebarOpen = !sidebarOpen" class="text-white p-2 absolute left-6">
                <svg x-show="!sidebarOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                    </path>
                </svg>
                <svg x-show="sidebarOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                <span class="text-xl font-bold tracking-widest text-white uppercase italic">STS<span
                        class="text-zinc-600">.</span></span>
            </a>
        </div>

        {{-- Mobile Sidebar Overlay --}}
        <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 bg-black/50 z-30 md:hidden"
            @click="sidebarOpen = false" x-cloak></div>

        {{-- Sidebar --}}
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
            class="fixed md:sticky top-0 left-0 h-screen w-64 bg-zinc-950 text-zinc-400 flex flex-col overflow-y-auto border-r border-zinc-900 transition-transform duration-300 z-40">
            <div class="h-20 flex items-center px-8 border-b border-zinc-900 flex-shrink-0 hidden md:flex">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 group">
                    <div
                        class="w-7 h-7 bg-white rounded flex items-center justify-center group-hover:rotate-12 transition-transform">
                        <span class="text-zinc-950 font-black text-[10px] italic">S</span>
                    </div>
                    <span class="text-xl font-bold tracking-widest text-white uppercase italic">STS<span
                            class="text-zinc-600">.</span></span>
                </a>
            </div>

            <nav class="flex-1 mt-6 px-4 space-y-1">
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center px-4 py-3 rounded-md transition-all duration-200 group
            {{ request()->routeIs('admin.dashboard') ? 'bg-zinc-900 text-white font-medium' : 'hover:text-zinc-200 hover:bg-zinc-900/50' }}">
                    <svg class="w-4 h-4 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    <span class="text-[11px] uppercase tracking-[0.15em]">Dashboard</span>
                </a>

                <div class="pt-6 pb-2 px-4 text-[9px] font-bold text-zinc-600 uppercase tracking-[0.2em]">Storefront
                </div>

                <a href="{{ route('admin.categories.index') }}"
                    class="flex items-center px-4 py-3 rounded-md transition-all duration-200 group
            {{ request()->routeIs('admin.categories.*') ? 'bg-zinc-900 text-white font-medium shadow-sm' : 'hover:text-zinc-200 hover:bg-zinc-900/50' }}">
                    <svg class="w-4 h-4 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581a1.5 1.5 0 002.122 0l4.75-4.75a1.5 1.5 0 000-2.122L10.74 3.659A2.25 2.25 0 009.568 3Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 6h.008v.008H6V6Z" />
                    </svg>
                    <span class="text-[11px] uppercase tracking-[0.15em]">Categories</span>
                </a>

                <a href="{{ route('admin.products.index') }}"
                    class="flex items-center px-4 py-3 rounded-md transition-all duration-200 group
            {{ request()->routeIs('admin.products.*') ? 'bg-zinc-900 text-white font-medium shadow-sm' : 'hover:text-zinc-200 hover:bg-zinc-900/50' }}">
                    <svg class="w-4 h-4 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0Zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0Z" />
                    </svg>
                    <span class="text-[11px] uppercase tracking-[0.15em]">Products</span>
                </a>

                <div class="pt-6 pb-2 px-4 text-[9px] font-bold text-zinc-600 uppercase tracking-[0.2em]">Management
                </div>

                <a href="{{ route('admin.stock-ins.index') }}"
                    class="flex items-center px-4 py-3 rounded-md transition-all duration-200 group
            {{ request()->routeIs('admin.stock-ins.*') ? 'bg-zinc-900 text-white font-medium' : 'hover:text-zinc-200 hover:bg-zinc-900/50' }}">
                    <svg class="w-4 h-4 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    <span class="text-[11px] uppercase tracking-[0.15em]">Stock In</span>
                </a>

                <a href="{{ route('admin.stock-outs.index') }}"
                    class="flex items-center px-4 py-3 rounded-md transition-all duration-200 group
            {{ request()->routeIs('admin.stock-outs.*') ? 'bg-zinc-900 text-white font-medium' : 'hover:text-zinc-200 hover:bg-zinc-900/50' }}">
                    <svg class="w-4 h-4 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                    </svg>
                    <span class="text-[11px] uppercase tracking-[0.15em]">Stock Out</span>
                </a>

                <a href="{{ route('admin.orders.index') }}"
                    class="flex items-center px-4 py-3 rounded-md transition-all duration-200 group
            {{ request()->routeIs('admin.orders.*') ? 'bg-zinc-900 text-white font-medium' : 'hover:text-zinc-200 hover:bg-zinc-900/50' }}">
                    <svg class="w-4 h-4 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0Zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0Z" />
                    </svg>
                    <span class="text-[11px] uppercase tracking-[0.15em]">Orders</span>
                </a>

                <div class="pt-6 pb-2 px-4 text-[9px] font-bold text-zinc-600 uppercase tracking-[0.2em]">Reports
                </div>

                <a href="{{ route('admin.reports.stock') }}"
                    class="flex items-center px-4 py-3 rounded-md transition-all duration-200 group
            {{ request()->routeIs('admin.reports.*') ? 'bg-zinc-900 text-white font-medium' : 'hover:text-zinc-200 hover:bg-zinc-900/50' }}">
                    <svg class="w-4 h-4 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                    <span class="text-[11px] uppercase tracking-[0.15em]">Reports</span>
                </a>

                <div class="pt-6 pb-2 px-4 text-[9px] font-bold text-zinc-600 uppercase tracking-[0.2em]">Website
                </div>

                <a href="{{ route('admin.site-settings.index') }}"
                    class="flex items-center px-4 py-3 rounded-md transition-all duration-200 group
            {{ request()->routeIs('admin.site-settings.*') ? 'bg-zinc-900 text-white font-medium shadow-sm' : 'hover:text-zinc-200 hover:bg-zinc-900/50' }}">
                    <svg class="w-4 h-4 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.764m3.42 3.42a6.776 6.776 0 00-3.42-3.42">
                        </path>
                    </svg>
                    <span class="text-[11px] uppercase tracking-[0.15em]">Edit Website</span>
                </a>

                <div class="pt-6 pb-2 px-4 text-[9px] font-bold text-zinc-600 uppercase tracking-[0.2em]">Account
                </div>

                <a href="{{ route('admin.profile.index') }}"
                    class="flex items-center px-4 py-3 rounded-md transition-all duration-200 group
            {{ request()->routeIs('admin.profile.*') ? 'bg-zinc-900 text-white font-medium shadow-sm' : 'hover:text-zinc-200 hover:bg-zinc-900/50' }}">
                    <svg class="w-4 h-4 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                        </path>
                    </svg>
                    <span class="text-[11px] uppercase tracking-[0.15em]">Admin Profile</span>
                </a>
            </nav>

            <div class="p-6 border-t border-zinc-900 flex-shrink-0">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-zinc-500 hover:text-white transition-colors">
                        <svg class="w-4 h-4 mr-3 opacity-70" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                            </path>
                        </svg>
                        Sign Out
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col min-w-0 bg-zinc-50/50">
            <header
                class="hidden md:flex h-20 bg-white border-b border-zinc-200 items-center justify-between px-10 sticky top-0 z-30">
                <div class="text-[11px] font-medium uppercase tracking-[0.2em] text-zinc-400">
                    Administration <span class="mx-2 text-zinc-200">/</span> <span
                        class="text-zinc-950">{{ ucfirst(request()->segment(2) ?? 'Dashboard') }}</span>
                </div>

                <div class="flex items-center gap-6 relative" x-data="{ profileOpen: false }">
                    <button @click="profileOpen = !profileOpen" @click.away="profileOpen = false"
                        class="flex items-center gap-4 hover:opacity-80 transition-opacity focus:outline-none">
                        <div class="flex flex-col items-end hidden sm:flex">
                            <span
                                class="text-[11px] font-bold text-zinc-950 uppercase tracking-widest">{{ Auth::user()->name }}</span>
                            <span class="text-[9px] text-zinc-400 uppercase tracking-[0.1em]">Admin System</span>
                        </div>
                        <div
                            class="w-10 h-10 bg-zinc-950 rounded-xl flex items-center justify-center border border-zinc-800 overflow-hidden shadow-lg">
                            @if (Auth::user()->avatar)
                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}"
                                    class="w-full h-full object-cover">
                            @else
                                <span
                                    class="text-xs font-black text-white italic">{{ substr(Auth::user()->name, 0, 1) }}</span>
                            @endif
                        </div>
                    </button>

                    {{-- Dropdown Menu --}}
                    <div x-show="profileOpen" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                        class="absolute right-0 top-full mt-2 w-56 bg-white border border-zinc-100 rounded-[1.5rem] shadow-2xl py-3 z-[100]"
                        x-cloak>
                        <div class="px-5 pb-3 mb-2 border-b border-zinc-50">
                            <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Status: Online</p>
                        </div>
                        <a href="{{ route('admin.profile.index') }}"
                            class="flex items-center gap-3 px-5 py-3 text-[10px] font-black uppercase tracking-widest text-zinc-600 hover:text-zinc-950 hover:bg-zinc-50 transition-colors">
                            Profil Saya
                        </a>
                        <div class="mt-2 pt-2 border-t border-zinc-50">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center gap-3 px-5 py-3 text-[10px] font-black uppercase tracking-widest text-red-600 hover:bg-red-50 transition-colors text-left">
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 p-4 md:p-10 w-full overflow-x-hidden">
                @yield('content')
            </main>
        </div>
    </div>

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

        $(document).ready(function() {
            // Initialize DataTable for any table with class 'datatable'
            if ($('.datatable').length) {
                $('.datatable').DataTable({
                    language: {
                        search: "Cari:",
                        lengthMenu: "Tampilkan _MENU_ data",
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                        infoEmpty: "Tidak ada data yang tersedia",
                        infoFiltered: "(disaring dari _MAX_ total entri)",
                        paginate: {
                            first: "Pertama",
                            last: "Terakhir",
                            next: "Selanjutnya",
                            previous: "Sebelumnya"
                        },
                        emptyTable: "Belum ada data tersedia di tabel ini."
                    }
                });

                // Styling penyesuaian khusus DataTables untuk theme Tailwind
                $('.dataTables_wrapper select, .dataTables_wrapper input').addClass(
                    'bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-1.5 text-xs focus:ring-zinc-950 focus:border-zinc-950 ml-2'
                );
                $('.dataTables_wrapper label').addClass(
                    'text-[10px] font-bold uppercase tracking-widest text-zinc-500');
                $('.dataTables_wrapper .dataTables_paginate .paginate_button').addClass(
                    'text-xs font-bold text-zinc-600 hover:text-zinc-950 px-2');
                $('.dataTables_wrapper .dataTables_info').addClass(
                    'text-[10px] font-medium text-zinc-400 uppercase tracking-widest mt-4');
                $('.dataTables_wrapper .dataTables_paginate').addClass('mt-4');
            }
        });
    </script>
</body>

</html>
