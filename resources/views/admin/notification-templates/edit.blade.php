<x-admin.layouts.app>
    <x-slot name="title">{{ $template['name'] }} · Bildirim Şablonu</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.notification-templates.index') }}" class="text-sm text-gray-600 hover:text-gray-900">&larr; Bildirim Şablonları</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-2">{{ $template['name'] }}</h1>
        <p class="text-sm text-gray-500 mt-1">{{ $template['description'] }}</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg px-4 py-3 mb-6">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3 mb-6">
            @foreach($errors->all() as $error) <div>{{ $error }}</div> @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start"
         x-data="{
            sms: @js(old('sms_body', $template['sms'] ?? '')),
            subject: @js(old('email_subject', $template['email_subject'] ?? '')),
            body: @js(old('email_body', $template['email_body'] ?? '')),
            focus: 'sms',
            smsPreview: '', smsLength: 0, emailPreview: '',
            timer: null,
            insert(name) {
                const ref = this.$refs[this.focus];
                if (!ref) return;
                const open = String.fromCharCode(123, 123), close = String.fromCharCode(125, 125);
                const tag = open + name + close;
                const s = ref.selectionStart ?? ref.value.length, e = ref.selectionEnd ?? ref.value.length;
                ref.value = ref.value.slice(0, s) + tag + ref.value.slice(e);
                ref.dispatchEvent(new Event('input'));
                ref.focus(); ref.selectionStart = ref.selectionEnd = s + tag.length;
            },
            preview() {
                clearTimeout(this.timer);
                this.timer = setTimeout(async () => {
                    const post = (text, html) => fetch(@js(route('admin.notification-templates.preview')), {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': @js(csrf_token()) },
                        body: JSON.stringify({ text, html }),
                    }).then((r) => r.json());
                    @if($template['has_sms'])
                        const s = await post(this.sms, false); this.smsPreview = s.result; this.smsLength = s.length;
                    @endif
                    @if($template['has_email'])
                        const m = await post(this.body, true); this.emailPreview = m.result;
                    @endif
                }, 300);
            },
         }"
         x-init="preview(); $watch('sms', () => preview()); $watch('body', () => preview())">

        <form method="POST" action="{{ route('admin.notification-templates.update', $template['key']) }}" class="lg:col-span-2 space-y-6">
            @csrf
            @method('PUT')

            @if($template['has_sms'])
                <div class="bg-white rounded-lg shadow p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="font-semibold text-gray-800">SMS</h2>
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="sms_enabled" value="1" {{ old('sms_enabled', $template['sms_enabled']) ? 'checked' : '' }} class="rounded border-gray-300 text-primary focus:ring-primary">
                            Gönderilsin
                        </label>
                    </div>
                    <textarea name="sms_body" x-ref="sms" x-model="sms" @focus="focus = 'sms'" rows="6"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:border-primary"></textarea>
                    <div class="mt-3 bg-gray-50 border border-gray-200 rounded-lg p-3">
                        <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                            <span>Önizleme (örnek değerlerle)</span>
                            <span><span x-text="smsLength"></span> karakter · yaklaşık <span x-text="Math.max(1, Math.ceil(smsLength / 155))"></span> SMS</span>
                        </div>
                        <p class="text-sm text-gray-800 whitespace-pre-line" x-text="smsPreview"></p>
                    </div>
                </div>
            @endif

            @if($template['has_email'])
                <div class="bg-white rounded-lg shadow p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="font-semibold text-gray-800">E-posta</h2>
                        @if($template['key'] !== 'password_reset')
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                <input type="checkbox" name="email_enabled" value="1" {{ old('email_enabled', $template['email_enabled']) ? 'checked' : '' }} class="rounded border-gray-300 text-primary focus:ring-primary">
                                Gönderilsin
                            </label>
                        @else
                            <span class="text-xs text-gray-500">Her zaman gönderilir</span>
                        @endif
                    </div>
                    <label class="block text-xs text-gray-500 mb-1">Konu</label>
                    <input type="text" name="email_subject" x-ref="subject" x-model="subject" @focus="focus = 'subject'"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
                    <label class="block text-xs text-gray-500 mt-4 mb-1">Metin (HTML: &lt;p&gt; paragraf, &lt;strong&gt; kalın, &lt;a class="button"&gt; buton)</label>
                    <textarea name="email_body" x-ref="body" x-model="body" @focus="focus = 'body'" rows="12"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:border-primary"></textarea>
                    <p class="text-xs text-gray-500 mt-2">Üstteki BeArtShare başlığı ve alttaki şirket bilgisi otomatik eklenir.</p>
                    <div class="mt-3 border border-gray-200 rounded-lg overflow-hidden">
                        <div class="text-xs text-gray-500 bg-gray-50 px-3 py-2 border-b border-gray-200">Önizleme (örnek değerlerle)</div>
                        <iframe class="w-full h-[420px] bg-white" :srcdoc="emailPreview"></iframe>
                    </div>
                </div>
            @endif

            <div class="flex items-center gap-3">
                <button type="submit" class="bg-gray-900 text-white px-5 py-2 rounded-lg text-sm hover:bg-gray-800 transition">Kaydet</button>
                <a href="{{ route('admin.notification-templates.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Vazgeç</a>
            </div>
        </form>

        <div class="space-y-6 lg:sticky lg:top-6">
            <div class="bg-white rounded-lg shadow p-5">
                <h2 class="font-semibold text-gray-800 mb-1">Değişkenler</h2>
                <p class="text-xs text-gray-500 mb-3">Tıklayınca imlecin olduğu alana eklenir. Gönderimde gerçek değerle doldurulur.</p>
                <div class="space-y-1.5">
                    @foreach($template['variables'] as $name => $desc)
                        <button type="button" @click="insert(@js($name))" class="w-full text-left flex items-start gap-2 px-2 py-1.5 rounded hover:bg-gray-50">
                            @php $tag = '{' . '{' . $name . '}' . '}'; @endphp
                            <code class="text-xs bg-gray-100 text-gray-800 px-1.5 py-0.5 rounded flex-shrink-0">{{ $tag }}</code>
                            <span class="text-xs text-gray-500">{{ $desc }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            @if($template['customized'])
                <form method="POST" action="{{ route('admin.notification-templates.reset', $template['key']) }}"
                      onsubmit="return confirm('Bu şablondaki değişiklikler silinip varsayılan metne dönülsün mü?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-50 transition">Varsayılan metne dön</button>
                </form>
            @endif
        </div>
    </div>
</x-admin.layouts.app>
