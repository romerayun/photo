@extends('layouts.app')

@section('title', __('site.nav_about') . ' — ' . __('site.author_name'))
@section('description', __('site.about_subtitle') . '. ' . __('site.about_p1'))

@section('content')
<div class="bg-arch-bg text-arch-text py-16 md:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-start">
            
            {{-- Left: Crimson Framed Portrait --}}
            <div class="lg:col-span-5 sticky top-28">
                <div class="bg-crimson p-4 pb-14 shadow-card-depth relative group">
                    <div class="aspect-[3/4] overflow-hidden bg-neutral-900">
                        <img src="{{ asset('images/romanyun.jpg') }}" 
                             alt="{{ __('site.author_name') }} — {{ __('site.author_role') }}" 
                             loading="eager"
                             width="928"
                             height="1152"
                             class="w-full h-full object-cover filter contrast-110">
                    </div>
                    
                    <div class="absolute bottom-4 left-4 right-4 flex items-center justify-between text-white font-mono text-xs uppercase font-bold">
                        <span>{{ __('site.author_name') }}</span>
                        <span>{{ __('site.location') }} &bull; СИБИРЬ</span>
                    </div>
                </div>
            </div>

            {{-- Right: Narrative --}}
            <div class="lg:col-span-7 space-y-8 lg:pt-4">
                
                <div>
                    <span class="text-xs uppercase tracking-widest text-crimson font-mono font-bold block mb-2">
                        01 / О ФОТОГРАФЕ
                    </span>
                    <h1 class="text-4xl sm:text-6xl font-extrabold uppercase tracking-tightest font-display text-arch-text mb-3">
                        {{ __('site.about_title') }}
                    </h1>
                    <p class="text-xl font-mono text-neutral-500 uppercase tracking-wide">
                       Фотограф в Иркутске
                    </p>
                </div>

                <div class="space-y-6 text-base sm:text-lg text-neutral-800 font-mono leading-relaxed border-t border-arch-border pt-8">
                    <p>
                        {{ __('site.about_p1') }}
                    </p>
                    <p>
                        {{ __('site.about_p2') }}
                    </p>
                    <p>
                        {{ __('site.about_p3') }}
                    </p>
                    <p>
                        {{ __('site.about_p4') }}
                    </p>
                </div>

                {{-- Action links --}}
                <div class="pt-6 border-t border-arch-border flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
                    <a href="{{ route('contacts.index') }}" 
                       class="btn-crimson px-8 py-4 text-xs font-bold">
                        <span>{{ __('site.about_cta') }}</span>
                        <span>&nearr;</span>
                    </a>
                    <a href="{{ route('portfolio.index') }}" 
                       class="px-7 py-4 bg-white border border-arch-border text-arch-text text-xs uppercase tracking-widest font-bold hover:border-black transition-colors font-mono">
                        {{ __('site.hero_portfolio') }} &rarr;
                    </a>
                </div>

            </div>

        </div>

    </div>
</div>
@endsection
