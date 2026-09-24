@extends('layouts.admin')

@section('title', 'Редактировать: ' . $series->title_ru)

@section('content')
<div class="space-y-10">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-200 pb-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-serif font-bold text-gray-900">{{ $series->title_ru }}</h1>
                @if($series->is_demo)
                    <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 text-xs font-bold uppercase">Demo</span>
                @endif
            </div>
            <p class="text-xs text-gray-500 font-mono mt-0.5">/series/{{ $series->slug }}</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('series.show', ['locale' => 'ru', 'slug' => $series->slug]) }}" target="_blank" class="px-3 py-1.5 border border-gray-300 text-xs uppercase tracking-wider text-gray-700 hover:border-gray-900 rounded">
                Смотреть RU &rarr;
            </a>
            <a href="{{ route('series.show', ['locale' => 'en', 'slug' => $series->slug]) }}" target="_blank" class="px-3 py-1.5 border border-gray-300 text-xs uppercase tracking-wider text-gray-700 hover:border-gray-900 rounded">
                Смотреть EN &rarr;
            </a>
            <a href="{{ route('admin.series.index') }}" class="px-3 py-1.5 text-xs uppercase tracking-wider text-gray-500 hover:text-gray-900">
                К списку
            </a>
        </div>
    </div>

    {{-- Form: Edit Properties --}}
    <div class="bg-white p-6 sm:p-8 border border-gray-200 rounded">
        <h2 class="text-base font-semibold text-gray-900 mb-6">Параметры и переводы серии</h2>

        <form method="POST" action="{{ route('admin.series.update', $series) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- Title RU --}}
                <div>
                    <label for="title_ru" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                        Название на русском (RU) *
                    </label>
                    <input type="text" name="title_ru" id="title_ru" value="{{ old('title_ru', $series->title_ru) }}" required
                           class="w-full px-3.5 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">
                </div>

                {{-- Title EN --}}
                <div>
                    <label for="title_en" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                        Название на английском (EN)
                    </label>
                    <input type="text" name="title_en" id="title_en" value="{{ old('title_en', $series->title_en) }}"
                           class="w-full px-3.5 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">
                </div>

                {{-- Slug --}}
                <div>
                    <label for="slug" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                        URL-адрес (slug) *
                    </label>
                    <input type="text" name="slug" id="slug" value="{{ old('slug', $series->slug) }}" required
                           class="w-full px-3.5 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta font-mono text-xs">
                </div>

                {{-- Category --}}
                <div>
                    <label for="category_id" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                        Категория
                    </label>
                    <select name="category_id" id="category_id" class="w-full px-3.5 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">
                        <option value="">Без категории</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $series->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name_ru }} ({{ $cat->name_en }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Location RU --}}
                <div>
                    <label for="location_ru" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                        Локация (RU)
                    </label>
                    <input type="text" name="location_ru" id="location_ru" value="{{ old('location_ru', $series->location_ru) }}"
                           class="w-full px-3.5 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">
                </div>

                {{-- Location EN --}}
                <div>
                    <label for="location_en" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                        Локация (EN)
                    </label>
                    <input type="text" name="location_en" id="location_en" value="{{ old('location_en', $series->location_en) }}"
                           class="w-full px-3.5 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">
                </div>

                {{-- Shooting Date --}}
                <div>
                    <label for="shooting_date" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                        Дата съёмки
                    </label>
                    <input type="text" name="shooting_date" id="shooting_date" value="{{ old('shooting_date', $series->shooting_date) }}"
                           class="w-full px-3.5 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">
                </div>

                {{-- Sort Order --}}
                <div>
                    <label for="sort_order" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                        Порядок сортировки
                    </label>
                    <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $series->sort_order) }}"
                           class="w-full px-3.5 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">
                </div>

            </div>

            {{-- Description RU --}}
            <div>
                <label for="description_ru" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                    Описание на русском (RU)
                </label>
                <textarea name="description_ru" id="description_ru" rows="3"
                          class="w-full px-3.5 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">{{ old('description_ru', $series->description_ru) }}</textarea>
            </div>

            {{-- Description EN --}}
            <div>
                <label for="description_en" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                    Описание на английском (EN)
                </label>
                <textarea name="description_en" id="description_en" rows="3"
                          class="w-full px-3.5 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">{{ old('description_en', $series->description_en) }}</textarea>
            </div>

            {{-- Toggles --}}
            <div class="border-t border-gray-200 pt-6 flex flex-wrap items-center gap-6">
                <label class="flex items-center text-sm font-medium text-gray-700">
                    <input type="checkbox" name="is_published" value="1" {{ old('is_published', $series->is_published) ? 'checked' : '' }} class="rounded border-gray-300 text-terracotta focus:ring-terracotta mr-2">
                    Опубликовать серию
                </label>

                <label class="flex items-center text-sm font-medium text-gray-700">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $series->is_featured) ? 'checked' : '' }} class="rounded border-gray-300 text-terracotta focus:ring-terracotta mr-2">
                    Показывать в избранном на главной
                </label>

                <label class="flex items-center text-sm font-medium text-gray-700">
                    <input type="checkbox" name="is_demo" value="1" {{ old('is_demo', $series->is_demo) ? 'checked' : '' }} class="rounded border-gray-300 text-terracotta focus:ring-terracotta mr-2">
                    Пометить как демонстрационную (Demo)
                </label>
            </div>

            <div class="border-t border-gray-200 pt-6 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-graphite-900 text-white text-xs uppercase tracking-wider font-medium hover:bg-terracotta transition-colors rounded">
                    Сохранить изменения
                </button>
            </div>
        </form>
    </div>

    {{-- Section: Photo Upload & Gallery --}}
    <div class="bg-white p-6 sm:p-8 border border-gray-200 rounded space-y-8">
        
        <div>
            <h2 class="text-base font-semibold text-gray-900">Фотографии серии ({{ $series->photos->count() }})</h2>
            <p class="text-xs text-gray-500 mt-1">Загрузите новые кадры в формате JPG, PNG или WEBP (до 10 МБ на файл). Нажмите «Сделать обложкой», чтобы выбрать главное фото.</p>
        </div>

        {{-- Upload Form --}}
        <form method="POST" action="{{ route('admin.series.photos.store', $series) }}" enctype="multipart/form-data" class="p-6 border-2 border-dashed border-gray-300 rounded bg-gray-50 text-center hover:border-terracotta transition-colors">
            @csrf
            <div class="space-y-3">
                <svg class="mx-auto h-10 w-10 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <div class="text-xs text-gray-600">
                    <label for="photos-input" class="cursor-pointer font-semibold text-terracotta hover:underline">
                        Выберите файлы
                    </label>
                    <span>или перетащите их сюда</span>
                    <input id="photos-input" name="photos[]" type="file" multiple accept="image/jpeg,image/png,image/webp" class="sr-only" onchange="this.form.submit()">
                </div>
                <p class="text-[0.7rem] text-gray-400">JPG, PNG, WEBP до 10 МБ. Разрешение и пропорции сохраняются автоматически.</p>
            </div>
        </form>

        {{-- Photos Grid --}}
        @if($series->photos->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($series->photos as $photo)
                    @php
                        $isCover = ($series->cover_image === $photo->image_path);
                    @endphp
                    <div class="border border-gray-200 rounded overflow-hidden flex flex-col justify-between bg-gray-50 relative group">
                        
                        {{-- Thumbnail --}}
                        <div class="aspect-[4/3] bg-gray-200 overflow-hidden relative">
                            <img src="{{ $photo->url }}" alt="" class="w-full h-full object-cover">
                            
                            @if($isCover)
                                <div class="absolute top-2 left-2 bg-emerald-600 text-white text-[0.65rem] uppercase tracking-wider px-2 py-0.5 rounded font-bold shadow-sm">
                                    Обложка
                                </div>
                            @endif

                            <div class="absolute bottom-2 right-2 bg-black/60 text-white text-[0.65rem] font-mono px-1.5 py-0.5 rounded">
                                {{ $photo->width }}x{{ $photo->height }}
                            </div>
                        </div>

                        {{-- Actions Bar --}}
                        <div class="p-3 bg-white border-t border-gray-200 flex items-center justify-between text-xs">
                            @if(!$isCover)
                                <form method="POST" action="{{ route('admin.photos.cover', $photo) }}">
                                    @csrf
                                    <button type="submit" class="text-terracotta hover:underline font-medium">
                                        Сделать обложкой
                                    </button>
                                </form>
                            @else
                                <span class="text-gray-400 font-medium">Главное фото</span>
                            @endif

                            <form method="POST" action="{{ route('admin.photos.destroy', $photo) }}" onsubmit="return confirm('Удалить эту фотографию?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700">
                                    Удалить
                                </button>
                            </form>
                        </div>

                    </div>
                @endforeach
            </div>
        @else
            <div class="p-8 text-center text-gray-400 text-xs border border-gray-200 rounded">
                В этой серии пока нет фотографий. Загрузите файлы через форму выше.
            </div>
        @endif

    </div>

</div>
@endsection
