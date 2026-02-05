<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'BeArtShare - Yeni Çağın Sanat Galerisi' }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#D4A017',
                        'primary-dark': '#B8860B',
                        'brand-black100': '#14171c',
                        'brand-grey250': '#e5e5e5',
                        'brand-grey300': '#d1d5db',
                    },
                    fontFamily: {
                        sans: ['Prompt', 'Helvetica', 'Arial', 'ui-sans-serif', 'system-ui', 'sans-serif', 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji'],
                    },
                    fontSize: {
                        'xsm': '0.65rem',
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts: Prompt -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@100;200;300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        [x-cloak] { display: none !important; }
        .link-underline {
            position: relative;
        }
        .link-underline::after {
            content: '';
            position: absolute;
            width: 0;
            height: 1px;
            bottom: 0;
            left: 50%;
            background-color: currentColor;
            transition: all 0.3s ease;
        }
        .link-underline:hover::after {
            width: 100%;
            left: 0;
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>

    @livewireStyles
</head>
<body class="bg-white font-sans antialiased">
    <!-- Header -->
    <header class="sticky top-0 z-50 bg-white border-b border-gray-100" x-data="{ mobileMenu: false }">
        <div class="container mx-auto px-4">
            <div class="w-full h-28 lg:h-[100px] flex items-center justify-between relative">

                <!-- Mobile: Left Icons (User & Search) -->
                <div class="absolute left-4 top-6 lg:hidden flex items-center space-x-4 z-10">
                    <a href="{{ route('login') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 25 25">
                            <g data-name="Group 161" transform="translate(.5 .5)">
                                <path data-name="Path 84" d="M174.17 461.723a7.758 7.758 0 0 1 4.386-5.713" transform="translate(-169.815 -440.478)" style="fill:none;stroke:#14171c;stroke-miterlimit:10"/>
                                <path data-name="Path 85" d="M189.62 456.01a7.841 7.841 0 0 1 2.232 1.561 7.7 7.7 0 0 1 2.154 4.153" transform="translate(-174.36 -440.478)" style="fill:none;stroke:#14171c;stroke-miterlimit:10"/>
                                <circle data-name="Ellipse 39" cx="12" cy="12" r="12" style="fill:none;stroke:#14171c;stroke-miterlimit:10"/>
                                <ellipse data-name="Ellipse 40" cx="4.944" cy="5.65" rx="4.944" ry="5.65" transform="translate(7.056 5.645)" style="fill:none;stroke:#14171c;stroke-miterlimit:10"/>
                            </g>
                        </svg>
                    </a>
                    <button>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24.854" height="24.854" viewBox="0 0 24.854 24.854">
                            <g data-name="Group 75" transform="translate(.5 .5)">
                                <circle data-name="Ellipse 22" cx="10.499" cy="10.499" r="10.499" style="fill:none;stroke:#14171c;stroke-linejoin:round"/>
                                <path data-name="Line 46" transform="translate(17.998 17.998)" style="fill:none;stroke:#14171c;stroke-linejoin:round" d="M6.002 6.002 0 0"/>
                            </g>
                        </svg>
                    </button>
                </div>

                <!-- Logo -->
                <a href="/" class="lg:relative lg:z-20 lg:h-[100px] lg:flex lg:items-center
                                   absolute left-0 right-0 mx-auto w-24 h-[80px] pt-3 overflow-hidden z-20 lg:pt-0 lg:mx-0 lg:w-auto lg:h-[100px]">
                    <img alt="BeArtShare Logo" src="{{ asset('images/logo.svg') }}" class="lg:h-[65px] h-[50px] relative mx-auto lg:mx-0" width="65" height="65">
                    <p class="text-xs ml-1 opacity-60 rounded-full bg-brand-grey250 px-2 text-brand-black100 hidden lg:block whitespace-nowrap">Yeni çağın sanat galerisi</p>
                </a>

                <!-- Mobile: Right Icons (Cart & Menu) -->
                <div class="absolute right-2 top-6 lg:hidden flex items-center z-20">
                    <a href="{{ route('cart') }}" class="mr-4 relative z-20 p-1">
                        @livewire('cart-icon')
                    </a>
                    <button @click="mobileMenu = !mobileMenu" class="p-1">
                        <!-- Hamburger / X icon -->
                        <svg x-show="!mobileMenu" xmlns="http://www.w3.org/2000/svg" width="24" height="16" viewBox="0 0 24 16">
                            <g data-name="Group 61">
                                <path data-name="Line 17" style="fill:none;stroke:#14171c;stroke-miterlimit:10" d="M0 0h24" transform="translate(0 .5)"/>
                                <path data-name="Line 18" transform="translate(0 8)" style="fill:none;stroke:#14171c;stroke-miterlimit:10" d="M0 0h24"/>
                                <path data-name="Line 19" transform="translate(0 15.5)" style="fill:none;stroke:#14171c;stroke-miterlimit:10" d="M0 0h24"/>
                            </g>
                        </svg>
                        <svg x-show="mobileMenu" x-cloak class="w-6 h-6" fill="none" stroke="#14171c" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Desktop: Full Navigation -->
                <div class="flex-1 h-28 lg:h-[100px] relative hidden lg:flex">
                    <!-- Navigation -->
                    <div class="absolute inset-y-0 right-0 flex items-center text-xs text-left">
                        <div class="flex items-center mr-5">
                            <a href="{{ route('artworks') }}" class="transition-all ml-2 p-2 mr-2 link-underline">Eserler</a>
                            <a href="{{ route('about') }}" class="transition-all ml-2 mr-2 p-2 link-underline">Hakkımızda</a>
                            <a href="{{ route('artists') }}" class="transition-all ml-2 mr-2 p-2 link-underline">Sanatçılar</a>
                            <a href="{{ route('artpuan') }}" class="transition-all ml-2 mr-2 p-2 link-underline">ArtPuan</a>
                            <a href="#" class="transition-all ml-2 mr-2 p-2 link-underline">Sanat Sözlüğü</a>
                            <a href="{{ route('blog') }}" class="transition-all ml-2 mr-2 p-2 link-underline">Blog</a>
                        </div>
                        <div class="flex items-center">
                            <div class="m-4">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24.854" height="24.854" viewBox="0 0 24.854 24.854" class="cursor-pointer">
                                    <g data-name="Group 75" transform="translate(.5 .5)">
                                        <circle data-name="Ellipse 22" cx="10.499" cy="10.499" r="10.499" style="fill:none;stroke:#14171c;stroke-linejoin:round"/>
                                        <path data-name="Line 46" transform="translate(17.998 17.998)" style="fill:none;stroke:#14171c;stroke-linejoin:round" d="M6.002 6.002 0 0"/>
                                    </g>
                                </svg>
                            </div>
                            <a href="{{ route('login') }}" class="m-4 flex leading-6 items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 25 25" class="mr-2">
                                    <g data-name="Group 161" transform="translate(.5 .5)">
                                        <path data-name="Path 84" d="M174.17 461.723a7.758 7.758 0 0 1 4.386-5.713" transform="translate(-169.815 -440.478)" style="fill:none;stroke:#14171c;stroke-miterlimit:10"/>
                                        <path data-name="Path 85" d="M189.62 456.01a7.841 7.841 0 0 1 2.232 1.561 7.7 7.7 0 0 1 2.154 4.153" transform="translate(-174.36 -440.478)" style="fill:none;stroke:#14171c;stroke-miterlimit:10"/>
                                        <circle data-name="Ellipse 39" cx="12" cy="12" r="12" style="fill:none;stroke:#14171c;stroke-miterlimit:10"/>
                                        <ellipse data-name="Ellipse 40" cx="4.944" cy="5.65" rx="4.944" ry="5.65" transform="translate(7.056 5.645)" style="fill:none;stroke:#14171c;stroke-miterlimit:10"/>
                                    </g>
                                </svg>
                                Giriş Yap
                            </a>
                            <a href="{{ route('cart') }}" class="m-4 mr-2 leading-6 flex items-center whitespace-nowrap">
                                Sepetim
                                <span class="ml-2 relative" style="padding: 4px 6px 0 0;">
                                    @livewire('cart-icon')
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenu"
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="lg:hidden border-t border-gray-100 bg-white"
        >
            <nav class="container mx-auto px-4 py-4">
                <ul class="space-y-1">
                    <li><a href="{{ route('artworks') }}" class="block py-3 px-2 text-sm text-brand-black100 hover:text-primary transition border-b border-gray-50" @click="mobileMenu = false">Eserler</a></li>
                    <li><a href="{{ route('artists') }}" class="block py-3 px-2 text-sm text-brand-black100 hover:text-primary transition border-b border-gray-50" @click="mobileMenu = false">Sanatçılar</a></li>
                    <li><a href="{{ route('about') }}" class="block py-3 px-2 text-sm text-brand-black100 hover:text-primary transition border-b border-gray-50" @click="mobileMenu = false">Biz Kimiz</a></li>
                    <li><a href="{{ route('artpuan') }}" class="block py-3 px-2 text-sm text-brand-black100 hover:text-primary transition border-b border-gray-50" @click="mobileMenu = false">ArtPuan</a></li>
                    <li><a href="{{ route('blog') }}" class="block py-3 px-2 text-sm text-brand-black100 hover:text-primary transition border-b border-gray-50" @click="mobileMenu = false">Blog</a></li>
                    <li><a href="{{ route('contact') }}" class="block py-3 px-2 text-sm text-brand-black100 hover:text-primary transition border-b border-gray-50" @click="mobileMenu = false">İletişim</a></li>
                </ul>

                <!-- Mobile Contact Info -->
                <div class="mt-4 pt-4 border-t border-gray-100 space-y-3">
                    <a href="tel:05102216413" class="flex items-center gap-2 text-xs text-gray-500 hover:text-primary transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        0510 221 64 13
                    </a>
                    <a href="mailto:info@beartshare.com" class="flex items-center gap-2 text-xs text-gray-500 hover:text-primary transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        info@beartshare.com
                    </a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Flash Messages -->
    @if(session()->has('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if(session()->has('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    @if(session()->has('info'))
        <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4" role="alert">
            <p>{{ session('info') }}</p>
        </div>
    @endif

    <!-- Main Content -->
    <main>
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12 mt-16">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <a href="/" class="block mb-4 w-24">
                        <img src="{{ asset('images/logo.svg') }}" alt="BeArtShare" class="w-full h-auto brightness-0 invert" width="65">
                    </a>
                    <p class="text-gray-400 text-sm">
                        Ülkemizin kıymetli sanatçılarına ve uluslararası Blue Chip sanatçılara güvenle ve kazançla ulaşmanın yolu.
                    </p>
                </div>

                <div>
                    <h4 class="font-semibold mb-4">Hızlı Linkler</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="{{ route('artworks') }}" class="hover:text-white transition">Eserler</a></li>
                        <li><a href="{{ route('artists') }}" class="hover:text-white transition">Sanatçılar</a></li>
                        <li><a href="{{ route('about') }}" class="hover:text-white transition">Hakkımızda</a></li>
                        <li><a href="{{ route('blog') }}" class="hover:text-white transition">Blog</a></li>
                        <li><a href="{{ route('artpuan') }}" class="hover:text-white transition">ArtPuan</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold mb-4">Yardım</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="{{ route('contact') }}" class="hover:text-white transition">İletişim</a></li>
                        <li><a href="#" class="hover:text-white transition">Kargo ve Teslimat</a></li>
                        <li><a href="#" class="hover:text-white transition">İade Politikası</a></li>
                        <li><a href="#" class="hover:text-white transition">SSS</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold mb-4">İletişim</h4>
                    <ul class="space-y-3 text-gray-400 text-sm">
                        <li class="flex items-start space-x-2">
                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <span>Harmancı Giz Plaza, Harman Sok. No:5 K:21 D:118 Esentepe/İST</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <a href="tel:05102216413" class="hover:text-white transition">0510 221 64 13</a>
                        </li>
                        <li class="flex items-center space-x-2">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <a href="mailto:info@beartshare.com" class="hover:text-white transition">info@beartshare.com</a>
                        </li>
                    </ul>

                </div>
            </div>

            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400 text-sm space-y-2">
                <p>&copy; {{ date('Y') }} BeArtShare. Tüm hakları saklıdır.</p>
                <p class="flex items-center justify-center gap-1.5 text-xs text-gray-500">
                    <svg class="w-3.5 h-3.5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Bu site <a href="https://etbis.eticaret.gov.tr/sitedogrulama/2428037130442436" target="_blank" rel="noopener noreferrer" class="underline hover:text-white transition">ETBİS</a>'e kayıtlıdır.
                </p>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
