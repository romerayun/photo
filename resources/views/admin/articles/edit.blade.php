@extends('layouts.admin')

@section('title', 'Редактировать статью: ' . $article->title)

@section('content')
<div class="max-w-4xl space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-serif font-bold text-slate-900">Редактирование статьи</h1>
            <p class="text-xs text-slate-500 mt-0.5">ID: {{ $article->id }} &bull; Просмотров: {{ $article->views_count }} &bull; Комментариев: {{ $article->allComments->count() }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('articles.show', $article->slug) }}" target="_blank" class="px-3.5 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 text-xs uppercase tracking-wider font-bold rounded-lg transition-all inline-flex items-center gap-1.5">
                <span>На сайте</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
            <a href="{{ route('admin.articles.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-xs uppercase font-bold text-slate-700 hover:bg-slate-50 transition-colors">
                Назад к списку
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.articles.update', $article) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

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
                       value="{{ old('title', $article->title) }}" 
                       required 
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
                           value="{{ old('slug', $article->slug) }}" 
                           class="w-full text-xs font-mono px-3.5 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:border-neutral-900">
                </div>

                <div>
                    <label for="reading_time" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1">
                        Время чтения (в минутах)
                    </label>
                    <input type="number" 
                           id="reading_time" 
                           name="reading_time" 
                           value="{{ old('reading_time', $article->reading_time) }}" 
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
                          class="w-full text-xs px-3.5 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-neutral-900">{{ old('excerpt', $article->excerpt) }}</textarea>
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
                <input type="hidden" name="content" id="article_content_input" value="{{ old('content', $article->content) }}">

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

        {{-- Images & Gallery --}}
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-6">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700 border-b border-slate-100 pb-2">
                Изображения
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                <div class="space-y-3">
                    <label class="block text-xs uppercase tracking-wider font-bold text-slate-700">
                        Текущая обложка
                    </label>
                    @if($article->cover_url)
                        <div class="relative aspect-[16/10] rounded-lg overflow-hidden border border-slate-200 bg-slate-100 max-w-sm">
                            <img src="{{ $article->cover_url }}" alt="" class="w-full h-full object-cover">
                        </div>
                    @endif
                    <div>
                        <label for="cover" class="block text-[0.7rem] uppercase tracking-wider font-bold text-slate-500 mb-1">
                            Заменить обложку
                        </label>
                        <input type="file" 
                               id="cover" 
                               name="cover" 
                               accept="image/jpeg,image/png,image/webp" 
                               class="w-full text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-neutral-900 file:text-white hover:file:bg-neutral-800">
                    </div>
                </div>

                <div class="space-y-3">
                    <label class="block text-xs uppercase tracking-wider font-bold text-slate-700">
                        Загрузить дополнительные фото
                    </label>
                    <input type="file" 
                           id="photos" 
                           name="photos[]" 
                           multiple 
                           accept="image/jpeg,image/png,image/webp" 
                           class="w-full text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-neutral-900 file:text-white hover:file:bg-neutral-800">
                    <p class="text-[0.7rem] text-slate-400">Файлы будут автоматически оптимизированы и прикреплены к галерее статьи.</p>
                </div>
            </div>

            {{-- Existing Gallery Photos --}}
            @if($article->images && $article->images->count() > 0)
                <div class="pt-4 border-t border-slate-100 space-y-3">
                    <label class="block text-xs uppercase tracking-wider font-bold text-slate-700">
                        Фотографии в галерее статьи ({{ $article->images->count() }})
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        @foreach($article->images as $img)
                            <div class="relative group rounded-lg overflow-hidden border border-slate-200 aspect-[4/3] bg-slate-100 shadow-sm">
                                <img src="{{ $img->image_url }}" alt="" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center p-2">
                                    <form action="{{ route('admin.articles.photos.destroy', [$article, $img]) }}" method="POST" onsubmit="return confirm('Удалить эту фотографию из статьи?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded text-xs font-bold transition-colors">
                                            Удалить
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
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
                           {{ old('is_published', $article->is_published) ? 'checked' : '' }} 
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
                           value="{{ old('published_at', $article->published_at ? $article->published_at->format('Y-m-d\TH:i') : '') }}" 
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
                           value="{{ old('meta_title', $article->meta_title) }}" 
                           class="w-full text-xs px-3.5 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-neutral-900">
                </div>

                <div>
                    <label for="meta_description" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1">
                        Meta Description (SEO)
                    </label>
                    <textarea id="meta_description" 
                              name="meta_description" 
                              rows="2" 
                              class="w-full text-xs px-3.5 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-neutral-900">{{ old('meta_description', $article->meta_description) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between gap-3 pt-2">
            <form action="{{ route('admin.articles.destroy', $article) }}" method="POST" onsubmit="return confirm('Вы действительно хотите удалить статью «{{ $article->title }}»?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2.5 text-xs uppercase font-bold text-rose-600 hover:text-rose-800 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer">
                    Удалить статью
                </button>
            </form>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.articles.index') }}" class="px-5 py-2.5 border border-slate-300 rounded-lg text-xs uppercase font-bold text-slate-700 hover:bg-slate-50 transition-colors">
                    Отмена
                </a>
                <button type="submit" class="px-6 py-2.5 bg-neutral-900 hover:bg-neutral-800 text-white text-xs uppercase tracking-wider font-bold rounded-lg shadow-sm hover:shadow transition-all inline-flex items-center gap-2">
                    <span>Сохранить изменения</span>
                    <span>&rarr;</span>
                </button>
            </div>
        </div>
    </form>

    {{-- Comments Moderation Block --}}
    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-4 pt-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700">
                    Комментарии к статье ({{ $article->allComments->count() }})
                </h2>
                <p class="text-xs text-slate-400">Модерация отзывов читателей, скрытие или удаление спама.</p>
            </div>
        </div>

        <div class="space-y-3">
            @forelse($article->allComments as $comment)
                <div class="p-4 rounded-lg border {{ $comment->is_approved ? 'border-slate-200 bg-white' : 'border-amber-200 bg-amber-50/50' }} flex flex-col sm:flex-row items-start justify-between gap-4">
                    <div class="space-y-1.5 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-bold text-slate-900 text-xs">{{ $comment->author_name }}</span>
                            @if($comment->author_email)
                                <span class="text-slate-400 text-xs font-mono">&lt;{{ $comment->author_email }}&gt;</span>
                            @endif
                            <span class="text-[0.7rem] text-slate-400 font-mono">&bull; {{ $comment->formatted_date }}</span>
                            
                            @if($comment->is_approved)
                                <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[0.65rem] uppercase font-bold">Опубликован</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[0.65rem] uppercase font-bold">Скрыт</span>
                            @endif
                        </div>
                        <div class="text-xs text-slate-700 whitespace-pre-line leading-relaxed">
                            {{ $comment->content }}
                        </div>
                        @if($comment->ip_address)
                            <div class="text-[0.65rem] font-mono text-slate-400">
                                IP: {{ $comment->ip_address }}
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        {{-- Toggle Status --}}
                        <form action="{{ route('admin.comments.toggle', $comment) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 rounded text-xs font-bold {{ $comment->is_approved ? 'bg-amber-100 hover:bg-amber-200 text-amber-900' : 'bg-emerald-100 hover:bg-emerald-200 text-emerald-900' }} transition-colors">
                                {{ $comment->is_approved ? 'Скрыть' : 'Опубликовать' }}
                            </button>
                        </form>

                        {{-- Delete --}}
                        <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST" onsubmit="return confirm('Удалить этот комментарий?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded transition-colors" title="Удалить">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-slate-400 text-xs font-mono bg-slate-50 rounded-lg">
                    К этой статье пока нет комментариев.
                </div>
            @endforelse
        </div>
    </div>

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
        min-height: 400px;
        background: #ffffff;
    }
    .ql-editor {
        min-height: 400px;
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
            e.preventDefault();
            if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length) {
                const file = e.dataTransfer.files[0];
                if (file.type.startsWith('image/')) {
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
