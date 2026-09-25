@extends('layouts.admin')

@section('title', 'Создать статью')

@section('content')
<div class="max-w-4xl space-y-6">

    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-serif font-bold text-slate-900">Создать новую статью</h1>
            <p class="text-xs text-slate-500 mt-0.5">Добавьте публикацию в блог с текстом, обложкой и фотографиями.</p>
        </div>
        <a href="{{ route('admin.articles.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-xs uppercase font-bold text-slate-700 hover:bg-slate-50 transition-colors">
            Назад к списку
        </a>
    </div>

    <form method="POST" action="{{ route('admin.articles.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-6">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700 border-b border-slate-100 pb-2">
                Основная информация
            </h2>

            <div>
                <label for="title" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1">
                    Название статьи <span class="text-rose-600">*</span>
                </label>
                <input type="text" 
                       id="title" 
                       name="title" 
                       value="{{ old('title') }}" 
                       required 
                       placeholder="Например: Как подготовиться к фотосессии на Байкале: советы и выбор локаций" 
                       class="w-full text-sm px-3.5 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:border-neutral-900">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="slug" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1">
                        URL Slug (ЧПУ)
                    </label>
                    <input type="text" 
                           id="slug" 
                           name="slug" 
                           value="{{ old('slug') }}" 
                           placeholder="kak-podgotovitsya-k-fotosessii (оставьте пустым для авто)" 
                           class="w-full text-xs font-mono px-3.5 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:border-neutral-900">
                </div>

                <div>
                    <label for="reading_time" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1">
                        Время чтения (в минутах)
                    </label>
                    <input type="number" 
                           id="reading_time" 
                           name="reading_time" 
                           value="{{ old('reading_time', 5) }}" 
                           min="1" 
                           max="60" 
                           class="w-full text-sm px-3.5 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:border-neutral-900">
                </div>
            </div>

            <div>
                <label for="excerpt" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1">
                    Краткий анонс (лид)
                </label>
                <textarea id="excerpt" 
                          name="excerpt" 
                          rows="2" 
                          placeholder="Краткое описание статьи для превью в списке и соцсетях..." 
                          class="w-full text-xs px-3.5 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-neutral-900">{{ old('excerpt') }}</textarea>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs uppercase tracking-wider font-bold text-slate-700">
                        Текст статьи <span class="text-rose-600">*</span>
                    </label>
                    <span class="text-[0.7rem] text-slate-500 font-mono">
                        Поддерживаются заголовки, списки, цитаты и вставка изображений в текст
                    </span>
                </div>

                {{-- Hidden input for form submission --}}
                <input type="hidden" name="content" id="article_content_input" value="{{ old('content') }}">

                {{-- Quill editor container --}}
                <div id="editor-container" class="bg-white"></div>

                {{-- Hidden file input for image upload handler --}}
                <input type="file" id="quill-image-upload" accept="image/*" class="hidden">
                
                {{-- Uploading status indicator --}}
                <div id="editor-uploading" class="hidden mt-2 p-2 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-semibold flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4 text-indigo-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Загрузка и оптимизация изображения...</span>
                </div>
            </div>
        </div>

        {{-- Photos block --}}
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-6">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700 border-b border-slate-100 pb-2">
                Изображения
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="cover" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1">
                        Главная обложка статьи
                    </label>
                    <input type="file" 
                           id="cover" 
                           name="cover" 
                           accept="image/jpeg,image/png,image/webp" 
                           class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-neutral-900 file:text-white hover:file:bg-neutral-800">
                    <p class="text-[0.7rem] text-slate-400 mt-1">Рекомендуется горизонтальное фото высокого разрешения. Система автоматически оптимизирует его для веба.</p>
                </div>

                <div>
                    <label for="photos" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1">
                        Дополнительные фотографии статьи (галерея)
                    </label>
                    <input type="file" 
                           id="photos" 
                           name="photos[]" 
                           multiple 
                           accept="image/jpeg,image/png,image/webp" 
                           class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-neutral-900 file:text-white hover:file:bg-neutral-800">
                    <p class="text-[0.7rem] text-slate-400 mt-1">Можно выбрать несколько файлов для галереи в теле статьи.</p>
                </div>
            </div>
        </div>

        {{-- Publication & SEO --}}
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-6">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700 border-b border-slate-100 pb-2">
                Публикация и SEO
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-center space-x-3 pt-2">
                    <input type="hidden" name="is_published" value="0">
                    <input type="checkbox" 
                           id="is_published" 
                           name="is_published" 
                           value="1" 
                           {{ old('is_published', true) ? 'checked' : '' }} 
                           class="w-4 h-4 text-neutral-900 border-slate-300 rounded focus:ring-neutral-900">
                    <label for="is_published" class="text-xs font-bold uppercase tracking-wider text-slate-700 cursor-pointer">
                        Опубликовать статью на сайте
                    </label>
                </div>

                <div>
                    <label for="published_at" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1">
                        Дата публикации
                    </label>
                    <input type="datetime-local" 
                           id="published_at" 
                           name="published_at" 
                           value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}" 
                           class="w-full text-xs px-3.5 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-neutral-900">
                </div>
            </div>

            <div class="space-y-4 pt-2">
                <div>
                    <label for="meta_title" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1">
                        Meta Title (SEO)
                    </label>
                    <input type="text" 
                           id="meta_title" 
                           name="meta_title" 
                           value="{{ old('meta_title') }}" 
                           placeholder="Оставьте пустым, чтобы использовать заголовок статьи" 
                           class="w-full text-xs px-3.5 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-neutral-900">
                </div>

                <div>
                    <label for="meta_description" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1">
                        Meta Description (SEO)
                    </label>
                    <textarea id="meta_description" 
                              name="meta_description" 
                              rows="2" 
                              placeholder="Краткое описание для поисковых систем (150-160 символов)..." 
                              class="w-full text-xs px-3.5 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-neutral-900">{{ old('meta_description') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('admin.articles.index') }}" class="px-5 py-2.5 border border-slate-300 rounded-lg text-xs uppercase font-bold text-slate-700 hover:bg-slate-50 transition-colors">
                Отмена
            </a>
            <button type="submit" class="px-6 py-2.5 bg-neutral-900 hover:bg-neutral-800 text-white text-xs uppercase tracking-wider font-bold rounded-lg shadow-sm hover:shadow transition-all inline-flex items-center gap-2">
                <span>Опубликовать статью</span>
                <span>&rarr;</span>
            </button>
        </div>
    </form>

