@extends('layouts.app')

@section('title', __('site.meta_title'))
@section('description', __('site.meta_description'))

@section('content')

{{-- ========================================================
     1. HERO SECTION (CINEMATIC DARK WITH RED FOCUS BRACKET)
     ======================================================== --}}
<section class="relative min-h-[90vh] flex items-center justify-center bg-cine-black overflow-hidden pt-12 pb-24 border-b border-cine-border">
    
    {{-- Background Motion / Atmosphere Layer --}}
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('storage/demo/hero-main.jpg') }}" 
             alt="{{ __('site.author_name') }}" 
             fetchpriority="high"
             loading="eager"
             class="w-full h-full object-cover object-center opacity-30 filter grayscale contrast-125 scale-105">
        <div class="absolute inset-0 bg-gradient-to-t from-cine-black via-cine-black/60 to-cine-black/40"></div>
        <div class="absolute inset-0 bg-radial from-transparent via-cine-black/50 to-cine-black"></div>
    </div>

    {{-- Floating Author Tag (Top Left as in Reference) --}}
    <div class="absolute top-8 left-6 sm:left-12 z-20 hidden sm:flex items-center gap-3 bg-cine-surface/80 backdrop-blur-md p-2.5 pr-4 border border-white/10 rounded-sm shadow-xl">
        <div class="w-10 h-10 overflow-hidden bg-neutral-800 rounded-sm relative border border-white/20">
            <img src="{{ asset('storage/demo/portrait-2.jpg') }}" alt="Roman Yun" class="w-full h-full object-cover">
        </div>
        <div class="text-left font-mono">
            <span class="text-xs font-bold text-white uppercase block leading-none tracking-wider">Роман Юн</span>
            <span class="text-[0.62rem] text-crimson uppercase tracking-widest font-semibold">Иркутск &bull; Автор</span>
        </div>
    </div>

    {{-- Camera Auto-Focus Frame (Center Visual Anchor from Reference) --}}
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-44 z-10 pointer-events-none">
        <div class="camera-focus-bracket w-36 h-36 sm:w-44 sm:h-44 rounded-sm">
            <span class="absolute -top-3 left-0 bg-crimson text-white font-mono text-[0.6rem] px-1 font-bold uppercase tracking-widest">
                AF-C 50MM
            </span>
            <span class="absolute -bottom-3 right-0 bg-white/20 backdrop-blur-sm text-white font-mono text-[0.6rem] px-1 uppercase tracking-widest">
                RAW &bull; 1/250s
            </span>
        </div>
    </div>

    {{-- Center Typographic Impact --}}
    <div class="relative z-20 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center pt-28">
        
        <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 backdrop-blur-md border border-white/15 mb-6 text-[0.68rem] uppercase font-mono tracking-widest text-neutral-300">
            <span class="w-2 h-2 rounded-full bg-crimson shadow-crimson-glow"></span>
            <span>PORTFOLIO &bull; IRKUTSK 2026</span>
        </div>

        <h1 class="text-4xl sm:text-6xl md:text-7xl lg:text-8xl font-extrabold uppercase tracking-tight text-white font-display leading-[0.95] mb-6">
            {{ \App\Models\Setting::get("hero_phrase_{$locale}", __('site.hero_phrase')) }}
        </h1>

        <p class="text-sm sm:text-base md:text-lg text-neutral-300 font-light max-w-2xl mx-auto uppercase tracking-wide font-mono mb-10 opacity-90">
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
            
            {{-- Column 1: Statement & Yearbook --}}
            <div class="lg:col-span-4 p-8 sm:p-12 flex flex-col justify-between space-y-8">
                <div>
                    <span class="text-[0.68rem] uppercase tracking-widest text-neutral-400 font-mono font-bold block mb-4">
                        01 / YEARBOOK 2026
                    </span>
                    <blockquote class="text-xl sm:text-2xl font-bold uppercase tracking-tight text-arch-text leading-snug">
                        «Каждый кадр — это пойманный свет, честные эмоции и внимание к индивидуальности человека.»
                    </blockquote>
                </div>
                <div>
                    <a href="{{ route('about.index') }}" 
                       class="btn-crimson px-6 py-3 text-[0.7rem] font-bold">
                        <span>О подходе автора</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

            {{-- Column 2: Folder Tab Photo Card --}}
            <div class="lg:col-span-4 p-8 sm:p-12 flex flex-col justify-between space-y-6">
                <div class="flex items-center justify-between font-mono text-xs text-neutral-400">
                    <span class="text-3xl font-extrabold text-arch-text font-display">05</span>
                    <span class="uppercase tracking-widest">DIRECTIONS</span>
                </div>

                {{-- Folder Tab Styled Card --}}
                <div class="bg-white p-4 border border-arch-border shadow-card-depth group">
                    <div class="aspect-[4/5] overflow-hidden bg-neutral-100 mb-3 relative">
                        <img src="{{ asset('storage/demo/portrait-3.jpg') }}" alt="" class="w-full h-full object-cover filter grayscale contrast-110 group-hover:scale-105 transition-transform duration-700">
                        <div class="absolute top-2 left-2 bg-black text-white text-[0.6rem] font-mono uppercase px-2 py-0.5">
                            PORTRAIT
                        </div>
                    </div>
                    <p class="text-xs text-neutral-600 font-mono leading-relaxed">
                        Естественный свет Сибири, геометрия городского пространства и искренний взгляд.
                    </p>
                </div>

                <div class="text-[0.68rem] font-mono text-neutral-400 uppercase tracking-widest">
                    IRKUTSK &bull; EDITORIAL ARCHIVE
                </div>
            </div>

            {{-- Column 3: Red Duotone Card --}}
            <div class="lg:col-span-4 p-8 sm:p-12 flex flex-col justify-between space-y-6">
                <div>
                    <span class="text-[0.68rem] uppercase tracking-widest text-crimson font-mono font-bold block mb-2">
                        02 / CONCEPT
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight uppercase font-display text-arch-text">
                        CAPTURING LIFE BEYOND THE FRAME
                    </h2>
                </div>

                {{-- Crimson Duotone Editorial Highlight Card --}}
                <a href="{{ route('portfolio.index') }}" 
                   class="block relative rounded-sm overflow-hidden group bg-crimson text-white p-8 aspect-[4/5] flex flex-col justify-between shadow-card-depth">
                    <img src="{{ asset('storage/demo/hero-secondary.jpg') }}" alt="" class="absolute inset-0 w-full h-full object-cover mix-blend-multiply opacity-70 group-hover:scale-105 transition-transform duration-700">
                    <div class="relative z-10 flex items-center justify-between text-xs font-mono font-bold uppercase">
                        <span>PORTFOLIO</span>
                        <span class="w-8 h-8 rounded-full border border-white/40 flex items-center justify-center group-hover:bg-white group-hover:text-crimson transition-colors">
                            &nearr;
                        </span>
                    </div>
                    <div class="relative z-10">
                        <span class="text-xs uppercase tracking-widest font-mono text-white/80 block mb-1">Смотреть работы</span>
                        <span class="text-xl font-bold uppercase tracking-tight font-display">
                            Открыть серии &rarr;
                        </span>
                    </div>
                </a>

                <div class="text-[0.68rem] font-mono text-neutral-400 uppercase tracking-widest">
                    BAIKAL REGION &bull; AVAILABLE 2026
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
                <h2 class="text-4xl sm:text-6xl font-extrabold uppercase tracking-tight text-white font-display leading-[0.95]">
                    WHAT I CAN OFFER
                </h2>
                <p class="text-sm text-neutral-400 font-light max-w-md leading-relaxed font-mono">
                    Живые моменты, чистая геометрия кадра и естественный свет. Выберите подходящее направление для вашей съёмки.
                </p>
                <div class="pt-4">
                    <a href="{{ route('portfolio.index') }}" class="btn-crimson px-8 py-3.5 text-xs font-bold">
                        <span>Смотреть все серии</span>
                        <span>&nearr;</span>
                    </a>
                </div>
            </div>

            {{-- Right Column: Horizontal Accordion Rows with Thumbnails --}}
            <div class="lg:col-span-7 divide-y divide-white/10 border-y border-white/10">
                @foreach($categories as $cat)
                    <a href="{{ route('portfolio.index', ['category' => $cat->slug]) }}" 
                       class="py-6 px-4 flex flex-col sm:flex-row sm:items-center justify-between gap-6 group hover:bg-white/[0.03] transition-colors">
                        
                        <div class="space-y-1">
                            <div class="text-xs font-mono text-crimson font-bold">
                                0{{ $loop->iteration }} &bull; DIRECTION
                            </div>
                            <h3 class="text-2xl font-bold uppercase tracking-tight text-white group-hover:text-crimson font-display transition-colors">
                                {{ $cat->localizedName($locale) }}
                            </h3>
                            <p class="text-xs text-neutral-400 font-light font-mono">
                                {{ $cat->series_count }} готовых историй в портфолио
                            </p>
                        </div>

                        {{-- Inset Preview Thumbnail & Arrow --}}
                        <div class="flex items-center gap-4 shrink-0">
                            <div class="w-24 h-16 overflow-hidden bg-neutral-800 rounded-sm border border-white/10">
                                @php
                                    $sampleImg = match($cat->slug) {
                                        'portraits' => 'storage/demo/portrait-1.jpg',
                                        'couples' => 'storage/demo/couple-1.jpg',
                                        'families' => 'storage/demo/family-1.jpg',
                                        'events' => 'storage/demo/event-1.jpg',
                                        default => 'storage/demo/business-1.jpg',
                                    };
                                @endphp
                                <img src="{{ asset($sampleImg) }}" alt="" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            </div>
                            <span class="w-8 h-8 rounded-full border border-white/20 flex items-center justify-center text-white group-hover:bg-crimson group-hover:border-crimson transition-all text-xs font-bold">
                                &nearr;
                            </span>
                        </div>

                    </a>
                @endforeach
            </div>

        </div>

    </div>
