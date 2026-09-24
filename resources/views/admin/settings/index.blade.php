@extends('layouts.admin')

@section('title', 'Настройки сайта')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">

    <div class="border-b border-gray-200 pb-4">
        <h1 class="text-2xl font-serif font-bold text-gray-900">Настройки сайта</h1>
        <p class="text-xs text-gray-500 mt-0.5">Управление контактами, локализацией города, главными текстами и режимом работы сайта.</p>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-8">
        @csrf

        {{-- 1. Contacts Section --}}
        <div class="bg-white p-6 sm:p-8 border border-gray-200 rounded space-y-6">
            <div>
                <h2 class="text-base font-semibold text-gray-900">Контакты для связи</h2>
                <p class="text-xs text-gray-500 mt-0.5">Оставьте поля пустыми, если данные ещё не готовы к публикации. В демо-режиме отображается аккуратная подсказка, а в публичном режиме незаполненные контакты скрываются без неработающих кнопок.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="telegram" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                        Telegram (основной канал связи)
                    </label>
                    <input type="text" name="telegram" id="telegram" value="{{ old('telegram', $settings['telegram']) }}" placeholder="Например: @username или https://t.me/username"
                           class="w-full px-3.5 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">
                    <span class="text-[0.7rem] text-gray-400 mt-1 block">Можно ввести юзернейм с @ или прямую ссылку.</span>
                </div>

                <div>
                    <label for="phone" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                        Номер телефона
                    </label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $settings['phone']) }}" placeholder="+7 (999) 000-00-00"
                           class="w-full px-3.5 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">
                    <span class="text-[0.7rem] text-gray-400 mt-1 block">На сайте будет сформирована кликабельная ссылка tel:.</span>
                </div>
            </div>
        </div>

        {{-- 2. Location & Cities --}}
        <div class="bg-white p-6 sm:p-8 border border-gray-200 rounded space-y-6">
            <div>
                <h2 class="text-base font-semibold text-gray-900">Город и локация</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="city_ru" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                        Город на русском (RU) *
                    </label>
                    <input type="text" name="city_ru" id="city_ru" value="{{ old('city_ru', $settings['city_ru']) }}" required
                           class="w-full px-3.5 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">
                </div>

                <div>
                    <label for="city_en" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                        Город на английском (EN) *
                    </label>
                    <input type="text" name="city_en" id="city_en" value="{{ old('city_en', $settings['city_en']) }}" required
                           class="w-full px-3.5 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">
                </div>
            </div>
        </div>

        {{-- 3. Hero Texts --}}
        <div class="bg-white p-6 sm:p-8 border border-gray-200 rounded space-y-6">
            <div>
                <h2 class="text-base font-semibold text-gray-900">Главный экран (Hero)</h2>
            </div>

            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="hero_phrase_ru" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                            Ключевая фраза (RU) *
                        </label>
                        <input type="text" name="hero_phrase_ru" id="hero_phrase_ru" value="{{ old('hero_phrase_ru', $settings['hero_phrase_ru']) }}" required
                               class="w-full px-3.5 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">
                    </div>

                    <div>
                        <label for="hero_phrase_en" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                            Ключевая фраза (EN) *
                        </label>
                        <input type="text" name="hero_phrase_en" id="hero_phrase_en" value="{{ old('hero_phrase_en', $settings['hero_phrase_en']) }}" required
                               class="w-full px-3.5 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="hero_sub_ru" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                            Подзаголовок / описание (RU)
                        </label>
                        <textarea name="hero_sub_ru" id="hero_sub_ru" rows="2"
                                  class="w-full px-3.5 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">{{ old('hero_sub_ru', $settings['hero_sub_ru']) }}</textarea>
                    </div>

                    <div>
                        <label for="hero_sub_en" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                            Подзаголовок / описание (EN)
                        </label>
                        <textarea name="hero_sub_en" id="hero_sub_en" rows="2"
                                  class="w-full px-3.5 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">{{ old('hero_sub_en', $settings['hero_sub_en']) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. Mode Selection (Demo vs Live) --}}
        <div class="bg-white p-6 sm:p-8 border border-gray-200 rounded space-y-4">
            <h2 class="text-base font-semibold text-gray-900">Режим работы сайта</h2>
            
            <div class="p-4 bg-gray-50 border border-gray-200 rounded">
                <label class="flex items-start">
                    <input type="checkbox" name="demo_mode" value="1" {{ old('demo_mode', $settings['demo_mode']) === '1' ? 'checked' : '' }}
                           class="mt-1 rounded border-gray-300 text-terracotta focus:ring-terracotta mr-3">
                    <div>
                        <span class="text-sm font-semibold text-gray-900 block">Включить демонстрационный режим (Demo Mode)</span>
                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                            Когда этот флаг активен: на сайте показываются демонстрационные серии с пометкой Demo, поисковая индексация (robots.txt и meta robots) запрещена, а для незаполненных контактов показывается аккуратная подсказка о настройке.<br>
                            Когда вы снимете этот флаг и сохраните: сайт перейдет в публичный режим, демонстрационные серии скроются, поисковая индексация откроется, и посетители увидят только ваши реальные опубликованные работы.
                        </p>
                    </div>
                </label>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-8 py-3 bg-graphite-900 text-white text-xs uppercase tracking-wider font-medium hover:bg-terracotta transition-colors rounded shadow-sm">
                Сохранить все настройки
            </button>
        </div>

    </form>

</div>
@endsection
