<x-admin.layouts.app title="Eser Duzenle">
    <div class="mb-8">
        <a href="{{ route('admin.artworks.index') }}" class="text-gray-600 hover:text-gray-900">&larr; Eserlere Don</a>
    </div>

    <div class="max-w-2xl">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Eser Duzenle: {{ $artwork->title }}</h1>

        <form action="{{ route('admin.artworks.update', $artwork) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm p-6">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sanatci *</label>
                    <x-admin.artist-select :artists="$artists" :selected="old('artist_id', $artwork->artist_id)" />
                    @error('artist_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                    <select name="category_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary">
                        <option value="">Kategori secin</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $artwork->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Eser Adi *</label>
                    <input type="text" name="title" value="{{ old('title', $artwork->title) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary" required>
                    @error('title') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Aciklama</label>
                    <textarea name="description" rows="4" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary">{{ old('description', $artwork->description) }}</textarea>
                    @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Teknik</label>
                        <input type="text" name="technique" value="{{ old('technique', $artwork->technique) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary">
                        @error('technique') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Boyutlar</label>
                        <input type="text" name="dimensions" value="{{ old('dimensions', $artwork->dimensions) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary">
                        @error('dimensions') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ek Not <span class="text-gray-400 font-normal">(isteğe bağlı)</span></label>
                    <input type="text" name="extra_note" value="{{ old('extra_note', $artwork->extra_note) }}" placeholder="Ör. Eserin Evin Sanat Galerisi tarafından sertifikası mevcuttur" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary">
                    <p class="text-xs text-gray-500 mt-1">Boyut alanına yalnızca ölçü yazın; sertifika, literatür, edisyon gibi bilgiler buraya. Eser listelerinde ve eser sayfasında boyutun altında görünür.</p>
                    @error('extra_note') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Yil</label>
                    <input type="number" name="year" value="{{ old('year', $artwork->year) }}" min="1800" max="{{ date('Y') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary">
                    @error('year') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fiyat (TL) *</label>
                        @php $priceTl = old('price_tl', $artwork->price_tl); @endphp
                        <input type="text" inputmode="decimal" data-price-format value="{{ $priceTl ? number_format((float) $priceTl, 2, ',', '.') : '' }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary" placeholder="100.000" required>
                        <input type="hidden" name="price_tl" value="{{ $priceTl }}">
                        <p class="text-gray-400 text-xs mt-1">Örn. 100.000 veya 100.000,50</p>
                        @error('price_tl') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fiyat (USD)</label>
                        @php $priceUsd = old('price_usd', $artwork->price_usd); @endphp
                        <input type="text" inputmode="decimal" data-price-format value="{{ $priceUsd ? number_format((float) $priceUsd, 2, ',', '.') : '' }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary" placeholder="3.500">
                        <p class="text-gray-400 text-xs mt-1">TL fiyatı ve güncel TCMB kuru ile otomatik hesaplanır (saatlik güncellenir).</p>
                        <input type="hidden" name="price_usd" value="{{ $priceUsd }}">
                        <p class="text-gray-400 text-xs mt-1">Örn. 3.500 veya 3.500,50</p>
                        @error('price_usd') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                @include('admin.partials.price-format-script')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mevcut Görseller</label>
                    @php
                        $imageItems = collect($artwork->images ?? [])->map(fn ($p) => [
                            'path' => $p,
                            'url' => \App\Support\ImageUrl::make($p, 'thumb'),
                        ])->values();
                    @endphp
                    <input type="hidden" name="images_managed" value="1">
                    <div x-data="{
                            items: @js($imageItems),
                            drag: null,
                            move(from, to) {
                                if (to < 0 || to >= this.items.length || from === to) return;
                                const [it] = this.items.splice(from, 1);
                                this.items.splice(to, 0, it);
                            }
                         }" class="mb-4">
                        <template x-if="items.length === 0">
                            <p class="text-gray-500 text-sm">Görsel yok</p>
                        </template>
                        <div class="flex gap-3 flex-wrap">
                            <template x-for="(item, i) in items" :key="item.path">
                                <div class="relative w-28 group select-none"
                                     draggable="true"
                                     @dragstart="drag = i; $event.dataTransfer.effectAllowed = 'move'"
                                     @dragover.prevent
                                     @drop.prevent="move(drag, i); drag = null"
                                     :class="drag === i ? 'opacity-40' : ''">
                                    <input type="hidden" name="existing_images[]" :value="item.path">
                                    <img :src="item.url" alt="" class="w-28 h-28 object-cover rounded-lg border border-gray-200 cursor-move bg-gray-100">
                                    <span class="absolute top-1 left-1 bg-black/70 text-white text-[10px] px-1.5 py-0.5 rounded" x-text="i === 0 ? 'Ana' : (i + 1)"></span>
                                    <button type="button" @click="items.splice(i, 1)" title="Kaldır"
                                            class="absolute top-1 right-1 bg-white/90 text-red-600 w-6 h-6 rounded-full text-sm leading-none shadow hover:bg-red-600 hover:text-white transition">&times;</button>
                                    <div class="flex justify-between mt-1">
                                        <button type="button" @click="move(i, i - 1)" :disabled="i === 0"
                                                class="text-xs px-2 py-0.5 border border-gray-200 rounded disabled:opacity-30 hover:bg-gray-50">&larr;</button>
                                        <button type="button" @click="move(i, i + 1)" :disabled="i === items.length - 1"
                                                class="text-xs px-2 py-0.5 border border-gray-200 rounded disabled:opacity-30 hover:bg-gray-50">&rarr;</button>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Sürükleyip bırakarak ya da oklarla sıralayın. İlk görsel ana görseldir (kapak fotoğrafı yoksa listelerde de o görünür). Değişiklikler "Güncelle" ile kaydedilir.</p>
                    </div>

                    <label class="block text-sm font-medium text-gray-700 mb-2">Yeni Gorsel Ekle</label>
                    <input type="file" name="images[]" multiple accept="image/*" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary">
                    <p class="text-sm text-gray-500 mt-1">Birden fazla gorsel secebilirsiniz</p>
                    @error('images.*') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div x-data="{ remove: false }">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kapak Fotoğrafı <span class="text-gray-400 font-normal">(isteğe bağlı)</span></label>
                    @if($artwork->cover_image)
                        <div class="flex items-start gap-4 mb-3" :class="remove ? 'opacity-40' : ''">
                            <img src="{{ \App\Support\ImageUrl::make($artwork->cover_image, 'thumb') }}" alt="" class="w-28 h-28 object-cover rounded-lg border border-gray-200 bg-gray-100">
                            <label class="flex items-center text-sm text-red-600 mt-1">
                                <input type="checkbox" name="remove_cover" value="1" x-model="remove" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                <span class="ml-2">Kapak fotoğrafını kaldır</span>
                            </label>
                        </div>
                    @endif
                    <input type="file" name="cover" accept="image/*" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary">
                    <p class="text-sm text-gray-500 mt-1">Yüklenirse eser listelerinde (ana sayfa, eserler, sanatçı, favoriler) ilk görselin yerine bu gösterilir. Eser sayfasındaki görseller değişmez.</p>
                    @error('cover') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ $artwork->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-primary focus:ring-primary">
                        <span class="ml-2 text-gray-700">Aktif</span>
                    </label>
                    <div x-data="{ featured: {{ $artwork->is_featured ? 'true' : 'false' }} }" class="space-y-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_featured" value="1" x-model="featured" {{ $artwork->is_featured ? 'checked' : '' }} class="rounded border-gray-300 text-primary focus:ring-primary">
                        <span class="ml-2 text-gray-700">Öne Çıkar</span>
                    </label>
                    <div class="flex items-center gap-2 ml-6 -mt-1" x-show="featured" x-cloak>
                        <label for="featured_weight" class="text-sm text-gray-600">Sıra ağırlığı</label>
                        <input type="number" id="featured_weight" name="featured_weight" value="{{ old('featured_weight', $artwork->featured_weight) }}" min="0" max="100000" class="w-24 border border-gray-300 rounded-lg px-3 py-1 text-sm focus:outline-none focus:border-primary">
                        <span class="text-xs text-gray-500">Yüksek olan ana sayfada önce gösterilir</span>
                    </div>
                    @error('featured_weight') <p class="text-red-500 text-sm ml-6">{{ $message }}</p> @enderror
                    </div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_sold" value="1" {{ $artwork->is_sold ? 'checked' : '' }} class="rounded border-gray-300 text-primary focus:ring-primary">
                        <span class="ml-2 text-gray-700">Satildi</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="allow_credit_card" value="1" {{ $artwork->allow_credit_card ? 'checked' : '' }} class="rounded border-gray-300 text-primary focus:ring-primary">
                        <span class="ml-2 text-gray-700">Kredi Kartı ile Alınabilir</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="hide_from_gallery" value="1" {{ $artwork->hide_from_gallery ? 'checked' : '' }} class="rounded border-gray-300 text-primary focus:ring-primary">
                        <span class="ml-2 text-gray-700">Sanal Galeri Dışı Bırak</span>
                        <span class="ml-2 text-xs text-gray-400">(3D sergilerde gösterilmez)</span>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Satış Notu</label>
                    <textarea name="sale_note" rows="3" placeholder="Eser sayfasında fiyatın altında gösterilir (ör. komisyon faturası / KDV bilgisi)" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary">{{ old('sale_note', $artwork->sale_note) }}</textarea>
                    @error('sale_note') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="border-t border-gray-200 pt-6 space-y-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Yalnızca Admin Görür</p>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Asıl Sahibi (Konsinye)</label>
                        <input type="text" name="owner_name" value="{{ old('owner_name', $artwork->owner_name) }}" placeholder="Eser konsinye ise sahibinin adı" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary">
                        <p class="text-gray-400 text-xs mt-1">Sitede gösterilmez.</p>
                        @error('owner_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Admin Notu</label>
                        <textarea name="admin_notes" rows="3" placeholder="Dahili not (anlaşma şartları, iletişim, vb.)" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-primary">{{ old('admin_notes', $artwork->admin_notes) }}</textarea>
                        @error('admin_notes') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-4">
                    <a href="{{ route('admin.artworks.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Iptal</a>
                    <button type="submit" class="px-6 py-2 bg-primary hover:bg-yellow-600 text-white rounded-lg font-medium">Guncelle</button>
                </div>
            </div>
        </form>

        <!-- Favoriye Ekleyenler -->
        <div class="bg-white rounded-xl shadow-sm mt-8">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                    Favoriye Ekleyenler
                </h2>
                <span class="text-xs text-gray-400 font-normal">{{ $favoritedBy->count() }} kullanici</span>
            </div>
            @if($favoritedBy->count() > 0)
                <div class="divide-y divide-gray-100">
                    @foreach($favoritedBy as $favUser)
                        <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-500">
                                    {{ strtoupper(mb_substr($favUser->name, 0, 1)) }}
                                </div>
                                <div>
                                    <a href="{{ route('admin.users.show', $favUser) }}" class="text-sm font-medium text-gray-900 hover:text-blue-600 transition">{{ $favUser->name }}</a>
                                    <p class="text-xs text-gray-500">{{ $favUser->email }}</p>
                                </div>
                            </div>
                            <span class="text-xs text-gray-400">{{ $favUser->pivot->created_at ? \Carbon\Carbon::parse($favUser->pivot->created_at)->format('d.m.Y H:i') : '' }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="px-5 py-8 text-center">
                    <p class="text-sm text-gray-400">Bu eseri henuz kimse favoriye eklemedi.</p>
                </div>
            @endif
        </div>
    </div>
</x-admin.layouts.app>
