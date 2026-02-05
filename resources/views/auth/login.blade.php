<x-layouts.app title="Giriş Yap - BeArtShare">
    <div class="min-h-[60vh] flex items-center justify-center py-12">
        <div class="w-full max-w-sm px-4">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-light text-brand-black100">Giriş <span class="font-semibold">Yap</span></h1>
                <p class="text-gray-400 text-xs mt-2">Hesabınıza giriş yapın</p>
            </div>

            <form class="space-y-4">
                <div>
                    <label class="block text-xs text-gray-500 mb-1.5">E-posta</label>
                    <input type="email" class="w-full border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-brand-black100 transition" placeholder="ornek@email.com" required>
                </div>

                <div>
                    <label class="block text-xs text-gray-500 mb-1.5">Şifre</label>
                    <input type="password" class="w-full border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-brand-black100 transition" placeholder="••••••••" required>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" class="rounded border-gray-300 text-brand-black100 focus:ring-brand-black100">
                        <span class="ml-2 text-gray-400 text-xs">Beni hatırla</span>
                    </label>
                    <a href="#" class="text-xs text-gray-400 hover:text-brand-black100 transition link-underline pb-0.5">Şifremi unuttum</a>
                </div>

                <button type="submit" class="w-full bg-brand-black100 hover:bg-black text-white py-3 text-sm font-medium transition">
                    Giriş Yap
                </button>
            </form>

            <div class="text-center mt-8">
                <p class="text-gray-400 text-xs">
                    Hesabınız yok mu?
                    <a href="{{ route('register') }}" class="text-brand-black100 font-medium hover:underline">Kayıt Ol</a>
                </p>
            </div>
        </div>
    </div>
</x-layouts.app>
