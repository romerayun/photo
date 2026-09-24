@extends('layouts.app')

@section('title', __('site.meta_title'))
@section('description', __('site.meta_description'))

@section('content')

{{-- 1. HERO SECTION (APPLE LIGHT PRO) --}}
<section class="relative pt-12 pb-20 md:pt-16 md:pb-28 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        
        {{-- Floating Apple Pro Eyebrow Badge --}}
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/80 border border-black/5 shadow-sm mb-6 backdrop-blur-md">
            <span class="w-2 h-2 rounded-full bg-apple-accent shadow-[0_0_8px_#B07D53]"></span>
            <span class="text-xs uppercase tracking-widest text-apple-text font-semibold font-mono">
                {{ __('site.location') }} &bull; 2026
            </span>
            <span class="text-neutral-300">|</span>
            <span class="text-xs text-apple-textMuted font-medium">
                {{ __('site.author_role') }}
            </span>
        </div>

        {{-- Master Typographic Title --}}
        <div class="max-w-4xl mx-auto space-y-4">
            <h1 class="text-5xl sm:text-7xl md:text-8xl font-extrabold tracking-tightest text-apple-text leading-none">
                {{ __('site.author_name') }}
            </h1>
            <p class="text-2xl sm:text-3xl md:text-4xl font-semibold tracking-tight text-apple-text/90">
                {{ \App\Models\Setting::get("hero_phrase_{$locale}", __('site.hero_phrase')) }}
            </p>
            <p class="text-base sm:text-lg text-apple-textMuted max-w-xl mx-auto font-normal leading-relaxed pt-1">
                {{ \App\Models\Setting::get("hero_sub_{$locale}", __('site.hero_description')) }}
            </p>
        </div>

        {{-- Apple Dual Action Pill Buttons --}}
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-8">
            <a href="{{ route('contacts.index', ['locale' => $locale]) }}" 
               class="w-full sm:w-auto px-8 py-3.5 bg-apple-text text-white hover:bg-black active:scale-95 text-xs uppercase tracking-widest font-bold rounded-full transition-all duration-200 shadow-sm">
                {{ __('site.hero_cta') }}
            </a>
            <a href="{{ route('portfolio.index', ['locale' => $locale]) }}" 
               class="w-full sm:w-auto px-7 py-3.5 bg-white hover:bg-neutral-50 text-apple-text active:scale-95 text-xs uppercase tracking-widest font-semibold rounded-full border border-black/10 shadow-sm transition-all duration-200 inline-flex items-center justify-center gap-1.5">
                <span>{{ __('site.hero_portfolio') }}</span>
                <span class="text-apple-accent font-bold">&nearr;</span>
            </a>
        </div>

        {{-- Cinematic Hero Visual Showcase --}}
        <div class="mt-14 sm:mt-20 max-w-5xl mx-auto">
            <div class="relative rounded-3xl overflow-hidden border border-black/5 shadow-apple-card bg-white group">
                
                {{-- Master Image --}}
                <div class="aspect-[16/10] sm:aspect-[21/9] overflow-hidden">
                    <img src="{{ asset('storage/demo/hero-main.jpg') }}" 
                         alt="{{ __('site.author_name') }}" 
                         fetchpriority="high"
                         loading="eager"
                         width="1400"
                         height="600"
                         class="w-full h-full object-cover transition-transform duration-1000 ease-out group-hover:scale-105">
                </div>

                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent pointer-events-none"></div>

                <div class="absolute bottom-6 left-6 right-6 flex items-end justify-between text-left text-white z-20">
                    <div>
                        <span class="text-[0.68rem] uppercase tracking-widest text-amber-200 font-mono font-semibold block mb-1">
                            Cinematic Master &bull; 2026
                        </span>
                        <h2 class="text-xl sm:text-2xl font-bold tracking-tight">
                            Естественный свет и чистая геометрия кадра
                        </h2>
                    </div>
                    <div class="hidden sm:block text-right font-mono text-[0.68rem] text-neutral-300 uppercase tracking-widest">
                        <span>IRKUTSK &bull; SIBERIA</span>
                    </div>
                </div>

            </div>
        </div>

    </div>
