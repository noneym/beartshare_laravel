@props(['name' => 'uploaded_images[]', 'multiple' => true, 'kind' => 'image', 'maxMb' => 99, 'attachUrl' => null])

{{--
    Görselleri seçildiği anda tek tek yükler (her dosya ayrı istek: büyük dosyalar form gönderim sınırına takılmaz).
    Her dosya için önizleme + ilerleme çubuğu; yüklenen yol formda gizli alan olarak gönderilir.
    Yükleme sürerken form gönderilemez.
    attachUrl verilirse (eser düzenleme): yüklenen görsel hemen esere eklenir ve
    'artwork-image-added' olayıyla mevcut görsellere düşer; formda gizli alan gönderilmez.
--}}
<div x-data="{
        items: [],
        multiple: @js((bool) $multiple),
        maxBytes: {{ (int) $maxMb }} * 1024 * 1024,
        get busy() { return this.items.some((i) => i.status === 'uploading'); },
        size(b) { return b >= 1048576 ? (b / 1048576).toFixed(1) + ' MB' : Math.round(b / 1024) + ' KB'; },
        pick(e) {
            const files = Array.from(e.target.files || []);
            e.target.value = '';
            if (!this.multiple) { this.items = []; }
            for (const file of (this.multiple ? files : files.slice(0, 1))) { this.add(file); }
        },
        add(file) {
            const item = { id: Math.random().toString(36).slice(2), name: file.name, bytes: file.size, progress: 0,
                           status: 'uploading', path: null, error: null, preview: URL.createObjectURL(file) };
            this.items.push(item);
            const it = this.items[this.items.length - 1];
            if (!file.type.startsWith('image/')) { it.status = 'error'; it.error = 'Görsel dosyası değil'; return; }
            if (file.size > this.maxBytes) { it.status = 'error'; it.error = 'En fazla {{ (int) $maxMb }} MB'; return; }

            const data = new FormData();
            data.append('file', file);
            data.append('kind', @js($kind));
            const xhr = new XMLHttpRequest();
            xhr.open('POST', @js($attachUrl ?: route('admin.artworks.upload-image')));
            xhr.setRequestHeader('X-CSRF-TOKEN', @js(csrf_token()));
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.upload.onprogress = (ev) => { if (ev.lengthComputable) it.progress = Math.round(ev.loaded / ev.total * 100); };
            xhr.onload = () => {
                let res = {};
                try { res = JSON.parse(xhr.responseText); } catch (_) {}
                if (xhr.status >= 200 && xhr.status < 300 && res.path) {
                    it.status = 'done'; it.progress = 100; it.path = res.path;
                    if (@js((bool) $attachUrl)) {
                        window.dispatchEvent(new CustomEvent('artwork-image-added', { detail: { path: res.path, url: res.url || it.preview } }));
                        setTimeout(() => { const k = this.items.indexOf(it); if (k > -1) this.items.splice(k, 1); }, 1500);
                    }
                }
                else if (xhr.status === 413) { it.status = 'error'; it.error = 'Dosya sunucu sınırını aşıyor'; }
                else { it.status = 'error'; it.error = (res.errors && res.errors.file && res.errors.file[0]) || res.message || ('Yükleme hatası (' + xhr.status + ')'); }
            };
            xhr.onerror = () => { it.status = 'error'; it.error = 'Bağlantı hatası'; };
            it.xhr = xhr;
            xhr.send(data);
        },
        remove(i) { const it = this.items[i]; if (it.status === 'uploading' && it.xhr) it.xhr.abort(); this.items.splice(i, 1); },
     }"
     x-init="$el.closest('form')?.addEventListener('submit', (e) => {
        if (busy) { e.preventDefault(); alert('Görseller hâlâ yükleniyor, lütfen yükleme bitince kaydedin.'); }
     })">

    <label class="flex flex-col items-center justify-center gap-1 w-full border-2 border-dashed border-gray-300 rounded-lg px-4 py-5 text-center cursor-pointer hover:border-primary hover:bg-gray-50 transition">
        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M16 8l-4-4m0 0L8 8m4-4v12"/></svg>
        <span class="text-sm text-gray-700">{{ $multiple ? 'Görsel seçin (birden fazla seçilebilir)' : 'Görsel seçin' }}</span>
        <span class="text-xs text-gray-500">JPG, PNG, WEBP · dosya başına en fazla {{ (int) $maxMb }} MB · seçince hemen yüklenir</span>
        <input type="file" accept="image/*" class="sr-only" @change="pick($event)" {{ $multiple ? 'multiple' : '' }}>
    </label>

    <div class="mt-3 space-y-2" x-show="items.length" x-cloak>
        <template x-for="(it, i) in items" :key="it.id">
            <div class="flex items-center gap-3 border border-gray-200 rounded-lg p-2">
                <img :src="it.preview" alt="" class="w-12 h-12 object-cover rounded bg-gray-100 flex-shrink-0">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2 text-xs">
                        <span class="truncate text-gray-800" x-text="it.name"></span>
                        <span class="flex-shrink-0 text-gray-500" x-text="size(it.bytes)"></span>
                    </div>
                    <div class="mt-1.5 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-200"
                             :class="it.status === 'error' ? 'bg-red-500' : (it.status === 'done' ? 'bg-green-500' : 'bg-primary')"
                             :style="'width:' + (it.status === 'error' ? 100 : it.progress) + '%'"></div>
                    </div>
                    <p class="mt-1 text-[11px]"
                       :class="it.status === 'error' ? 'text-red-600' : (it.status === 'done' ? 'text-green-600' : 'text-gray-500')"
                       x-text="it.status === 'error' ? it.error : (it.status === 'done' ? @js($attachUrl ? 'Yüklendi, esere eklendi' : 'Yüklendi') : ('Yükleniyor… %' + it.progress))"></p>
                </div>
                <button type="button" @click="remove(i)" title="Kaldır" class="flex-shrink-0 w-7 h-7 rounded-full text-gray-400 hover:bg-red-50 hover:text-red-600 transition">&times;</button>
                <template x-if="it.status === 'done' && !@js((bool) $attachUrl)">
                    <input type="hidden" name="{{ $name }}" :value="it.path">
                </template>
            </div>
        </template>
    </div>
</div>