</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
<style>
    .ql-toolbar.ql-snow {
        border-top-left-radius: 0.5rem;
        border-top-right-radius: 0.5rem;
        border-color: #cbd5e1;
        background: #f8fafc;
    }
    .ql-container.ql-snow {
        border-bottom-left-radius: 0.5rem;
        border-bottom-right-radius: 0.5rem;
        border-color: #cbd5e1;
        font-family: inherit;
        font-size: 0.95rem;
        min-height: 380px;
        background: #ffffff;
    }
    .ql-editor {
        min-height: 380px;
        line-height: 1.7;
    }
    .ql-editor img {
        max-width: 100%;
        height: auto;
        border-radius: 0.5rem;
        margin: 1rem 0;
        display: block;
    }
    .ql-editor h2 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
    }
    .ql-editor h3 {
        font-size: 1.25rem;
        font-weight: 600;
        margin-top: 1.25rem;
        margin-bottom: 0.5rem;
    }
    .ql-editor blockquote {
        border-left: 3px solid #E51920;
        padding-left: 1rem;
        color: #475569;
        font-style: italic;
        margin: 1rem 0;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const uploadUrl = '{{ route("admin.articles.upload_image") }}';
        const csrfToken = '{{ csrf_token() }}';
        const hiddenInput = document.getElementById('article_content_input');
        const fileInput = document.getElementById('quill-image-upload');
        const uploadingIndicator = document.getElementById('editor-uploading');

        const quill = new Quill('#editor-container', {
            theme: 'snow',
            placeholder: 'Напишите текст статьи. Нажмите на иконку изображения в панели инструментов, чтобы загрузить и вставить фотографию в текст...',
            modules: {
                toolbar: {
                    container: [
                        [{ 'header': [2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ['blockquote', 'code-block'],
                        ['link', 'image'],
                        ['clean']
                    ],
                    handlers: {
                        image: function () {
                            fileInput.click();
                        }
                    }
                }
            }
        });

        // Preload existing content
        if (hiddenInput && hiddenInput.value) {
            quill.root.innerHTML = hiddenInput.value;
        }

        // Upload function for Quill
        async function uploadImageFile(file) {
            if (!file || !file.type.startsWith('image/')) return;
            
            uploadingIndicator.classList.remove('hidden');
            const formData = new FormData();
            formData.append('image', file);

            try {
                const response = await fetch(uploadUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (!response.ok) {
                    const errJson = await response.json().catch(() => null);
                    throw new Error(errJson?.message || 'Ошибка загрузки файла на сервер');
                }

                const data = await response.json();
                if (data.url) {
                    const range = quill.getSelection(true) || { index: quill.getLength() };
                    quill.insertEmbed(range.index, 'image', data.url);
                    quill.setSelection(range.index + 1);
                }
            } catch (err) {
                alert('Не удалось загрузить изображение: ' + err.message);
            } finally {
                uploadingIndicator.classList.add('hidden');
                fileInput.value = '';
            }
        }

        // File input change
        fileInput.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                uploadImageFile(this.files[0]);
            }
        });

        // Drag and drop & paste upload support
        quill.root.addEventListener('drop', function (e) {
            if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length) {
                const file = e.dataTransfer.files[0];
                if (file.type.startsWith('image/')) {
                    e.preventDefault();
                    uploadImageFile(file);
                }
            }
        });

        quill.root.addEventListener('paste', function (e) {
            if (e.clipboardData && e.clipboardData.files && e.clipboardData.files.length) {
                const file = e.clipboardData.files[0];
                if (file.type.startsWith('image/')) {
                    e.preventDefault();
                    uploadImageFile(file);
                    return;
                }
            }

            // Clean pasted text from dirty inline styles (Word/Docs/web styles)
            const html = e.clipboardData.getData('text/html');
            if (html) {
                e.preventDefault();
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;

                // Remove problematic style tags, scripts, font tags, classes, and inline styles
                tempDiv.querySelectorAll('style, script, meta, link').forEach(el => el.remove());
                tempDiv.querySelectorAll('*').forEach(el => {
                    el.removeAttribute('style');
                    el.removeAttribute('class');
                    el.removeAttribute('face');
                    el.removeAttribute('color');
                    el.removeAttribute('size');
                    el.removeAttribute('id');
                    el.removeAttribute('dir');
                    el.removeAttribute('align');
                });

                // Convert spans without semantic value to unwrapped text
                tempDiv.querySelectorAll('span').forEach(span => {
                    span.replaceWith(...span.childNodes);
                });

                const cleanHtml = tempDiv.innerHTML.trim();
                if (cleanHtml) {
                    const range = quill.getSelection(true) || { index: quill.getLength() };
                    quill.clipboard.dangerouslyPasteHTML(range.index, cleanHtml, 'user');
                } else {
                    const plainText = e.clipboardData.getData('text/plain');
                    if (plainText) {
                        const range = quill.getSelection(true) || { index: quill.getLength() };
                        quill.insertText(range.index, plainText, 'user');
                    }
                }
            }
        });

        // Sync to hidden input on change & submit
        quill.on('text-change', function () {
            hiddenInput.value = quill.root.innerHTML === '<p><br></p>' ? '' : quill.root.innerHTML;
        });

        const form = hiddenInput.closest('form');
        if (form) {
            form.addEventListener('submit', function (e) {
                const content = quill.root.innerHTML === '<p><br></p>' ? '' : quill.root.innerHTML;
                hiddenInput.value = content;
                if (!content || quill.getText().trim().length === 0) {
                    e.preventDefault();
                    alert('Пожалуйста, напишите текст статьи.');
                    quill.focus();
                }
            });
        }
    });
</script>
@endpush
