@extends('layouts.app')

@section('title', __('site.meta_title'))
@section('description', __('site.meta_description'))

@section('content')

{{-- ========================================================
     1. HERO SECTION (CINEMATIC DARK WITH RED FOCUS BRACKET)
     ======================================================== --}}
<section class="relative min-h-[75vh] sm:min-h-[80vh] flex items-center justify-center bg-cine-black overflow-hidden pt-8 pb-16 sm:pt-10 sm:pb-20 border-b border-cine-border">
    
    {{-- Background Motion / Video Atmosphere Layer --}}
    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none" id="hero-media-container">
        {{-- Mobile & Fallback Crisp Poster (AVIF / WebP / JPEG) --}}
        <picture class="absolute inset-0 w-full h-full block">
            <source srcset="{{ asset('images/hero-bg.avif') }}" type="image/avif">
            <source srcset="{{ asset('images/hero-bg.webp') }}" type="image/webp">
            <img src="{{ asset('images/hero-bg.jpg') }}" 
                 alt="{{ __('site.author_name') }}" 
                 fetchpriority="high"
                 class="w-full h-full object-cover object-center scale-105" 
                 id="hero-poster-img">
        </picture>

        {{-- Desktop Video: loaded only on screens >= 768px and when connection allows to save 15MB mobile traffic --}}
        <video id="hero-desktop-video"
               autoplay loop muted playsinline preload="none"
               poster="{{ asset('images/hero-bg.webp') }}" 
               class="hidden md:block absolute inset-0 w-full h-full object-cover object-center opacity-95 sm:opacity-100 scale-105 transition-opacity duration-1000">
        </video>

        <script>
            (function() {
                // Only load the 15MB background video on desktop (>=768px) and if Save-Data is not requested
                var isDesktop = window.innerWidth >= 768;
                var saveData = navigator.connection && navigator.connection.saveData;
                if (isDesktop && !saveData) {
                    var vid = document.getElementById('hero-desktop-video');
                    if (vid) {
                        var srcMp4 = document.createElement('source');
                        srcMp4.src = "{{ asset('videos/video-bg.mp4') }}";
                        srcMp4.type = 'video/mp4';
                        vid.appendChild(srcMp4);

                        var srcMov = document.createElement('source');
                        srcMov.src = "{{ asset('videos/video-bg.mov') }}";
                        srcMov.type = 'video/quicktime';
                        vid.appendChild(srcMov);

                        vid.load();
                        var playPromise = vid.play();
                        if (playPromise !== undefined) {
                            playPromise.catch(function() {
                                // Autoplay policy silently handled; fallback poster remains visible
                            });
                        }
                    }
                }
            })();
        </script>

        {{-- Subtle edge transition gradients only --}}
        <div class="absolute inset-x-0 top-0 h-24 bg-gradient-to-b from-cine-black/60 to-transparent"></div>
        <div class="absolute inset-x-0 bottom-0 h-28 bg-gradient-to-t from-cine-black/80 to-transparent"></div>
    </div>

    {{-- Center Typographic Impact --}}
    <div class="relative z-20 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center py-14 sm:py-20">
        @php
            $rawHeroPhrase = \App\Models\Setting::get("hero_phrase_{$locale}", __('site.hero_phrase'));
            $authorName = __('site.author_name');

            if (preg_match('/^(.*?)\s+[–—-]\s+(.*)$/u', $rawHeroPhrase, $matches)) {
                $heroRole = trim($matches[1]);
                $heroAuthor = trim($matches[2]);
            } elseif (mb_stripos($rawHeroPhrase, $authorName) !== false) {
                $heroRole = trim(str_ireplace($authorName, '', $rawHeroPhrase));
                $heroRole = trim(preg_replace('/[–—-]$/u', '', $heroRole));
                $heroAuthor = $authorName;
            } else {
                $heroRole = $rawHeroPhrase;
                $heroAuthor = ($locale === 'ru' && mb_stripos($heroRole, 'фотограф') !== false) ? $authorName : null;
            }

            $formattedHeroRole = preg_replace('/\s+([всикзоу])\s+/ui', ' $1&nbsp;', $heroRole);
        @endphp

        <h1 class="tracking-tight text-white font-display mb-6">
            @if(mb_stripos($heroRole, 'фотограф в иркутске') !== false)
                <span class="block text-[1.85rem] sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-extrabold uppercase leading-[1.08] drop-shadow-[0_4px_18px_rgba(0,0,0,0.95)] drop-shadow-[0_1px_3px_rgba(0,0,0,0.95)]">
                    <span class="inline-block">Фотограф</span> <span class="inline-block whitespace-nowrap">в&nbsp;Иркутске</span>
                </span>
            @else
                <span class="block text-[1.85rem] sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-extrabold uppercase leading-[1.08] drop-shadow-[0_4px_18px_rgba(0,0,0,0.95)] drop-shadow-[0_1px_3px_rgba(0,0,0,0.95)]">
                    {!! $formattedHeroRole !!}
                </span>
            @endif

            @if(!empty($heroAuthor))
                <span class="block text-base sm:text-xl md:text-2xl lg:text-3xl font-bold uppercase tracking-widest text-neutral-200 mt-3 sm:mt-4 font-display opacity-95 drop-shadow-[0_3px_12px_rgba(0,0,0,0.95)]">
                    {{ $heroAuthor }}
                </span>
            @endif
        </h1>

        <p class="text-xs sm:text-sm md:text-base text-neutral-200 font-normal max-w-2xl mx-auto uppercase tracking-wide font-mono mb-10 opacity-95 drop-shadow-[0_2px_8px_rgba(0,0,0,0.95)]">
            {{ \App\Models\Setting::get("hero_sub_{$locale}", __('site.hero_description')) }}
        </p>

        {{-- Bold Crimson CTA Button with Arrow (As in Reference) --}}
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ route('contacts.index') }}" 
               class="btn-crimson px-10 py-4 text-xs font-extrabold tracking-mega shadow-crimson-btn">
                <span>{{ __('site.hero_cta') }}</span>
                <span class="text-sm font-bold">&nearr;</span>
            </a>
            <a href="{{ route('portfolio.index') }}" 
               class="px-8 py-4 bg-white/10 hover:bg-white/15 text-white border border-white/20 text-xs font-bold uppercase tracking-widest backdrop-blur-md transition-colors inline-flex items-center gap-2">
                <span>{{ __('site.hero_portfolio') }}</span>
                <span>&rarr;</span>
            </a>
        </div>

    </div>
