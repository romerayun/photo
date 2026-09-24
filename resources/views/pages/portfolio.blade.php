@extends('layouts.app')

@section('title', __('site.portfolio_title') . ' — ' . __('site.author_name'))
@section('description', __('site.portfolio_subtitle'))

@section('content')
<div class="py-12 md:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="max-w-3xl mb-12">
            <span class="text-xs uppercase tracking-widest text-terracotta font-medium block mb-2">
                {{ __('site.portfolio_title') }}
            </span>
            <h1 class="font-serif text-4xl sm:text-5xl text-graphite-950 editorial-heading mb-4">
                {{ __('site.portfolio_subtitle') }}
            </h1>
        </div>

        {{-- Category Filters --}}
        <nav class="flex flex-wrap items-center gap-2 pb-8 mb-12 border-b border-editorial-border" aria-label="Category Filters">
            <a href="{{ route('portfolio.index', ['locale' => $locale]) }}" 
               class="px-4 py-2 text-xs uppercase tracking-widest rounded-full transition-all duration-200 {{ empty($selectedCategorySlug) ? 'bg-graphite-900 text-white font-medium' : 'bg-surface text-graphite-700 hover:bg-subtle hover:text-graphite-950' }}">
                {{ __('site.all_categories') }}
            </a>

            @foreach($categories as $category)
                <a href="{{ route('portfolio.index', ['locale' => $locale, 'category' => $category->slug]) }}" 
                   class="px-4 py-2 text-xs uppercase tracking-widest rounded-full transition-all duration-200 {{ $selectedCategorySlug === $category->slug ? 'bg-graphite-900 text-white font-medium' : 'bg-surface text-graphite-700 hover:bg-subtle hover:text-graphite-950' }}">
                    {{ $category->localizedName($locale) }}
                    <span class="ml-1 text-[0.68rem] opacity-75 font-mono">({{ $category->series_count }})</span>
                </a>
            @endforeach
        </nav>

        {{-- Series Grid --}}
        @if($seriesList->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-16">
                @foreach($seriesList as $index => $series)
                    <article class="group">
                        <a href="{{ route('series.show', ['locale' => $locale, 'slug' => $series->slug]) }}" class="block focus:outline-none focus-visible:ring-2 focus-visible:ring-terracotta">
                            
                            {{-- Photo Container with Natural Ratio --}}
                            <div class="overflow-hidden bg-subtle aspect-[16/10] relative mb-5">
                                <img src="{{ $series->cover_url }}" 
                                     alt="{{ $series->localizedTitle($locale) }}" 
                                     loading="lazy"
                                     width="800"
                                     height="500"
                                     class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105">
                                
                                <div class="absolute top-4 left-4 bg-canvas/90 backdrop-blur-sm text-graphite-900 text-[0.7rem] uppercase tracking-wider px-3 py-1 font-mono">
                                    {{ sprintf('%02d', $index + 1) }} &bull; {{ $series->category ? $series->category->localizedName($locale) : 'Серия' }}
                                </div>

                                @if($series->is_demo)
                                <div class="absolute top-4 right-4 bg-terracotta/90 text-white text-[0.65rem] uppercase tracking-widest px-2.5 py-0.5">
                                    Demo
                                </div>
                                @endif
                            </div>

                            {{-- Metadata --}}
                            <div class="space-y-1">
                                <div class="flex items-center justify-between text-xs text-graphite-500 font-mono">
                                    <span>{{ $series->localizedLocation($locale) }}</span>
                                    <span>{{ $series->photos->count() }} кадров</span>
                                </div>

                                <h2 class="font-serif text-2xl sm:text-3xl text-graphite-950 group-hover:text-terracotta transition-colors">
                                    {{ $series->localizedTitle($locale) }}
                                </h2>

                                @if($series->localizedDescription($locale))
                                <p class="text-xs text-graphite-600 line-clamp-2 pt-1 font-light leading-relaxed">
                                    {{ $series->localizedDescription($locale) }}
                                </p>
                                @endif

                                <div class="pt-2">
                                    <span class="text-xs uppercase tracking-widest text-graphite-400 group-hover:text-terracotta group-hover:underline underline-offset-4 transition-colors font-medium">
                                        {{ __('site.view_series') }} &rarr;
                                    </span>
                                </div>
                            </div>

                        </a>
                    </article>
                @endforeach
            </div>
        @else
            {{-- Clean Empty State --}}
            <div class="text-center py-20 px-4 border border-editorial-border bg-surface max-w-xl mx-auto">
                <span class="font-serif text-2xl text-graphite-800 block mb-2">
                    {{ __('site.empty_category') }}
                </span>
                <div class="pt-6">
                    <a href="{{ route('portfolio.index', ['locale' => $locale]) }}" 
                       class="inline-flex px-6 py-2.5 bg-graphite-900 text-white text-xs uppercase tracking-widest font-medium hover:bg-terracotta transition-colors">
                        {{ __('site.all_categories') }}
                    </a>
                </div>
            </div>
        @endif

    </div>
</div>
@endsection
