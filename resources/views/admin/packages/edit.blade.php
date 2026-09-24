@extends('layouts.admin')

@section('title', 'Редактировать пакет: ' . $package->title_ru)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between border-b border-gray-200 pb-4">
        <div>
            <h1 class="text-2xl font-serif font-bold text-gray-900">Редактирование пакета «{{ $package->title_ru }}»</h1>
            <p class="text-xs text-gray-500 mt-0.5">Укажите параметры пакета, включённые услуги и стоимость.</p>
        </div>
        <a href="{{ route('admin.packages.index') }}" class="text-xs uppercase tracking-wider text-gray-500 hover:text-gray-900">
            &larr; Назад к списку
        </a>
    </div>

    <form method="POST" action="{{ route('admin.packages.update', $package) }}" class="space-y-6 bg-white p-6 sm:p-8 border border-gray-200 rounded">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            {{-- Title RU --}}
            <div>
                <label for="title_ru" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                    Название пакета (RU) *
                </label>
                <input type="text" name="title_ru" id="title_ru" value="{{ old('title_ru', $package->title_ru) }}" required
                       class="w-full px-3.5 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">
            </div>

            {{-- Title EN --}}
            <div>
                <label for="title_en" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                    Название пакета (EN)
                </label>
                <input type="text" name="title_en" id="title_en" value="{{ old('title_en', $package->title_en) }}"
                       class="w-full px-3.5 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">
            </div>

            {{-- Subtitle RU --}}
            <div>
                <label for="subtitle_ru" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                    Краткое описание (RU)
                </label>
                <input type="text" name="subtitle_ru" id="subtitle_ru" value="{{ old('subtitle_ru', $package->subtitle_ru) }}"
                       class="w-full px-3.5 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">
            </div>

            {{-- Subtitle EN --}}
            <div>
                <label for="subtitle_en" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                    Краткое описание (EN)
                </label>
                <input type="text" name="subtitle_en" id="subtitle_en" value="{{ old('subtitle_en', $package->subtitle_en) }}"
                       class="w-full px-3.5 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">
            </div>

            {{-- Duration RU --}}
            <div>
                <label for="duration_ru" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                    Продолжительность (RU)
                </label>
                <input type="text" name="duration_ru" id="duration_ru" value="{{ old('duration_ru', $package->duration_ru) }}" placeholder="Например: 1–1.5 часа"
                       class="w-full px-3.5 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">
            </div>

            {{-- Duration EN --}}
            <div>
                <label for="duration_en" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                    Продолжительность (EN)
                </label>
                <input type="text" name="duration_en" id="duration_en" value="{{ old('duration_en', $package->duration_en) }}" placeholder="e.g. 1–1.5 hours"
                       class="w-full px-3.5 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">
            </div>

            {{-- Photo Count RU --}}
            <div>
                <label for="photo_count_ru" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                    Количество фотографий (RU)
                </label>
                <input type="text" name="photo_count_ru" id="photo_count_ru" value="{{ old('photo_count_ru', $package->photo_count_ru) }}" placeholder="от 30 кадров"
                       class="w-full px-3.5 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">
            </div>

            {{-- Photo Count EN --}}
            <div>
                <label for="photo_count_en" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                    Количество фотографий (EN)
                </label>
                <input type="text" name="photo_count_en" id="photo_count_en" value="{{ old('photo_count_en', $package->photo_count_en) }}" placeholder="from 30 frames"
                       class="w-full px-3.5 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">
            </div>

            {{-- Delivery Time RU --}}
            <div>
                <label for="delivery_time_ru" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                    Срок готовности (RU)
                </label>
                <input type="text" name="delivery_time_ru" id="delivery_time_ru" value="{{ old('delivery_time_ru', $package->delivery_time_ru) }}" placeholder="Например: до 14 дней"
                       class="w-full px-3.5 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">
            </div>

            {{-- Delivery Time EN --}}
            <div>
                <label for="delivery_time_en" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                    Срок готовности (EN)
                </label>
                <input type="text" name="delivery_time_en" id="delivery_time_en" value="{{ old('delivery_time_en', $package->delivery_time_en) }}" placeholder="e.g. up to 14 days"
                       class="w-full px-3.5 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">
            </div>

            {{-- Price in Rubles --}}
            <div>
                <label for="price" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                    Цена в рублях (оставьте пустым для «Стоимость уточняется»)
                </label>
                <input type="number" name="price" id="price" value="{{ old('price', $package->price) }}" placeholder="Например: 15000" min="0" step="500"
                       class="w-full px-3.5 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">
                <span class="text-[0.7rem] text-gray-400 mt-1 block">В английской версии сумма будет автоматически выводиться с постфиксом RUB.</span>
            </div>

            {{-- Sort order & Price From --}}
            <div class="space-y-4">
                <div>
                    <label for="sort_order" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                        Порядок вывода
                    </label>
                    <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $package->sort_order) }}"
                           class="w-full px-3.5 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">
                </div>

                <div class="pt-2">
                    <label class="flex items-center text-sm font-medium text-gray-700">
                        <input type="checkbox" name="is_price_from" value="1" {{ old('is_price_from', $package->is_price_from) ? 'checked' : '' }} class="rounded border-gray-300 text-terracotta focus:ring-terracotta mr-2">
                        Обозначение «от» (например, «от 15 000 ₽»)
                    </label>
                </div>
            </div>

        </div>

        {{-- Includes RU --}}
        <div>
            <label for="includes_ru" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                Что входит (RU) — каждый пункт с новой строки
            </label>
            <textarea name="includes_ru" id="includes_ru" rows="4"
                      class="w-full px-3.5 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">{{ old('includes_ru', $package->includes_ru) }}</textarea>
        </div>

        {{-- Includes EN --}}
        <div>
            <label for="includes_en" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                Что входит (EN) — each point on a new line
            </label>
            <textarea name="includes_en" id="includes_en" rows="4"
                      class="w-full px-3.5 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">{{ old('includes_en', $package->includes_en) }}</textarea>
        </div>

        {{-- Extra Conditions RU --}}
        <div>
            <label for="extra_conditions_ru" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                Дополнительные условия (RU)
            </label>
            <textarea name="extra_conditions_ru" id="extra_conditions_ru" rows="2"
                      class="w-full px-3.5 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">{{ old('extra_conditions_ru', $package->extra_conditions_ru) }}</textarea>
        </div>

        {{-- Extra Conditions EN --}}
        <div>
            <label for="extra_conditions_en" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                Дополнительные условия (EN)
            </label>
            <textarea name="extra_conditions_en" id="extra_conditions_en" rows="2"
                      class="w-full px-3.5 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">{{ old('extra_conditions_en', $package->extra_conditions_en) }}</textarea>
        </div>

        {{-- Published Toggle --}}
        <div class="border-t border-gray-200 pt-6">
            <label class="flex items-center text-sm font-medium text-gray-700">
                <input type="checkbox" name="is_published" value="1" {{ old('is_published', $package->is_published) ? 'checked' : '' }} class="rounded border-gray-300 text-terracotta focus:ring-terracotta mr-2">
                Пакет активен и отображается на сайте
            </label>
        </div>

        <div class="border-t border-gray-200 pt-6 flex justify-end">
            <button type="submit" class="px-6 py-2.5 bg-graphite-900 text-white text-xs uppercase tracking-wider font-medium hover:bg-terracotta transition-colors rounded">
                Сохранить пакет
            </button>
        </div>

    </form>

</div>
@endsection