</section>

{{-- ========================================================
     2. EDITORIAL GRID SHOWCASE (ARCHITECTURAL LIGHT SECTION)
     ======================================================== --}}
<section class="bg-arch-bg text-arch-text border-b border-arch-border py-20 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- 3-Column Editorial Grid with Vertical Borders (Exact match to reference) --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 divide-y lg:divide-y-0 lg:divide-x divide-arch-border border-y border-arch-border">
            
            {{-- Column 1: Approach & Comfort in Front of Camera --}}
            <div class="lg:col-span-4 p-8 sm:p-12 flex flex-col justify-between space-y-8">
                <div>
                    <span class="text-[0.68rem] uppercase tracking-widest text-neutral-400 font-mono font-bold block mb-4">
                        01 / ПОДХОД К СЪЁМКЕ
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold uppercase tracking-tight font-display text-arch-text leading-tight mb-4">
                        Вам не&nbsp;нужно уметь позировать
                    </h2>
                    <p class="text-xs sm:text-sm text-neutral-600 font-mono leading-relaxed">
                        Помогу выбрать место и&nbsp;подскажу, как встать, куда посмотреть и&nbsp;что делать в&nbsp;кадре. Оставим время привыкнуть к&nbsp;камере, чтобы съёмка проходила спокойно.
                    </p>
                </div>
                <div>
                    <a href="#shooting-process" 
                       class="inline-flex items-center gap-2 text-xs font-mono uppercase tracking-widest font-bold text-arch-text hover:text-crimson transition-colors group">
                        <span class="border-b border-arch-text/30 group-hover:border-crimson transition-colors pb-0.5">Как проходит съёмка</span>
                        <span class="group-hover:translate-x-1 transition-transform duration-200">&rarr;</span>
                    </a>
                </div>
            </div>

            {{-- Column 2: Spotlight Series Photo from Database --}}
            <div class="lg:col-span-4 p-8 sm:p-12 flex flex-col justify-between space-y-6">
                @if($spotlightSeries)
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-[0.68rem] uppercase tracking-widest text-neutral-400 font-mono font-bold">
                                02 / КРАСИВЫЙ КАДР
                            </span>
                            @if($spotlightSeries->category)
                                <span class="text-[0.68rem] font-mono text-crimson uppercase tracking-widest font-bold">
                                    {{ $spotlightSeries->category->localizedName($locale) }}
                                </span>
                            @endif
                        </div>

                        {{-- Prominent Large Photo (Clean, without distracting badges) --}}
                        <a href="{{ route('series.show', ['slug' => $spotlightSeries->slug]) }}" 
                           class="block relative overflow-hidden bg-neutral-900 border border-arch-border shadow-card-depth aspect-[4/5] group mb-4">
                            <picture>
                                <source type="image/avif" 
                                        srcset="{{ $spotlightSeries->getCoverSrcsetAttribute('avif') }}" 
                                        sizes="(max-width: 1024px) 100vw, 450px">
                                <source type="image/webp" 
                                        srcset="{{ $spotlightSeries->getCoverSrcsetAttribute('webp') }}" 
                                        sizes="(max-width: 1024px) 100vw, 450px">
                                <img src="{{ $spotlightSeries->medium_cover_url }}" 
                                     srcset="{{ $spotlightSeries->getCoverSrcsetAttribute() }}"
                                     sizes="(max-width: 1024px) 100vw, 450px"
                                     alt="{{ $spotlightSeries->localizedTitle($locale) }}" 
                                     loading="lazy" 
                                     decoding="async"
                                     class="w-full h-full object-cover filter contrast-105 group-hover:scale-105 transition-transform duration-700 ease-out">
                            </picture>
                        </a>

                        {{-- Specific Caption: Type, Real Location, Story --}}
                        <div class="space-y-2">
                            <div class="flex flex-wrap items-baseline justify-between gap-x-3 gap-y-1">
                                <h3 class="text-xl sm:text-2xl font-bold uppercase font-display tracking-tight text-arch-text leading-tight">
                                    {{ $spotlightSeries->localizedTitle($locale) }}
                                </h3>
                                @if($spotlightSeries->localizedLocation($locale))
                                    <span class="text-[0.68rem] font-mono text-neutral-500 uppercase tracking-wider">
                                        {{ $spotlightSeries->localizedLocation($locale) }}
                                    </span>
                                @endif
                            </div>

                            @if($spotlightSeries->localizedDescription($locale))
                                <p class="text-xs sm:text-sm text-neutral-600 font-mono leading-relaxed">
                                    {{ $spotlightSeries->localizedDescription($locale) }}
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Clear Action Link: "Посмотреть всю съёмку" --}}
                    <div>
                        <a href="{{ route('series.show', ['slug' => $spotlightSeries->slug]) }}" 
                           class="inline-flex items-center gap-2 text-xs font-mono uppercase tracking-widest font-bold text-arch-text hover:text-crimson transition-colors group">
                            <span class="border-b border-arch-text/30 group-hover:border-crimson transition-colors pb-0.5">Посмотреть всю съёмку</span>
                            <span class="group-hover:translate-x-1 transition-transform duration-200">&rarr;</span>
                        </a>
                    </div>
                @else
                    <div>
                        <span class="text-[0.68rem] uppercase tracking-widest text-neutral-400 font-mono font-bold block mb-4">
                            02 / ФОТОГРАФИЯ
                        </span>
                        <div class="aspect-[4/5] bg-neutral-100 border border-arch-border flex items-center justify-center font-mono text-xs text-neutral-400 uppercase tracking-widest">
                            Серия готовится к публикации
                        </div>
                    </div>
                @endif
            </div>

            {{-- Column 3: Portfolio Card --}}
            <div class="lg:col-span-4 p-8 sm:p-12 flex flex-col justify-between space-y-6">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-[0.68rem] uppercase tracking-widest text-neutral-400 font-mono font-bold">
                            03 / ПОРТФОЛИО
                        </span>
                        <span class="text-[0.68rem] font-mono text-neutral-400 uppercase tracking-widest">
                            ВСЕ СЕРИИ
                        </span>
                    </div>

                    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight uppercase font-display text-arch-text leading-tight mb-4">
                        Посмотрите,<br>как я&nbsp;снимаю
                    </h2>

                    {{-- Highlight Portfolio Photo (Clean, No overlay text) --}}
                    <a href="{{ route('portfolio.index') }}" 
                       class="block relative overflow-hidden bg-neutral-900 border border-arch-border shadow-card-depth aspect-[4/5] group mb-4">
                        <picture>
                            <source srcset="{{ asset('images/photo-portfolio.avif') }}" type="image/avif">
                            <source srcset="{{ asset('images/photo-portfolio.webp') }}" type="image/webp">
                            <img src="{{ asset('images/photo-portfolio.jpg') }}" 
                                 alt="Посмотрите, как я снимаю" 
                                 loading="lazy" 
                                 decoding="async"
                                 class="w-full h-full object-cover filter contrast-105 group-hover:scale-105 transition-transform duration-700 ease-out">
                        </picture>
                    </a>
                </div>

                {{-- Bottom Text Link: "Все работы" (Identical to other columns) --}}
                <div>
                    <a href="{{ route('portfolio.index') }}" 
                       class="inline-flex items-center gap-2 text-xs font-mono uppercase tracking-widest font-bold text-arch-text hover:text-crimson transition-colors group">
                        <span class="border-b border-arch-text/30 group-hover:border-crimson transition-colors pb-0.5">Все работы</span>
                        <span class="group-hover:translate-x-1 transition-transform duration-200">&rarr;</span>
                    </a>
                </div>
            </div>

        </div>

    </div>