</section>

{{-- ========================================================
     4. PANORAMIC STORIES: "SEE THE MAGIC FOR YOURSELF"
     ======================================================== --}}
<section class="bg-arch-bg text-arch-text py-24 border-b border-arch-border">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Section Header --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6">
            <div>
                <span class="text-xs uppercase tracking-widest text-crimson font-mono font-bold block mb-2">
                    03 / CURATED ARCHIVE
                </span>
                <h2 class="text-3xl sm:text-5xl font-extrabold tracking-tight uppercase font-display text-arch-text">
                    SEE THE MAGIC FOR YOURSELF
                </h2>
            </div>
            <a href="{{ route('portfolio.index') }}" 
               class="text-xs uppercase tracking-widest font-bold text-arch-text hover:text-crimson inline-flex items-center gap-2 transition-colors font-mono">
                <span>{{ __('site.view_all_series') }}</span>
                <span>&rarr;</span>
            </a>
        </div>

        {{-- Panoramic Series Cards with Inset Overlapping Photo (Exact match to reference) --}}
        <div class="space-y-16">
            @foreach($featuredSeries as $index => $series)
                <div class="bg-white border border-arch-border shadow-card-depth overflow-hidden group">
                    <div class="relative aspect-[16/9] sm:aspect-[21/9] overflow-hidden bg-neutral-900">
                        
                        {{-- Panoramic Background Image --}}
                        <img src="{{ $series->cover_url }}" 
                             alt="{{ $series->localizedTitle($locale) }}" 
                             loading="lazy"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-1000 ease-out opacity-85">
                        
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>

                        {{-- Floating Inset Inverted Secondary Card (Signature from reference!) --}}
                        <div class="absolute bottom-6 left-6 sm:bottom-8 sm:left-8 z-20 flex flex-col sm:flex-row sm:items-end gap-6">
                            
                            {{-- Mini Inset Thumbnail --}}
                            @php
                                $secondPhoto = $series->photos->skip(1)->first();
                            @endphp
                            @if($secondPhoto)
                            <div class="w-32 h-44 sm:w-40 sm:h-52 bg-black border-2 border-white shadow-2xl overflow-hidden hidden sm:block relative">
                                <img src="{{ $secondPhoto->url }}" alt="" class="w-full h-full object-cover">
                                <span class="absolute top-2 left-2 bg-crimson text-white font-mono text-[0.55rem] px-1 font-bold">
                                    INSET 0{{ $index + 1 }}
                                </span>
                            </div>
                            @endif

                            {{-- Title and Red Action Button --}}
                            <div class="space-y-3 text-white text-left">
                                <span class="text-xs font-mono uppercase tracking-widest text-crimson font-bold bg-black/60 px-2 py-0.5">
                                    {{ $series->category ? $series->category->localizedName($locale) : 'SERIES' }} &bull; {{ $series->localizedLocation($locale) }}
                                </span>
                                <h3 class="text-2xl sm:text-4xl font-extrabold uppercase tracking-tight font-display drop-shadow-md">
                                    {{ $series->localizedTitle($locale) }}
                                </h3>
                                <div>
                                    <a href="{{ route('series.show', ['slug' => $series->slug]) }}" 
                                       class="btn-crimson px-6 py-3 text-xs tracking-wider font-bold">
                                        <span>Смотреть серию</span>
                                        <span>&nearr;</span>
                                    </a>
                                </div>
                            </div>

                        </div>

                        {{-- Top Right Badge --}}
                        <div class="absolute top-6 right-6 font-mono text-xs text-white bg-black/70 backdrop-blur-sm px-3 py-1 uppercase tracking-widest border border-white/20">
                            {{ $series->photos->count() }} КАДРОВ
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

    </div>
