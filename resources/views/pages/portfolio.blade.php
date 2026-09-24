@extends('layouts.app')

@section('title', __('site.portfolio_title') . ' — ' . __('site.author_name'))
@section('description', __('site.portfolio_subtitle'))

@section('content')
<div class="bg-arch-bg text-arch-text py-16 md:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="max-w-3xl mb-12">
            <span class="text-xs uppercase tracking-widest text-crimson font-mono font-bold block mb-2">
                ПОРТФОЛИО &bull; АРХИВ
            </span>
            <h1 class="text-4xl sm:text-6xl font-extrabold uppercase tracking-tightest font-display text-arch-text mb-4">
                {{ __('site.portfolio_subtitle') }}
            </h1>
        </div>

        {{-- Category Filters --}}
        <nav class="flex flex-wrap items-center gap-2 pb-6 mb-12 border-b border-arch-border font-mono text-xs uppercase" aria-label="Category Filters">
            <a href="{{ route('portfolio.index') }}" 
               class="px-5 py-2.5 transition-colors font-bold {{ empty($selectedCategorySlug) ? 'bg-crimson text-white shadow-crimson-btn' : 'bg-white text-arch-text border border-arch-border hover:border-black' }}">
                {{ __('site.all_categories') }}
            </a>

            @foreach($categories as $category)
                <a href="{{ route('portfolio.index', ['category' => $category->slug]) }}" 
                   class="px-5 py-2.5 transition-colors font-bold {{ $selectedCategorySlug === $category->slug ? 'bg-crimson text-white shadow-crimson-btn' : 'bg-white text-arch-text border border-arch-border hover:border-black' }}">
                    {{ $category->localizedName($locale) }}
                    <span class="ml-1 opacity-70">({{ $category->series_count }})</span>
                </a>
            @endforeach
        </nav>

        {{-- Series Grid --}}
        @if($seriesList->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                @foreach($seriesList as $index => $series)
                    <article class="bg-white border border-arch-border shadow-card-depth overflow-hidden group">
                        <a href="{{ route('series.show', ['slug' => $series->slug]) }}" class="block">
                            
                            <div class="overflow-hidden aspect-[16/10] relative bg-neutral-900">
                                <img src="{{ $series->cover_url }}" 
                                     alt="{{ $series->localizedTitle($locale) }}" 
                                     loading="lazy"
                                     class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105">
                                
                                <div class="absolute top-4 left-4 bg-black/80 text-white text-[0.68rem] uppercase tracking-wider font-mono font-bold px-3 py-1">
                                    {{ sprintf('%02d', $index + 1) }} &bull; {{ $series->category ? $series->category->localizedName($locale) : 'СЕРИЯ' }}
                                </div>

                                @if($series->is_demo)
                                <div class="absolute top-4 right-4 bg-crimson text-white text-[0.62rem] uppercase tracking-widest px-2.5 py-0.5 font-bold">
                                    ДЕМО
                                </div>
                                @endif
                            </div>

                            <div class="p-6 sm:p-8 space-y-3">
                                <div class="flex items-center justify-between text-xs text-neutral-500 font-mono">
                                    <span>{{ $series->localizedLocation($locale) }}</span>
                                    <span>{{ $series->photos->count() }} кадров</span>
                                </div>

                                <h2 class="text-2xl sm:text-3xl font-extrabold uppercase tracking-tight font-display text-arch-text group-hover:text-crimson transition-colors">
                                    {{ $series->localizedTitle($locale) }}
                                </h2>

                                @if($series->localizedDescription($locale))
                                <p class="text-xs text-neutral-600 line-clamp-2 pt-1 font-mono leading-relaxed">
                                    {{ $series->localizedDescription($locale) }}
                                </p>
                                @endif

                                <div class="pt-3">
                                    <span class="btn-crimson px-5 py-2.5 text-[0.7rem] font-bold">
                                        {{ __('site.view_series') }} &nearr;
                                    </span>
                                </div>
                            </div>

                        </a>
                    </article>
                @endforeach
            </div>
        @else
            <div class="text-center py-20 px-4 border border-arch-border bg-white max-w-xl mx-auto space-y-4">
                <span class="text-lg text-neutral-600 block font-mono">
                    {{ __('site.empty_category') }}
                </span>
                <div class="pt-2">
                    <a href="{{ route('portfolio.index') }}" 
                       class="btn-crimson px-6 py-3 text-xs font-bold">
                        {{ __('site.all_categories') }}
                    </a>
                </div>
            </div>
        @endif

    </div>
</div>
@endsection