</section>

{{-- ========================================================
     3. DIRECTIONS / WHAT I CAN OFFER (CINEMATIC DARK ACCORDION)
     ======================================================== --}}
<section class="bg-cine-black text-white py-24 border-b border-cine-border relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
            
            {{-- Left Column: Huge Headline with Camera Crosshair --}}
            <div class="lg:col-span-5 space-y-6 sticky top-28">
                <div class="inline-flex items-center gap-2 text-xs font-mono uppercase tracking-widest text-crimson font-bold">
                    <span class="w-2 h-2 rounded-full bg-crimson animate-pulse"></span>
                    <span>{{ __('site.directions_title') }}</span>
                </div>
                <h2 class="text-3xl sm:text-4xl lg:text-[2.6rem] xl:text-5xl font-extrabold uppercase tracking-tight text-white font-display leading-[1.05] break-words">
                    КАКУЮ СЪЁМКУ<br>ВЫ ПЛАНИРУЕТЕ?
                </h2>
                <p class="text-sm text-neutral-400 font-light max-w-md leading-relaxed font-mono">
                    Для себя, вдвоём или всей семьёй. Для важного события или вашего бизнеса — выберите формат, чтобы посмотреть примеры и узнать условия.
                </p>
                <div class="pt-4">
                    <a href="{{ route('portfolio.index') }}" class="btn-crimson px-8 py-3.5 text-xs font-bold">
                        <span>ВСЕ РАБОТЫ</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

            {{-- Right Column: Horizontal Accordion Rows with Categories & Custom Format Card --}}
            <div class="lg:col-span-7 divide-y divide-white/10 border-y border-white/10">
                @foreach($categories as $cat)
                    <a href="{{ route('categories.show', $cat->slug) }}" 
                       class="py-6 sm:py-7 px-4 flex flex-col sm:flex-row sm:items-center justify-between gap-6 group hover:bg-white/[0.03] transition-colors">
                        
                        <div class="space-y-1.5 max-w-md">
                            <div class="text-xs font-mono text-crimson font-bold tracking-wider">
                                0{{ $loop->iteration }}
                            </div>
                            <h3 class="text-2xl sm:text-3xl font-bold uppercase tracking-tight text-white group-hover:text-crimson font-display transition-colors">
                                {{ $cat->localizedName($locale) }}
                            </h3>
                            @if($cat->localizedDescription($locale))
                                <p class="text-xs text-neutral-400 font-light font-mono leading-relaxed">
                                    {{ $cat->localizedDescription($locale) }}
                                </p>
                            @endif
                        </div>

                        {{-- Inset Preview Thumbnail & Arrow --}}
                        <div class="flex items-center gap-4 shrink-0">
                            <div class="w-32 sm:w-40 h-20 sm:h-24 overflow-hidden bg-neutral-800 rounded-sm border border-white/10">
                                <picture>
                                    <source srcset="{{ $cat->getVariantImageUrl('thumb', 'avif') }}" type="image/avif">
                                    <source srcset="{{ $cat->getVariantImageUrl('thumb', 'webp') }}" type="image/webp">
                                    <img src="{{ $cat->thumbnail_image_url }}" 
                                         alt="{{ $cat->localizedName($locale) }}" 
                                         loading="lazy"
                                         decoding="async"
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                </picture>
                            </div>
                            <span class="w-8 h-8 rounded-full border border-white/20 flex items-center justify-center text-white group-hover:bg-crimson group-hover:border-crimson transition-all text-xs font-bold">
                                &rarr;
                            </span>
                        </div>

                    </a>
                @endforeach

                {{-- Static Card: Custom Format --}}
                <div class="py-6 sm:py-7 px-4 flex flex-col sm:flex-row sm:items-center justify-between gap-6 bg-white/[0.02]">
                    <div class="space-y-1.5 max-w-md">
                        <div class="text-xs font-mono text-crimson font-bold tracking-wider uppercase">
                            ДРУГОЙ ФОРМАТ
                        </div>
                        <h3 class="text-xl sm:text-2xl font-bold uppercase tracking-tight text-white font-display">
                            Планируете другой формат?
                        </h3>
                        <p class="text-xs text-neutral-400 font-light font-mono leading-relaxed">
                            Расскажите, что хотите снять — обсудим задачу и возможность съёмки.
                        </p>
                    </div>

                    <div class="shrink-0">
                        <a href="{{ route('contacts.index') }}" 
                           class="btn-crimson px-7 py-3.5 text-xs font-bold whitespace-nowrap">
                            <span>ОБСУДИТЬ ИДЕЮ</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>

    </div>
