@extends('layouts.admin')

@section('title', 'Редактировать категорию «' . $category->name_ru . '»')

@section('content')
<div class="max-w-5xl space-y-6">

    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.categories.index') }}" class="text-xs text-slate-500 hover:text-crimson font-mono transition-colors">&larr; Все категории</a>
            </div>
            <h1 class="text-2xl font-serif font-bold text-slate-900 mt-1">Редактировать категорию «{{ $category->name_ru }}»</h1>
            <p class="text-xs text-slate-500 mt-0.5">Управляйте описанием услуги, обложкой, подробным текстом (Quill) и SEO-параметрами.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('categories.show', $category->slug) }}" target="_blank" class="px-4 py-2 bg-white border border-slate-300 rounded-lg text-xs uppercase font-bold text-slate-700 hover:bg-slate-50 hover:border-slate-400 transition-colors inline-flex items-center gap-1.5 shadow-sm">
                <span>Смотреть на сайте</span>
                <span class="text-crimson">&nearr;</span>
            </a>
            <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-xs uppercase font-bold text-slate-700 hover:bg-slate-50 transition-colors">
                Назад
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-sm rounded-xl">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Basic Information & Cover Photo --}}
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-6">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700 border-b border-slate-100 pb-2">
                Основная информация и обложка
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                {{-- Left column: Image preview & replace --}}
                <div class="md:col-span-4 space-y-3">
                    <label class="block text-xs uppercase tracking-wider font-bold text-slate-700">
                        Фотография обложки
                    </label>
                    <div class="relative w-full aspect-[3/2] rounded-lg overflow-hidden bg-slate-100 border border-slate-200 shadow-inner">
                        <img src="{{ $category->image_url }}" alt="{{ $category->name_ru }}" class="w-full h-full object-cover">
                        @if($category->image)
                            <span class="absolute top-2 right-2 px-2 py-0.5 bg-black/75 text-[0.65rem] text-white font-mono rounded">
                                Своё фото
                            </span>
                        @else
                            <span class="absolute top-2 right-2 px-2 py-0.5 bg-neutral-800/80 text-[0.65rem] text-neutral-300 font-mono rounded">
                                Демо-кадр
                            </span>
                        @endif
                    </div>

                    <div>
                        <input type="file" name="image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                    </div>

                    @if($category->image)
                        <div class="flex items-center gap-2 pt-1">
                            <input type="checkbox" name="remove_image" id="remove_image" value="1" class="rounded border-slate-300 text-crimson focus:ring-crimson text-xs">
                            <label for="remove_image" class="text-xs text-rose-600 font-medium cursor-pointer">
                                Удалить загруженное фото
                            </label>
                        </div>
                    @endif

                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 text-xs text-slate-600 font-mono space-y-1">
                        <div>Серий в категории: <span class="font-bold text-slate-900">{{ $category->series()->count() }}</span></div>
                        <div>URL: <a href="{{ route('categories.show', $category->slug) }}" target="_blank" class="text-crimson hover:underline">/category/{{ $category->slug }}</a></div>
                    </div>
                </div>

                {{-- Right column: Titles & Slugs --}}
                <div class="md:col-span-8 space-y-4">
                    <div>
                        <label for="name_ru" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1">
                            Название категории услуг <span class="text-rose-600">*</span>
                        </label>
                        <input type="text" name="name_ru" id="name_ru" value="{{ old('name_ru', $category->name_ru) }}" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-neutral-900">
                    </div>

                    <div>
                        <label for="description_ru" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1">
                            Краткий подзаголовок (анонс)
                        </label>
                        <textarea name="description_ru" id="description_ru" rows="2" placeholder="Краткое вступительное описание для карточки на главной и подзаголовка страницы..." class="w-full px-3.5 py-2 border border-slate-300 rounded-lg text-xs text-slate-900 focus:outline-none focus:border-neutral-900 leading-relaxed">{{ old('description_ru', $category->description_ru) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="slug" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1">
                                Slug (URL страницы) <span class="text-rose-600">*</span>
                            </label>
                            <input type="text" name="slug" id="slug" value="{{ old('slug', $category->slug) }}" required class="w-full px-3.5 py-2 border border-slate-300 rounded-lg text-xs font-mono text-slate-900 focus:outline-none focus:border-neutral-900">
                        </div>
                        <div>
                            <label for="sort_order" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1">
                                Порядок сортировки
                            </label>
                            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $category->sort_order) }}" class="w-full px-3.5 py-2 border border-slate-300 rounded-lg text-xs font-mono text-slate-900 focus:outline-none focus:border-neutral-900">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quill Detailed Description / Rich Content --}}
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-4">
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700">
                        Подробное описание категории услуг (Quill)
                    </h2>
                    <span class="text-[0.7rem] text-slate-500 font-mono">
                        Заголовки, списки, цитаты, форматирование и загрузка фотографий
                    </span>
                </div>
                <p class="text-xs text-slate-500">
                    Этот текст отобразится на странице категории над галереей выполненных работ: расскажите об особенностях съёмки, подготовке, локациях и подходе к кадру.
                </p>
            </div>

            {{-- Hidden input for form submission --}}
            <input type="hidden" name="content" id="category_content_input" value="{{ old('content', $category->content) }}">

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

        {{-- Banner & CTA Settings --}}
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-4">
            <div>
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700 border-b border-slate-100 pb-2">
                    Нижний баннер заявки («Запись на съёмку»)
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    Индивидуальный призыв к действию внизу страницы категории. Если поля не заполнены, используются стандартные тексты для категории.
                </p>
            </div>

            <div class="space-y-4">
                <div>
                    <label for="banner_title" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1">
                        Заголовок баннера
                    </label>
                    <input type="text" 
                           id="banner_title" 
                           name="banner_title" 
                           value="{{ old('banner_title', $category->banner_title) }}" 
                           placeholder="По умолчанию: Хотите съёмку в стиле «{{ $category->name_ru }}»?" 
                           class="w-full text-xs px-3.5 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:border-neutral-900">
                    <p class="text-[0.68rem] text-slate-400 mt-1">Например: <em>Нужен контент для бренда или эксперта?</em> или оставьте пустым для значения по умолчанию.</p>
                </div>

                <div>
                    <label for="banner_description" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1">
                        Текст описания в баннере
                    </label>
                    <textarea id="banner_description" 
                              name="banner_description" 
                              rows="3" 
                              placeholder="Текст описания для этой категории..." 
                              class="w-full text-xs px-3.5 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:border-neutral-900 leading-relaxed">{{ old('banner_description', $category->banner_description) }}</textarea>
                    <p class="text-[0.68rem] text-slate-400 mt-1">Например для коммерческих съёмок: расскажите про подбор локации (офис/производство/студия), согласование концепции, тайминга и подготовку визуала под задачи бизнеса.</p>
                </div>
            </div>
        </div>

        {{-- SEO Settings --}}
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-4">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700 border-b border-slate-100 pb-2">
                SEO оптимизация страницы услуги
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="meta_title" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1">
                        Meta Title
                    </label>
                    <input type="text" 
                           id="meta_title" 
                           name="meta_title" 
                           value="{{ old('meta_title', $category->meta_title) }}" 
                           placeholder="Например: Фотосессия портретов в Иркутске — Роман Юн" 
                           class="w-full text-xs px-3.5 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:border-neutral-900">
                    <p class="text-[0.68rem] text-slate-400 mt-1">Если оставить пустым, сформируется автоматически из названия.</p>
                </div>

                <div>
                    <label for="meta_description" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1">
                        Meta Description
                    </label>
                    <textarea id="meta_description" 
                              name="meta_description" 
                              rows="2" 
                              placeholder="Краткое привлекательное описание страницы для поисковиков Яндекс и Google..." 
                              class="w-full text-xs px-3.5 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-neutral-900 leading-relaxed">{{ old('meta_description', $category->meta_description) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center justify-between pt-2">
            <a href="{{ route('admin.categories.index') }}" class="px-5 py-2.5 border border-slate-300 rounded-lg text-xs uppercase font-bold text-slate-700 hover:bg-slate-50 transition-colors">
                Отмена
            </a>
            <div class="flex items-center gap-3">
                <button type="submit" class="px-7 py-3 bg-neutral-900 hover:bg-neutral-800 text-white text-xs uppercase tracking-wider font-bold rounded-lg shadow-sm hover:shadow transition-all inline-flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>Сохранить категорию</span>
                </button>
            </div>
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
        min-height: 350px;
        background: #ffffff;
    }
    .ql-editor {
        min-height: 350px;
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
        const uploadUrl = '{{ route("admin.categories.upload_image") }}';
        const csrfToken = '{{ csrf_token() }}';
        const hiddenInput = document.getElementById('category_content_input');
        const fileInput = document.getElementById('quill-image-upload');
        const uploadingIndicator = document.getElementById('editor-uploading');

        const quill = new Quill('#editor-container', {
            theme: 'snow',
            placeholder: 'Напишите подробное описание услуги: концепция, формат, как проходит съёмка, советы клиенту...',
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

        fileInput.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                uploadImageFile(this.files[0]);
            }
        });

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

            const html = e.clipboardData.getData('text/html');
            if (html) {
                e.preventDefault();
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;

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

        quill.on('text-change', function () {
            hiddenInput.value = quill.root.innerHTML === '<p><br></p>' ? '' : quill.root.innerHTML;
        });

        const form = hiddenInput.closest('form');
        if (form) {
            form.addEventListener('submit', function (e) {
                const content = quill.root.innerHTML === '<p><br></p>' ? '' : quill.root.innerHTML;
                hiddenInput.value = content;
            });
        }
    });
</script>
@endpush
