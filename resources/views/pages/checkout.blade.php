<x-layouts.app title="Ödeme - BeArtShare">
    <div class="py-12">
        <div class="container mx-auto px-4">
            <h1 class="text-4xl font-bold text-gray-900 mb-8">Ödeme</h1>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Teslimat Bilgileri</h2>

                    <form class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 font-medium mb-2">Ad</label>
                                <input type="text" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:border-primary" required>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-medium mb-2">Soyad</label>
                                <input type="text" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:border-primary" required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-medium mb-2">E-posta</label>
                            <input type="email" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:border-primary" required>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Telefon</label>
                            <input type="tel" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:border-primary" required>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Adres</label>
                            <textarea rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:border-primary" required></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 font-medium mb-2">İl</label>
                                <input type="text" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:border-primary" required>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-medium mb-2">İlçe</label>
                                <input type="text" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:border-primary" required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Notlar (Opsiyonel)</label>
                            <textarea rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:border-primary"></textarea>
                        </div>
                    </form>
                </div>

                <div>
                    <div class="bg-gray-50 rounded-xl p-6 sticky top-24">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">Sipariş Özeti</h2>

                        <div class="text-center py-8 text-gray-500">
                            Sepetiniz boş
                        </div>

                        <button class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-4 rounded-xl font-medium transition" disabled>
                            Siparişi Tamamla
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
