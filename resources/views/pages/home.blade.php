@extends('layouts.app')

@section('title', __('site.meta_title'))
@section('description', __('site.meta_description'))

@section('content')

{{-- 1. HERO SECTION --}}
<section class="relative pt-8 pb-20 md:pt-16 md:pb-28 border-b border-editorial-border overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Top Editorial Kicker --}}
        <div class="flex items-center justify-between pb-6 border-b border-editorial-line text-xs uppercase tracking-widest text-graphite-500">
            <span>{{ __('site.location') }}, RU</span>
            <span class="hidden sm:inline font-mono">AUTUMN / WINTER 2026</span>
            <span>{{ __('site.author_role') }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center pt-10">
            
            {{-- Left Typographic Column --}}
            <div class="lg:col-span-6 space-y-8 pr-0 lg:pr-6">
                <div>
                    <h1 class="font-serif text-5xl sm:text-6xl md:text-7xl font-normal tracking-tight text-graphite-950 editorial-heading">
                        {{ __('site.author_name') }}
                    </h1>
                    <p class="font-serif italic text-2xl sm:text-3xl text-terracotta mt-3 font-normal">
                        {{ \App\Models\Setting::get("hero_phrase_{$locale}", __('site.hero_phrase')) }}
                    </p>
                </div>

                <p class="text-base sm:text-lg text-graphite-700 leading-relaxed max-w-xl font-light">
                    {{ \App\Models\Setting::get("hero_sub_{$locale}", __('site.hero_description')) }}
                </p>

                {{-- Action Buttons --}}
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 pt-2">
                    <a href="{{ route('contacts.index', ['locale' => $locale]) }}" 
                       class="inline-flex justify-center items-center px-8 py-4 bg-graphite-900 text-white text-xs uppercase tracking-widest font-medium hover:bg-terracotta transition-colors duration-200 shadow-sm">
                        {{ __('site.hero_cta') }}
                    </a>
                    <a href="{{ route('portfolio.index', ['locale' => $locale]) }}" 
                       class="inline-flex justify-center items-center px-6 py-4 text-xs uppercase tracking-widest font-medium text-graphite-900 hover:text-terracotta border-b border-graphite-900 hover:border-terracotta transition-colors">
                        {{ __('site.hero_portfolio') }} &rarr;
                    </a>
                </div>
            </div>

            {{-- Right Asymmetric Photo Composition --}}
            <div class="lg:col-span-6 relative">
                <div class="grid grid-cols-12 gap-4 items-end">
                    
                    {{-- Main Vertical Photo --}}
                    <div class="col-span-8 overflow-hidden bg-subtle aspect-[3/4] shadow-sm relative group">
                        <img src="{{ asset('storage/demo/hero-main.jpg') }}" 
                             alt="{{ __('site.author_name') }} — {{ __('site.author_role') }}" 
                             fetchpriority="high"
                             loading="eager"
                             width="800"
                             height="1067"
                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                        <div class="absolute bottom-3 left-3 bg-graphite-950/80 backdrop-blur-sm text-[0.65rem] uppercase tracking-widest text-canvas px-2.5 py-1">
                            01 &bull; PORTRAITS
                        </div>
                    </div>

                    {{-- Offset Secondary Photo --}}
                    <div class="col-span-4 overflow-hidden bg-subtle aspect-[4/5] shadow-sm relative group -mb-6 sm:-mb-10">
                        <img src="{{ asset('storage/demo/hero-secondary.jpg') }}" 
                             alt="Atmospheric light and mood" 
                             loading="eager"
                             width="400"
                             height="500"
                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                        <div class="absolute bottom-2 left-2 bg-graphite-950/80 backdrop-blur-sm text-[0.6rem] uppercase tracking-widest text-canvas px-2 py-0.5">
                            02 &bull; LIGHT
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>
</section>

{{-- 2. SELECTED SERIES SECTION --}}
<section class="py-20 md:py-28 border-b border-editorial-border">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6">
            <div>
                <span class="text-xs uppercase tracking-widest text-terracotta font-medium block mb-2">
                    {{ __('site.featured_title') }}
                </span>
                <h2 class="font-serif text-3xl sm:text-4xl text-graphite-950">
                    {{ __('site.featured_subtitle') }}
                </h2>
            </div>
            <a href="{{ route('portfolio.index', ['locale' => $locale]) }}" 
               class="text-xs uppercase tracking-widest font-medium text-graphite-800 hover:text-terracotta inline-flex items-center gap-1 shrink-0 transition-colors">
                <span>{{ __('site.view_all_series') }}</span>
                <span>&rarr;</span>
            </a>
        </div>

        {{-- Selected Series Editorial Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-16">
            @foreach($featuredSeries as $index => $series)
                <article class="group">
                    <a href="{{ route('series.show', ['locale' => $locale, 'slug' => $series->slug]) }}" class="block focus:outline-none focus-visible:ring-2 focus-visible:ring-terracotta">
                        
                        {{-- Photo Container --}}
                        <div class="overflow-hidden bg-subtle aspect-[16/10] relative mb-5">
                            <img src="{{ $series->cover_url }}" 
                                 alt="{{ $series->localizedTitle($locale) }}" 
                                 loading="lazy"
                                 width="800"
                                 height="500"
                                 class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105">
                            
                            {{-- Number & Category Pill --}}
                            <div class="absolute top-4 left-4 bg-canvas/90 backdrop-blur-sm text-graphite-900 text-[0.7rem] uppercase tracking-wider px-3 py-1 font-mono">
                                {{ sprintf('%02d', $index + 1) }} &bull; {{ $series->category ? $series->category->localizedName($locale) : 'Серия' }}
                            </div>

                            @if($series->is_demo)
                            <div class="absolute top-4 right-4 bg-terracotta/90 text-white text-[0.65rem] uppercase tracking-widest px-2.5 py-0.5">
                                Demo
                            </div>
                            @endif
                        </div>

                        {{-- Metadata & Title --}}
                        <div class="space-y-1">
                            <div class="flex items-center justify-between text-xs text-graphite-500 font-mono">
                                <span>{{ $series->localizedLocation($locale) }}</span>
                                <span>{{ $series->photos->count() }} {{ __('site.photos_count', ['count' => '']) }}</span>
                            </div>
                            <h3 class="font-serif text-2xl text-graphite-950 group-hover:text-terracotta transition-colors">
                                {{ $series->localizedTitle($locale) }}
                            </h3>
                            @if($series->localizedDescription($locale))
                            <p class="text-xs text-graphite-600 line-clamp-2 pt-1 font-light leading-relaxed">
                                {{ $series->localizedDescription($locale) }}
                            </p>
                            @endif
                        </div>

                    </a>
                </article>
            @endforeach
        </div>

    </div>
</section>

{{-- 3. DIRECTIONS / GENRES --}}
<section class="py-20 md:py-28 border-b border-editorial-border bg-surface">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="max-w-2xl mb-14">
            <span class="text-xs uppercase tracking-widest text-terracotta font-medium block mb-2">
                {{ __('site.directions_title') }}
            </span>
            <h2 class="font-serif text-3xl sm:text-4xl text-graphite-950">
                {{ __('site.directions_subtitle') }}
            </h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            @foreach($categories as $cat)
                <a href="{{ route('portfolio.index', ['locale' => $locale, 'category' => $cat->slug]) }}" 
                   class="p-6 bg-canvas border border-editorial-border hover:border-terracotta hover:shadow-sm transition-all duration-200 group flex flex-col justify-between h-44">
                    <div class="text-xs font-mono text-graphite-400 group-hover:text-terracotta transition-colors">
                        0{{ $loop->iteration }}
                    </div>
                    <div>
                        <h3 class="font-serif text-xl text-graphite-950 group-hover:text-terracotta transition-colors mb-1">
                            {{ $cat->localizedName($locale) }}
                        </h3>
                        <p class="text-xs text-graphite-500">
                            {{ $cat->series_count }} {{ __('site.photos_count', ['count' => '']) }}
                        </p>
                    </div>
                </a>
            @endforeach
        </div>

    </div>
</section>

{{-- 4. ABOUT / APPROACH BLOCK --}}
<section class="py-20 md:py-28 border-b border-editorial-border">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <div class="lg:col-span-5">
                <div class="aspect-[3/4] bg-subtle overflow-hidden relative">
                    <img src="{{ asset('storage/demo/portrait-2.jpg') }}" 
                         alt="{{ __('site.author_name') }}" 
                         loading="lazy"
                         width="600"
                         height="800"
                         class="w-full h-full object-cover">
                    <div class="absolute bottom-4 left-4 bg-canvas/90 backdrop-blur-sm px-3 py-1.5 text-xs font-mono text-graphite-800">
                        {{ __('site.author_name') }} &bull; {{ __('site.location') }}
                    </div>
                </div>
            </div>

            <div class="lg:col-span-7 space-y-6 lg:pl-10">
                <span class="text-xs uppercase tracking-widest text-terracotta font-medium block">
                    {{ __('site.about_preview_title') }}
                </span>
                <h2 class="font-serif text-3xl sm:text-4xl text-graphite-950 leading-tight">
                    Каждая съёмка — это живой разговор, а не набор механических поз.
                </h2>
                <p class="text-base text-graphite-700 leading-relaxed font-light">
                    {{ __('site.about_preview_text') }}
                </p>
                <div class="pt-4">
                    <a href="{{ route('about.index', ['locale' => $locale]) }}" 
                       class="text-xs uppercase tracking-widest font-medium text-graphite-900 border-b border-graphite-900 pb-1 hover:text-terracotta hover:border-terracotta transition-colors">
                        {{ __('site.nav_about') }} &rarr;
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- 5. PRICING PACKAGES PREVIEW --}}
<section class="py-20 md:py-28 border-b border-editorial-border bg-surface">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6">
            <div>
                <span class="text-xs uppercase tracking-widest text-terracotta font-medium block mb-2">
                    {{ __('site.pricing_preview_title') }}
                </span>
                <h2 class="font-serif text-3xl sm:text-4xl text-graphite-950">
                    {{ __('site.pricing_preview_subtitle') }}
                </h2>
            </div>
            <a href="{{ route('pricing.index', ['locale' => $locale]) }}" 
               class="text-xs uppercase tracking-widest font-medium text-graphite-800 hover:text-terracotta inline-flex items-center gap-1 shrink-0 transition-colors">
                <span>{{ __('site.all_packages') }}</span>
                <span>&rarr;</span>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            @foreach($packages as $pkg)
                <div class="bg-canvas p-8 border border-editorial-border flex flex-col justify-between hover:border-terracotta/60 transition-colors">
                    <div>
                        <div class="flex items-baseline justify-between mb-4 border-b border-editorial-border pb-3">
                            <h3 class="font-serif text-2xl text-graphite-950">
                                {{ $pkg->localizedTitle($locale) }}
                            </h3>
                            <span class="text-xs font-mono text-graphite-400">
                                {{ sprintf('%02d', $loop->iteration) }}
                            </span>
                        </div>

                        <p class="text-xs text-graphite-600 mb-6 font-light">
                            {{ $pkg->localizedSubtitle($locale) }}
                        </p>

                        <div class="mb-6">
                            <span class="text-2xl font-serif text-terracotta font-normal block">
                                {{ $pkg->formattedPrice($locale) }}
                            </span>
                            @if(is_null($pkg->price))
                            <span class="text-[0.68rem] text-graphite-500 uppercase tracking-widest">
                                Индивидуальный расчет
                            </span>
                            @endif
                        </div>

                        <div class="space-y-2 text-xs text-graphite-700 font-light border-t border-editorial-border pt-4">
                            @if($pkg->localizedDuration($locale))
                            <div class="flex justify-between">
                                <span class="text-graphite-500">{{ __('site.duration') }}:</span>
                                <span class="font-medium text-graphite-900">{{ $pkg->localizedDuration($locale) }}</span>
                            </div>
                            @endif

                            @if($pkg->localizedPhotoCount($locale))
                            <div class="flex justify-between">
                                <span class="text-graphite-500">{{ __('site.photo_count') }}:</span>
                                <span class="font-medium text-graphite-900">{{ $pkg->localizedPhotoCount($locale) }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="pt-8">
                        <a href="{{ route('contacts.index', ['locale' => $locale]) }}" 
                           class="block w-full text-center py-3 border border-graphite-900 text-xs uppercase tracking-widest text-graphite-900 hover:bg-graphite-900 hover:text-white transition-colors">
                            {{ __('site.book_package') }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</section>

{{-- 6. FINAL CONTACT CALLOUT --}}
<section class="py-20 md:py-28 text-center">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <span class="text-xs uppercase tracking-widest text-terracotta font-medium block">
            {{ __('site.location') }} &bull; {{ __('site.author_name') }}
        </span>
        <h2 class="font-serif text-4xl sm:text-5xl text-graphite-950 editorial-heading">
            Готовы обсудить идею вашей съёмки?
        </h2>
        <p class="text-base text-graphite-600 font-light max-w-xl mx-auto leading-relaxed">
            Напишите пару слов о вашем событии, предпочтительных датах или просто задайте вопрос.
        </p>
        <div class="pt-4 flex flex-col sm:flex-row justify-center gap-4">
            <a href="{{ route('contacts.index', ['locale' => $locale]) }}" 
               class="inline-flex justify-center items-center px-8 py-4 bg-graphite-900 text-white text-xs uppercase tracking-widest font-medium hover:bg-terracotta transition-colors shadow-sm">
                {{ __('site.hero_cta') }}
            </a>
            <a href="{{ route('portfolio.index', ['locale' => $locale]) }}" 
               class="inline-flex justify-center items-center px-8 py-4 border border-editorial-border bg-surface text-graphite-800 text-xs uppercase tracking-widest font-medium hover:border-graphite-900 transition-colors">
                {{ __('site.hero_portfolio') }}
            </a>
        </div>
    </div>
</section>

@endsection
