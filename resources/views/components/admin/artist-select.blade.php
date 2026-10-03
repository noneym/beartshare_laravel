@props(['artists', 'selected' => null, 'name' => 'artist_id', 'placeholder' => 'Sanatci secin'])

{{-- Aranabilir sanatçı seçimi: yazdıkça liste daralır, Türkçe harfler sade karşılıklarıyla eşleşir (ş→s, ı→i...) --}}
<div class="relative"
     x-data="{
        open: false,
        q: '',
        active: 0,
        selected: @js($selected ? (string) $selected : ''),
        artists: @js($artists->map(fn ($a) => ['id' => (string) $a->id, 'name' => $a->name])->values()),
        norm(s) { return (s || '').toLocaleLowerCase('tr').normalize('NFD').replace(/[̀-ͯ]/g, '').replace(/ı/g, 'i'); },
        get label() { const a = this.artists.find((x) => x.id === this.selected); return a ? a.name : @js($placeholder); },
        get list() { const n = this.norm(this.q.trim()); return n ? this.artists.filter((a) => this.norm(a.name).includes(n)) : this.artists; },
        toggle() { this.open = !this.open; if (this.open) { this.q = ''; this.active = 0; this.$nextTick(() => this.$refs.q.focus()); } },
        pick(id) { this.selected = id; this.open = false; },
        move(d) { this.active = Math.max(0, Math.min(this.list.length - 1, this.active + d)); },
     }"
     @click.outside="open = false"
     @keydown.escape.prevent="open = false">
    <input type="hidden" name="{{ $name }}" :value="selected" value="{{ $selected }}">
    <button type="button" @click="toggle()"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-left bg-white flex items-center justify-between gap-2 focus:outline-none focus:border-primary">
        <span class="truncate" :class="selected ? '' : 'text-gray-400'" x-text="label">{{ $artists->firstWhere('id', $selected)?->name ?? $placeholder }}</span>
        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </button>
    <div x-show="open" style="display: none"
         class="absolute z-30 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg">
        <input x-ref="q" x-model="q" type="text" placeholder="Sanatci ara..." autocomplete="off"
               @input="active = 0"
               @keydown.down.prevent="move(1)"
               @keydown.up.prevent="move(-1)"
               @keydown.enter.prevent="list[active] && pick(list[active].id)"
               class="w-full border-b border-gray-200 px-4 py-2 text-sm focus:outline-none rounded-t-lg">
        <ul class="max-h-64 overflow-y-auto py-1 text-sm">
            <template x-for="(a, i) in list" :key="a.id">
                <li>
                    <button type="button" @click="pick(a.id)" @mouseenter="active = i"
                            :class="{ 'bg-gray-100': active === i, 'font-medium text-primary': a.id === selected }"
                            class="w-full text-left px-4 py-1.5" x-text="a.name"></button>
                </li>
            </template>
            <li x-show="!list.length" class="px-4 py-2 text-gray-400">Sonuc yok</li>
        </ul>
    </div>
</div>