</section>

{{-- ========================================================
     4. PORTRAIT STORIES: 3:4 FEATURED WORKS
     ======================================================== --}}
<section class="bg-arch-bg text-arch-text py-20 sm:py-24 border-b border-arch-border">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Section Header --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12 sm:mb-16">
            <div>
                <span class="text-xs uppercase tracking-widest text-crimson font-mono font-bold block mb-2">
                    03 / ИЗБРАННЫЕ РАБОТЫ
                </span>
                <h2 class="text-3xl sm:text-5xl font-extrabold tracking-tight uppercase font-display text-arch-text">
                    ПОРТРЕТНЫЕ ИСТОРИИ
                </h2>
                <p class="text-xs sm:text-sm text-neutral-600 font-mono leading-relaxed mt-3 max-w-2xl">
                    Разные люди, свет и&nbsp;настроение. Посмотрите съёмки целиком, чтобы познакомиться с&nbsp;моим подходом к&nbsp;портрету.
                </p>
            </div>
            <div class="shrink-0">
                <a href="{{ route('portfolio.index') }}" 
                   class="text-xs uppercase tracking-widest font-bold text-arch-text hover:text-crimson inline-flex items-center gap-2 transition-colors font-mono">
                    <span>{{ __('site.view_all_series') }}</span>
                    <span>&rarr;</span>
                </a>
            </div>
        </div>

        {{-- 3:4 Cards Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
            @foreach($featuredSeries as $index => $series)
                <article class="bg-white border border-arch-border shadow-card-depth overflow-hidden group flex flex-col justify-between hover:border-black/40 transition-all duration-300">
                    {{-- 3:4 Aspect Ratio Photo --}}
                    <a href="{{ route('series.show', ['slug' => $series->slug]) }}" 
                       class="block relative aspect-[3/4] overflow-hidden bg-neutral-900">
                        <picture>
                            <source type="image/avif" 
                                    srcset="{{ $series->getCoverSrcsetAttribute('avif') }}" 
                                    sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 320px">
                            <source type="image/webp" 
                                    srcset="{{ $series->getCoverSrcsetAttribute('webp') }}" 
                                    sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 320px">
                            <img src="{{ $series->medium_cover_url }}" 
                                 srcset="{{ $series->getCoverSrcsetAttribute() }}"
                                 sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 320px"
                                 alt="{{ $series->localizedTitle($locale) }}" 
                                 loading="lazy" 
                                 decoding="async"
                                 class="w-full h-full object-cover filter contrast-[1.02] group-hover:scale-105 transition-transform duration-700 ease-out">
                        </picture>
                        
                        @if($series->category)
                            <span class="absolute top-3 left-3 text-[0.62rem] font-mono uppercase tracking-widest text-white bg-black/80 backdrop-blur-sm px-2.5 py-1 font-bold">
                                {{ $series->category->localizedName($locale) }}
                            </span>
                        @endif

                        @if($series->is_demo)
                            <span class="absolute top-3 right-3 text-[0.62rem] font-mono uppercase tracking-widest text-white bg-crimson px-2 py-0.5 font-bold">
                                ДЕМО
                            </span>
                        @endif
                    </a>

                    {{-- Card Info --}}
                    <div class="p-5 sm:p-6 flex flex-col flex-grow justify-between space-y-4">
                        <div class="space-y-1.5">
                            @if($series->localizedLocation($locale))
                                <span class="text-[0.68rem] font-mono text-neutral-400 uppercase tracking-wider block">
                                    {{ $series->localizedLocation($locale) }}
                                </span>
                            @endif
                            <h3 class="text-lg sm:text-xl font-bold uppercase font-display tracking-tight text-arch-text group-hover:text-crimson transition-colors leading-snug">
                                <a href="{{ route('series.show', ['slug' => $series->slug]) }}">
                                    {{ $series->localizedTitle($locale) }}
                                </a>
                            </h3>
                            @if($series->localizedDescription($locale))
                                <p class="text-xs text-neutral-600 font-mono line-clamp-2 pt-1 leading-relaxed">
                                    {{ $series->localizedDescription($locale) }}
                                </p>
                            @endif
                        </div>

                        <div class="pt-3 border-t border-arch-border">
                            <a href="{{ route('series.show', ['slug' => $series->slug]) }}" 
                               class="btn-crimson w-full py-2.5 text-xs text-center font-bold flex items-center justify-center gap-2">
                                <span>Смотреть серию</span>
                                <span class="text-sm font-bold">&nearr;</span>
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- ========================================================
     5. HOW THE SHOOTING GOES: EDITORIAL TIMELINE
     ======================================================== --}}
