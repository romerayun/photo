@extends('layouts.app')

@section('title', ($category->meta_title ?: ($category->localizedName($locale) . ' — ' . __('site.author_name') . ' • Фотограф в Иркутске')))
@section('description', ($category->meta_description ?: ($category->localizedDescription($locale) ?: 'Фотосъёмка в категории ' . $category->localizedName($locale) . ' от фотографа Романа Юна в Иркутске. Примеры серий, идеи и подробное описание.')))

@if($seriesList->currentPage() > 1)
    @section('canonical', $seriesList->url($seriesList->currentPage()))
    @section('og_url', $seriesList->url($seriesList->currentPage()))
@else
    @section('canonical', route('categories.show', $category->slug))
    @section('og_url', route('categories.show', $category->slug))
@endif

@section('og_title', ($category->meta_title ?: ($category->localizedName($locale) . ' — Роман Юн')))
@section('og_description', ($category->meta_description ?: ($category->localizedDescription($locale) ?: 'Фотосъёмка в категории ' . $category->localizedName($locale) . ' в Иркутске.')))
@section('og_image', $category->image_url)

@push('meta_links')
    @if($seriesList->hasMorePages())
        <link rel="next" href="{{ $seriesList->nextPageUrl() }}">
    @endif
    @if(!$seriesList->onFirstPage())
        <link rel="prev" href="{{ $seriesList->currentPage() === 2 ? route('categories.show', $category->slug) : $seriesList->previousPageUrl() }}">
    @endif
@endpush

