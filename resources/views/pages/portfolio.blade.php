@extends('layouts.app')

@section('title', __('site.portfolio_title') . ' — ' . __('site.author_name'))
@section('description', __('site.portfolio_subtitle'))

@if($seriesList->currentPage() > 1)
    @section('canonical', $seriesList->url($seriesList->currentPage()))
    @section('og_url', $seriesList->url($seriesList->currentPage()))
@else
    @section('canonical', route('portfolio.index'))
    @section('og_url', route('portfolio.index'))
@endif

@push('meta_links')
    @if($seriesList->hasMorePages())
        <link rel="next" href="{{ $seriesList->nextPageUrl() }}">
    @endif
    @if(!$seriesList->onFirstPage())
        <link rel="prev" href="{{ $seriesList->currentPage() === 2 ? route('portfolio.index') : $seriesList->previousPageUrl() }}">
    @endif
@endpush

@section('content')
<div class="bg-arch-bg text-arch-text py-16 md:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="max-w-3xl mb-12 sm:mb-16">
            <span class="text-xs uppercase tracking-widest text-crimson font-mono font-bold block mb-2">
                РОМАН ЮН &bull; ФОТОГРАФ В ИРКУТСКЕ
            </span>
            <h1 class="text-4xl sm:text-6xl font-extrabold uppercase tracking-tightest font-display text-arch-text mb-4">
                ПОРТФОЛИО
            </h1>
            <p class="text-xs sm:text-sm text-neutral-600 font-mono leading-relaxed">
                Люди, свет и настроение — в моих портретных съёмках. <br>Откройте серию, чтобы посмотреть больше фотографий.
            </p>
        </div>

        {{-- Series Grid (3:4 Cards, 3 Columns on desktop) --}}
        @if($seriesList->count() > 0)
            <div id="series-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                @include('pages.partials.series-cards', ['seriesList' => $seriesList, 'locale' => $locale])
            </div>

            {{-- Load More Section (standard <a> for crawlers & noscript) --}}
            @if($seriesList->hasMorePages())
                <div id="load-more-container" class="mt-14 sm:mt-16 text-center">
                    <a href="{{ $seriesList->nextPageUrl() }}"
                       id="load-more-btn"
                       data-next-page="{{ $seriesList->currentPage() + 1 }}"
                       class="group inline-flex items-center justify-center gap-3 px-10 py-4 bg-white hover:bg-neutral-900 text-arch-text hover:text-white border-2 border-arch-text font-mono text-xs uppercase tracking-widest font-bold transition-all duration-300 shadow-card-depth hover:shadow-crimson-btn active:scale-95 cursor-pointer">
                        <span id="load-more-text" class="inline-flex items-center gap-2">
                            <span>Загрузить ещё</span>
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

            {{-- Standard SEO Pagination Links --}}
            @if($seriesList->lastPage() > 1)
                <nav class="mt-10 flex items-center justify-center gap-2 font-mono text-xs" aria-label="Пагинация портфолио">
                    @if(!$seriesList->onFirstPage())
                        <a href="{{ $seriesList->currentPage() === 2 ? route('portfolio.index') : $seriesList->previousPageUrl() }}" 
                           class="px-3.5 py-2 border border-arch-border hover:border-arch-text bg-white text-arch-text transition-colors"
                           title="Предыдущая страница">
                            &larr; Назад
                        </a>
                    @endif

                    @for($p = 1; $p <= $seriesList->lastPage(); $p++)
                        @if($p == $seriesList->currentPage())
                            <span class="px-3.5 py-2 border border-crimson bg-crimson text-white font-bold" aria-current="page">
                                {{ $p }}
                            </span>
                        @else
                            <a href="{{ $p === 1 ? route('portfolio.index') : $seriesList->url($p) }}" 
                               class="px-3.5 py-2 border border-arch-border hover:border-arch-text bg-white text-arch-text transition-colors">
                                {{ $p }}
                            </a>
                        @endif
                    @endfor

                    @if($seriesList->hasMorePages())
                        <a href="{{ $seriesList->nextPageUrl() }}" 
                           class="px-3.5 py-2 border border-arch-border hover:border-arch-text bg-white text-arch-text transition-colors"
                           title="Следующая страница">
                            Вперёд &rarr;
                        </a>
                    @endif
                </nav>
            @endif

            {{-- Transition to booking / Call to Action --}}
            <section class="mt-16 sm:mt-24 pt-12 sm:pt-16 border-t border-arch-border" aria-label="Переход к заказу съёмки">
                <div class="bg-white border border-arch-border p-8 sm:p-12 lg:p-14 shadow-card-depth">
                    <div class="max-w-3xl space-y-4">
                        <h2 class="text-2xl sm:text-4xl lg:text-5xl font-extrabold uppercase tracking-tight font-display text-arch-text leading-[1.1]">
                            ХОТИТЕ СВОЮ СЪЁМКУ?
                        </h2>
                        <p class="text-xs sm:text-sm md:text-base text-neutral-600 font-mono leading-relaxed pt-1">
                            Посмотрите форматы и стоимость или напишите мне — обсудим вашу идею.
                        </p>
                        <div class="pt-4 sm:pt-6 flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
                            <a href="{{ route('pricing.index') }}" 
                               class="px-8 py-4 bg-white hover:bg-neutral-900 text-arch-text hover:text-white border-2 border-arch-text font-mono text-xs uppercase tracking-widest font-bold transition-all duration-300 inline-flex items-center justify-center gap-2">
                                <span>УСЛУГИ И ЦЕНЫ</span>
                                <span>&rarr;</span>
                            </a>
                            <a href="{{ route('contacts.index') }}" 
                               class="btn-crimson px-8 py-4 text-xs font-bold font-mono tracking-widest shadow-crimson-btn inline-flex items-center justify-center gap-2">
                                <span>ОБСУДИТЬ СЪЁМКУ</span>
                                <span>&rarr;</span>
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        @else
            <div class="text-center py-20 px-4 border border-arch-border bg-white max-w-xl mx-auto space-y-4">
                <span class="text-lg text-neutral-600 block font-mono">
                    Пока нет опубликованных серий.
                </span>
                <div class="pt-2">
                    <a href="{{ route('home') }}" 
                       class="btn-crimson px-6 py-3 text-xs font-bold">
                        На главную
                    </a>
                </div>
            </div>
        @endif

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const loadMoreBtn = document.getElementById('load-more-btn');
    const loadMoreContainer = document.getElementById('load-more-container');
    const seriesGrid = document.getElementById('series-grid');
    const loadMoreText = document.getElementById('load-more-text');
    const loadMoreSpinner = document.getElementById('load-more-spinner');

    if (!loadMoreBtn || !seriesGrid) return;

    let isLoading = false;

    loadMoreBtn.addEventListener('click', async function (e) {
        e.preventDefault();
        if (isLoading) return;

        const nextPage = loadMoreBtn.getAttribute('data-next-page');
        if (!nextPage) return;

        isLoading = true;
        loadMoreBtn.classList.add('opacity-70', 'pointer-events-none');
        loadMoreText.classList.add('hidden');
        loadMoreSpinner.classList.remove('hidden');
        loadMoreSpinner.classList.add('inline-flex');

        try {
            const url = new URL(loadMoreBtn.getAttribute('href') || window.location.href);
            url.searchParams.set('page', nextPage);
            url.searchParams.set('ajax', '1');

            const response = await fetch(url.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Ошибка сети при загрузке серий');
            }

            const data = await response.json();

            if (data.html) {
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = data.html;
                const newCards = Array.from(tempDiv.children);

                newCards.forEach((card) => {
                    card.classList.add('animate-card-fade-in');
                    seriesGrid.appendChild(card);
                });
            }

            if (data.hasMore) {
                loadMoreBtn.setAttribute('data-next-page', data.nextPage);
                loadMoreBtn.setAttribute('href', `{{ route('portfolio.index') }}?page=${data.nextPage}`);
                loadMoreBtn.classList.remove('opacity-70', 'pointer-events-none');
                loadMoreText.classList.remove('hidden');
                loadMoreSpinner.classList.add('hidden');
                loadMoreSpinner.classList.remove('inline-flex');
            } else {
                if (loadMoreContainer) {
                    loadMoreContainer.innerHTML = '<p class="text-xs font-mono text-neutral-400 uppercase tracking-widest py-4">Все серии загружены</p>';
                }
            }
        } catch (error) {
            console.error('Ошибка:', error);
            loadMoreBtn.classList.remove('opacity-70', 'pointer-events-none');
            loadMoreText.classList.remove('hidden');
            loadMoreSpinner.classList.add('hidden');
            loadMoreSpinner.classList.remove('inline-flex');
        } finally {
            isLoading = false;
        }
    });
});
</script>
@endsection
