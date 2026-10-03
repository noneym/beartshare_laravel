<div>
    <!-- Page Header -->
    <section class="bg-brand-black100 py-16">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl md:text-4xl font-semibold text-white">Eserler</h1>
            <p class="text-white/50 text-sm mt-2">BeArtShare koleksiyonundaki tüm eserleri keşfedin</p>
        </div>
    </section>

    <div class="container mx-auto px-4 py-10">
        <!-- Filters -->
        <div class="border-b border-gray-100 pb-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
                <!-- Search -->
                <div class="lg:col-span-2 relative">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Eser veya sanatçı ara..."
                        class="w-full border border-gray-200 px-4 py-2.5 pl-10 text-sm focus:outline-none focus:border-brand-black100 transition"
                    >
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <!-- Artist Filter (searchable; kept in sync with artistId, Turkish letters match their plain forms) -->
                <div class="relative" wire:ignore
                     x-data="{
                        open: false,
                        q: '',
                        active: 0,
                        selected: $wire.entangle('artistId').live,
                        artists: @js($artists->map(fn ($a) => ['id' => (string) $a->id, 'name' => $a->name])->values()),
                        norm(s) { return (s || '').toLocaleLowerCase('tr').normalize('NFD').replace(/[̀-ͯ]/g, '').replace(/ı/g, 'i'); },
                        get label() { const a = this.artists.find((x) => x.id === String(this.selected ?? '')); return a ? a.name : 'Tüm Sanatçılar'; },
                        get list() { const n = this.norm(this.q.trim()); return n ? this.artists.filter((a) => this.norm(a.name).includes(n)) : this.artists; },
                        toggle() { this.open = !this.open; if (this.open) { this.q = ''; this.active = 0; this.$nextTick(() => this.$refs.q.focus()); } },
                        pick(id) { this.open = false; if (String(this.selected ?? '') !== id) this.selected = id; },
                        move(d) { this.active = Math.max(0, Math.min(this.list.length, this.active + d)); },
                     }"
                     @click.outside="open = false"
                     @keydown.escape.prevent="open = false">
                    <button type="button" @click="toggle()"
                            class="w-full border border-gray-200 px-3 py-2.5 text-sm text-left bg-white flex items-center justify-between gap-2 focus:outline-none focus:border-brand-black100 transition">
                        <span class="truncate" x-text="label">{{ $artists->firstWhere('id', $artistId)?->name ?? 'Tüm Sanatçılar' }}</span>
                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" style="display: none"
                         class="absolute z-30 mt-1 w-full min-w-[240px] bg-white border border-gray-200 shadow-lg">
                        <input x-ref="q" x-model="q" type="text" placeholder="Sanatçı ara..." autocomplete="off"
                               @input="active = list.length ? 1 : 0"
                               @keydown.down.prevent="move(1)"
                               @keydown.up.prevent="move(-1)"
                               @keydown.enter.prevent="pick(active === 0 ? '' : list[active - 1].id)"
                               class="w-full border-b border-gray-100 px-3 py-2.5 text-sm focus:outline-none">
                        <ul class="max-h-64 overflow-y-auto py-1 text-sm">
                            <li>
                                <button type="button" @click="pick('')" @mouseenter="active = 0"
                                        :class="active === 0 ? 'bg-gray-50' : ''"
                                        class="w-full text-left px-3 py-2 text-gray-500">Tüm Sanatçılar</button>
                            </li>
                            <template x-for="(a, i) in list" :key="a.id">
                                <li>
                                    <button type="button" @click="pick(a.id)" @mouseenter="active = i + 1"
                                            :class="{ 'bg-gray-50': active === i + 1, 'font-medium text-brand-black100': a.id === String(selected ?? '') }"
                                            class="w-full text-left px-3 py-2" x-text="a.name"></button>
                                </li>
                            </template>
                            <li x-show="q.trim() && !list.length" class="px-3 py-2.5 text-gray-400">Sonuç bulunamadı</li>
                        </ul>
                    </div>
                </div>

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
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-10">
            @forelse($artworks as $artwork)
                <div class="group" wire:key="artwork-{{ $artwork->id }}">
                    <a href="{{ route('artwork.detail', $artwork->slug) }}" class="block">
                        <div class="relative bg-gray-50 overflow-hidden aspect-[4/3] mb-4">
                            @if($artwork->is_sold)
                                <span class="absolute top-3 left-3 bg-red-500 text-white text-[10px] px-3 py-1 z-10 uppercase tracking-wider">Satıldı</span>
                            @elseif($artwork->is_reserved)
                                <span class="absolute top-3 left-3 bg-amber-500 text-white text-[10px] px-3 py-1 z-10 uppercase tracking-wider">Rezerve</span>
                            @endif
                            @if($artwork->first_image)
                                <img src="{{ $artwork->first_image_url }}" alt="{{ $artwork->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                                    <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-all duration-300"></div>
                        </div>
                    </a>

                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0 pr-4">
                            <h3 class="font-medium text-brand-black100 text-sm truncate">{{ $artwork->artist->name }}</h3>
                            <p class="text-gray-500 text-xs mt-0.5">{{ $artwork->artist->life_span }}</p>
                            <p class="text-gray-400 text-xs mt-1 truncate">{{ $artwork->title }}</p>
                            <p class="text-gray-300 text-[10px] mt-0.5">{{ $artwork->technique }}, {{ $artwork->year }}</p>
                            <p class="text-gray-300 text-[10px]">{{ $artwork->dimensions }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="font-medium text-brand-black100 text-sm">{{ $artwork->formatted_price_tl }}</p>
                            <p class="text-gray-400 text-[10px]">{{ $artwork->formatted_price_usd }}</p>
                            <x-credit-card-badge :artwork="$artwork" />
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16">
                    <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-gray-400 text-sm">Aradığınız kriterlere uygun eser bulunamadı.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-12">
            {{ $artworks->links() }}
        </div>
    </div>
</div>
