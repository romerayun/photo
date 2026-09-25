@extends('layouts.admin')

@section('title', 'Создать серию')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-serif font-bold text-slate-900">Новая серия съёмок</h1>
            <p class="text-xs text-slate-500 mt-0.5">Заполните основную информацию. После сохранения вы сможете загрузить фотографии.</p>
        </div>
        <a href="{{ route('admin.series.index') }}" class="px-3.5 py-1.5 rounded-lg border border-slate-300 text-xs uppercase tracking-wider font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
            &larr; Назад к списку
        </a>
    </div>

    <form method="POST" action="{{ route('admin.series.store') }}" class="space-y-6 bg-white p-6 sm:p-8 border border-slate-200 rounded-xl shadow-sm">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            {{-- Title RU --}}
            <div class="md:col-span-2">
                <label for="title_ru" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1.5">
                    Название серии *
                </label>
                <input type="text" name="title_ru" id="title_ru" value="{{ old('title_ru') }}" required
                       placeholder="Например: Портретная съёмка в студии «Свет»"
                       class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900">
            </div>

            {{-- Slug --}}
            <div>
                <label for="slug" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1.5">
                    URL-адрес (slug)
                </label>
                <input type="text" name="slug" id="slug" value="{{ old('slug') }}" placeholder="генерируется автоматически, если пусто"
                       class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 placeholder-slate-400 font-mono text-xs focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900">
            </div>

            {{-- Category --}}
            <div>
                <label for="category_id" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1.5">
                    Категория
                </label>
                <select name="category_id" id="category_id" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 bg-white focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900">
                    <option value="">Без категории</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
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
                <input type="text" name="location_ru" id="location_ru" value="{{ old('location_ru') }}" placeholder="Например: Иркутск, исторический центр"
                       class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900">
            </div>

            {{-- Shooting Date --}}
            <div>
                <label for="shooting_date" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1.5">
                    Дата съёмки
                </label>
                <input type="text" name="shooting_date" id="shooting_date" value="{{ old('shooting_date') }}" placeholder="Например: Сентябрь 2026"
                       class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900">
            </div>

            {{-- Sort Order --}}
            <div class="md:col-span-2">
                <label for="sort_order" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1.5">
                    Порядок сортировки (чем меньше число, тем выше в списке)
                </label>
                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}"
                       class="w-full max-w-xs px-4 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900">
            </div>

        </div>

        {{-- Description RU --}}
        <div>
            <label for="description_ru" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1.5">
                Описание серии
            </label>
            <textarea name="description_ru" id="description_ru" rows="3"
                      placeholder="Несколько предложений об атмосфере съёмки, героях или локации..."
                      class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900 leading-relaxed">{{ old('description_ru') }}</textarea>
        </div>

        {{-- Toggles --}}
        <div class="border-t border-slate-200 pt-6 flex flex-wrap items-center gap-6">
            <label class="flex items-center text-sm font-semibold text-slate-800 cursor-pointer select-none">
                <input type="checkbox" name="is_published" value="1" {{ old('is_published', true) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 text-crimson focus:ring-crimson mr-2.5">
                Опубликовать серию на сайте
            </label>

            <label class="flex items-center text-sm font-semibold text-slate-800 cursor-pointer select-none">
                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 text-crimson focus:ring-crimson mr-2.5">
                Показывать в избранном на главной
            </label>

            <label class="flex items-center text-sm font-semibold text-slate-800 cursor-pointer select-none">
                <input type="checkbox" name="is_demo" value="1" {{ old('is_demo') ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 text-crimson focus:ring-crimson mr-2.5">
                Пометить как Demo
            </label>
        </div>

        <div class="border-t border-slate-200 pt-6 flex items-center justify-between">
            <a href="{{ route('admin.series.index') }}" class="text-xs uppercase tracking-wider text-slate-500 hover:text-slate-800 font-semibold">
                Отмена
            </a>
            <button type="submit" class="px-7 py-3 bg-neutral-900 hover:bg-neutral-800 text-white text-xs uppercase tracking-wider font-bold rounded-lg shadow-sm hover:shadow transition-all inline-flex items-center gap-2 cursor-pointer">
                <span>Создать и перейти к загрузке фото</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>
        </div>

    </form>

</div>
@endsection