<section id="shooting-process" class="bg-cine-black text-white py-20 sm:py-28 border-b border-cine-border relative overflow-hidden">
    {{-- Camera Flashes Atmosphere Background --}}
    <div class="absolute inset-0 pointer-events-none overflow-hidden z-0">
        {{-- Base ambient glow --}}
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(229,25,32,0.12),rgba(255,255,255,0))]"></div>

        {{-- Camera Strobe Bursts --}}
        <div class="camera-flash-burst flash-pos-1"></div>
        <div class="camera-flash-burst flash-pos-2"></div>
        <div class="camera-flash-burst flash-pos-3"></div>
        <div class="camera-flash-burst flash-pos-4"></div>

        {{-- Ambient Room Flash Illumination --}}
        <div class="ambient-flash-strobe"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        {{-- Section Header --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-14 sm:mb-20">
            <div>
                <div class="inline-flex items-center gap-2 text-xs font-mono uppercase tracking-widest text-crimson font-bold mb-3">
                    <span class="w-2 h-2 rounded-full bg-crimson animate-pulse"></span>
                    <span>04 / ПРОЦЕСС</span>
                </div>
                <h2 class="text-3xl sm:text-5xl font-extrabold uppercase font-display tracking-tight text-white leading-tight">
                    КАК ПРОХОДИТ СЪЁМКА
                </h2>
                <p class="text-xs sm:text-sm text-neutral-400 font-mono mt-3 max-w-xl leading-relaxed">
                    От первой идеи до готовых фотографий
                </p>
            </div>

            <div class="shrink-0 hidden md:block">
                <a href="{{ route('contacts.index') }}" 
                   class="inline-flex items-center gap-2 text-xs font-mono uppercase tracking-widest font-bold text-neutral-400 hover:text-crimson transition-colors group">
                    <span class="border-b border-neutral-700 group-hover:border-crimson transition-colors pb-0.5">Обсудить детали</span>
                    <span class="group-hover:translate-x-1 transition-transform duration-200">&rarr;</span>
                </a>
            </div>
        </div>

        {{-- Desktop Connected Timeline (lg:grid) --}}
        <div class="hidden lg:block relative">
            {{-- Horizontal Timeline Connecting Guide Line --}}
            <div class="absolute top-7 left-12 right-12 h-px bg-gradient-to-r from-crimson/80 via-white/20 to-crimson/80 z-0 pointer-events-none"></div>

            <div class="grid grid-cols-4 gap-6 xl:gap-8 relative z-10">
                
                {{-- Step 01 --}}
                <div class="flex flex-col group">
                    <div class="flex items-center justify-start mb-6">
                        <div class="w-14 h-14 rounded-full bg-cine-black border-2 border-white/25 group-hover:border-crimson flex items-center justify-center font-mono text-sm font-bold text-white group-hover:shadow-[0_0_16px_rgba(229,25,32,0.4)] group-hover:scale-110 transition-all duration-300 relative">
                            <span>01</span>
                        </div>
                    </div>

                    <div class="bg-cine-card/90 border border-cine-border group-hover:border-crimson/50 p-6 xl:p-7 rounded-sm flex flex-col justify-between flex-grow transition-all duration-300 shadow-card-depth group-hover:-translate-y-1">
                        <div>
                            <div class="flex items-center justify-between mb-4 pb-3 border-b border-white/5">
                                <span class="text-[0.68rem] font-mono uppercase tracking-widest text-crimson font-bold">ЭТАП 01</span>
                                <svg class="w-4 h-4 text-neutral-400 group-hover:text-crimson transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                            </div>

                            <h3 class="text-lg xl:text-xl font-extrabold uppercase font-display tracking-tight text-white group-hover:text-crimson transition-colors mb-3 leading-snug">
                                01 / ЗНАКОМИМСЯ
                            </h3>

                            <p class="text-xs xl:text-sm text-neutral-300 font-mono leading-relaxed font-light">
                                Вы рассказываете, какие фотографии хотите получить. Обсуждаем идею, выбираем формат и&nbsp;дату, согласовываем стоимость.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Step 02 --}}
                <div class="flex flex-col group">
                    <div class="flex items-center justify-start mb-6">
                        <div class="w-14 h-14 rounded-full bg-cine-black border-2 border-white/25 group-hover:border-crimson flex items-center justify-center font-mono text-sm font-bold text-white group-hover:shadow-[0_0_16px_rgba(229,25,32,0.4)] group-hover:scale-110 transition-all duration-300 relative">
                            <span>02</span>
                        </div>
                    </div>

                    <div class="bg-cine-card/90 border border-cine-border group-hover:border-crimson/50 p-6 xl:p-7 rounded-sm flex flex-col justify-between flex-grow transition-all duration-300 shadow-card-depth group-hover:-translate-y-1">
                        <div>
                            <div class="flex items-center justify-between mb-4 pb-3 border-b border-white/5">
                                <span class="text-[0.68rem] font-mono uppercase tracking-widest text-neutral-400 group-hover:text-crimson font-bold transition-colors">ЭТАП 02</span>
                                <svg class="w-4 h-4 text-neutral-400 group-hover:text-crimson transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>

                            <h3 class="text-lg xl:text-xl font-extrabold uppercase font-display tracking-tight text-white group-hover:text-crimson transition-colors mb-3 leading-snug">
                                02 / ГОТОВИМСЯ
                            </h3>

                            <p class="text-xs xl:text-sm text-neutral-300 font-mono leading-relaxed font-light">
                                Выбираем место и&nbsp;одежду. Можно прислать понравившиеся фотографии&nbsp;— они помогут понять настроение будущей съёмки.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Step 03 --}}
                <div class="flex flex-col group">
                    <div class="flex items-center justify-start mb-6">
                        <div class="w-14 h-14 rounded-full bg-cine-black border-2 border-white/25 group-hover:border-crimson flex items-center justify-center font-mono text-sm font-bold text-white group-hover:shadow-[0_0_16px_rgba(229,25,32,0.4)] group-hover:scale-110 transition-all duration-300 relative">
                            <span>03</span>
                        </div>
                    </div>

                    <div class="bg-cine-card/90 border border-cine-border group-hover:border-crimson/50 p-6 xl:p-7 rounded-sm flex flex-col justify-between flex-grow transition-all duration-300 shadow-card-depth group-hover:-translate-y-1">
                        <div>
                            <div class="flex items-center justify-between mb-4 pb-3 border-b border-white/5">
                                <span class="text-[0.68rem] font-mono uppercase tracking-widest text-neutral-400 group-hover:text-crimson font-bold transition-colors">ЭТАП 03</span>
                                <svg class="w-4 h-4 text-neutral-400 group-hover:text-crimson transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <circle cx="12" cy="13" r="4"/>
                                </svg>
                            </div>

                            <h3 class="text-lg xl:text-xl font-extrabold uppercase font-display tracking-tight text-white group-hover:text-crimson transition-colors mb-3 leading-snug">
                                03 / СНИМАЕМ
                            </h3>

                            <p class="text-xs xl:text-sm text-neutral-300 font-mono leading-relaxed font-light">
                                Начинаем с&nbsp;простых кадров. Я&nbsp;подсказываю позы и&nbsp;движения, помогаю освоиться перед камерой. Опыт съёмок не&nbsp;нужен.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Step 04 --}}
                <div class="flex flex-col group">
                    <div class="flex items-center justify-start mb-6">
                        <div class="w-14 h-14 rounded-full bg-cine-black border-2 border-white/25 group-hover:border-crimson flex items-center justify-center font-mono text-sm font-bold text-white group-hover:shadow-[0_0_16px_rgba(229,25,32,0.4)] group-hover:scale-110 transition-all duration-300 relative">
                            <span>04</span>
                        </div>
                    </div>

                    <div class="bg-cine-card/90 border border-cine-border group-hover:border-crimson/50 p-6 xl:p-7 rounded-sm flex flex-col justify-between flex-grow transition-all duration-300 shadow-card-depth group-hover:-translate-y-1">
                        <div>
                            <div class="flex items-center justify-between mb-4 pb-3 border-b border-white/5">
                                <span class="text-[0.68rem] font-mono uppercase tracking-widest text-neutral-400 group-hover:text-crimson font-bold transition-colors">ЭТАП 04</span>
                                <svg class="w-4 h-4 text-neutral-400 group-hover:text-crimson transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <rect x="3" y="3" width="18" height="18" rx="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <circle cx="8.5" cy="8.5" r="1.5"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 15l-5-5L5 21"/>
                                </svg>
                            </div>

                            <h3 class="text-lg xl:text-xl font-extrabold uppercase font-display tracking-tight text-white group-hover:text-crimson transition-colors mb-3 leading-snug">
                                04 / ПОЛУЧАЕТЕ ФОТОГРАФИИ
                            </h3>

                            <p class="text-xs xl:text-sm text-neutral-300 font-mono leading-relaxed font-light">
                                Я&nbsp;отбираю и&nbsp;обрабатываю снимки, затем отправляю ссылку на&nbsp;готовую серию. Количество фотографий и&nbsp;срок готовности указаны в&nbsp;выбранном пакете.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Mobile & Tablet Vertical Timeline (< lg) --}}
        <div class="lg:hidden relative pl-6 sm:pl-8 border-l-2 border-white/10 ml-4 sm:ml-6 space-y-8">
            
            {{-- Mobile Step 01 --}}
            <div class="relative group">
                <div class="absolute -left-[33px] sm:-left-[41px] top-1.5 w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-cine-black border-2 border-white/30 group-hover:border-crimson flex items-center justify-center font-mono text-xs sm:text-sm font-bold text-white transition-colors">
                    01
                </div>
                <div class="bg-cine-card/90 border border-cine-border p-5 sm:p-6 rounded-sm space-y-2.5">
                    <span class="text-[0.68rem] font-mono uppercase tracking-widest text-crimson font-bold block">ЭТАП 01</span>
                    <h3 class="text-lg sm:text-xl font-bold uppercase font-display text-white">
                        01 / ЗНАКОМИМСЯ
                    </h3>
                    <p class="text-xs sm:text-sm text-neutral-300 font-mono leading-relaxed font-light">
                        Вы рассказываете, какие фотографии хотите получить. Обсуждаем идею, выбираем формат и&nbsp;дату, согласовываем стоимость.
                    </p>
                </div>
            </div>

            {{-- Mobile Step 02 --}}
            <div class="relative group">
                <div class="absolute -left-[33px] sm:-left-[41px] top-1.5 w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-cine-black border-2 border-white/30 group-hover:border-crimson flex items-center justify-center font-mono text-xs sm:text-sm font-bold text-white transition-colors">
                    02
                </div>
                <div class="bg-cine-card/90 border border-cine-border p-5 sm:p-6 rounded-sm space-y-2.5">
                    <span class="text-[0.68rem] font-mono uppercase tracking-widest text-neutral-400 font-bold block">ЭТАП 02</span>
                    <h3 class="text-lg sm:text-xl font-bold uppercase font-display text-white">
                        02 / ГОТОВИМСЯ
                    </h3>
                    <p class="text-xs sm:text-sm text-neutral-300 font-mono leading-relaxed font-light">
                        Выбираем место и&nbsp;одежду. Можно прислать понравившиеся фотографии&nbsp;— они помогут понять настроение будущей съёмки.
                    </p>
                </div>
            </div>

            {{-- Mobile Step 03 --}}
            <div class="relative group">
                <div class="absolute -left-[33px] sm:-left-[41px] top-1.5 w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-cine-black border-2 border-white/30 group-hover:border-crimson flex items-center justify-center font-mono text-xs sm:text-sm font-bold text-white transition-colors">
                    03
                </div>
                <div class="bg-cine-card/90 border border-cine-border p-5 sm:p-6 rounded-sm space-y-2.5">
                    <span class="text-[0.68rem] font-mono uppercase tracking-widest text-neutral-400 font-bold block">ЭТАП 03</span>
                    <h3 class="text-lg sm:text-xl font-bold uppercase font-display text-white">
                        03 / СНИМАЕМ
                    </h3>
                    <p class="text-xs sm:text-sm text-neutral-300 font-mono leading-relaxed font-light">
                        Начинаем с&nbsp;простых кадров. Я&nbsp;подсказываю позы и&nbsp;движения, помогаю освоиться перед камерой. Опыт съёмок не&nbsp;нужен.
                    </p>
                </div>
            </div>

            {{-- Mobile Step 04 --}}
            <div class="relative group">
                <div class="absolute -left-[33px] sm:-left-[41px] top-1.5 w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-cine-black border-2 border-white/30 group-hover:border-crimson flex items-center justify-center font-mono text-xs sm:text-sm font-bold text-white transition-colors">
                    04
                </div>
                <div class="bg-cine-card/90 border border-cine-border p-5 sm:p-6 rounded-sm space-y-2.5">
                    <span class="text-[0.68rem] font-mono uppercase tracking-widest text-neutral-400 font-bold block">ЭТАП 04</span>
                    <h3 class="text-lg sm:text-xl font-bold uppercase font-display text-white">
                        04 / ПОЛУЧАЕТЕ ФОТОГРАФИИ
                    </h3>
                    <p class="text-xs sm:text-sm text-neutral-300 font-mono leading-relaxed font-light">
                        Я&nbsp;отбираю и&nbsp;обрабатываю снимки, затем отправляю ссылку на&nbsp;готовую серию. Количество фотографий и&nbsp;срок готовности указаны в&nbsp;выбранном пакете.
                    </p>
                </div>
            </div>

        </div>

        {{-- Section CTA Action --}}
        <div class="mt-14 sm:mt-18 text-center pt-8 border-t border-white/10">
            <div class="inline-flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-6">
                <a href="{{ route('contacts.index') }}" 
                   class="btn-crimson px-10 py-4 text-xs font-extrabold tracking-mega shadow-crimson-btn">
                    <span>ОБСУДИТЬ СЪЁМКУ</span>
                    <span class="text-sm font-bold">&rarr;</span>
                </a>
                <a href="{{ route('pricing.index') }}" 
                   class="text-xs font-mono uppercase tracking-widest font-bold text-neutral-400 hover:text-white transition-colors inline-flex items-center gap-2">
                    <span class="border-b border-neutral-700 hover:border-white transition-colors pb-0.5">Посмотреть форматы и цены</span>
                    <span>&rarr;</span>
                </a>
            </div>
        </div>

    </div>
