@extends('layouts.app')

@section('title', 'Карта сайта — ' . __('site.author_name') . ' • Фотограф в Иркутске')
@section('description', 'Полная структура сайта фотографа Романа Юна: основные разделы, направления съёмок, фотосерии портфолио, тарифы и контактная информация.')

@section('content')
<div class="bg-arch-bg text-arch-text py-16 md:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16 sm:space-y-20">
        
        {{-- Header --}}
        <div class="border-b border-arch-border pb-10">
            <div class="max-w-3xl space-y-3">
                <div class="inline-flex items-center gap-2 text-xs font-mono uppercase tracking-widest text-crimson font-bold">
                    <span class="w-2 h-2 rounded-full bg-crimson animate-pulse"></span>
                    <span>НАВИГАЦИЯ</span>
                </div>
                <h1 class="text-4xl sm:text-6xl font-extrabold uppercase tracking-tightest font-display text-arch-text">
                    КАРТА САЙТА
                </h1>
                <p class="text-xs sm:text-sm text-neutral-600 font-mono leading-relaxed max-w-2xl">
                    Удобный указатель всех разделов, направлений съёмок, опубликованных серий и доступных услуг фотографа Романа Юна.
                </p>
            </div>
        </div>

        {{-- 1. Main Pages Section --}}
        <section aria-labelledby="section-main-pages" class="space-y-6">
            <div class="flex items-baseline justify-between border-b border-arch-border pb-3">
                <h2 id="section-main-pages" class="text-xl sm:text-2xl font-bold uppercase tracking-tight font-display text-arch-text flex items-center gap-3">
                    <span class="text-xs font-mono text-crimson font-bold">01</span>
                    <span>Основные разделы</span>
                </h2>
                <span class="text-xs font-mono text-neutral-500 uppercase">6 страниц</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                {{-- Home --}}
                <a href="{{ route('home') }}" class="group bg-white border border-arch-border p-6 shadow-sm hover:shadow-card-depth hover:border-arch-text transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between text-xs font-mono text-crimson font-bold mb-2">
                            <span>01.1</span>
                            <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                        </div>
                        <h3 class="text-lg font-bold uppercase tracking-tight font-display text-arch-text group-hover:text-crimson transition-colors">
                            {{ __('site.nav_home') }}
                        </h3>
                        <p class="text-xs text-neutral-600 font-mono mt-2 leading-relaxed">
                            Главная страница, ключевые кадры, творческий манифест, направления работы и отзывы.
                        </p>
                    </div>
                    <span class="text-[0.7rem] font-mono text-neutral-400 mt-4 block border-t border-neutral-100 pt-3">
                        /
                    </span>
                </a>

                {{-- Portfolio --}}
                <a href="{{ route('portfolio.index') }}" class="group bg-white border border-arch-border p-6 shadow-sm hover:shadow-card-depth hover:border-arch-text transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between text-xs font-mono text-crimson font-bold mb-2">
                            <span>01.2</span>
                            <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                        </div>
                        <h3 class="text-lg font-bold uppercase tracking-tight font-display text-arch-text group-hover:text-crimson transition-colors">
                            {{ __('site.nav_portfolio') }}
                        </h3>
                        <p class="text-xs text-neutral-600 font-mono mt-2 leading-relaxed">
                            Каталог портретных съёмок, индивидуальных историй, лавстори и студийных проектов.
                        </p>
                    </div>
                    <span class="text-[0.7rem] font-mono text-neutral-400 mt-4 block border-t border-neutral-100 pt-3">
                        /portfolio
                    </span>
                </a>

                {{-- Articles --}}
                <a href="{{ route('articles.index') }}" class="group bg-white border border-arch-border p-6 shadow-sm hover:shadow-card-depth hover:border-arch-text transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between text-xs font-mono text-crimson font-bold mb-2">
                            <span>01.3</span>
                            <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                        </div>
                        <h3 class="text-lg font-bold uppercase tracking-tight font-display text-arch-text group-hover:text-crimson transition-colors">
                            {{ __('site.nav_articles') }}
                        </h3>
                        <p class="text-xs text-neutral-600 font-mono mt-2 leading-relaxed">
                            Статьи и полезные заметки: свет, выбор образов, локации в Иркутске и на Байкале, подготовка к фотосессии.
                        </p>
                    </div>
                    <span class="text-[0.7rem] font-mono text-neutral-400 mt-4 block border-t border-neutral-100 pt-3">
                        /articles
                    </span>
                </a>

                {{-- Pricing --}}
                <a href="{{ route('pricing.index') }}" class="group bg-white border border-arch-border p-6 shadow-sm hover:shadow-card-depth hover:border-arch-text transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between text-xs font-mono text-crimson font-bold mb-2">
                            <span>01.4</span>
                            <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                        </div>
                        <h3 class="text-lg font-bold uppercase tracking-tight font-display text-arch-text group-hover:text-crimson transition-colors">
                            {{ __('site.nav_pricing') }}
                        </h3>
                        <p class="text-xs text-neutral-600 font-mono mt-2 leading-relaxed">
                            Форматы съёмок, пакеты услуг, условия работы, процесс подготовки и частые вопросы (FAQ).
                        </p>
                    </div>
                    <span class="text-[0.7rem] font-mono text-neutral-400 mt-4 block border-t border-neutral-100 pt-3">
                        /pricing
                    </span>
                </a>

                {{-- About --}}
                <a href="{{ route('about.index') }}" class="group bg-white border border-arch-border p-6 shadow-sm hover:shadow-card-depth hover:border-arch-text transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between text-xs font-mono text-crimson font-bold mb-2">
                            <span>01.5</span>
                            <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                        </div>
                        <h3 class="text-lg font-bold uppercase tracking-tight font-display text-arch-text group-hover:text-crimson transition-colors">
                            {{ __('site.nav_about') }}
                        </h3>
                        <p class="text-xs text-neutral-600 font-mono mt-2 leading-relaxed">
                            О фотографе, визуальный стиль, философия кадра, принципы съёмки и опыт в фотографии.
                        </p>
                    </div>
                    <span class="text-[0.7rem] font-mono text-neutral-400 mt-4 block border-t border-neutral-100 pt-3">
                        /about
                    </span>
                </a>

                {{-- Contacts --}}
                <a href="{{ route('contacts.index') }}" class="group bg-white border border-arch-border p-6 shadow-sm hover:shadow-card-depth hover:border-arch-text transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between text-xs font-mono text-crimson font-bold mb-2">
                            <span>01.6</span>
                            <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                        </div>
                        <h3 class="text-lg font-bold uppercase tracking-tight font-display text-arch-text group-hover:text-crimson transition-colors">
                            {{ __('site.nav_contacts') }}
                        </h3>
                        <p class="text-xs text-neutral-600 font-mono mt-2 leading-relaxed">
                            Прямые контакты, Telegram, WhatsApp, ссылки на соцсети и форма быстрой записи на съёмку.
                        </p>
                    </div>
                    <span class="text-[0.7rem] font-mono text-neutral-400 mt-4 block border-t border-neutral-100 pt-3">
                        /contacts
                    </span>
                </a>
            </div>
        </section>

        {{-- 2. Directions / Categories Section --}}
        @if($categories->count() > 0)
            <section aria-labelledby="section-categories" class="space-y-6">
                <div class="flex items-baseline justify-between border-b border-arch-border pb-3">
                    <h2 id="section-categories" class="text-xl sm:text-2xl font-bold uppercase tracking-tight font-display text-arch-text flex items-center gap-3">
                        <span class="text-xs font-mono text-crimson font-bold">02</span>
                        <span>Направления съёмок</span>
                    </h2>
                    <span class="text-xs font-mono text-neutral-500 uppercase">{{ $categories->count() }} формата</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($categories as $category)
                        <div class="bg-white border border-arch-border p-5 flex flex-col justify-between">
                            <div>
                                <span class="text-xs font-mono text-crimson font-bold block mb-1">
                                    #{{ $loop->iteration }}
                                </span>
                                <h3 class="text-base font-bold uppercase tracking-tight font-display text-arch-text">
                                    {{ $category->localizedName($locale) }}
                                </h3>
                                @if($category->localizedDescription($locale))
                                    <p class="text-xs text-neutral-500 font-mono mt-2 leading-relaxed line-clamp-3">
                                        {{ $category->localizedDescription($locale) }}
                                    </p>
                                @endif
                            </div>
                            <div class="pt-4 mt-3 border-t border-neutral-100 flex items-center justify-between text-xs font-mono">
                                <span class="text-neutral-400">Страница услуги:</span>
                                <a href="{{ route('categories.show', $category->slug) }}" class="text-crimson hover:underline font-bold flex items-center gap-1">
                                    <span>Перейти</span>
                                    <span>&rarr;</span>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- 3. All Portfolio Series --}}
        <section aria-labelledby="section-series" class="space-y-6">
            <div class="flex items-baseline justify-between border-b border-arch-border pb-3">
                <h2 id="section-series" class="text-xl sm:text-2xl font-bold uppercase tracking-tight font-display text-arch-text flex items-center gap-3">
                    <span class="text-xs font-mono text-crimson font-bold">03</span>
                    <span>Серии портфолио</span>
                </h2>
                <span class="text-xs font-mono text-neutral-500 uppercase">{{ $seriesList->count() }} историй</span>
            </div>

            @if($seriesList->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($seriesList as $series)
                        <a href="{{ route('series.show', ['slug' => $series->slug]) }}" 
                           class="group bg-white border border-arch-border p-4 hover:border-arch-text hover:shadow-card-depth transition-all duration-300 flex items-start gap-4">
                            
                            {{-- Thumbnail --}}
                            <div class="w-20 h-24 shrink-0 bg-neutral-900 border border-arch-border overflow-hidden">
                                @if($series->cover_photo_url)
                                    <img src="{{ $series->cover_photo_url }}" 
                                         alt="{{ $series->title }}" 
                                         loading="lazy"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-[0.65rem] font-mono text-neutral-500 uppercase">
                                        Фото
                                    </div>
                                @endif
                            </div>

                            {{-- Details --}}
                            <div class="min-w-0 flex-1 space-y-1">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-[0.68rem] font-mono text-crimson font-bold uppercase tracking-wider truncate">
                                        {{ $series->category?->localizedName($locale) ?? 'Серия' }}
                                    </span>
                                    <span class="text-neutral-400 group-hover:text-black group-hover:translate-x-1 transition-all text-xs font-bold">
                                        &rarr;
                                    </span>
                                </div>
                                <h3 class="text-sm font-bold uppercase tracking-tight text-arch-text group-hover:text-crimson font-display line-clamp-1 transition-colors">
                                    {{ $series->title }}
                                </h3>
                                @if($series->description)
                                    <p class="text-[0.72rem] text-neutral-500 font-mono line-clamp-2 leading-relaxed">
                                        {{ $series->description }}
                                    </p>
                                @endif
                                <div class="text-[0.68rem] font-mono text-neutral-400 pt-1 flex items-center gap-3">
                                    @if($series->photos_count ?? $series->photos->count())
                                        <span>{{ $series->photos_count ?? $series->photos->count() }} кадров</span>
                                    @endif
                                    @if($series->shoot_date)
                                        <span>&bull; {{ \Carbon\Carbon::parse($series->shoot_date)->translatedFormat('F Y') }}</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="bg-white border border-arch-border p-8 text-center text-xs font-mono text-neutral-500">
                    Серии портфолио готовятся к публикации.
                </div>
            @endif
        </section>

        {{-- 4. Pricing Packages --}}
        @if($packages->count() > 0)
            <section aria-labelledby="section-pricing" class="space-y-6">
                <div class="flex items-baseline justify-between border-b border-arch-border pb-3">
                    <h2 id="section-pricing" class="text-xl sm:text-2xl font-bold uppercase tracking-tight font-display text-arch-text flex items-center gap-3">
                        <span class="text-xs font-mono text-crimson font-bold">04</span>
                        <span>Услуги и пакеты съёмок</span>
                    </h2>
                    <span class="text-xs font-mono text-neutral-500 uppercase">{{ $packages->count() }} тарифа</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($packages as $pkg)
                        <div class="bg-white border border-arch-border p-6 flex flex-col justify-between">
                            <div>
                                <div class="flex items-baseline justify-between gap-2 mb-2">
                                    <h3 class="text-lg font-bold uppercase tracking-tight font-display text-arch-text">
                                        {{ $pkg->title_ru }}
                                    </h3>
                                    <span class="text-sm font-mono font-bold text-crimson">
                                        {{ $pkg->formatted_price }}
                                    </span>
                                </div>
                                @if($pkg->subtitle_ru)
                                    <p class="text-xs text-neutral-600 font-mono mb-3">
                                        {{ $pkg->subtitle_ru }}
                                    </p>
                                @endif
                                <div class="space-y-1.5 text-xs font-mono text-neutral-500 border-t border-neutral-100 pt-3">
                                    @if($pkg->duration_ru)
                                        <div>&bull; Длительность: <span class="text-neutral-800">{{ $pkg->duration_ru }}</span></div>
                                    @endif
                                    @if($pkg->photo_count_ru)
                                        <div>&bull; Фотографии: <span class="text-neutral-800">{{ $pkg->photo_count_ru }}</span></div>
                                    @endif
                                    @if($pkg->delivery_time_ru)
                                        <div>&bull; Готовность: <span class="text-neutral-800">{{ $pkg->delivery_time_ru }}</span></div>
                                    @endif
                                </div>
                            </div>

                            <div class="pt-5 mt-4 border-t border-neutral-100 flex items-center justify-between">
                                <a href="{{ route('pricing.index') }}" class="text-xs font-mono font-bold text-arch-text hover:text-crimson transition-colors flex items-center gap-1">
                                    <span>Условия и детали</span>
                                    <span>&rarr;</span>
                                </a>
                                <a href="{{ route('contacts.index') }}" class="text-xs font-mono text-crimson font-bold hover:underline">
                                    Записаться
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- 5. Articles Section --}}
        @if(isset($articles) && $articles->count() > 0)
            <section aria-labelledby="section-articles" class="space-y-6">
                <div class="flex items-baseline justify-between border-b border-arch-border pb-3">
                    <h2 id="section-articles" class="text-xl sm:text-2xl font-bold uppercase tracking-tight font-display text-arch-text flex items-center gap-3">
                        <span class="text-xs font-mono text-crimson font-bold">05</span>
                        <span>Статьи и полезные материалы</span>
                    </h2>
                    <span class="text-xs font-mono text-neutral-500 uppercase">{{ $articles->count() }} публикаций</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($articles as $art)
                        <a href="{{ route('articles.show', $art->slug) }}" class="group bg-white border border-arch-border p-5 flex gap-4 items-start shadow-sm hover:shadow-card-depth hover:border-arch-text transition-all duration-300">
                            <div class="w-16 h-16 shrink-0 bg-neutral-900 border border-arch-border overflow-hidden">
                                <img src="{{ $art->cover_url }}" alt="{{ $art->title }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            </div>
                            <div class="min-w-0 flex-1 space-y-1">
                                <div class="flex items-center justify-between gap-1 text-[0.68rem] font-mono text-crimson font-bold uppercase">
                                    <span>{{ $art->formatted_date }}</span>
                                    <span class="text-neutral-400 group-hover:text-black group-hover:translate-x-1 transition-all">&rarr;</span>
                                </div>
                                <h3 class="text-xs font-bold uppercase tracking-tight text-arch-text group-hover:text-crimson font-display line-clamp-2 transition-colors">
                                    {{ $art->title }}
                                </h3>
                                <div class="text-[0.65rem] font-mono text-neutral-400 pt-1 flex items-center gap-2">
                                    <span>👁 {{ $art->views_count }}</span>
                                    <span>&bull;</span>
                                    <span>💬 {{ $art->comments_count ?? $art->comments->count() }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- 6. Call to action banner --}}
        <section class="bg-white border border-arch-border p-8 sm:p-12 shadow-card-depth flex flex-col md:flex-row md:items-center justify-between gap-6" aria-label="Связь с фотографом">
            <div class="space-y-2 max-w-2xl">
                <span class="text-xs font-mono uppercase tracking-widest text-crimson font-bold block">
                    ОБСУДИТЬ СЪЁМКУ
                </span>
                <h2 class="text-2xl sm:text-3xl font-extrabold uppercase tracking-tight font-display text-arch-text">
                    Не нашли ответ или хотите индивидуальный формат?
                </h2>
                <p class="text-xs sm:text-sm text-neutral-600 font-mono leading-relaxed">
                    Напишите мне в Telegram или WhatsApp — расскажу все нюансы, подскажу локации и подберу образ.
                </p>
            </div>

            <div class="flex items-center gap-4 shrink-0">
                <a href="{{ route('contacts.index') }}" class="btn-crimson px-8 py-4 text-xs font-bold font-mono tracking-widest shadow-crimson-btn inline-flex items-center gap-2">
                    <span>СВЯЗАТЬСЯ</span>
                    <span>&rarr;</span>
                </a>
            </div>
        </section>

    </div>
</div>
@endsection
