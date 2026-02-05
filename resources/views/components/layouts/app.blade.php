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
    <!-- Top Bar -->
    <div class="bg-brand-black100 text-white hidden lg:block">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-9 text-[11px] tracking-wide">
                <div class="flex items-center gap-6">
                    <a href="tel:05102216413" class="flex items-center gap-1.5 text-white/60 hover:text-primary transition-colors">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        0510 221 64 13
                    </a>
                    <a href="mailto:info@beartshare.com" class="flex items-center gap-1.5 text-white/60 hover:text-primary transition-colors">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        info@beartshare.com
                    </a>
                </div>
                <div class="flex items-center gap-5">
                    <a href="{{ route('artpuan') }}" class="text-primary hover:text-white transition-colors font-medium">ArtPuan</a>
                    <span class="w-px h-3 bg-white/20"></span>
                    <a href="{{ route('contact') }}" class="text-white/60 hover:text-primary transition-colors">İletişim</a>
                    <span class="w-px h-3 bg-white/20"></span>
                    <a href="{{ route('login') }}" class="flex items-center gap-1.5 text-white/60 hover:text-primary transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Giriş Yap
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-gray-100/80 shadow-sm" x-data="{ mobileMenu: false, searchOpen: false }">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16 lg:h-[72px]">

                <!-- Mobile: Hamburger (Left) -->
                <button @click="mobileMenu = !mobileMenu" class="lg:hidden p-2 -ml-2 text-brand-black100 hover:text-primary transition-colors">
                    <svg x-show="!mobileMenu" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="mobileMenu" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <!-- Logo -->
                <a href="/" class="flex items-center gap-2 group">
                    <img alt="BeArtShare Logo" src="{{ asset('images/logo.svg') }}" class="h-10 lg:h-12 w-auto transition-transform duration-300 group-hover:scale-105" width="65" height="65">
                    <span class="hidden xl:inline-block text-[10px] text-gray-400 font-light tracking-wider border-l border-gray-200 pl-2 ml-0.5 leading-tight">Yeni Çağın<br>Sanat Galerisi</span>
                </a>

                <!-- Desktop Navigation -->
                <nav class="hidden lg:flex items-center">
                    <div class="flex items-center gap-1">
                        <a href="{{ route('artworks') }}" class="nav-link px-3 py-2 text-[13px] text-brand-black100 hover:text-primary transition-colors relative group">
                            Eserler
                            <span class="absolute bottom-0 left-3 right-3 h-[2px] bg-primary scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></span>
                        </a>
                        <a href="{{ route('artists') }}" class="nav-link px-3 py-2 text-[13px] text-brand-black100 hover:text-primary transition-colors relative group">
                            Sanatçılar
                            <span class="absolute bottom-0 left-3 right-3 h-[2px] bg-primary scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></span>
                        </a>
                        <a href="{{ route('about') }}" class="nav-link px-3 py-2 text-[13px] text-brand-black100 hover:text-primary transition-colors relative group">
                            Hakkımızda
                            <span class="absolute bottom-0 left-3 right-3 h-[2px] bg-primary scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></span>
                        </a>
                        <a href="{{ route('blog') }}" class="nav-link px-3 py-2 text-[13px] text-brand-black100 hover:text-primary transition-colors relative group">
                            Blog
                            <span class="absolute bottom-0 left-3 right-3 h-[2px] bg-primary scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></span>
                        </a>
                    </div>
                </nav>

                <!-- Right Actions -->
                <div class="flex items-center gap-1 lg:gap-2">
                    <!-- Search Toggle -->
                    <button @click="searchOpen = !searchOpen" class="p-2 text-brand-black100 hover:text-primary transition-colors rounded-full hover:bg-gray-50">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                        </svg>
                    </button>

                    <!-- User (Mobile) -->
                    <a href="{{ route('login') }}" class="lg:hidden p-2 text-brand-black100 hover:text-primary transition-colors rounded-full hover:bg-gray-50">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </a>

                    <!-- Cart -->
                    <a href="{{ route('cart') }}" class="p-2 text-brand-black100 hover:text-primary transition-colors rounded-full hover:bg-gray-50 relative">
                        @livewire('cart-icon')
                    </a>
                </div>
            </div>
        </div>

        <!-- Search Bar (Expandable) -->
        <div x-show="searchOpen"
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-1"
             class="border-t border-gray-100 bg-white"
        >
            <div class="container mx-auto px-4 py-3">
                <div class="relative max-w-xl mx-auto">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                    <input type="text" placeholder="Eser, sanatçı veya kategori ara..." class="w-full pl-10 pr-10 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition-all bg-gray-50 focus:bg-white">
                    <button @click="searchOpen = false" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-brand-black100 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenu"
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="lg:hidden fixed inset-0 top-16 z-40"
        >
            <!-- Overlay -->
            <div class="absolute inset-0 bg-black/20 backdrop-blur-sm" @click="mobileMenu = false"></div>

            <!-- Menu Panel -->
            <div x-show="mobileMenu"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="-translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="-translate-x-full"
                 class="relative w-[280px] h-full bg-white shadow-2xl overflow-y-auto"
            >
                <!-- User Section -->
                <div class="p-5 bg-brand-black100">
                    <a href="{{ route('login') }}" class="flex items-center gap-3" @click="mobileMenu = false">
                        <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm text-white font-medium">Giriş Yap</p>
                            <p class="text-[10px] text-white/40">veya hesap oluştur</p>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <nav class="py-2">
                    <a href="{{ route('artworks') }}" class="mobile-nav-link flex items-center gap-3 px-5 py-3.5 text-sm text-brand-black100 hover:bg-gray-50 hover:text-primary transition-all" @click="mobileMenu = false">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Eserler
                    </a>
                    <a href="{{ route('artists') }}" class="mobile-nav-link flex items-center gap-3 px-5 py-3.5 text-sm text-brand-black100 hover:bg-gray-50 hover:text-primary transition-all" @click="mobileMenu = false">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Sanatçılar
                    </a>
                    <a href="{{ route('about') }}" class="mobile-nav-link flex items-center gap-3 px-5 py-3.5 text-sm text-brand-black100 hover:bg-gray-50 hover:text-primary transition-all" @click="mobileMenu = false">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Hakkımızda
                    </a>
                    <a href="{{ route('artpuan') }}" class="mobile-nav-link flex items-center gap-3 px-5 py-3.5 text-sm text-brand-black100 hover:bg-gray-50 hover:text-primary transition-all" @click="mobileMenu = false">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                        <span class="text-primary font-medium">ArtPuan</span>
                    </a>
                    <a href="{{ route('blog') }}" class="mobile-nav-link flex items-center gap-3 px-5 py-3.5 text-sm text-brand-black100 hover:bg-gray-50 hover:text-primary transition-all" @click="mobileMenu = false">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                        Blog
                    </a>
                    <a href="{{ route('contact') }}" class="mobile-nav-link flex items-center gap-3 px-5 py-3.5 text-sm text-brand-black100 hover:bg-gray-50 hover:text-primary transition-all" @click="mobileMenu = false">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        İletişim
                    </a>
                </nav>

                <!-- Divider -->
                <div class="mx-5 border-t border-gray-100"></div>

                <!-- Contact Info -->
                <div class="p-5 space-y-3">
                    <a href="tel:05102216413" class="flex items-center gap-2 text-xs text-gray-500 hover:text-primary transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        0510 221 64 13
                    </a>
                    <a href="mailto:info@beartshare.com" class="flex items-center gap-2 text-xs text-gray-500 hover:text-primary transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        info@beartshare.com
                    </a>
                </div>
            </div>
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