</section>

{{-- ========================================================
     6. PRICING & PACKAGES: "CHOOSE YOUR STORY"
     ======================================================== --}}
<section class="bg-arch-bg text-arch-text pt-8 pb-20 sm:pt-10 sm:pb-24 border-b border-arch-border">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-12">
            
            {{-- Column 1: Intro --}}
            <div class="lg:col-span-4 p-6 sm:p-8 lg:p-10 flex flex-col justify-between space-y-6">
                <div>
                    <span class="text-[0.68rem] uppercase tracking-widest text-crimson font-mono font-bold block mb-3">
                        05 / СТОИМОСТЬ СЪЁМКИ
                    </span>
                    <h2 class="text-2xl sm:text-3xl lg:text-3xl xl:text-4xl font-extrabold uppercase tracking-tight font-display text-arch-text mb-4 leading-tight break-words">
                        ВЫБЕРИТЕ СВОЙ ФОРМАТ
                    </h2>
                    <p class="text-xs sm:text-sm text-neutral-600 font-mono leading-relaxed">
                        Короткая съёмка для нескольких новых портретов или более продолжительная – с разными образами и настроением.
                    </p>
                    <p class="text-xs sm:text-sm text-neutral-600 font-mono leading-relaxed mt-4">
                        Не знаете, что выбрать? Напишите мне – помогу определиться.
                    </p>
                </div>

                <div class="pt-4">
                    <a href="{{ route('pricing.index') }}" 
                       class="inline-flex items-center gap-2 text-xs font-mono uppercase tracking-widest font-bold text-neutral-500 hover:text-crimson transition-colors group">
                        <span class="border-b border-neutral-300 group-hover:border-crimson transition-colors pb-0.5">Посмотреть все форматы</span>
                        <span class="group-hover:translate-x-1 transition-transform duration-200">&rarr;</span>
                    </a>
                </div>
            </div>

            {{-- Column 2 & 3: Packages with Dark Image Headers --}}
            @foreach($packages->take(2) as $pkg)
                <div class="lg:col-span-4 p-6 sm:p-8 flex flex-col justify-between bg-white space-y-6 {{ $loop->iteration === 2 ? 'lg:border-l border-arch-border' : '' }}">
                    <div>
                        {{-- Dark Header Card --}}
                        <div class="bg-neutral-950 text-white p-5 sm:p-6 rounded-sm mb-6 relative overflow-hidden">
                            <span class="text-[0.65rem] font-mono text-crimson uppercase tracking-widest font-bold block mb-1">
                                ПАКЕТ 0{{ $loop->iteration }}
                            </span>
                            <h3 class="text-xl sm:text-2xl font-bold uppercase font-display mb-3 leading-snug break-words">
                                {{ $pkg->localizedTitle($locale) }}
                            </h3>
                            <div class="flex items-baseline gap-2.5 flex-wrap">
                                <span class="text-lg sm:text-xl font-bold text-neutral-200 font-display">
                                    {{ $pkg->formattedPrice($locale) }}
                                </span>
                                @if($pkg->localizedDuration($locale))
                                    <span class="text-xs font-mono text-neutral-400">
                                        / {{ $pkg->localizedDuration($locale) }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <p class="text-xs text-neutral-600 font-mono mb-6 leading-relaxed">
                            {{ $pkg->localizedSubtitle($locale) }}
                        </p>

                        {{-- Inclusions --}}
                        <ul class="space-y-2.5 text-xs font-mono text-neutral-700 mb-8 border-t border-arch-border pt-4">
                            @foreach($pkg->getIncludesList($locale) as $item)
                                <li class="flex items-start gap-2">
                                    <span class="text-crimson font-bold shrink-0">&bull;</span>
                                    <span class="leading-relaxed">{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <a href="{{ route('contacts.index', ['package' => $pkg->localizedTitle($locale)]) }}#feedback-form" 
                       class="btn-crimson w-full py-3.5 text-xs text-center font-bold">
                        <span>{{ __('site.book_package') }}</span>
                        <span>&nearr;</span>
                    </a>
                </div>
            @endforeach

        </div>

    </div>
</section>

{{-- ========================================================
     6. PHILOSOPHY / CLIENT STORIES (EDITORIAL QUOTE & PORTRAIT)
     ======================================================== --}}
<section class="bg-arch-bg text-arch-text py-24 border-b border-arch-border">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            {{-- Left: Crimson Framed Portrait --}}
            <div class="lg:col-span-4">
                <div class="relative bg-crimson p-4 pb-12 shadow-card-depth">
                    <div class="aspect-[3/4] overflow-hidden bg-neutral-900">
                        <img src="{{ asset('images/romanyun.jpg') }}" alt="Роман Юн" class="w-full h-full object-cover filter contrast-110">
                    </div>
                    <div class="absolute bottom-3 left-4 right-4 flex items-center justify-between text-white font-mono text-xs uppercase font-bold">
                        <span>Роман Юн</span>
                        <span>Иркутск</span>
                    </div>
                </div>
            </div>

            {{-- Right: Editorial Bio Statement --}}
            <div class="lg:col-span-8 space-y-6 lg:pl-6">
                <div>
                    <span class="text-xs uppercase tracking-widest text-crimson font-mono font-bold block mb-3">
                        06 / О фотографе
                    </span>
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold uppercase tracking-tight text-arch-text font-display leading-tight">
                        Привет, я Роман
                    </h2>
                </div>

                <div class="space-y-4 text-xs sm:text-sm text-neutral-600 font-mono leading-relaxed">
                    <p>
                        Снимаю в&nbsp;Иркутске. Мне интересно находить в&nbsp;человеке его собственный характер — во&nbsp;взгляде, движении и&nbsp;небольших деталях.
                    </p>
                    <p>
                        Если вы впервые перед камерой, не нужно заранее знать, как позировать. Я помогу выбрать место, подскажу, что делать в&nbsp;кадре, и&nbsp;дам время освоиться.
                    </p>
                    <p>
                        До&nbsp;съёмки обсудим ваши пожелания и&nbsp;примеры фотографий, которые вам нравятся.
                    </p>
                </div>

                <div class="pt-4 flex flex-wrap items-center gap-6">
                    <a href="{{ route('contacts.index') }}" class="btn-crimson px-8 py-3.5 text-xs font-bold">
                        <span>Обсудить съёмку</span>
                        <span>&nearr;</span>
                    </a>
                    <a href="{{ route('about.index') }}" class="text-xs font-mono uppercase tracking-widest font-bold text-arch-text hover:text-crimson transition-colors">
                        Больше обо мне &rarr;
                    </a>
                </div>
            </div>

        </div>

    </div>
</section>

@endsection
