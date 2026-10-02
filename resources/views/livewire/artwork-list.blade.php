<div>
    <x-page-header title="Eserler" subtitle="BeArtShare koleksiyonundaki tüm eserleri keşfedin" />

    <div class="container mx-auto px-4 py-10">
        <!-- Filters -->
        <div class="border-b border-gray-100 pb-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
                <!-- Search -->
                <div class="lg:col-span-2 relative">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Eser veya sanatçı ara"
                        class="w-full border border-gray-200 px-4 py-2.5 pl-10 text-sm focus:outline-none focus:border-brand-black100 transition"
                    >
                    <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>

                <!-- Artist Filter -->
                <select
                    wire:model.live="artistId"
                    class="w-full border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:border-brand-black100 bg-white transition"
                >
                    <option value="">Tüm Sanatçılar</option>
                    @foreach($artists as $artist)
                        <option value="{{ $artist->id }}">{{ $artist->name }}</option>
                    @endforeach
                </select>

                <!-- Satılan Filtresi -->
                <select
                    wire:model.live="soldFilter"
                    class="w-full border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:border-brand-black100 bg-white transition"
                >
                    <option value="">Tüm Eserler</option>
                    <option value="hide">Satılanları Gösterme</option>
                    <option value="only">Sadece Satılanlar</option>
                </select>

                <!-- Sort -->
                <select
                    wire:model.live="sortBy"
                    class="w-full border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:border-brand-black100 bg-white transition"
                >
                    <option value="latest">En Yeni</option>
                    <option value="oldest">En Eski</option>
                    <option value="price_asc">Fiyat (Artan)</option>
                    <option value="price_desc">Fiyat (Azalan)</option>
                    <option value="name">İsim (A-Z)</option>
                </select>
            </div>
            <div class="flex items-center justify-between mt-3 flex-wrap gap-2">
                <p class="text-gray-400 text-xs">{{ $artworks->total() }} eser listeleniyor</p>
            </div>
        </div>

        <!-- Artworks Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-10">
            @forelse($artworks as $artwork)
                <div wire:key="artwork-{{ $artwork->id }}">
                    <x-artwork-card :artwork="$artwork" />
                </div>
            @empty
                <div class="col-span-full text-center py-16">
                    <i class="ph ph-image text-4xl text-gray-300"></i>
                    <p class="text-gray-500 mt-3">Aradığınız kriterlere uygun eser bulunamadı.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-12">
            {{ $artworks->links() }}
        </div>
    </div>
</div>
