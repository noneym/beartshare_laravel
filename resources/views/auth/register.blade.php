<x-layouts.app title="Kayıt Ol - BeArtShare">
    <div class="min-h-[60vh] flex items-center justify-center py-12">
        <div class="w-full max-w-sm px-4">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-light text-brand-black100">Kayıt <span class="font-semibold">Ol</span></h1>
                <p class="text-gray-400 text-xs mt-2">BeArtShare ailesine katılın</p>
            </div>

            <form class="space-y-4">
                <div>
                    <label class="block text-xs text-gray-500 mb-1.5">Ad Soyad</label>
                    <input type="text" class="w-full border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-brand-black100 transition" placeholder="Adınız Soyadınız" required>
                </div>

                <div>
                    <label class="block text-xs text-gray-500 mb-1.5">E-posta</label>
                    <input type="email" class="w-full border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-brand-black100 transition" placeholder="ornek@email.com" required>
                </div>

                <div>
                    <label class="block text-xs text-gray-500 mb-1.5">Şifre</label>
                    <input type="password" class="w-full border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-brand-black100 transition" placeholder="••••••••" required>
                </div>

                <div>
                    <label class="block text-xs text-gray-500 mb-1.5">Şifre Tekrar</label>
                    <input type="password" class="w-full border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-brand-black100 transition" placeholder="••••••••" required>
                </div>

                <div>
                    <label class="flex items-start">
                        <input type="checkbox" class="rounded border-gray-300 text-brand-black100 focus:ring-brand-black100 mt-0.5" required>
                        <span class="ml-2 text-gray-400 text-xs leading-relaxed">
                            <a href="#" class="text-brand-black100 hover:underline">Kullanım şartlarını</a> ve
                            <a href="#" class="text-brand-black100 hover:underline">gizlilik politikasını</a> okudum, kabul ediyorum.
                        </span>
                    </label>
                </div>

                <button type="submit" class="w-full bg-brand-black100 hover:bg-black text-white py-3 text-sm font-medium transition">
                    Kayıt Ol
                </button>
            </form>

            <div class="text-center mt-8">
                <p class="text-gray-400 text-xs">
                    Zaten hesabınız var mı?
                    <a href="{{ route('login') }}" class="text-brand-black100 font-medium hover:underline">Giriş Yap</a>
                </p>
            </div>
        </div>
    </div>
</x-layouts.app>
