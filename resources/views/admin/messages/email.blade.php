<x-admin.layouts.app title="E-posta Gonder">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900">E-posta Gonder</h1>
    </div>

    <form method="POST" action="{{ route('admin.messages.email.send') }}" id="emailForm">
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
                <input type="text" id="userSearch" placeholder="Kullanici ara (ad, e-posta)..."
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary"
                       oninput="filterUsers()">
            </div>

            <!-- Selected Count -->
            <div class="mb-3 text-xs text-gray-500">
                Secili: <span id="selectedCount" class="font-semibold text-gray-700">0</span> kullanici
            </div>

            <!-- User List -->
            <div class="border border-gray-200 rounded max-h-56 overflow-y-auto divide-y divide-gray-100" id="userList">
                @foreach($users as $user)
                    <label class="flex items-center px-4 py-2.5 hover:bg-gray-50 cursor-pointer user-item" data-search="{{ strtolower($user->name . ' ' . $user->email) }}">
                        <input type="checkbox" name="user_ids[]" value="{{ $user->id }}"
                               class="user-checkbox w-4 h-4 border-gray-300 rounded text-primary focus:ring-primary"
                               {{ in_array($user->id, old('user_ids', $preselected)) ? 'checked' : '' }}
                               onchange="updateCount()">
                        <div class="ml-3 flex-1 min-w-0">
                            <p class="text-sm text-gray-900 truncate">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Subject -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Konu</h2>
            @error('subject')
                <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-2 rounded mb-4">{{ $message }}</div>
            @enderror
            <input type="text" name="subject" id="emailSubject" value="{{ old('subject') }}" required
                   class="w-full border border-gray-300 rounded px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-primary"
                   placeholder="E-posta konusu...">

            <!-- Degisken Tagleri (Konu) -->
            <div class="mt-3 pt-3 border-t border-gray-100">
                <p class="text-xs text-gray-500 mb-2">Degiskenler <span class="text-gray-400">(tikla ekle)</span></p>
                <div class="flex flex-wrap gap-1.5">
                    <button type="button" onclick="insertTagToSubject('{isim}')" class="inline-flex items-center gap-1 text-xs px-2.5 py-1 bg-blue-50 text-blue-700 rounded-full hover:bg-blue-100 transition border border-blue-200">
                        <span class="font-mono">{isim}</span> <span class="text-blue-400">Ad Soyad</span>
                    </button>
                    <button type="button" onclick="insertTagToSubject('{ad}')" class="inline-flex items-center gap-1 text-xs px-2.5 py-1 bg-blue-50 text-blue-700 rounded-full hover:bg-blue-100 transition border border-blue-200">
                        <span class="font-mono">{ad}</span> <span class="text-blue-400">Sadece Ad</span>
                    </button>
                    <button type="button" onclick="insertTagToSubject('{artpuan}')" class="inline-flex items-center gap-1 text-xs px-2.5 py-1 bg-purple-50 text-purple-700 rounded-full hover:bg-purple-100 transition border border-purple-200">
                        <span class="font-mono">{artpuan}</span> <span class="text-purple-400">Bakiye</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Email Body - TinyMCE / HTML Source -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Icerik</h2>
                <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-0.5">
                    <button type="button" id="btnVisual" onclick="switchToVisual()" class="px-3 py-1.5 text-xs font-medium rounded-md bg-white text-gray-900 shadow-sm transition">
                        Gorsel Editor
                    </button>
                    <button type="button" id="btnSource" onclick="switchToSource()" class="px-3 py-1.5 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700 transition">
                        HTML Kaynak
                    </button>
                </div>
            </div>

            @error('body')
                <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-2 rounded mb-4">{{ $message }}</div>
            @enderror

            <!-- Visual Editor -->
            <div id="visualEditor">
                <textarea id="tinymceEditor">{{ old('body') }}</textarea>
            </div>

            <!-- HTML Source Editor -->
            <div id="sourceEditor" style="display: none;">
                <textarea id="htmlSource" rows="20"
                          class="w-full border border-gray-300 rounded px-4 py-3 text-sm font-mono focus:outline-none focus:ring-1 focus:ring-primary bg-gray-900 text-green-400 resize-y"
                          placeholder="HTML kodunuzu buraya yapistirin..."></textarea>
            </div>

            <!-- Hidden field for form submission -->
            <input type="hidden" name="body" id="emailBody">

            <!-- Degisken Tagleri (Icerik) -->
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-xs text-gray-500 mb-2">Degiskenler <span class="text-gray-400">(tikla ekle)</span></p>
                <div class="flex flex-wrap gap-1.5">
                    <button type="button" onclick="insertTagToBody('{isim}')" class="inline-flex items-center gap-1 text-xs px-2.5 py-1 bg-blue-50 text-blue-700 rounded-full hover:bg-blue-100 transition border border-blue-200">
                        <span class="font-mono">{isim}</span> <span class="text-blue-400">Ad Soyad</span>
                    </button>
                    <button type="button" onclick="insertTagToBody('{ad}')" class="inline-flex items-center gap-1 text-xs px-2.5 py-1 bg-blue-50 text-blue-700 rounded-full hover:bg-blue-100 transition border border-blue-200">
                        <span class="font-mono">{ad}</span> <span class="text-blue-400">Sadece Ad</span>
                    </button>
                    <button type="button" onclick="insertTagToBody('{email}')" class="inline-flex items-center gap-1 text-xs px-2.5 py-1 bg-green-50 text-green-700 rounded-full hover:bg-green-100 transition border border-green-200">
                        <span class="font-mono">{email}</span>
                    </button>
                    <button type="button" onclick="insertTagToBody('{telefon}')" class="inline-flex items-center gap-1 text-xs px-2.5 py-1 bg-green-50 text-green-700 rounded-full hover:bg-green-100 transition border border-green-200">
                        <span class="font-mono">{telefon}</span>
                    </button>
                    <button type="button" onclick="insertTagToBody('{artpuan}')" class="inline-flex items-center gap-1 text-xs px-2.5 py-1 bg-purple-50 text-purple-700 rounded-full hover:bg-purple-100 transition border border-purple-200">
                        <span class="font-mono">{artpuan}</span> <span class="text-purple-400">Bakiye</span>
                    </button>
                    <button type="button" onclick="insertTagToBody('{referans_kodu}')" class="inline-flex items-center gap-1 text-xs px-2.5 py-1 bg-orange-50 text-orange-700 rounded-full hover:bg-orange-100 transition border border-orange-200">
                        <span class="font-mono">{referans_kodu}</span>
                    </button>
                    <button type="button" onclick="insertTagToBody('{referans_linki}')" class="inline-flex items-center gap-1 text-xs px-2.5 py-1 bg-orange-50 text-orange-700 rounded-full hover:bg-orange-100 transition border border-orange-200">
                        <span class="font-mono">{referans_linki}</span>
                    </button>
                </div>
            </div>

            <!-- Quick Templates -->
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-xs text-gray-500 mb-2">Hazir Sablon:</p>
                <div class="flex flex-wrap gap-2">
                    <button type="button" onclick="insertTemplate('basic')" class="text-xs px-3 py-1.5 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition">
                        Temel Sablon
                    </button>
                    <button type="button" onclick="insertTemplate('announcement')" class="text-xs px-3 py-1.5 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition">
                        Duyuru
                    </button>
                    <button type="button" onclick="insertTemplate('campaign')" class="text-xs px-3 py-1.5 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition">
                        Kampanya
                    </button>
                </div>
            </div>
        </div>

        <!-- Preview -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Onizleme</h2>
                <button type="button" onclick="updatePreview()" class="text-xs text-blue-600 hover:text-blue-800">Yenile</button>
            </div>
            <div class="border border-gray-200 rounded bg-gray-50 p-4">
                <iframe id="previewFrame" class="w-full bg-white rounded" style="min-height: 300px; border: none;" sandbox="allow-same-origin"></iframe>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex items-center gap-3">
            <button type="submit" class="bg-purple-600 text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-purple-700 transition flex items-center gap-2"
                    onclick="return submitForm()">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                E-posta Gonder
            </button>
            <button type="button" onclick="updatePreview()" class="border border-gray-300 text-gray-700 px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                Onizle
            </button>
        </div>
    </form>

    <!-- TinyMCE Self-Hosted -->
    <script src="{{ asset('vendor/tinymce/tinymce.min.js') }}"></script>
    <script>
        let currentMode = 'visual';

        // TinyMCE Init
        tinymce.init({
            selector: '#tinymceEditor',
            height: 400,
            menubar: true,
            license_key: 'gpl',
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'preview', 'help', 'wordcount',
                'emoticons', 'codesample'
            ],
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | forecolor backcolor | ' +
                     'alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | ' +
                     'link image table | removeformat code fullscreen | help',
            content_style: `
                body {
                    font-family: Arial, sans-serif;
                    font-size: 14px;
                    color: #333;
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 16px;
                }
                img { max-width: 100%; height: auto; }
            `,
            promotion: false,
            branding: false,
            convert_urls: false,
            relative_urls: false,
            remove_script_host: false,
            setup: function(editor) {
                editor.on('change', function() {
                    editor.save();
                });
            }
        });

        // Mode Switching
        function switchToVisual() {
            if (currentMode === 'visual') return;
            currentMode = 'visual';

            const htmlContent = document.getElementById('htmlSource').value;
            tinymce.get('tinymceEditor').setContent(htmlContent);

            document.getElementById('visualEditor').style.display = '';
            document.getElementById('sourceEditor').style.display = 'none';

            document.getElementById('btnVisual').className = 'px-3 py-1.5 text-xs font-medium rounded-md bg-white text-gray-900 shadow-sm transition';
            document.getElementById('btnSource').className = 'px-3 py-1.5 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700 transition';
        }

        function switchToSource() {
            if (currentMode === 'source') return;
            currentMode = 'source';

            const editor = tinymce.get('tinymceEditor');
            if (editor) {
                document.getElementById('htmlSource').value = editor.getContent();
            }

            document.getElementById('visualEditor').style.display = 'none';
            document.getElementById('sourceEditor').style.display = '';

            document.getElementById('btnSource').className = 'px-3 py-1.5 text-xs font-medium rounded-md bg-white text-gray-900 shadow-sm transition';
            document.getElementById('btnVisual').className = 'px-3 py-1.5 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700 transition';
        }

        // Get current content based on mode
        function getCurrentContent() {
            if (currentMode === 'source') {
                return document.getElementById('htmlSource').value;
            }
            const editor = tinymce.get('tinymceEditor');
            return editor ? editor.getContent() : '';
        }

        // Preview
        function updatePreview() {
            const content = getCurrentContent();
            const frame = document.getElementById('previewFrame');
            const doc = frame.contentDocument || frame.contentWindow.document;
            doc.open();
            doc.write(content);
            doc.close();
            // Auto-resize
            setTimeout(() => {
                frame.style.height = Math.max(300, doc.body.scrollHeight + 40) + 'px';
            }, 100);
        }

        // Submit
        function submitForm() {
            const content = getCurrentContent();
            document.getElementById('emailBody').value = content;

            if (!content.trim()) {
                alert('E-posta icerigi bos olamaz.');
                return false;
            }

            return confirm('Secilen kullanicilara e-posta gondermek istediginize emin misiniz?');
        }

        // Templates
        function insertTemplate(type) {
            let html = '';

            if (type === 'basic') {
                html = `<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
    <div style="background: #1a1a1a; padding: 24px; text-align: center;">
        <h1 style="color: #fff; font-size: 20px; margin: 0;">BeArtShare</h1>
    </div>
    <div style="padding: 32px 24px; background: #fff;">
        <p style="color: #333; font-size: 14px;">Merhaba,</p>
        <p style="color: #555; font-size: 14px;">Mesajinizi buraya yazin.</p>
    </div>
    <div style="padding: 16px 24px; background: #f8f8f8; text-align: center;">
        <p style="color: #999; font-size: 11px; margin: 0;">BeArtShare &copy; ${new Date().getFullYear()}</p>
    </div>
</div>`;
            } else if (type === 'announcement') {
                html = `<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
    <div style="background: #1a1a1a; padding: 24px; text-align: center;">
        <h1 style="color: #fff; font-size: 20px; margin: 0;">BeArtShare</h1>
    </div>
    <div style="padding: 32px 24px; background: #fff;">
        <h2 style="color: #333; font-size: 18px; margin: 0 0 16px; text-align: center;">Duyuru Basligi</h2>
        <p style="color: #555; font-size: 14px; line-height: 1.6;">Duyuru icerigini buraya yazin.</p>
        <div style="text-align: center; margin: 24px 0;">
            <a href="https://beartshare.com" style="background: #1a1a1a; color: #fff; padding: 12px 32px; text-decoration: none; font-size: 14px; display: inline-block;">Detayli Bilgi</a>
        </div>
    </div>
    <div style="padding: 16px 24px; background: #f8f8f8; text-align: center;">
        <p style="color: #999; font-size: 11px; margin: 0;">BeArtShare &copy; ${new Date().getFullYear()}</p>
    </div>
</div>`;
            } else if (type === 'campaign') {
                html = `<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
    <div style="background: linear-gradient(135deg, #5C4290 0%, #7C3AED 100%); padding: 40px 24px; text-align: center;">
        <h1 style="color: #fff; font-size: 28px; margin: 0;">Ozel Kampanya!</h1>
        <p style="color: rgba(255,255,255,0.8); font-size: 14px; margin: 8px 0 0;">Sinirli sureli firsat</p>
    </div>
    <div style="padding: 32px 24px; background: #fff;">
        <h2 style="color: #333; font-size: 18px; margin: 0 0 16px; text-align: center;">Kampanya Detaylari</h2>
        <p style="color: #555; font-size: 14px; line-height: 1.6;">Kampanya aciklamasini buraya yazin.</p>
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; padding: 20px; margin: 20px 0; text-align: center; border-radius: 8px;">
            <p style="color: #16a34a; font-size: 28px; font-weight: bold; margin: 0;">%20 Indirim</p>
            <p style="color: #15803d; font-size: 12px; margin: 4px 0 0;">Tum eserlerimizde gecerli</p>
        </div>
        <div style="text-align: center; margin: 24px 0;">
            <a href="https://beartshare.com/eserler" style="background: #7C3AED; color: #fff; padding: 14px 40px; text-decoration: none; font-size: 14px; display: inline-block; border-radius: 4px;">Eserleri Kesfet</a>
        </div>
    </div>
    <div style="padding: 16px 24px; background: #f8f8f8; text-align: center;">
        <p style="color: #999; font-size: 11px; margin: 0;">BeArtShare &copy; ${new Date().getFullYear()}</p>
    </div>
</div>`;
            }

            if (currentMode === 'visual') {
                const editor = tinymce.get('tinymceEditor');
                if (editor) editor.setContent(html);
            } else {
                document.getElementById('htmlSource').value = html;
            }
        }

        // User filter / select
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

        // Insert tag into subject field
        function insertTagToSubject(tag) {
            const input = document.getElementById('emailSubject');
            const start = input.selectionStart;
            const end = input.selectionEnd;
            const text = input.value;
            input.value = text.substring(0, start) + tag + text.substring(end);
            input.selectionStart = input.selectionEnd = start + tag.length;
            input.focus();
        }

        // Insert tag into body (TinyMCE visual or HTML source)
        function insertTagToBody(tag) {
            if (currentMode === 'visual') {
                const editor = tinymce.get('tinymceEditor');
                if (editor) {
                    editor.execCommand('mceInsertContent', false, tag);
                    editor.focus();
                }
            } else {
                const textarea = document.getElementById('htmlSource');
                const start = textarea.selectionStart;
                const end = textarea.selectionEnd;
                const text = textarea.value;
                textarea.value = text.substring(0, start) + tag + text.substring(end);
                textarea.selectionStart = textarea.selectionEnd = start + tag.length;
                textarea.focus();
            }
        }

        // Init
        updateCount();
    </script>
</x-admin.layouts.app>
