@props(['artists', 'name' => 'artist_id', 'label' => 'Sanatci'])

{{-- Aranabilir sanatçı filtresi (GET formlarında): yazarak daraltılır; Türkçe harfler sade karşılıklarıyla eşleşir --}}
<div class="min-w-[200px] relative"
     x-data="{
        open: false,
        q: '',
        active: 0,
        selected: @js(request($name) ? (string) request($name) : ''),
        artists: @js($artists->map(fn ($a) => ['id' => (string) $a->id, 'name' => $a->name])->values()),
        norm(s) { return (s || '').toLocaleLowerCase('tr').normalize('NFD').replace(/[̀-ͯ]/g, '').replace(/ı/g, 'i'); },
        get label() { const a = this.artists.find((x) => x.id === this.selected); return a ? a.name : 'Tum Sanatcilar'; },
        get list() { const n = this.norm(this.q.trim()); return n ? this.artists.filter((a) => this.norm(a.name).includes(n)) : this.artists; },
        toggle() { this.open = !this.open; if (this.open) { this.q = ''; this.active = 0; this.$nextTick(() => this.$refs.q.focus()); } },
        pick(id) { this.selected = id; this.open = false; },
        move(d) { this.active = Math.max(0, Math.min(this.list.length, this.active + d)); },
     }"
     @click.outside="open = false"
     @keydown.escape.prevent="open = false">
    <label class="block text-xs text-gray-500 mb-1">{{ $label }}</label>
    <input type="hidden" name="{{ $name }}" :value="selected">
    <button type="button" @click="toggle()"
            class="w-full border border-gray-300 rounded px-3 py-2 text-sm text-left bg-white flex items-center justify-between gap-2 focus:outline-none focus:ring-1 focus:ring-primary">
        <span class="truncate" x-text="label">{{ $artists->firstWhere('id', request($name))?->name ?? 'Tum Sanatcilar' }}</span>
        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </button>
    <div x-show="open" style="display: none"
         class="absolute z-30 mt-1 w-full min-w-[260px] bg-white border border-gray-200 rounded shadow-lg">
        <input x-ref="q" x-model="q" type="text" placeholder="Sanatci ara..." autocomplete="off"
               @input="active = list.length ? 1 : 0"
               @keydown.down.prevent="move(1)"
               @keydown.up.prevent="move(-1)"
               @keydown.enter.prevent="pick(active === 0 ? '' : list[active - 1].id)"
               class="w-full border-b border-gray-200 px-3 py-2 text-sm focus:outline-none">
        <ul class="max-h-64 overflow-y-auto py-1 text-sm">
            <li>
                <button type="button" @click="pick('')" @mouseenter="active = 0"
                        :class="active === 0 ? 'bg-gray-100' : ''"
                        class="w-full text-left px-3 py-1.5 text-gray-500">Tum Sanatcilar</button>
            </li>
            <template x-for="(a, i) in list" :key="a.id">
                <li>
                    <button type="button" @click="pick(a.id)" @mouseenter="active = i + 1"
                            :class="{ 'bg-gray-100': active === i + 1, 'font-medium text-primary': a.id === selected }"
                            class="w-full text-left px-3 py-1.5" x-text="a.name"></button>
                </li>
            </template>
            <li x-show="q.trim() && !list.length" class="px-3 py-2 text-gray-400">Sonuc yok</li>
        </ul>
    </div>
</div>
