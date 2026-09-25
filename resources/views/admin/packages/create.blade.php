@extends('layouts.admin')

@section('title', 'Создать пакет услуг')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-serif font-bold text-slate-900">Новый пакет услуг</h1>
            <p class="text-xs text-slate-500 mt-0.5">Укажите параметры пакета, включённые услуги и стоимость.</p>
        </div>
        <a href="{{ route('admin.packages.index') }}" class="px-3.5 py-1.5 rounded-lg border border-slate-300 text-xs uppercase tracking-wider font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
            &larr; Назад к списку
        </a>
    </div>

    <form method="POST" action="{{ route('admin.packages.store') }}" class="space-y-6 bg-white p-6 sm:p-8 border border-slate-200 rounded-xl shadow-sm">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            {{-- Title RU --}}
            <div>
                <label for="title_ru" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1.5">
                    Название пакета *
                </label>
                <input type="text" name="title_ru" id="title_ru" value="{{ old('title_ru') }}" required
                       placeholder="Например: Индивидуальная съёмка"
                       class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900">
            </div>

            {{-- Subtitle RU --}}
            <div>
                <label for="subtitle_ru" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1.5">
                    Краткое описание / подзаголовок
                </label>
                <input type="text" name="subtitle_ru" id="subtitle_ru" value="{{ old('subtitle_ru') }}"
                       placeholder="Например: Для портретов, пар и личного бренда"
                       class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900">
            </div>

            {{-- Duration RU --}}
            <div>
                <label for="duration_ru" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1.5">
                    Продолжительность
                </label>
                <input type="text" name="duration_ru" id="duration_ru" value="{{ old('duration_ru') }}" placeholder="Например: 1–1.5 часа"
                       class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900">
            </div>

            {{-- Photo Count RU --}}
            <div>
                <label for="photo_count_ru" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1.5">
                    Количество фотографий
                </label>
                <input type="text" name="photo_count_ru" id="photo_count_ru" value="{{ old('photo_count_ru') }}" placeholder="Например: от 40 кадров в обработке"
                       class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900">
            </div>

            {{-- Delivery Time RU --}}
            <div>
                <label for="delivery_time_ru" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1.5">
                    Срок готовности
                </label>
                <input type="text" name="delivery_time_ru" id="delivery_time_ru" value="{{ old('delivery_time_ru') }}" placeholder="Например: до 10 дней"
                       class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900">
            </div>

            {{-- Price in Rubles --}}
            <div>
                <label for="price" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1.5">
                    Цена в рублях (оставьте пустым для «Стоимость уточняется»)
                </label>
                <input type="number" name="price" id="price" value="{{ old('price') }}" placeholder="Например: 15000" min="0" step="500"
                       class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900">
            </div>

            {{-- Sort order --}}
            <div>
                <label for="sort_order" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1.5">
                    Порядок вывода
                </label>
                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}"
                       class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900">
            </div>

            <div class="flex items-center pt-6">
                <label class="flex items-center text-sm font-semibold text-slate-800 cursor-pointer select-none">
                    <input type="checkbox" name="is_price_from" value="1" {{ old('is_price_from') ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 text-crimson focus:ring-crimson mr-2.5">
                    Обозначение «от» (например, «от 15 000 ₽»)
                </label>
            </div>

        </div>

        {{-- Includes RU --}}
        <div>
            <label for="includes_ru" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1.5">
                Что входит в пакет (каждый пункт с новой строки)
            </label>
            <textarea name="includes_ru" id="includes_ru" rows="5"
                      placeholder="Консультация по подготовке и подбору образов&#10;Помощь в позировании на съёмке&#10;Все удачные кадры в авторской цветокоррекции"
                      class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900 leading-relaxed">{{ old('includes_ru') }}</textarea>
        </div>

        {{-- Extra Conditions RU --}}
        <div>
            <label for="extra_conditions_ru" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1.5">
                Дополнительные условия / примечания
            </label>
            <textarea name="extra_conditions_ru" id="extra_conditions_ru" rows="2"
                      placeholder="Например: Аренда фотостудии и услуги визажиста оплачиваются отдельно"
                      class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900 leading-relaxed">{{ old('extra_conditions_ru') }}</textarea>
        </div>

        {{-- Published Toggle --}}
        <div class="border-t border-slate-200 pt-6">
            <label class="flex items-center text-sm font-semibold text-slate-800 cursor-pointer select-none">
                <input type="checkbox" name="is_published" value="1" {{ old('is_published', true) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 text-crimson focus:ring-crimson mr-2.5">
                Пакет активен и отображается на сайте
            </label>
        </div>

        <div class="border-t border-slate-200 pt-6 flex items-center justify-between">
            <a href="{{ route('admin.packages.index') }}" class="px-4 py-2.5 text-slate-500 hover:text-slate-900 text-xs uppercase tracking-wider font-semibold transition-colors">
                Отмена
            </a>
            <button type="submit" class="px-8 py-3 bg-neutral-900 hover:bg-neutral-800 text-white text-xs uppercase tracking-wider font-bold rounded-lg shadow-sm hover:shadow transition-all inline-flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span>Создать пакет</span>
            </button>
        </div>

    </form>

</div>
@endsection
