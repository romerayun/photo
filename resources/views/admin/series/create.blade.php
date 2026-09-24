@extends('layouts.admin')

@section('title', 'Создать серию')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between border-b border-gray-200 pb-4">
        <div>
            <h1 class="text-2xl font-serif font-bold text-gray-900">Новая серия съёмок</h1>
            <p class="text-xs text-gray-500 mt-0.5">После сохранения параметров вы сможете загрузить фотографии.</p>
        </div>
        <a href="{{ route('admin.series.index') }}" class="text-xs uppercase tracking-wider text-gray-500 hover:text-gray-900">
            &larr; Назад к списку
        </a>
    </div>

    <form method="POST" action="{{ route('admin.series.store') }}" class="space-y-6 bg-white p-6 sm:p-8 border border-gray-200 rounded">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            {{-- Title RU --}}
            <div>
                <label for="title_ru" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                    Название на русском *
                </label>
                <input type="text" name="title_ru" id="title_ru" value="{{ old('title_ru') }}" required
                       class="w-full px-3.5 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">
            </div>

            {{-- Title EN --}}
            <div>
                <label for="title_en" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                    Название на английском (Title EN)
                </label>
                <input type="text" name="title_en" id="title_en" value="{{ old('title_en') }}"
                       class="w-full px-3.5 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">
            </div>

            {{-- Slug --}}
            <div>
                <label for="slug" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                    URL-адрес (slug)
                </label>
                <input type="text" name="slug" id="slug" value="{{ old('slug') }}" placeholder="генерируется автоматически, если пусто"
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
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
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
                <input type="text" name="location_ru" id="location_ru" value="{{ old('location_ru') }}" placeholder="Например: Иркутск, исторический центр"
                       class="w-full px-3.5 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">
            </div>

            {{-- Location EN --}}
            <div>
                <label for="location_en" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                    Локация (EN)
                </label>
                <input type="text" name="location_en" id="location_en" value="{{ old('location_en') }}" placeholder="e.g. Irkutsk, historical quarter"
                       class="w-full px-3.5 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">
            </div>

            {{-- Shooting Date --}}
            <div>
                <label for="shooting_date" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                    Дата съёмки
                </label>
                <input type="text" name="shooting_date" id="shooting_date" value="{{ old('shooting_date') }}" placeholder="Например: Сентябрь 2026"
                       class="w-full px-3.5 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">
            </div>

            {{-- Sort Order --}}
            <div>
                <label for="sort_order" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                    Порядок сортировки
                </label>
                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}"
                       class="w-full px-3.5 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">
            </div>

        </div>

        {{-- Description RU --}}
        <div>
            <label for="description_ru" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                Описание серии на русском (RU)
            </label>
            <textarea name="description_ru" id="description_ru" rows="3"
                      class="w-full px-3.5 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">{{ old('description_ru') }}</textarea>
        </div>

        {{-- Description EN --}}
        <div>
            <label for="description_en" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                Описание серии на английском (EN)
            </label>
            <textarea name="description_en" id="description_en" rows="3"
                      class="w-full px-3.5 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">{{ old('description_en') }}</textarea>
        </div>

        {{-- Toggles --}}
        <div class="border-t border-gray-200 pt-6 flex flex-wrap items-center gap-6">
            <label class="flex items-center text-sm font-medium text-gray-700">
                <input type="checkbox" name="is_published" value="1" {{ old('is_published', true) ? 'checked' : '' }} class="rounded border-gray-300 text-terracotta focus:ring-terracotta mr-2">
                Опубликовать серию
            </label>

            <label class="flex items-center text-sm font-medium text-gray-700">
                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="rounded border-gray-300 text-terracotta focus:ring-terracotta mr-2">
                Показывать в избранном на главной
            </label>

            <label class="flex items-center text-sm font-medium text-gray-700">
                <input type="checkbox" name="is_demo" value="1" {{ old('is_demo') ? 'checked' : '' }} class="rounded border-gray-300 text-terracotta focus:ring-terracotta mr-2">
                Пометить как демонстрационную (Demo)
            </label>
        </div>

        <div class="border-t border-gray-200 pt-6 flex justify-end">
            <button type="submit" class="px-6 py-2.5 bg-graphite-900 text-white text-xs uppercase tracking-wider font-medium hover:bg-terracotta transition-colors rounded">
                Создать и перейти к загрузке фото &rarr;
            </button>
        </div>

    </form>

</div>
@endsection
