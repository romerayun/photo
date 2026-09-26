@extends('layouts.app')

@section('no_preloader', 'true')
@section('title', ($status ?? '404') . ' — ' . ($title ?? 'Страница не найдена') . ' | ' . __('site.author_name'))
@section('description', $description ?? 'Запрашиваемая страница не существует или была перемещена.')
@section('robots', 'noindex, nofollow')

@section('content')
<section class="relative min-h-[78vh] flex items-center justify-center overflow-hidden bg-cine-black text-white px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
    {{-- Ambient Cinematic Background Elements --}}
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
        {{-- Subtle radial gradient glow centered --}}
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[340px] sm:w-[600px] h-[340px] sm:h-[600px] bg-crimson/10 rounded-full blur-[100px] sm:blur-[140px]"></div>
        
        {{-- Architectural Grid / Film Guide Crosshairs --}}
        <div class="absolute inset-0 bg-[linear-gradient(to_right,rgba(255,255,255,0.02)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.02)_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_50%,#000_70%,transparent_100%)]"></div>
        
        {{-- Viewfinder Framing Marks --}}
        <div class="hidden sm:block absolute top-12 left-12 w-8 h-8 border-t border-l border-white/20"></div>
        <div class="hidden sm:block absolute top-12 right-12 w-8 h-8 border-t border-r border-white/20"></div>
        <div class="hidden sm:block absolute bottom-12 left-12 w-8 h-8 border-b border-l border-white/20"></div>
        <div class="hidden sm:block absolute bottom-12 right-12 w-8 h-8 border-b border-r border-white/20"></div>
    </div>

    <div class="relative max-w-3xl mx-auto w-full text-center z-10">
        
        {{-- Viewfinder Status Badge / Frame Indicator --}}
        <div class="inline-flex items-center gap-2.5 px-3.5 py-1.5 rounded-full border border-cine-border bg-cine-surface/90 backdrop-blur-md mb-8 text-[0.68rem] uppercase font-mono tracking-widest text-neutral-400">
            <span class="w-2 h-2 rounded-full {{ ($status ?? 404) >= 500 ? 'bg-amber-500 animate-pulse' : 'bg-crimson' }}"></span>
            <span>FRAME_SIGNAL // ERROR_{{ $status ?? '404' }}</span>
            <span class="text-neutral-600">|</span>
            <span class="text-neutral-400 font-bold uppercase">{{ $label ?? 'OUT OF FOCUS' }}</span>
        </div>

        {{-- Huge Artistic Error Code with Viewfinder Target Brackets --}}
        <div class="relative inline-block select-none my-2 sm:my-4">
            {{-- Ghost Glow Background Text --}}
            <div class="text-7xl sm:text-9xl md:text-[11rem] font-black font-display tracking-tight text-white/5 absolute inset-0 blur-md transform scale-105 pointer-events-none" aria-hidden="true">
                {{ $status ?? '404' }}
            </div>

            {{-- Main Code --}}
            <h1 class="relative text-7xl sm:text-9xl md:text-[11rem] font-black font-display tracking-tightest text-white leading-none">
                <span class="bg-clip-text text-transparent bg-gradient-to-b from-white via-neutral-200 to-neutral-500">
                    {{ $status ?? '404' }}
                </span>
            </h1>

            {{-- Camera Viewfinder Crosshairs --}}
            <div class="absolute -top-3 -left-3 sm:-top-5 sm:-left-5 w-6 h-6 sm:w-8 sm:h-8 border-t-2 border-l-2 border-crimson pointer-events-none"></div>
            <div class="absolute -bottom-3 -right-3 sm:-bottom-5 sm:-right-5 w-6 h-6 sm:w-8 sm:h-8 border-b-2 border-r-2 border-crimson pointer-events-none"></div>
        </div>

        {{-- Error Heading & Description --}}
        <div class="space-y-3 mt-6 sm:mt-8 max-w-xl mx-auto">
            <h2 class="text-xl sm:text-3xl font-extrabold uppercase tracking-tight font-display text-white">
                {{ $title ?? 'Кадр не найден' }}
            </h2>
            <p class="text-sm sm:text-base text-neutral-400 font-mono leading-relaxed">
                {{ $description ?? 'Возможно, страница была удалена, перемещена или адрес введён с опечаткой.' }}
            </p>
        </div>

        {{-- Quick Direction Links / Action Buttons --}}
        <div class="mt-10 sm:mt-12 flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4 font-mono text-xs uppercase tracking-wider font-bold">
            <a href="{{ route('home') }}" 
               class="btn-crimson w-full sm:w-auto px-7 py-3.5 inline-flex items-center justify-center gap-2 shadow-crimson-btn transition-transform hover:-translate-y-0.5">
                <span>&larr;</span>
                <span>На главную</span>
            </a>

            <a href="{{ route('portfolio.index') }}" 
               class="w-full sm:w-auto px-6 py-3.5 bg-neutral-900/90 hover:bg-neutral-800 text-white border border-cine-border hover:border-neutral-500 transition-all inline-flex items-center justify-center gap-2">
                <span>В портфолио</span>
                <span>&rarr;</span>
            </a>

            <a href="{{ route('contacts.index') }}" 
               class="w-full sm:w-auto px-6 py-3.5 text-neutral-400 hover:text-white transition-colors inline-flex items-center justify-center gap-1.5">
                <span>Связаться</span>
                <span>&nearr;</span>
            </a>
        </div>

        {{-- Popular Sections Breadcrumb Navigation --}}
        <div class="mt-14 pt-8 border-t border-cine-border/60 max-w-md mx-auto">
            <span class="text-[0.62rem] uppercase font-mono tracking-widest text-neutral-500 block mb-3">
                Популярные разделы
            </span>
            <div class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-xs font-mono uppercase text-neutral-400">
                <a href="{{ route('portfolio.index') }}" class="hover:text-crimson transition-colors">Портфолио</a>
                <span class="text-neutral-700">&bull;</span>
                <a href="{{ route('pricing.index') }}" class="hover:text-crimson transition-colors">Услуги и цены</a>
                <span class="text-neutral-700">&bull;</span>
                <a href="{{ route('articles.index') }}" class="hover:text-crimson transition-colors">Статьи</a>
                <span class="text-neutral-700">&bull;</span>
                <a href="{{ route('about.index') }}" class="hover:text-crimson transition-colors">Обо мне</a>
            </div>
        </div>

    </div>
</section>
@endsection