@section('content')
<div class="bg-arch-bg text-arch-text py-12 md:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16 md:space-y-24">

        {{-- 1. Top Navigation & Category Master Header --}}
        <div>
            {{-- Breadcrumbs / Back button --}}
            <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
                <nav class="flex items-center gap-2 text-xs font-mono uppercase tracking-widest text-neutral-500 font-bold" aria-label="Хлебные крошки">
                    <a href="{{ route('home') }}" class="hover:text-crimson transition-colors">Главная</a>
                    <span>/</span>
                    <a href="{{ route('portfolio.index') }}" class="hover:text-crimson transition-colors">Портфолио</a>
                    <span>/</span>
                    <span class="text-arch-text">{{ $category->localizedName($locale) }}</span>
                </nav>

                {{-- Category Pill Switcher --}}
                @if(isset($otherCategories) && $otherCategories->count() > 0)
                <div class="flex items-center gap-2 overflow-x-auto pb-1 max-w-full text-xs font-mono scrollbar-none">
                    <span class="text-neutral-400 uppercase text-[0.68rem] tracking-wider shrink-0">Другие направления:</span>
                    @foreach($otherCategories as $otherCat)
                        <a href="{{ route('categories.show', $otherCat->slug) }}" 
                           class="px-2.5 py-1 bg-white hover:bg-neutral-900 text-neutral-700 hover:text-white border border-arch-border hover:border-black text-[0.68rem] font-bold uppercase tracking-wider transition-colors shrink-0">
                            {{ $otherCat->localizedName($locale) }}
                        </a>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Hero Header with Split Layout --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center border-b border-arch-border pb-12 sm:pb-16">
                
                {{-- Left Text Column --}}
                <div class="lg:col-span-7 space-y-5">
                    <div class="inline-flex items-center gap-2 text-xs font-mono uppercase tracking-widest text-crimson font-bold">
                        <span class="w-2 h-2 rounded-full bg-crimson animate-pulse"></span>
                        <span>НАПРАВЛЕНИЕ СЪЁМКИ &bull; ИРКУТСК</span>
                    </div>

                    <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold uppercase tracking-tightest font-display text-arch-text leading-[1.05]">
                        {{ $category->localizedName($locale) }}
                    </h1>

                    @if($category->localizedDescription($locale))
                        <p class="text-sm sm:text-base text-neutral-600 font-mono leading-relaxed max-w-2xl">
                            {{ $category->localizedDescription($locale) }}
                        </p>
                    @endif

                    <div class="pt-2 flex flex-wrap items-center gap-4">
                        <a href="#series-works" class="btn-crimson px-7 py-3 text-xs font-bold font-mono tracking-wider uppercase inline-flex items-center gap-2">
                            <span>Смотреть работы</span>
                            <span>&darr;</span>
                        </a>

                        @if(\App\Models\Setting::hasTelegram())
                            <a href="{{ \App\Models\Setting::telegramUrl('Здравствуйте, Роман! Меня интересует съёмка в категории «' . $category->localizedName($locale) . '». Хочу узнать подробнее.') }}" 
                               target="_blank" 
                               rel="noopener" 
                               class="px-5 py-3 bg-white hover:bg-neutral-900 text-arch-text hover:text-white border border-arch-border hover:border-black font-mono text-xs uppercase tracking-wider font-bold transition-colors inline-flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 text-[#229ED9]" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.75-.55 2.92-1.27 4.86-2.11 5.83-2.52 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .38z"/>
                                </svg>
                                <span>Обсудить это направление</span>
                                <span>&nearr;</span>
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Right Visual Banner --}}
                <div class="lg:col-span-5">
                    <div class="relative aspect-[4/3] sm:aspect-[16/10] overflow-hidden bg-neutral-900 border border-arch-border shadow-card-depth group">
                        <img src="{{ $category->image_url }}" 
                             alt="{{ $category->localizedName($locale) }}" 
                             loading="eager" 
                             class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105">
                        
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        <div class="absolute bottom-4 left-4 right-4 flex items-center justify-between text-white font-mono text-xs uppercase font-bold">
                            <span>{{ $category->localizedName($locale) }}</span>
                            <span>{{ $seriesList->total() }} {{ trans_choice('серия|серии|серий', $seriesList->total()) }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- 2. Detailed Category Quill Rich Content (Description) --}}
        @if($category->localizedContent($locale))
            <section class="max-w-4xl mx-auto space-y-6">
                <div class="flex items-center gap-2 text-xs font-mono text-crimson uppercase tracking-widest font-bold border-b border-arch-border pb-3">
                    <span>ПОДРОБНО О ФОРМАТЕ СЪЁМКИ</span>
                </div>

                <div class="article-rich-content category-rich-content prose max-w-none 
                            prose-headings:font-display prose-headings:tracking-tight prose-headings:text-arch-text
                            prose-h2:text-2xl sm:prose-h2:text-3xl prose-h2:mt-10 prose-h2:mb-4 prose-h2:border-b prose-h2:border-arch-border prose-h2:pb-3
                            prose-h3:text-xl sm:prose-h3:text-2xl prose-h3:mt-8 prose-h3:mb-3 prose-h3:text-neutral-800
                            prose-p:text-neutral-700 prose-p:text-base sm:prose-p:text-lg prose-p:leading-relaxed prose-p:mb-5 font-mono
                            prose-li:text-neutral-700 prose-li:text-base sm:prose-li:text-lg font-mono
                            prose-strong:text-arch-text prose-strong:font-bold
                            prose-blockquote:border-l-4 prose-blockquote:border-crimson prose-blockquote:bg-white prose-blockquote:py-4 prose-blockquote:px-6 prose-blockquote:shadow-sm prose-blockquote:text-neutral-700 prose-blockquote:italic
                            prose-img:rounded-md prose-img:border prose-img:border-arch-border prose-img:shadow-card-depth prose-img:my-8 prose-img:max-h-[600px] prose-img:w-full prose-img:object-cover">
                    {!! $category->localizedContent($locale) !!}
                </div>
            </section>
        @endif

        {{-- 3. Works Grid (Series belonging to this category) --}}
        <section id="series-works" class="space-y-8 pt-6">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-arch-border pb-4">
                <div>
                    <span class="text-xs uppercase tracking-widest text-crimson font-mono font-bold block mb-1">
                        ПОРТФОЛИО
                    </span>
                    <h2 class="text-2xl sm:text-4xl font-extrabold uppercase tracking-tight font-display text-arch-text">
                        РАБОТЫ: {{ $category->localizedName($locale) }}
                    </h2>
                </div>
                <div class="text-xs font-mono text-neutral-500">
                    Всего в категории: <span class="font-bold text-arch-text">{{ $seriesList->total() }}</span>
                </div>
            </div>

            @if($seriesList->count() > 0)
                <div id="series-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                    @include('pages.partials.series-cards', ['seriesList' => $seriesList, 'locale' => $locale])
                </div>

                {{-- Load More Section --}}
                @if($seriesList->hasMorePages())
                    <div id="load-more-container" class="mt-14 sm:mt-16 text-center">
                        <a href="{{ $seriesList->nextPageUrl() }}"
                           id="load-more-btn"
                           data-next-page="{{ $seriesList->currentPage() + 1 }}"
                           class="group inline-flex items-center justify-center gap-3 px-10 py-4 bg-white hover:bg-neutral-900 text-arch-text hover:text-white border-2 border-arch-text font-mono text-xs uppercase tracking-widest font-bold transition-all duration-300 shadow-card-depth hover:shadow-crimson-btn active:scale-95 cursor-pointer">
                            <span id="load-more-text" class="inline-flex items-center gap-2">
                                <span>Загрузить ещё работы</span>
                                <span class="text-crimson font-bold text-sm transition-transform duration-300 group-hover:translate-y-1">&darr;</span>
                            </span>
                            <span id="load-more-spinner" class="hidden items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-crimson" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                <span>Загрузка...</span>
                            </span>
                        </a>
                    </div>
                @endif

                {{-- SEO Pagination Links --}}
                @if($seriesList->lastPage() > 1)
                    <nav class="mt-10 flex items-center justify-center gap-2 font-mono text-xs" aria-label="Пагинация работ">
                        @if(!$seriesList->onFirstPage())
                            <a href="{{ $seriesList->currentPage() === 2 ? route('categories.show', $category->slug) : $seriesList->previousPageUrl() }}" 
                               class="px-3.5 py-2 border border-arch-border hover:border-arch-text bg-white text-arch-text transition-colors">
                                &larr; Назад
                            </a>
                        @endif

                        @for($p = 1; $p <= $seriesList->lastPage(); $p++)
                            @if($p == $seriesList->currentPage())
                                <span class="px-3.5 py-2 border border-crimson bg-crimson text-white font-bold" aria-current="page">
                                    {{ $p }}
                                </span>
                            @else
                                <a href="{{ $p === 1 ? route('categories.show', $category->slug) : $seriesList->url($p) }}" 
                                   class="px-3.5 py-2 border border-arch-border hover:border-arch-text bg-white text-arch-text transition-colors">
                                    {{ $p }}
                                </a>
                            @endif
                        @endfor

                        @if($seriesList->hasMorePages())
                            <a href="{{ $seriesList->nextPageUrl() }}" 
                               class="px-3.5 py-2 border border-arch-border hover:border-arch-text bg-white text-arch-text transition-colors">
                                Вперёд &rarr;
                            </a>
                        @endif
                    </nav>
                @endif
            @else
                <div class="bg-white border border-arch-border p-12 text-center space-y-4 shadow-sm max-w-xl mx-auto">
                    <p class="text-base text-neutral-700 font-mono">
                        Серии по направлению «{{ $category->localizedName($locale) }}» сейчас готовятся к публикации.
                    </p>
                    <p class="text-xs text-neutral-500 font-mono">
                        Вы можете посмотреть все серии в общем портфолио или написать мне, чтобы обсудить такую съёмку.
                    </p>
                    <div class="pt-2 flex justify-center gap-3">
                        <a href="{{ route('portfolio.index') }}" class="px-5 py-2.5 bg-white border border-arch-text text-arch-text font-mono text-xs uppercase font-bold hover:bg-neutral-900 hover:text-white transition-colors">
                            Все работы
                        </a>
                        <a href="{{ route('contacts.index') }}" class="btn-crimson px-5 py-2.5 text-xs font-bold">
                            Связаться &rarr;
                        </a>
                    </div>
                </div>
            @endif
        </section>

        {{-- 4. Consultation & Booking Banner --}}
        <section class="bg-neutral-950 text-white border border-neutral-800 p-8 sm:p-12 shadow-card-depth relative overflow-hidden">
            <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-crimson/15 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                <div class="lg:col-span-8 space-y-3">
                    <span class="text-xs uppercase tracking-widest text-crimson font-mono font-bold block">
                        ЗАПИСЬ НА СЪЁМКУ
                    </span>
                    <h3 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold uppercase font-display tracking-tight text-white leading-tight">
                        Хотите съёмку в стиле «{{ $category->localizedName($locale) }}»?
                    </h3>
                    <p class="text-xs sm:text-sm text-neutral-400 font-mono leading-relaxed max-w-2xl">
                        Подскажу подходящие места в Иркутске или на природе, посоветую одежду и время суток с красивым светом. На съёмке будет легко и комфортно.
                    </p>
                </div>

                <div class="lg:col-span-4 flex flex-col gap-3">
                    @if(\App\Models\Setting::hasTelegram())
                        <a href="{{ \App\Models\Setting::telegramUrl('Здравствуйте, Роман! Хочу провести съёмку в категории «' . $category->localizedName($locale) . '».') }}" 
                           target="_blank" 
                           rel="noopener" 
                           class="btn-crimson w-full py-3.5 text-xs font-bold text-center block font-mono">
                            <span>НАПИСАТЬ В TELEGRAM &rarr;</span>
                        </a>
                    @endif
                    <a href="{{ route('contacts.index', ['package' => $category->localizedName($locale)]) }}#feedback-form" 
                       class="w-full py-3 px-4 bg-white/5 hover:bg-white/10 text-neutral-200 hover:text-white border border-white/10 text-xs font-mono uppercase tracking-wider text-center transition-colors font-bold">
                        <span>Заполнить заявку на сайте</span>
                    </a>
                </div>
            </div>
        </section>

    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const loadMoreBtn = document.getElementById('load-more-btn');
        const grid = document.getElementById('series-grid');
        const spinner = document.getElementById('load-more-spinner');
        const btnText = document.getElementById('load-more-text');
        const container = document.getElementById('load-more-container');

        if (!loadMoreBtn) return;

        loadMoreBtn.addEventListener('click', function (e) {
            e.preventDefault();
            const nextPage = this.getAttribute('data-next-page');
            const targetUrl = new URL(this.href, window.location.origin);
            targetUrl.searchParams.set('page', nextPage);

            btnText.classList.add('hidden');
            spinner.classList.remove('hidden');
            spinner.classList.add('inline-flex');
            loadMoreBtn.classList.add('pointer-events-none', 'opacity-80');

            fetch(targetUrl.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.html) {
                    grid.insertAdjacentHTML('beforeend', data.html);
                }

                if (data.hasMore) {
                    loadMoreBtn.setAttribute('data-next-page', data.nextPage);
                    loadMoreBtn.href = targetUrl.toString();
                    btnText.classList.remove('hidden');
                    spinner.classList.add('hidden');
                    spinner.classList.remove('inline-flex');
                    loadMoreBtn.classList.remove('pointer-events-none', 'opacity-80');
                } else {
                    container.remove();
                }
            })
            .catch(err => {
                console.error('Ошибка загрузки серий:', err);
                window.location.href = targetUrl.toString();
            });
        });
    });
</script>
@endpush