</section>

{{-- 2. APPLE BENTO GRID (DIRECTIONS & GENRES) --}}
<section class="py-24 border-t border-black/5 bg-white relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-14 gap-6">
            <div>
                <span class="text-xs uppercase tracking-widest text-apple-accent font-semibold font-mono block mb-2">
                    {{ __('site.directions_title') }}
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-apple-text">
                    {{ __('site.directions_subtitle') }}
                </h2>
            </div>
            <a href="{{ route('portfolio.index', ['locale' => $locale]) }}" 
               class="text-xs uppercase tracking-widest font-semibold text-apple-textMuted hover:text-apple-text inline-flex items-center gap-1.5 transition-colors">
                <span>{{ __('site.view_all_series') }}</span>
                <span>&rarr;</span>
            </a>
        </div>

        {{-- Apple Bento Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
            
            {{-- Bento Card 1: Large Featured (Portraits) --}}
            <div class="md:col-span-8 relative rounded-3xl overflow-hidden border border-black/5 bg-apple-bg shadow-apple-card group apple-card-hover">
                <a href="{{ route('portfolio.index', ['locale' => $locale, 'category' => 'portraits']) }}" class="block p-8 h-96 flex flex-col justify-between relative z-10 text-white">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-mono text-amber-200 uppercase tracking-widest font-bold">01 &bull; PORTRAITS</span>
                        <span class="w-8 h-8 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center text-white text-xs group-hover:bg-white group-hover:text-black transition-all">
                            &nearr;
                        </span>
                    </div>
                    <div>
                        <h3 class="text-3xl sm:text-4xl font-extrabold text-white mb-2">
                            Портреты
                        </h3>
                        <p class="text-sm text-neutral-200 max-w-md font-light leading-relaxed">
                            Индивидуальные портретные истории без заученных поз. Живой взгляд, характер и глубина света.
                        </p>
                    </div>
                </a>
                <img src="{{ asset('storage/demo/portrait-1.jpg') }}" alt="" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-all duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
            </div>

            {{-- Bento Card 2: Couples --}}
            <div class="md:col-span-4 relative rounded-3xl overflow-hidden border border-black/5 bg-apple-bg shadow-apple-card group apple-card-hover">
                <a href="{{ route('portfolio.index', ['locale' => $locale, 'category' => 'couples']) }}" class="block p-8 h-96 flex flex-col justify-between relative z-10 text-white">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-mono text-amber-200 uppercase tracking-widest font-bold">02 &bull; LOVE STORIES</span>
                        <span class="w-8 h-8 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center text-white text-xs group-hover:bg-white group-hover:text-black transition-all">
                            &nearr;
                        </span>
                    </div>
                    <div>
                        <h3 class="text-2xl sm:text-3xl font-extrabold text-white mb-2">
                            Пары
                        </h3>
                        <p class="text-xs text-neutral-200 font-light leading-relaxed">
                            Искренние прогулки вдвоём на набережной Ангары или на природе.
                        </p>
                    </div>
                </a>
                <img src="{{ asset('storage/demo/couple-1.jpg') }}" alt="" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-all duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
            </div>

            {{-- Bento Card 3: Families --}}
            <div class="md:col-span-4 relative rounded-3xl overflow-hidden border border-black/5 bg-apple-bg shadow-apple-card group apple-card-hover">
                <a href="{{ route('portfolio.index', ['locale' => $locale, 'category' => 'families']) }}" class="block p-8 h-80 flex flex-col justify-between relative z-10 text-white">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-mono text-amber-200 uppercase tracking-widest font-bold">03 &bull; FAMILIES</span>
                        <span class="w-8 h-8 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center text-white text-xs group-hover:bg-white group-hover:text-black transition-all">
                            &nearr;
                        </span>
                    </div>
                    <div>
                        <h3 class="text-2xl font-extrabold text-white mb-1">
                            Семьи
                        </h3>
                        <p class="text-xs text-neutral-200 font-light">
                            Тепло домашнего утра, детские эмоции и моменты близости.
                        </p>
                    </div>
                </a>
                <img src="{{ asset('storage/demo/family-1.jpg') }}" alt="" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-all duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
            </div>

            {{-- Bento Card 4: Events --}}
            <div class="md:col-span-4 relative rounded-3xl overflow-hidden border border-black/5 bg-apple-bg shadow-apple-card group apple-card-hover">
                <a href="{{ route('portfolio.index', ['locale' => $locale, 'category' => 'events']) }}" class="block p-8 h-80 flex flex-col justify-between relative z-10 text-white">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-mono text-amber-200 uppercase tracking-widest font-bold">04 &bull; EVENTS</span>
                        <span class="w-8 h-8 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center text-white text-xs group-hover:bg-white group-hover:text-black transition-all">
                            &nearr;
                        </span>
                    </div>
                    <div>
                        <h3 class="text-2xl font-extrabold text-white mb-1">
                            События
                        </h3>
                        <p class="text-xs text-neutral-200 font-light">
                            Камерные выставки, лекции, фестивали и важные даты.
                        </p>
                    </div>
                </a>
                <img src="{{ asset('storage/demo/event-1.jpg') }}" alt="" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-all duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
            </div>

            {{-- Bento Card 5: Business & Brand --}}
            <div class="md:col-span-4 relative rounded-3xl overflow-hidden border border-black/5 bg-apple-bg shadow-apple-card group apple-card-hover">
                <a href="{{ route('portfolio.index', ['locale' => $locale, 'category' => 'business']) }}" class="block p-8 h-80 flex flex-col justify-between relative z-10 text-white">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-mono text-amber-200 uppercase tracking-widest font-bold">05 &bull; BRANDING</span>
                        <span class="w-8 h-8 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center text-white text-xs group-hover:bg-white group-hover:text-black transition-all">
                            &nearr;
                        </span>
                    </div>
                    <div>
                        <h3 class="text-2xl font-extrabold text-white mb-1">
                            Бизнес и контент
                        </h3>
                        <p class="text-xs text-neutral-200 font-light">
                            Визуальный контент для студий, мастерских и локальных брендов.
                        </p>
                    </div>
                </a>
                <img src="{{ asset('storage/demo/business-1.jpg') }}" alt="" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-all duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
            </div>

        </div>

    </div>
</section>

{{-- 3. SELECTED STORIES (SERIES SHOWCASE) --}}
<section class="py-24 border-t border-black/5">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-14 gap-6">
            <div>
                <span class="text-xs uppercase tracking-widest text-apple-accent font-semibold font-mono block mb-2">
                    {{ __('site.featured_title') }}
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-apple-text">
                    {{ __('site.featured_subtitle') }}
                </h2>
            </div>
            <a href="{{ route('portfolio.index', ['locale' => $locale]) }}" 
               class="text-xs uppercase tracking-widest font-semibold text-apple-textMuted hover:text-apple-text inline-flex items-center gap-1.5 transition-colors">
                <span>{{ __('site.view_all_series') }}</span>
                <span>&rarr;</span>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @foreach($featuredSeries as $index => $series)
                <article class="bg-white rounded-3xl overflow-hidden border border-black/5 shadow-apple-card apple-card-hover group">
                    <a href="{{ route('series.show', ['locale' => $locale, 'slug' => $series->slug]) }}" class="block">
                        
                        <div class="overflow-hidden aspect-[16/10] relative">
                            <img src="{{ $series->cover_url }}" 
                                 alt="{{ $series->localizedTitle($locale) }}" 
                                 loading="lazy"
                                 width="800"
                                 height="500"
                                 class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105">
                            
                            {{-- Apple Tag Pill --}}
                            <div class="absolute top-4 left-4 apple-glass rounded-full px-3.5 py-1 text-apple-text text-[0.68rem] uppercase tracking-wider font-mono font-semibold shadow-sm">
                                {{ sprintf('%02d', $index + 1) }} &bull; {{ $series->category ? $series->category->localizedName($locale) : 'Series' }}
                            </div>

                            @if($series->is_demo)
                            <div class="absolute top-4 right-4 bg-apple-accent text-white text-[0.62rem] uppercase tracking-widest px-2.5 py-0.5 rounded-full font-bold shadow-sm">
                                Demo
                            </div>
                            @endif
                        </div>

                        <div class="p-6 sm:p-8 space-y-2">
                            <div class="flex items-center justify-between text-xs text-apple-textMuted font-mono">
                                <span>{{ $series->localizedLocation($locale) }}</span>
                                <span>{{ $series->photos->count() }} кадров</span>
                            </div>

                            <h3 class="text-2xl font-bold text-apple-text group-hover:text-apple-accent transition-colors">
                                {{ $series->localizedTitle($locale) }}
                            </h3>

                            @if($series->localizedDescription($locale))
                            <p class="text-xs text-apple-textMuted line-clamp-2 pt-1 font-light leading-relaxed">
                                {{ $series->localizedDescription($locale) }}
                            </p>
                            @endif

                            <div class="pt-3">
                                <span class="text-xs font-semibold text-apple-accent inline-flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                                    {{ __('site.view_series') }} &rarr;
                                </span>
                            </div>
                        </div>

                    </a>
                </article>
            @endforeach
        </div>

    </div>
</section>

{{-- 4. PHILOSOPHY / CREATOR STATEMENT --}}
<section class="py-24 border-t border-black/5 bg-white text-center relative overflow-hidden">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 relative z-10">
        <span class="text-xs uppercase tracking-widest text-apple-accent font-semibold font-mono block">
            {{ __('site.about_preview_title') }}
        </span>
        <h2 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-apple-text leading-tight">
            Каждая съёмка — это живой разговор, а не набор искусственных поз.
        </h2>
        <p class="text-base sm:text-lg text-apple-textMuted font-light leading-relaxed max-w-2xl mx-auto">
            {{ __('site.about_preview_text') }}
        </p>
        <div class="pt-4">
            <a href="{{ route('about.index', ['locale' => $locale]) }}" 
               class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-apple-bg hover:bg-neutral-200 text-apple-text text-xs uppercase tracking-widest font-semibold transition-all">
                <span>{{ __('site.nav_about') }}</span>
                <span>&rarr;</span>
            </a>
        </div>
    </div>
</section>

{{-- 5. PRICING PACKAGES --}}
<section class="py-24 border-t border-black/5">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-14 gap-6">
            <div>
                <span class="text-xs uppercase tracking-widest text-apple-accent font-semibold font-mono block mb-2">
                    {{ __('site.pricing_preview_title') }}
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-apple-text">
                    {{ __('site.pricing_preview_subtitle') }}
                </h2>
            </div>
            <a href="{{ route('pricing.index', ['locale' => $locale]) }}" 
               class="text-xs uppercase tracking-widest font-semibold text-apple-textMuted hover:text-apple-text inline-flex items-center gap-1.5 transition-colors">
                <span>{{ __('site.all_packages') }}</span>
                <span>&rarr;</span>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            @foreach($packages as $pkg)
                <div class="bg-white rounded-3xl p-8 sm:p-10 flex flex-col justify-between border border-black/5 shadow-apple-card apple-card-hover relative {{ $loop->iteration === 2 ? 'ring-2 ring-apple-accent' : '' }}">
                    
                    @if($loop->iteration === 2)
                    <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 px-4 py-1 rounded-full bg-apple-accent text-white text-[0.65rem] uppercase tracking-widest font-bold shadow-md">
                        Рекомендуемый выбор
                    </div>
                    @endif

                    <div>
                        <div class="flex items-center justify-between pb-4 mb-4 border-b border-black/5">
                            <span class="font-mono text-xs text-neutral-400 uppercase tracking-widest">
                                TIER {{ sprintf('%02d', $loop->iteration) }}
                            </span>
                        </div>

                        <h3 class="text-2xl font-bold text-apple-text mb-2">
                            {{ $pkg->localizedTitle($locale) }}
                        </h3>

                        <p class="text-xs text-apple-textMuted mb-6 font-light">
                            {{ $pkg->localizedSubtitle($locale) }}
                        </p>

                        {{-- Price Highlight --}}
                        <div class="p-5 rounded-2xl bg-apple-bg border border-black/5 mb-6">
                            <span class="text-3xl font-extrabold text-apple-text block">
                                {{ $pkg->formattedPrice($locale) }}
                            </span>
                            @if(is_null($pkg->price))
                            <span class="text-[0.68rem] text-apple-accent uppercase tracking-widest font-mono font-semibold block mt-1">
                                Индивидуальный расчет
                            </span>
                            @endif
                        </div>

                        <div class="space-y-2.5 text-xs text-apple-textMuted mb-6">
                            @if($pkg->localizedDuration($locale))
                            <div class="flex justify-between py-1 border-b border-black/5">
                                <span class="text-neutral-400">{{ __('site.duration') }}</span>
                                <span class="font-semibold text-apple-text">{{ $pkg->localizedDuration($locale) }}</span>
                            </div>
                            @endif

                            @if($pkg->localizedPhotoCount($locale))
                            <div class="flex justify-between py-1 border-b border-black/5">
                                <span class="text-neutral-400">{{ __('site.photo_count') }}</span>
                                <span class="font-semibold text-apple-text">{{ $pkg->localizedPhotoCount($locale) }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="pt-6">
                        <a href="{{ route('contacts.index', ['locale' => $locale]) }}" 
                           class="block w-full text-center py-3.5 {{ $loop->iteration === 2 ? 'bg-apple-text text-white hover:bg-black' : 'bg-apple-bg text-apple-text hover:bg-neutral-200 border border-black/5' }} text-xs uppercase tracking-widest font-bold rounded-full transition-all shadow-sm">
                            {{ __('site.book_package') }}
                        </a>
                    </div>

                </div>
            @endforeach
        </div>

    </div>
</section>

{{-- 6. FINAL CALLOUT --}}
<section class="py-24 border-t border-black/5 bg-white text-center relative">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <span class="text-xs uppercase tracking-widest text-apple-accent font-semibold font-mono block">
            {{ __('site.location') }} &bull; {{ __('site.author_name') }}
        </span>
        <h2 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-apple-text leading-tight">
            Готовы обсудить идею вашей съёмки?
        </h2>
        <p class="text-base text-apple-textMuted font-light max-w-xl mx-auto leading-relaxed">
            Напишите пару слов о вашем событии или проекте в Telegram — я помогу выбрать локацию и подобрать подходящий формат.
        </p>
        <div class="pt-6 flex flex-col sm:flex-row justify-center gap-4">
            <a href="{{ route('contacts.index', ['locale' => $locale]) }}" 
               class="px-9 py-4 bg-apple-text text-white hover:bg-black text-xs uppercase tracking-widest font-bold rounded-full transition-all shadow-md">
                {{ __('site.hero_cta') }}
            </a>
            <a href="{{ route('portfolio.index', ['locale' => $locale]) }}" 
               class="px-8 py-4 bg-apple-bg hover:bg-neutral-200 text-apple-text text-xs uppercase tracking-widest font-semibold rounded-full border border-black/5 transition-all">
                {{ __('site.hero_portfolio') }}
            </a>
        </div>
    </div>
</section>

@endsection