</section>

{{-- ========================================================
     5. PRICING & PACKAGES: "CHOOSE YOUR STORY"
     ======================================================== --}}
<section class="bg-arch-bg text-arch-text py-24 border-b border-arch-border">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 divide-y lg:divide-y-0 lg:divide-x divide-arch-border border-y border-arch-border">
            
            {{-- Column 1: Intro & Portrait Tag --}}
            <div class="lg:col-span-4 p-8 sm:p-12 flex flex-col justify-between space-y-8">
                <div>
                    <span class="text-[0.68rem] uppercase tracking-widest text-crimson font-mono font-bold block mb-3">
                        04 / PRICING & PACKAGES
                    </span>
                    <h2 class="text-3xl sm:text-5xl font-extrabold uppercase tracking-tight font-display text-arch-text mb-4">
                        CHOOSE YOUR STORY
                    </h2>
                    <p class="text-xs text-neutral-600 font-mono leading-relaxed">
                        Прозрачные форматы сотрудничества. Каждый пакет адаптируется под ваши задачи в Иркутске и окрестностях.
                    </p>
                </div>

                {{-- Author Mini Profile Tag --}}
                <div class="bg-white p-4 border border-arch-border flex items-center gap-3">
                    <img src="{{ asset('storage/demo/portrait-2.jpg') }}" alt="" class="w-10 h-10 object-cover rounded-sm">
                    <div class="text-xs font-mono">
                        <span class="font-bold text-arch-text block">Роман Юн</span>
                        <span class="text-neutral-500 text-[0.65rem]">Консультация и бронь</span>
                    </div>
                </div>
            </div>

            {{-- Column 2 & 3: Packages with Dark Image Headers --}}
            @foreach($packages->take(2) as $pkg)
                <div class="lg:col-span-4 p-8 sm:p-10 flex flex-col justify-between bg-white space-y-6">
                    <div>
                        {{-- Dark Header Card --}}
                        <div class="bg-neutral-950 text-white p-6 rounded-sm mb-6 relative overflow-hidden">
                            <span class="text-[0.65rem] font-mono text-crimson uppercase tracking-widest font-bold block mb-1">
                                PACKAGE 0{{ $loop->iteration }}
                            </span>
                            <h3 class="text-2xl font-bold uppercase font-display mb-2">
                                {{ $pkg->localizedTitle($locale) }}
                            </h3>
                            <div class="text-2xl font-extrabold text-white">
                                {{ $pkg->formattedPrice($locale) }}
                            </div>
                        </div>

                        <p class="text-xs text-neutral-600 font-mono mb-6 leading-relaxed">
                            {{ $pkg->localizedSubtitle($locale) }}
                        </p>

                        {{-- Inclusions --}}
                        <ul class="space-y-2 text-xs font-mono text-neutral-700 mb-8 border-t border-arch-border pt-4">
                            @foreach($pkg->getIncludesList($locale) as $item)
                                <li class="flex items-start gap-2">
                                    <span class="text-crimson font-bold">&bull;</span>
                                    <span>{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <a href="{{ route('contacts.index') }}" 
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
                        <img src="{{ asset('storage/demo/portrait-2.jpg') }}" alt="Roman Yun" class="w-full h-full object-cover filter contrast-110">
                    </div>
                    <div class="absolute bottom-3 left-4 right-4 flex items-center justify-between text-white font-mono text-xs uppercase font-bold">
                        <span>Роман Юн</span>
                        <span>Иркутск</span>
                    </div>
                </div>
            </div>

            {{-- Right: Massive Editorial Quote Statement --}}
            <div class="lg:col-span-8 space-y-6 lg:pl-6">
                <span class="text-xs uppercase tracking-widest text-crimson font-mono font-bold block">
                    05 / PHILOSOPHY
                </span>
                <span class="text-6xl text-crimson font-serif font-black block leading-none select-none">“</span>
                <blockquote class="text-2xl sm:text-3xl lg:text-4xl font-extrabold uppercase tracking-tight text-arch-text font-display leading-snug -mt-8">
                    «Мне важно, чтобы во время съёмки вы чувствовали себя свободно и естественно. Без навязанных поз и шаблонов — только ваши настоящие эмоции.»
                </blockquote>
                <div class="pt-4 flex items-center gap-6">
                    <a href="{{ route('contacts.index') }}" class="btn-crimson px-8 py-3.5 text-xs font-bold">
                        <span>Обсудить съёмку</span>
                        <span>&nearr;</span>
                    </a>
                    <a href="{{ route('about.index') }}" class="text-xs font-mono uppercase tracking-widest font-bold text-arch-text hover:text-crimson transition-colors">
                        Подробнее о фотографе &rarr;
                    </a>
                </div>
            </div>

        </div>

    </div>
</section>

@endsection
