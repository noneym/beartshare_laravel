<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'BeArtShare - Yeni Çağın Sanat Galerisi' }}</title>

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
    <header class="sticky top-0 z-50 bg-white border-b border-gray-100">
        <div class="container mx-auto px-4">
            <div class="w-full h-28 lg:h-[75px] flex items-center justify-between overflow-hidden relative">

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
                <a href="/" class="lg:relative lg:-left-9 lg:-top-1 lg:z-20 lg:h-[100px] lg:flex lg:items-center
                                   absolute left-0 right-0 mx-auto w-40 h-[100px] pt-4 overflow-hidden z-20 lg:pt-0 lg:mx-0 lg:w-auto">
                    <img alt="BeArtShare Logo" src="{{ asset('images/logo.svg') }}" class="w-40 lg:h-[100px] relative" width="250" height="100">
                    <p class="text-xs ml-1 opacity-60 rounded-full bg-brand-grey250 px-2 text-brand-black100 hidden lg:block whitespace-nowrap">Yeni çağın sanat galerisi</p>
                </a>

                <!-- Mobile: Right Icons (Cart & Menu) -->
                <div class="absolute right-2 top-6 lg:hidden flex items-center z-20">
                    <a href="{{ route('cart') }}" class="mr-4 relative z-20">
                        @livewire('cart-icon')
                    </a>
                    <button id="mobile-menu-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="16" viewBox="0 0 24 16">
                            <g data-name="Group 61">
                                <path data-name="Line 17" style="fill:none;stroke:#14171c;stroke-miterlimit:10" d="M0 0h24" transform="translate(0 .5)"/>
                                <path data-name="Line 18" transform="translate(0 8)" style="fill:none;stroke:#14171c;stroke-miterlimit:10" d="M0 0h24"/>
                                <path data-name="Line 19" transform="translate(0 15.5)" style="fill:none;stroke:#14171c;stroke-miterlimit:10" d="M0 0h24"/>
                            </g>
                        </svg>
                    </button>
                </div>

                <!-- Desktop: Full Navigation -->
                <div class="flex-1 h-28 lg:h-[75px] relative hidden lg:flex">
                    <!-- Navigation -->
                    <div class="absolute bottom-2 right-0 flex text-xs text-left">
                        <div class="flex items-center mr-5">
                            <a href="{{ route('artworks') }}" class="transition-all ml-2 p-2 mr-2 link-underline">Eserler</a>
                            <a href="{{ route('about') }}" class="transition-all ml-2 mr-2 p-2 link-underline">Biz Kimiz</a>
                            <a href="{{ route('artists') }}" class="transition-all ml-2 mr-2 p-2 link-underline">Sanatçılar</a>
                            <a href="#" class="transition-all ml-2 mr-2 p-2 link-underline">ArtPuan</a>
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
                            <a href="{{ route('cart') }}" class="m-4 mr-0 leading-6 flex items-center">
                                Sepetim
                                <span class="ml-1 w-[25px] h-[25px] text-center rounded-full border border-brand-grey300 flex items-center justify-center text-xs">
                                    @livewire('cart-icon')
                                </span>
                            </a>
                        </div>
                    </div>
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
                    <a href="/" class="block mb-4 w-40">
                        <img src="{{ asset('images/logo.svg') }}" alt="BeArtShare" class="w-full h-auto brightness-0 invert">
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
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold mb-4">Yardım</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition">SSS</a></li>
                        <li><a href="#" class="hover:text-white transition">İletişim</a></li>
                        <li><a href="#" class="hover:text-white transition">Kargo ve Teslimat</a></li>
                        <li><a href="#" class="hover:text-white transition">İade Politikası</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold mb-4">İletişim</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                            </svg>
                            <span>info@beartshare.com</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                            </svg>
                            <span>+90 (212) 123 45 67</span>
                        </li>
                    </ul>

                    <div class="flex space-x-4 mt-4">
                        <a href="https://instagram.com/beartshare" target="_blank" class="text-gray-400 hover:text-white transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073z"/>
                            </svg>
                        </a>
                        <a href="https://wa.me/905102216413" target="_blank" class="text-gray-400 hover:text-white transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400 text-sm">
                <p>&copy; {{ date('Y') }} BeArtShare. Tüm hakları saklıdır.</p>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
