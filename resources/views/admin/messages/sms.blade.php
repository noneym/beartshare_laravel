<x-admin.layouts.app title="SMS Gonder">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900">SMS Gonder</h1>
    </div>

    <form method="POST" action="{{ route('admin.messages.sms.send') }}" class="max-w-3xl">
        @csrf

        <!-- User Selection -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Alicilar</h2>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="selectAll()" class="text-xs text-blue-600 hover:text-blue-800">Tumunu Sec</button>
                    <span class="text-gray-300">|</span>
                    <button type="button" onclick="deselectAll()" class="text-xs text-gray-500 hover:text-gray-700">Temizle</button>
                </div>
            </div>

            @error('user_ids')
                <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-2 rounded mb-4">{{ $message }}</div>
            @enderror

            <!-- Search -->
            <div class="mb-3">
                <input type="text" id="userSearch" placeholder="Kullanici ara (ad, e-posta, telefon)..."
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary"
                       oninput="filterUsers()">
            </div>

            <!-- Selected Count -->
            <div class="mb-3 text-xs text-gray-500">
                Secili: <span id="selectedCount" class="font-semibold text-gray-700">0</span> kullanici
            </div>

            <!-- User List -->
            <div class="border border-gray-200 rounded max-h-64 overflow-y-auto divide-y divide-gray-100" id="userList">
                @foreach($users as $user)
                    <label class="flex items-center px-4 py-2.5 hover:bg-gray-50 cursor-pointer user-item" data-search="{{ strtolower($user->name . ' ' . $user->email . ' ' . $user->phone) }}">
                        <input type="checkbox" name="user_ids[]" value="{{ $user->id }}"
                               class="user-checkbox w-4 h-4 border-gray-300 rounded text-primary focus:ring-primary"
                               {{ in_array($user->id, old('user_ids', $preselected)) ? 'checked' : '' }}
                               onchange="updateCount()">
                        <div class="ml-3 flex-1 min-w-0">
                            <p class="text-sm text-gray-900 truncate">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $user->email }} {{ $user->phone ? '- ' . $user->phone : '' }}</p>
                        </div>
                        @if(!$user->phone)
                            <span class="text-xs text-red-400 ml-2 flex-shrink-0">Tel. yok</span>
                        @endif
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Message -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Mesaj</h2>

            @error('message')
                <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-2 rounded mb-4">{{ $message }}</div>
            @enderror

            <textarea name="message" rows="4" maxlength="480" id="smsMessage"
                      class="w-full border border-gray-300 rounded px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-primary resize-none"
                      placeholder="SMS mesajinizi yazin..."
                      oninput="updateCharCount()">{{ old('message') }}</textarea>
            <div class="flex items-center justify-between mt-2">
                <p class="text-xs text-gray-400">
                    <span id="charCount">0</span> / 480 karakter
                    (<span id="smsCount">0</span> SMS)
                </p>
                <p class="text-xs text-gray-400">Turkce karakter kullanilmaz (GSM 7-bit)</p>
            </div>

            <!-- Degisken Tagleri -->
            <div class="mt-3 pt-3 border-t border-gray-100">
                <p class="text-xs text-gray-500 mb-2">Degiskenler <span class="text-gray-400">(tikla ekle)</span></p>
                <div class="flex flex-wrap gap-1.5">
                    <button type="button" onclick="insertTag('{isim}')" class="inline-flex items-center gap-1 text-xs px-2.5 py-1 bg-blue-50 text-blue-700 rounded-full hover:bg-blue-100 transition border border-blue-200">
                        <span class="font-mono">{isim}</span> <span class="text-blue-400">Ad Soyad</span>
                    </button>
                    <button type="button" onclick="insertTag('{ad}')" class="inline-flex items-center gap-1 text-xs px-2.5 py-1 bg-blue-50 text-blue-700 rounded-full hover:bg-blue-100 transition border border-blue-200">
                        <span class="font-mono">{ad}</span> <span class="text-blue-400">Sadece Ad</span>
                    </button>
                    <button type="button" onclick="insertTag('{email}')" class="inline-flex items-center gap-1 text-xs px-2.5 py-1 bg-green-50 text-green-700 rounded-full hover:bg-green-100 transition border border-green-200">
                        <span class="font-mono">{email}</span>
                    </button>
                    <button type="button" onclick="insertTag('{telefon}')" class="inline-flex items-center gap-1 text-xs px-2.5 py-1 bg-green-50 text-green-700 rounded-full hover:bg-green-100 transition border border-green-200">
                        <span class="font-mono">{telefon}</span>
                    </button>
                    <button type="button" onclick="insertTag('{artpuan}')" class="inline-flex items-center gap-1 text-xs px-2.5 py-1 bg-purple-50 text-purple-700 rounded-full hover:bg-purple-100 transition border border-purple-200">
                        <span class="font-mono">{artpuan}</span> <span class="text-purple-400">Bakiye</span>
                    </button>
                    <button type="button" onclick="insertTag('{referans_kodu}')" class="inline-flex items-center gap-1 text-xs px-2.5 py-1 bg-orange-50 text-orange-700 rounded-full hover:bg-orange-100 transition border border-orange-200">
                        <span class="font-mono">{referans_kodu}</span>
                    </button>
                    <button type="button" onclick="insertTag('{referans_linki}')" class="inline-flex items-center gap-1 text-xs px-2.5 py-1 bg-orange-50 text-orange-700 rounded-full hover:bg-orange-100 transition border border-orange-200">
                        <span class="font-mono">{referans_linki}</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex items-center gap-3">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-blue-700 transition flex items-center gap-2"
                    onclick="return confirm('Secilen kullanicilara SMS gondermek istediginize emin misiniz?')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                SMS Gonder
            </button>
        </div>
    </form>

    <script>
        function insertTag(tag) {
            const textarea = document.getElementById('smsMessage');
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const text = textarea.value;
            textarea.value = text.substring(0, start) + tag + text.substring(end);
            textarea.selectionStart = textarea.selectionEnd = start + tag.length;
            textarea.focus();
            updateCharCount();
        }

        function filterUsers() {
            const search = document.getElementById('userSearch').value.toLowerCase();
            document.querySelectorAll('.user-item').forEach(item => {
                const text = item.getAttribute('data-search');
                item.style.display = text.includes(search) ? '' : 'none';
            });
        }

        function selectAll() {
            document.querySelectorAll('.user-item').forEach(item => {
                if (item.style.display !== 'none') {
                    item.querySelector('.user-checkbox').checked = true;
                }
            });
            updateCount();
        }

        function deselectAll() {
            document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = false);
            updateCount();
        }

        function updateCount() {
            const count = document.querySelectorAll('.user-checkbox:checked').length;
            document.getElementById('selectedCount').textContent = count;
        }

        function updateCharCount() {
            const msg = document.getElementById('smsMessage').value;
            const len = msg.length;
            document.getElementById('charCount').textContent = len;
            document.getElementById('smsCount').textContent = len <= 160 ? 1 : Math.ceil(len / 153);
        }

        // Init
        updateCount();
        updateCharCount();
    </script>
</x-admin.layouts.app>
