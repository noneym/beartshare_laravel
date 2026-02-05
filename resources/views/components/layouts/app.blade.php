@props([
    'title' => null,
    'metaDescription' => null,
    'metaKeywords' => null,
    'metaRobots' => null,
    'canonical' => null,
    'ogType' => null,
    'ogTitle' => null,
    'ogDescription' => null,
    'ogUrl' => null,
    'ogImage' => null,
    'twitterCard' => null,
    'jsonLd' => null,
])
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SEO Meta Tags -->
    <title>{{ $title ?? 'BeArtShare - Yeni Çağın Sanat Galerisi' }}</title>
    <meta name="description" content="{{ $metaDescription ?? 'BeArtShare ile Türkiye\'nin en değerli sanatçılarının orijinal eserlerine güvenle ulaşın. Online sanat galerisi, tablo, heykel ve daha fazlası.' }}">
    <meta name="keywords" content="{{ $metaKeywords ?? 'sanat galerisi, online sanat, tablo satın al, orijinal eser, türk sanatçılar, yağlı boya tablo, heykel, sanat yatırımı' }}">
    <meta name="robots" content="{{ $metaRobots ?? 'index, follow' }}">
    <link rel="canonical" href="{{ $canonical ?? url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="{{ $ogType ?? 'website' }}">
    <meta property="og:url" content="{{ $ogUrl ?? url()->current() }}">
    <meta property="og:title" content="{{ $ogTitle ?? $title ?? 'BeArtShare - Yeni Çağın Sanat Galerisi' }}">
    <meta property="og:description" content="{{ $ogDescription ?? $metaDescription ?? 'BeArtShare ile Türkiye\'nin en değerli sanatçılarının orijinal eserlerine güvenle ulaşın.' }}">
    <meta property="og:image" content="{{ $ogImage ?? asset('images/og-default.jpg') }}">
    <meta property="og:site_name" content="BeArtShare">
    <meta property="og:locale" content="tr_TR">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="{{ $twitterCard ?? 'summary_large_image' }}">
    <meta name="twitter:title" content="{{ $ogTitle ?? $title ?? 'BeArtShare - Yeni Çağın Sanat Galerisi' }}">
    <meta name="twitter:description" content="{{ $ogDescription ?? $metaDescription ?? 'BeArtShare ile Türkiye\'nin en değerli sanatçılarının orijinal eserlerine güvenle ulaşın.' }}">
    <meta name="twitter:image" content="{{ $ogImage ?? asset('images/og-default.jpg') }}">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- JSON-LD Structured Data -->
    @if(isset($jsonLd))
        <script type="application/ld+json">{!! $jsonLd !!}</script>
    @else
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "WebSite",
            "name": "BeArtShare",
            "url": "{{ config('app.url') }}",
            "description": "Yeni Çağın Sanat Galerisi - Online sanat eseri satın alma platformu",
            "potentialAction": {
                "@type": "SearchAction",
                "target": "{{ url('/eserler') }}?search={search_term_string}",
                "query-input": "required name=search_term_string"
            }
        }
        </script>
    @endif

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
                    @auth
                        <a href="{{ route('favorites') }}" class="flex items-center gap-1.5 text-white/60 hover:text-primary transition-colors">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            Favorilerim
                        </a>
                        <span class="w-px h-3 bg-white/20"></span>
                        <a href="{{ route('profile') }}" class="flex items-center gap-1.5 text-white/60 hover:text-primary transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Hesabım
                        </a>
                        @if(auth()->user()->is_admin)
                        <span class="w-px h-3 bg-white/20"></span>
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-1.5 text-red-400 hover:text-red-300 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Admin
                        </a>
                        @endif
                        <span class="w-px h-3 bg-white/20"></span>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-white/60 hover:text-primary transition-colors">Çıkış</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="flex items-center gap-1.5 text-white/60 hover:text-primary transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Giriş Yap
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Header -->
    <div x-data="{ mobileMenu: false, searchOpen: false }" class="sticky top-0 z-50">
        <header class="bg-white border-b border-gray-100 shadow-sm">
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
                        <a href="{{ auth()->check() ? route('profile') : route('login') }}" class="lg:hidden p-2 text-brand-black100 hover:text-primary transition-colors rounded-full hover:bg-gray-50">
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
        </header>

        <!-- Mobile Menu (Teleported to body to avoid stacking context issues) -->
        <template x-teleport="body">
        <div x-show="mobileMenu"
             x-cloak
             class="lg:hidden fixed inset-0 z-[60]"
             style="display: none;"
        >
            <!-- Overlay -->
            <div x-show="mobileMenu"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="absolute inset-0 bg-black/30"
                 @click="mobileMenu = false"
            ></div>

            <!-- Menu Panel -->
            <div x-show="mobileMenu"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="-translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in duration-250"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="-translate-x-full"
                 class="absolute top-0 left-0 w-[280px] h-full bg-white shadow-2xl overflow-y-auto"
            >
                <!-- Close + Logo -->
                <div class="flex items-center justify-between p-4 border-b border-gray-100">
                    <a href="/" class="flex items-center gap-2" @click="mobileMenu = false">
                        <img alt="BeArtShare" src="{{ asset('images/logo.svg') }}" class="h-8 w-auto" width="65" height="65">
                    </a>
                    <button @click="mobileMenu = false" class="p-1 text-gray-400 hover:text-brand-black100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- User Section -->
                <div class="p-4 bg-gray-50 border-b border-gray-100">
                    @auth
                        <div class="flex items-center justify-between">
                            <a href="{{ route('profile') }}" class="flex items-center gap-3" @click="mobileMenu = false">
                                <div class="w-9 h-9 rounded-full bg-primary flex items-center justify-center">
                                    <span class="text-sm font-semibold text-white">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-brand-black100">{{ auth()->user()->name }}</p>
                                    <p class="text-[10px] text-gray-400">{{ auth()->user()->email }}</p>
                                </div>
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" @click="mobileMenu = false" class="text-xs text-gray-400 hover:text-red-500 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="flex items-center gap-3" @click="mobileMenu = false">
                            <div class="w-9 h-9 rounded-full bg-brand-black100 flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-brand-black100">Giriş Yap</p>
                                <p class="text-[10px] text-gray-400">veya hesap oluştur</p>
                            </div>
                        </a>
                    @endauth
                </div>

                <!-- Navigation Links -->
                <nav class="py-2">
                    <a href="{{ route('artworks') }}" class="flex items-center gap-3 px-5 py-3 text-sm text-brand-black100 hover:bg-gray-50 hover:text-primary transition-all" @click="mobileMenu = false">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Eserler
                    </a>
                    <a href="{{ route('artists') }}" class="flex items-center gap-3 px-5 py-3 text-sm text-brand-black100 hover:bg-gray-50 hover:text-primary transition-all" @click="mobileMenu = false">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Sanatçılar
                    </a>
                    <a href="{{ route('about') }}" class="flex items-center gap-3 px-5 py-3 text-sm text-brand-black100 hover:bg-gray-50 hover:text-primary transition-all" @click="mobileMenu = false">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Hakkımızda
                    </a>
                    <a href="{{ route('artpuan') }}" class="flex items-center gap-3 px-5 py-3 text-sm hover:bg-gray-50 transition-all" @click="mobileMenu = false">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                        <span class="text-primary font-medium">ArtPuan</span>
                    </a>
                    <a href="{{ route('blog') }}" class="flex items-center gap-3 px-5 py-3 text-sm text-brand-black100 hover:bg-gray-50 hover:text-primary transition-all" @click="mobileMenu = false">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                        Blog
                    </a>
                    <a href="{{ route('contact') }}" class="flex items-center gap-3 px-5 py-3 text-sm text-brand-black100 hover:bg-gray-50 hover:text-primary transition-all" @click="mobileMenu = false">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        İletişim
                    </a>
                    @auth
                    <div class="mx-5 my-1 border-t border-gray-100"></div>
                    <a href="{{ route('profile') }}" class="flex items-center gap-3 px-5 py-3 text-sm text-brand-black100 hover:bg-gray-50 hover:text-primary transition-all" @click="mobileMenu = false">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span class="text-primary font-medium">Hesabım</span>
                    </a>
                    <a href="{{ route('profile', 'orders') }}" class="flex items-center gap-3 px-5 py-3 text-sm text-brand-black100 hover:bg-gray-50 hover:text-primary transition-all pl-12" @click="mobileMenu = false">
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        Siparişlerim
                    </a>
                    <a href="{{ route('profile', 'favorites') }}" class="flex items-center gap-3 px-5 py-3 text-sm text-brand-black100 hover:bg-gray-50 hover:text-primary transition-all pl-12" @click="mobileMenu = false">
                        <svg class="w-3.5 h-3.5 text-red-400" fill="currentColor" viewBox="0 0 24 24"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        Favorilerim
                    </a>
                    <a href="{{ route('profile', 'artpuan') }}" class="flex items-center gap-3 px-5 py-3 text-sm text-brand-black100 hover:bg-gray-50 hover:text-primary transition-all pl-12" @click="mobileMenu = false">
                        <svg class="w-3.5 h-3.5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                        ArtPuan
                    </a>
                    @if(auth()->user()->is_admin)
                    <div class="mx-5 my-1 border-t border-red-100"></div>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-5 py-3 text-sm text-red-600 hover:bg-red-50 transition-all" @click="mobileMenu = false">
                        <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Admin Panel
                    </a>
                    <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 px-5 py-3 text-sm text-red-500 hover:bg-red-50 transition-all pl-12" @click="mobileMenu = false">
                        <svg class="w-3.5 h-3.5 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        Siparişler
                    </a>
                    <a href="{{ route('admin.art-puan-logs.index') }}" class="flex items-center gap-3 px-5 py-3 text-sm text-red-500 hover:bg-red-50 transition-all pl-12" @click="mobileMenu = false">
                        <svg class="w-3.5 h-3.5 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        ArtPuan Log
                    </a>
                    <a href="{{ route('admin.notification-logs.index') }}" class="flex items-center gap-3 px-5 py-3 text-sm text-red-500 hover:bg-red-50 transition-all pl-12" @click="mobileMenu = false">
                        <svg class="w-3.5 h-3.5 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Bildirim Log
                    </a>
                    @endif
                    @endauth
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
        </template>
    </div>

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
                        <li><a href="{{ route('banka-hesaplari') }}" class="hover:text-white transition">Banka Hesapları</a></li>
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
