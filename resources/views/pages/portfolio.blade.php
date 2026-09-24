@extends('layouts.app')

@section('title', __('site.portfolio_title') . ' — ' . __('site.author_name'))
@section('description', __('site.portfolio_subtitle'))

@section('content')
<div class="py-16 md:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Apple Pro Header --}}
        <div class="max-w-3xl mb-12">
            <span class="text-xs uppercase tracking-widest text-apple-blue font-semibold font-mono block mb-2">
                {{ __('site.portfolio_title') }}
            </span>
            <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tightest apple-text-gradient mb-4">
                {{ __('site.portfolio_subtitle') }}
            </h1>
        </div>

        {{-- iOS Segmented Category Filter Pills --}}
        <nav class="flex flex-wrap items-center gap-2 pb-6 mb-12 border-b border-white/10" aria-label="Category Filters">
            <a href="{{ route('portfolio.index', ['locale' => $locale]) }}" 
               class="px-5 py-2 text-xs uppercase tracking-wider rounded-full transition-all duration-200 {{ empty($selectedCategorySlug) ? 'bg-white text-black font-bold shadow-[0_0_15px_rgba(255,255,255,0.3)]' : 'bg-white/5 text-neutral-400 hover:bg-white/10 hover:text-white border border-white/5' }}">
                {{ __('site.all_categories') }}
            </a>

            @foreach($categories as $category)
                <a href="{{ route('portfolio.index', ['locale' => $locale, 'category' => $category->slug]) }}" 
                   class="px-5 py-2 text-xs uppercase tracking-wider rounded-full transition-all duration-200 {{ $selectedCategorySlug === $category->slug ? 'bg-white text-black font-bold shadow-[0_0_15px_rgba(255,255,255,0.3)]' : 'bg-white/5 text-neutral-400 hover:bg-white/10 hover:text-white border border-white/5' }}">
                    {{ $category->localizedName($locale) }}
                    <span class="ml-1 text-[0.65rem] opacity-60 font-mono">({{ $category->series_count }})</span>
                </a>
            @endforeach
        </nav>

        {{-- Series Grid --}}
        @if($seriesList->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-10">
                @foreach($seriesList as $index => $series)
                    <article class="apple-card-glow rounded-3xl overflow-hidden group">
                        <a href="{{ route('series.show', ['locale' => $locale, 'slug' => $series->slug]) }}" class="block">
                            
                            <div class="overflow-hidden aspect-[16/10] relative">
                                <img src="{{ $series->cover_url }}" 
                                     alt="{{ $series->localizedTitle($locale) }}" 
                                     loading="lazy"
                                     width="800"
                                     height="500"
                                     class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105">
                                
                                <div class="absolute top-4 left-4 apple-glass rounded-full px-3.5 py-1 text-white text-[0.68rem] uppercase tracking-wider font-mono">
                                    {{ sprintf('%02d', $index + 1) }} &bull; {{ $series->category ? $series->category->localizedName($locale) : 'Series' }}
                                </div>

                                @if($series->is_demo)
                                <div class="absolute top-4 right-4 bg-apple-blue/90 text-white text-[0.62rem] uppercase tracking-widest px-2.5 py-0.5 rounded-full font-bold">
                                    Demo
                                </div>
                                @endif
                            </div>

                            <div class="p-6 sm:p-8 space-y-2">
                                <div class="flex items-center justify-between text-xs text-neutral-400 font-mono">
                                    <span>{{ $series->localizedLocation($locale) }}</span>
                                    <span>{{ $series->photos->count() }} кадров</span>
                                </div>

                                <h2 class="text-2xl sm:text-3xl font-bold text-white group-hover:text-apple-blue transition-colors">
                                    {{ $series->localizedTitle($locale) }}
                                </h2>

                                @if($series->localizedDescription($locale))
                                <p class="text-xs text-neutral-400 line-clamp-2 pt-1 font-light leading-relaxed">
                                    {{ $series->localizedDescription($locale) }}
                                </p>
                                @endif

                                <div class="pt-3">
                                    <span class="text-xs font-semibold text-apple-blue inline-flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                                        {{ __('site.view_series') }} &rarr;
                                    </span>
                                </div>
                            </div>

                        </a>
                    </article>
                @endforeach
            </div>
        @else
            {{-- Empty State --}}
            <div class="text-center py-24 px-4 border border-white/10 rounded-3xl bg-apple-card max-w-xl mx-auto space-y-4">
                <span class="text-xl text-neutral-300 block">
                    {{ __('site.empty_category') }}
                </span>
                <div class="pt-2">
                    <a href="{{ route('portfolio.index', ['locale' => $locale]) }}" 
                       class="inline-flex px-6 py-2.5 bg-white text-black rounded-full text-xs uppercase tracking-widest font-bold hover:bg-neutral-200 transition-colors">
                        {{ __('site.all_categories') }}
                    </a>
                </div>
            </div>
        @endif

    </div>
</div>
@endsection
