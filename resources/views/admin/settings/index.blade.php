@extends('layouts.admin')

@section('title', 'Настройки сайта')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">

    <div class="border-b border-slate-200 pb-4">
        <h1 class="text-2xl font-serif font-bold text-slate-900">Настройки сайта</h1>
        <p class="text-xs text-slate-500 mt-0.5">Управление контактами, локацией, текстами главной страницы и режимом работы сайта.</p>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-8">
        @csrf

        {{-- 1. Contacts Section --}}
        <div class="bg-white p-6 sm:p-8 border border-slate-200 rounded-xl shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-slate-900">Контакты для связи</h2>
                <p class="text-xs text-slate-500 mt-0.5">Оставьте поля пустыми, если данные ещё не готовы к публикации.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="telegram" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1.5">
                        Telegram (основной канал связи)
                    </label>
                    <input type="text" name="telegram" id="telegram" value="{{ old('telegram', $settings['telegram']) }}" placeholder="Например: @username или https://t.me/username"
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900">
                    <span class="text-[0.7rem] text-slate-400 mt-1 block">Можно ввести юзернейм с @ или прямую ссылку.</span>
                </div>

                <div>
                    <label for="phone" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1.5">
                        Номер телефона
                    </label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $settings['phone']) }}" placeholder="+7 (999) 000-00-00"
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900">
                    <span class="text-[0.7rem] text-slate-400 mt-1 block">На сайте будет сформирована кликабельная ссылка tel:.</span>
                </div>

                <div class="md:col-span-2">
                    <label for="contact_email" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1.5">
                        Email для получения заявок с сайта
                    </label>
                    <input type="email" name="contact_email" id="contact_email" value="{{ old('contact_email', $settings['contact_email']) }}" placeholder="romerayun@gmail.com"
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900">
                    <span class="text-[0.7rem] text-slate-400 mt-1 block">На этот адрес будут приходить письма при заполнении формы обратной связи.</span>
                </div>
            </div>
        </div>

        {{-- 2. Location & Cities --}}
        <div class="bg-white p-6 sm:p-8 border border-slate-200 rounded-xl shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-slate-900">Город и локация</h2>
            </div>

            <div>
                <label for="city_ru" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1.5">
                    Город *
                </label>
                <input type="text" name="city_ru" id="city_ru" value="{{ old('city_ru', $settings['city_ru']) }}" required
                       placeholder="Например: Иркутск"
                       class="w-full max-w-md px-4 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900">
            </div>
        </div>

        {{-- 3. Hero Texts --}}
        <div class="bg-white p-6 sm:p-8 border border-slate-200 rounded-xl shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-slate-900">Главный экран (Hero)</h2>
            </div>

            <div class="space-y-4">
                <div>
                    <label for="hero_phrase_ru" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1.5">
                        Ключевая фраза / заголовок *
                    </label>
                    <input type="text" name="hero_phrase_ru" id="hero_phrase_ru" value="{{ old('hero_phrase_ru', $settings['hero_phrase_ru']) }}" required
                           placeholder="Например: Ваши истории. Мой взгляд."
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900">
                </div>

                <div>
                    <label for="hero_sub_ru" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1.5">
                        Подзаголовок / описание
                    </label>
                    <textarea name="hero_sub_ru" id="hero_sub_ru" rows="3"
                              placeholder="Например: Портреты, съёмки для пар и семей, события и контент для бизнеса. Иркутск."
                              class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900 leading-relaxed">{{ old('hero_sub_ru', $settings['hero_sub_ru']) }}</textarea>
                </div>
            </div>
        </div>

        {{-- 4. Home Page Spotlight Series --}}
        <div class="bg-white p-6 sm:p-8 border border-slate-200 rounded-xl shadow-sm space-y-4">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-slate-900">Главная страница: Серия во втором блоке (Центральная колонка)</h2>
                <p class="text-xs text-slate-500 mt-0.5">Выберите конкретную серию, фотография и описание которой будут отображаться в центральной колонке сразу после Hero. Если оставить пустым, сайт автоматически возьмёт первую серию по порядку сортировки.</p>
            </div>

            <div>
                <label for="home_spotlight_series_id" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1.5">
                    Избранная серия для карточки
                </label>
                <select name="home_spotlight_series_id" id="home_spotlight_series_id"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 bg-white focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900">
                    <option value="">По умолчанию (первая опубликованная серия)</option>
                    @foreach($seriesList as $s)
                        <option value="{{ $s->id }}" {{ old('home_spotlight_series_id', $settings['home_spotlight_series_id']) == $s->id ? 'selected' : '' }}>
                            {{ $s->title_ru }} ({{ $s->category ? $s->category->name_ru : 'Без категории' }}) {{ $s->location_ru ? '— ' . $s->location_ru : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- 5. Mode Selection (Demo vs Live) --}}
        <div class="bg-white p-6 sm:p-8 border border-slate-200 rounded-xl shadow-sm space-y-4">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-slate-900">Режим работы сайта</h2>
            </div>
            
            <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl">
                <label class="flex items-start cursor-pointer select-none">
                    <input type="checkbox" name="demo_mode" value="1" {{ old('demo_mode', $settings['demo_mode']) === '1' ? 'checked' : '' }}
                           class="mt-1 w-4 h-4 rounded border-slate-300 text-crimson focus:ring-crimson mr-3">
                    <div>
                        <span class="text-sm font-bold text-slate-900 block">Включить демонстрационный режим (Demo Mode)</span>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            Когда этот флаг активен: на сайте показываются демонстрационные серии с пометкой Demo, а для незаполненных контактов показывается аккуратная подсказка о настройке.<br>
                            Когда вы снимете этот флаг и сохраните: сайт перейдет в рабочий режим, демонстрационные серии скроются, и посетители увидят только ваши реальные опубликованные работы.
                        </p>
                    </div>
                </label>
            </div>
        </div>

        <div class="flex justify-end pt-2">
            <button type="submit" class="px-9 py-3.5 bg-neutral-900 hover:bg-neutral-800 text-white text-xs uppercase tracking-wider font-bold rounded-lg shadow-sm hover:shadow transition-all inline-flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span>Сохранить все настройки</span>
            </button>
        </div>

    </form>

</div>
@endsection
