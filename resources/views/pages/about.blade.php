@extends('layouts.app')

@section('title', __('site.nav_about') . ' — ' . __('site.author_name'))
@section('description', __('site.about_subtitle') . '. ' . __('site.about_p1'))

@section('content')
<div class="py-12 md:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-start">
            
            {{-- Left: Portrait spot --}}
            <div class="lg:col-span-5 sticky top-28">
                <div class="bg-subtle aspect-[3/4] overflow-hidden relative shadow-sm">
                    <img src="{{ asset('storage/demo/portrait-2.jpg') }}" 
                         alt="{{ __('site.author_name') }} — {{ __('site.author_role') }}" 
                         loading="eager"
                         width="800"
                         height="1067"
                         class="w-full h-full object-cover">
                    
                    <div class="absolute bottom-4 left-4 bg-canvas/95 backdrop-blur-sm px-4 py-2 border border-editorial-border">
                        <span class="font-serif text-lg text-graphite-950 block">{{ __('site.author_name') }}</span>
                        <span class="text-xs uppercase tracking-widest text-graphite-500 font-mono">{{ __('site.location') }}</span>
                    </div>
                </div>
            </div>

            {{-- Right: Sincere 1st-person statement --}}
            <div class="lg:col-span-7 space-y-8 lg:pt-4">
                
                <div>
                    <span class="text-xs uppercase tracking-widest text-terracotta font-medium block mb-2">
                        {{ __('site.nav_about') }}
                    </span>
                    <h1 class="font-serif text-4xl sm:text-5xl text-graphite-950 editorial-heading mb-3">
                        {{ __('site.about_title') }}
                    </h1>
                    <p class="font-serif italic text-xl text-graphite-600">
                        {{ __('site.about_subtitle') }}
                    </p>
                </div>

                <div class="space-y-6 text-base sm:text-lg text-graphite-700 font-light leading-relaxed border-t border-editorial-border pt-8">
                    <p>
                        {{ __('site.about_p1') }}
                    </p>
                    <p>
                        {{ __('site.about_p2') }}
                    </p>
                    <p>
                        {{ __('site.about_p3') }}
                    </p>
                </div>

                {{-- Action links --}}
                <div class="pt-6 border-t border-editorial-border flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
                    <a href="{{ route('contacts.index', ['locale' => $locale]) }}" 
                       class="inline-flex justify-center items-center px-8 py-4 bg-graphite-900 text-white text-xs uppercase tracking-widest font-medium hover:bg-terracotta transition-colors shadow-sm">
                        {{ __('site.about_cta') }}
                    </a>
                    <a href="{{ route('portfolio.index', ['locale' => $locale]) }}" 
                       class="inline-flex justify-center items-center px-6 py-4 border border-editorial-border bg-surface text-graphite-800 text-xs uppercase tracking-widest font-medium hover:border-graphite-900 transition-colors">
                        {{ __('site.hero_portfolio') }}
                    </a>
                </div>

            </div>

        </div>

    </div>
</div>
@endsection
