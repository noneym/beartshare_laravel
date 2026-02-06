<x-admin.layouts.app title="Yazi Duzenle">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Yazi Duzenle</h1>
        <div class="flex items-center gap-3">
            <a href="{{ route('blog.detail', $blogPost->slug) }}" target="_blank" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                Sitede Gor
            </a>
            <a href="{{ route('admin.blog-posts.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Geri Don
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.blog-posts.update', $blogPost) }}" enctype="multipart/form-data" id="blogForm">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Title -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="mb-5">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Baslik <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title', $blogPost->title) }}" required
                               class="w-full border border-gray-300 rounded px-4 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-primary"
                               placeholder="Yazi basligi...">
                        @error('title')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">Slug <span class="text-gray-400">(bos birakilirsa otomatik)</span></label>
                        <input type="text" name="slug" id="slug" value="{{ old('slug', $blogPost->slug) }}"
                               class="w-full border border-gray-300 rounded px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-1 focus:ring-primary"
                               placeholder="yazi-slug">
                        @error('slug')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Content - TinyMCE / HTML Source -->
                <div class="bg-white rounded-xl shadow-sm p-6">
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

                    @error('content')
                        <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-2 rounded mb-4">{{ $message }}</div>
                    @enderror

                    <!-- Visual Editor -->
                    <div id="visualEditor">
                        <textarea id="tinymceEditor">{{ old('content', $blogPost->content) }}</textarea>
                    </div>

                    <!-- HTML Source Editor -->
                    <div id="sourceEditor" style="display: none;">
                        <textarea id="htmlSource" rows="25"
                                  class="w-full border border-gray-300 rounded px-4 py-3 text-sm font-mono focus:outline-none focus:ring-1 focus:ring-primary bg-gray-900 text-green-400 resize-y"
                                  placeholder="HTML kodunuzu buraya yapistirin..."></textarea>
                    </div>

                    <!-- Hidden field for form submission -->
                    <input type="hidden" name="content" id="blogContent">
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Publish Settings -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Yayin Ayarlari</h2>

                    <!-- Category -->
                    <div class="mb-4">
                        <label for="blog_category_id" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                        <select name="blog_category_id" id="blog_category_id"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary">
                            <option value="">Kategorisiz</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('blog_category_id', $blogPost->blog_category_id) == $category->id ? 'selected' : '' }}>{{ $category->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status -->
                    <div class="mb-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $blogPost->is_active) ? 'checked' : '' }}
                                   class="w-4 h-4 border-gray-300 rounded text-primary focus:ring-primary">
                            <span class="text-sm text-gray-700">Aktif (yayinda)</span>
                        </label>
                    </div>

                    <!-- Info -->
                    <div class="mb-4 p-3 bg-gray-50 rounded text-xs text-gray-500 space-y-1">
                        <p>Yazar: <strong class="text-gray-700">{{ $blogPost->user?->name ?? 'Bilinmiyor' }}</strong></p>
                        <p>Olusturulma: <strong class="text-gray-700">{{ $blogPost->created_at->format('d.m.Y H:i') }}</strong></p>
                        <p>Son Guncelleme: <strong class="text-gray-700">{{ $blogPost->updated_at->format('d.m.Y H:i') }}</strong></p>
                    </div>

                    <!-- Actions -->
                    <div class="pt-4 border-t border-gray-100 space-y-2">
                        <button type="submit" onclick="return submitForm()" class="w-full bg-primary hover:bg-yellow-600 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition">
                            Guncelle
                        </button>
                    </div>
                </div>

                <!-- Featured Image -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Kapak Gorseli</h2>

                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center" id="imagePreviewArea">
                        <div id="imagePreview" class="{{ $blogPost->image ? '' : 'hidden' }} mb-3">
                            <img id="previewImg" src="{{ $blogPost->image ? $blogPost->image_url : '' }}" alt="" class="w-full h-40 object-cover rounded">
                        </div>
                        <div id="imagePlaceholder" class="{{ $blogPost->image ? 'hidden' : '' }}">
                            <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <p class="text-xs text-gray-400">PNG, JPG (max 4MB)</p>
                        </div>
                        <input type="file" name="image" id="imageInput" accept="image/*" class="hidden" onchange="previewImage(this)">
                        <button type="button" onclick="document.getElementById('imageInput').click()" class="mt-2 text-xs text-blue-600 hover:text-blue-800">
                            {{ $blogPost->image ? 'Gorseli Degistir' : 'Gorsel Sec' }}
                        </button>
                    </div>
                    @error('image')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </form>

    <!-- TinyMCE Self-Hosted -->
    <script src="{{ asset('vendor/tinymce/tinymce.min.js') }}"></script>
    <script>
        let currentMode = 'visual';

        // TinyMCE Init
        tinymce.init({
            selector: '#tinymceEditor',
            height: 500,
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
                    font-size: 15px;
                    color: #333;
                    max-width: 800px;
                    margin: 0 auto;
                    padding: 16px;
                    line-height: 1.7;
                }
                img { max-width: 100%; height: auto; border-radius: 8px; }
                h1, h2, h3 { color: #1a1a1a; margin-top: 1.5em; }
                blockquote { border-left: 4px solid #D4A017; padding-left: 16px; color: #666; }
            `,
            promotion: false,
            branding: false,
            convert_urls: false,
            relative_urls: false,
            remove_script_host: false,
            images_upload_url: false,
            automatic_uploads: false,
            file_picker_types: 'image',
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

        function getCurrentContent() {
            if (currentMode === 'source') {
                return document.getElementById('htmlSource').value;
            }
            const editor = tinymce.get('tinymceEditor');
            return editor ? editor.getContent() : '';
        }

        function submitForm() {
            document.getElementById('blogContent').value = getCurrentContent();
            return true;
        }

        // Image Preview
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImg').src = e.target.result;
                    document.getElementById('imagePreview').classList.remove('hidden');
                    document.getElementById('imagePlaceholder').classList.add('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</x-admin.layouts.app>
