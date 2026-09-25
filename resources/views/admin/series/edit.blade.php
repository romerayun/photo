@extends('layouts.admin')

@section('title', 'Редактировать: ' . $series->title_ru)

@section('content')
<div class="space-y-10">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-serif font-bold text-slate-900">{{ $series->title_ru }}</h1>
                @if($series->is_demo)
                    <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 text-xs font-bold uppercase tracking-wider">Demo</span>
                @endif
                @if($series->is_published)
                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold uppercase tracking-wider">Опубликована</span>
                @else
                    <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 text-xs font-medium uppercase tracking-wider">Черновик</span>
                @endif
            </div>
            <p class="text-xs text-slate-500 font-mono mt-1">/series/{{ $series->slug }}</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('series.show', ['slug' => $series->slug]) }}" target="_blank" class="px-3.5 py-2 border border-slate-300 rounded-lg text-xs uppercase tracking-wider font-semibold text-slate-700 hover:bg-slate-50 transition-colors inline-flex items-center gap-1.5">
                <span>Смотреть на сайте</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
            <a href="{{ route('admin.series.index') }}" class="px-3.5 py-2 text-xs uppercase tracking-wider font-semibold text-slate-500 hover:text-slate-900 transition-colors">
                &larr; К списку
            </a>
        </div>
    </div>

    {{-- Form: Edit Properties --}}
    <div class="bg-white p-6 sm:p-8 border border-slate-200 rounded-xl shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-6">
            <h2 class="text-base font-bold text-slate-900">Параметры серии</h2>
            <span class="text-xs text-slate-400">ID: {{ $series->id }}</span>
        </div>

        <form method="POST" action="{{ route('admin.series.update', $series) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- Title RU --}}
                <div class="md:col-span-2">
                    <label for="title_ru" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1.5">
                        Название серии *
                    </label>
                    <input type="text" name="title_ru" id="title_ru" value="{{ old('title_ru', $series->title_ru) }}" required
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900">
                </div>

                {{-- Slug --}}
                <div>
                    <label for="slug" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1.5">
                        URL-адрес (slug) *
                    </label>
                    <input type="text" name="slug" id="slug" value="{{ old('slug', $series->slug) }}" required
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 font-mono text-xs focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900">
                </div>

                {{-- Category --}}
                <div>
                    <label for="category_id" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1.5">
                        Категория
                    </label>
                    <select name="category_id" id="category_id" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 bg-white focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900">
                        <option value="">Без категории</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $series->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name_ru }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Location RU --}}
                <div>
                    <label for="location_ru" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1.5">
                        Локация
                    </label>
                    <input type="text" name="location_ru" id="location_ru" value="{{ old('location_ru', $series->location_ru) }}"
                           placeholder="Например: Иркутск, исторический центр"
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900">
                </div>

                {{-- Shooting Date --}}
                <div>
                    <label for="shooting_date" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1.5">
                        Дата съёмки
                    </label>
                    <input type="text" name="shooting_date" id="shooting_date" value="{{ old('shooting_date', $series->shooting_date) }}"
                           placeholder="Например: Сентябрь 2026"
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900">
                </div>

                {{-- Sort Order --}}
                <div class="md:col-span-2">
                    <label for="sort_order" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1.5">
                        Порядок сортировки
                    </label>
                    <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $series->sort_order) }}"
                           class="w-full max-w-xs px-4 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900">
                </div>

            </div>

            {{-- Description RU --}}
            <div>
                <label for="description_ru" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1.5">
                    Описание серии
                </label>
                <textarea name="description_ru" id="description_ru" rows="3"
                          class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900 leading-relaxed">{{ old('description_ru', $series->description_ru) }}</textarea>
            </div>

            {{-- Toggles --}}
            <div class="border-t border-slate-200 pt-6 flex flex-wrap items-center gap-6">
                <label class="flex items-center text-sm font-semibold text-slate-800 cursor-pointer select-none">
                    <input type="checkbox" name="is_published" value="1" {{ old('is_published', $series->is_published) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 text-crimson focus:ring-crimson mr-2.5">
                    Опубликовать серию
                </label>

                <label class="flex items-center text-sm font-semibold text-slate-800 cursor-pointer select-none">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $series->is_featured) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 text-crimson focus:ring-crimson mr-2.5">
                    Показывать в избранном на главной
                </label>

                <label class="flex items-center text-sm font-semibold text-slate-800 cursor-pointer select-none">
                    <input type="checkbox" name="is_demo" value="1" {{ old('is_demo', $series->is_demo) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 text-crimson focus:ring-crimson mr-2.5">
                    Пометить как Demo
                </label>
            </div>

            {{-- SEO Section Info --}}
            <div class="border-t border-slate-200 pt-6">
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="space-y-0.5">
                        <div class="flex items-center gap-2">
                            <span class="text-xs uppercase font-bold text-slate-700">SEO настройки страницы серии</span>
                            <span class="text-[0.65rem] px-2 py-0.5 bg-blue-50 text-blue-700 border border-blue-200 rounded font-bold font-mono">/series/{{ $series->slug }}</span>
                        </div>
                        <p class="text-xs text-slate-500">
                            {{ $seoMeta ? 'SEO заголовок: ' . ($seoMeta->title ?: 'Автоматический') : 'Генерируется автоматически на основе названия и фото.' }}
                        </p>
                    </div>
                    @if($seoMeta)
                        <a href="{{ route('admin.seo.edit', $seoMeta) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white border border-slate-300 hover:border-slate-800 text-slate-800 text-xs font-bold rounded-lg shadow-sm transition-all shrink-0">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span>Настроить SEO теги</span>
                        </a>
                    @endif
                </div>
            </div>

            <div class="border-t border-slate-200 pt-6 flex justify-end">
                <button type="submit" class="px-8 py-3 bg-neutral-900 hover:bg-neutral-800 text-white text-xs uppercase tracking-wider font-bold rounded-lg shadow-sm hover:shadow transition-all inline-flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>Сохранить изменения</span>
                </button>
            </div>
        </form>
    </div>

    {{-- Section: Photo Upload & Gallery --}}
    <div class="bg-white p-6 sm:p-8 border border-slate-200 rounded-xl shadow-sm space-y-8">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-4">
            <div>
                <h2 class="text-base font-bold text-slate-900">Фотографии серии ({{ $series->photos->count() }})</h2>
                <p class="text-xs text-slate-500 mt-0.5">Загрузите новые кадры в формате JPG, PNG или WEBP (до 50 МБ на файл).</p>
            </div>
        </div>

        {{-- Upload Form --}}
        <div id="drop-zone" class="p-8 border-2 border-dashed border-slate-300 hover:border-crimson rounded-xl bg-slate-50/70 text-center transition-all cursor-pointer relative">
            <div id="upload-idle-state" class="space-y-3">
                <div class="w-12 h-12 rounded-full bg-slate-200/80 mx-auto flex items-center justify-center text-slate-600">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="text-sm text-slate-700">
                    <span class="font-bold text-crimson hover:underline">Выберите фотографии</span>
                    <span class="text-slate-500"> или перетащите их сюда</span>
                </div>
                <p class="text-xs text-slate-400">JPG, PNG, WEBP до 50 МБ. Автоматическая оптимизация для Retina и SEO.</p>
                <input id="photos-input" type="file" multiple accept="image/jpeg,image/png,image/webp" class="sr-only">
            </div>

            {{-- Uploading & Progress State --}}
            <div id="upload-busy-state" class="hidden py-4 space-y-4">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-crimson/10 text-crimson">
                    <svg class="animate-spin h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <div>
                    <h3 id="upload-status-title" class="text-sm font-bold text-slate-900">Загрузка и оптимизация кадров...</h3>
                    <p id="upload-status-sub" class="text-xs text-slate-500 mt-0.5">Пожалуйста, подождите. Создаются веб-версии высокого разрешения.</p>
                </div>
                <div class="w-full max-w-md mx-auto bg-slate-200 rounded-full h-2 overflow-hidden">
                    <div id="upload-progress-bar" class="bg-crimson h-full w-0 transition-all duration-300"></div>
                </div>
                <div id="upload-percent-text" class="text-xs font-mono text-slate-600 font-bold">0%</div>
            </div>
        </div>

        {{-- Photos Grid --}}
        @if($series->photos->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5">
                @foreach($series->photos as $photo)
                    @php
                        $isCover = ($series->cover_image === $photo->image_path);
                    @endphp
                    <div class="border border-slate-200 rounded-xl overflow-hidden flex flex-col justify-between bg-white shadow-sm hover:shadow transition-shadow relative group">
                        
                        {{-- Thumbnail --}}
                        <div class="aspect-[4/3] bg-slate-100 overflow-hidden relative">
                            <img src="{{ $photo->url }}" alt="" class="w-full h-full object-cover">
                            
                            @if($isCover)
                                <div class="absolute top-2 left-2 bg-emerald-600 text-white text-[0.65rem] uppercase tracking-wider px-2.5 py-1 rounded-md font-bold shadow">
                                    ★ Обложка
                                </div>
                            @endif

                            @if($photo->width && $photo->height)
                                <div class="absolute bottom-2 right-2 bg-black/70 text-white text-[0.65rem] font-mono px-2 py-0.5 rounded backdrop-blur-xs">
                                    {{ $photo->width }}x{{ $photo->height }}
                                </div>
                            @endif
                        </div>

                        {{-- Actions Bar --}}
                        <div class="p-3 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-xs">
                            @if(!$isCover)
                                <form method="POST" action="{{ route('admin.photos.cover', $photo) }}">
                                    @csrf
                                    <button type="submit" class="text-crimson hover:underline font-bold cursor-pointer">
                                        Сделать обложкой
                                    </button>
                                </form>
                            @else
                                <span class="text-emerald-700 font-bold">Главное фото</span>
                            @endif

                            <form method="POST" action="{{ route('admin.photos.destroy', $photo) }}" onsubmit="return confirm('Удалить эту фотографию?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-600 hover:text-rose-800 font-semibold cursor-pointer">
                                    Удалить
                                </button>
                            </form>
                        </div>

                    </div>
                @endforeach
            </div>
        @else
            <div class="p-8 text-center text-slate-400 text-xs border border-slate-200 rounded-xl bg-slate-50/50">
                В этой серии пока нет фотографий. Нажмите «Выберите фотографии» выше.
            </div>
        @endif

    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('photos-input');
        const idleState = document.getElementById('upload-idle-state');
        const busyState = document.getElementById('upload-busy-state');
        const progressBar = document.getElementById('upload-progress-bar');
        const percentText = document.getElementById('upload-percent-text');
        const statusTitle = document.getElementById('upload-status-title');
        const statusSub = document.getElementById('upload-status-sub');
        const uploadUrl = '{{ route("admin.series.photos.store", $series) }}';
        const csrfToken = '{{ csrf_token() }}';

        if (!dropZone || !fileInput) return;

        dropZone.addEventListener('click', () => {
            if (busyState.classList.contains('hidden')) {
                fileInput.click();
            }
        });

        // Drag and drop highlights
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropZone.classList.add('border-crimson', 'bg-red-50/20');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropZone.classList.remove('border-crimson', 'bg-red-50/20');
            }, false);
        });

        dropZone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            if (dt && dt.files && dt.files.length) {
                handleFiles(dt.files);
            }
        });

        fileInput.addEventListener('change', function () {
            if (this.files && this.files.length) {
                handleFiles(this.files);
            }
        });

        async function handleFiles(files) {
            const validFiles = Array.from(files).filter(f => f.type.startsWith('image/'));
            if (!validFiles.length) {
                alert('Пожалуйста, выберите файлы изображений (JPG, PNG или WEBP).');
                return;
            }

            idleState.classList.add('hidden');
            busyState.classList.remove('hidden');

            const total = validFiles.length;
            let successCount = 0;
            let failedCount = 0;
            const errorsList = [];

            for (let i = 0; i < total; i++) {
                const file = validFiles[i];
                const fileNumber = i + 1;
                statusTitle.textContent = `Обработка фото ${fileNumber} из ${total}: «${file.name}»`;
                statusSub.textContent = 'Оптимизация под Retina и сохранение...';

                try {
                    await uploadSingleFile(file, fileNumber, total);
                    successCount++;
                } catch (err) {
                    console.error('Upload error for file', file.name, err);
                    failedCount++;
                    errorsList.push(`• ${file.name}: ${err.message || 'Ошибка'}`);
                }

                const overallPercent = Math.round((fileNumber / total) * 100);
                progressBar.style.width = overallPercent + '%';
                percentText.textContent = overallPercent + '%';
            }

            if (failedCount > 0) {
                alert(`Не удалось загрузить ${failedCount} из ${total} файлов:\n\n` + errorsList.join('\n'));
            }

            if (successCount > 0) {
                window.location.reload();
            } else {
                idleState.classList.remove('hidden');
                busyState.classList.add('hidden');
            }
        }

        // Pre-process huge files in browser if needed, preventing 503 server memory crashes
        async function prepareFileForUpload(file) {
            // If file is reasonably sized (under 12 MB), upload directly
            if (file.size <= 12 * 1024 * 1024) {
                return file;
            }

            // If file is very heavy (e.g. 28-50 MB camera JPEG), downsample in browser using HTML5 Canvas
            return new Promise((resolve) => {
                const img = new Image();
                const url = URL.createObjectURL(file);

                img.onload = function () {
                    URL.revokeObjectURL(url);
                    const maxDim = 3200; // Ultra high-res Retina format
                    let w = img.width;
                    let h = img.height;

                    if (w > maxDim || h > maxDim) {
                        if (w > h) {
                            h = Math.round((h * maxDim) / w);
                            w = maxDim;
                        } else {
                            w = Math.round((w * maxDim) / h);
                            h = maxDim;
                        }
                    }

                    const canvas = document.createElement('canvas');
                    canvas.width = w;
                    canvas.height = h;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, w, h);

                    canvas.toBlob((blob) => {
                        if (blob && blob.size > 0) {
                            const optimizedFile = new File([blob], file.name, {
                                type: 'image/jpeg',
                                lastModified: Date.now()
                            });
                            resolve(optimizedFile);
                        } else {
                            resolve(file); // Fallback to original
                        }
                    }, 'image/jpeg', 0.92);
                };

                img.onerror = function () {
                    URL.revokeObjectURL(url);
                    resolve(file); // Fallback to original
                };

                img.src = url;
            });
        }

        async function uploadSingleFile(file, fileIndex, totalFiles) {
            const fileToUpload = await prepareFileForUpload(file);

            return new Promise((resolve, reject) => {
                const formData = new FormData();
                formData.append('photos[]', fileToUpload);

                const xhr = new XMLHttpRequest();
                xhr.open('POST', uploadUrl, true);
                xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                xhr.setRequestHeader('Accept', 'application/json');

                xhr.upload.onprogress = function (e) {
                    if (e.lengthComputable) {
                        const filePercent = Math.round((e.loaded / e.total) * 100);
                        const basePercent = Math.round(((fileIndex - 1) / totalFiles) * 100);
                        const currentOverall = Math.round(basePercent + (filePercent / totalFiles));
                        progressBar.style.width = currentOverall + '%';
                        percentText.textContent = currentOverall + '%';
                    }
                };

                xhr.onload = function () {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        resolve(xhr.responseText);
                    } else {
                        let errMsg = `Ошибка сервера (HTTP ${xhr.status})`;
                        try {
                            const res = JSON.parse(xhr.responseText);
                            if (res.message) errMsg += ': ' + res.message;
                        } catch (e) {}
                        reject(new Error(errMsg));
                    }
                };

                xhr.onerror = function () {
                    reject(new Error('Сетевая ошибка соединения'));
                };

                xhr.send(formData);
            });
        }
    });
</script>
@endpush
