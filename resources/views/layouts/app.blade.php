<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STS. — Streetwear to Santri Essentials</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.bunny.net/css?family=figtree:400,900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }

        .nav-link {
            position: relative;
            transition: color 0.3s ease;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -4px;
            left: 0;
            background-color: currentColor;
            transition: width 0.3s ease;
        }

        /* Hover & Active underline */
        .nav-link.active-nav::after,
        .nav-link:hover::after {
            width: 100%;
        }

        /* Default scrolled navbar or other pages: links are zinc-400, active is zinc-950 */
        nav .nav-link {
            color: #a1a1aa;
            /* text-zinc-400 */
        }

        nav .nav-link.active-nav,
        nav .nav-link:hover {
            color: #09090b;
            /* text-zinc-950 */
        }

        /* Transparent navbar (when at the top of Home page) */
        nav.border-transparent .nav-link {
            color: rgba(255, 255, 255, 0.6);
        }

        nav.border-transparent .nav-link.active-nav,
        nav.border-transparent .nav-link:hover {
            color: #ffffff;
            /* text-white */
        }
    </style>
</head>

<body class="bg-white text-zinc-950 antialiased font-sans">

    @include('layouts.navigation')

    <main class="{{ request()->routeIs('home') ? '' : 'pt-20' }}">
        @yield('content')
    </main>

    <x-footer></x-footer>
</body>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const sections = document.querySelectorAll('section[id]');
        const navLinks = document.querySelectorAll('.nav-link');
        const mobileNavLinks = document.querySelectorAll('.mobile-nav-link');

        window.addEventListener('scroll', () => {
            let current = '';

            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (pageYOffset >= (sectionTop - 150)) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active-nav');
                if (link.getAttribute('data-section') === current) {
                    link.classList.add('active-nav');
                }
            });

            mobileNavLinks.forEach(link => {
                link.classList.remove('active-nav');
                if (link.getAttribute('data-section') === current) {
                    link.classList.add('active-nav');
                }
            });
        });
    });
</script>

</html>
