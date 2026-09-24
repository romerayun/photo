@extends('layouts.app')

@section('title', __('site.nav_about') . ' — ' . __('site.author_name'))
@section('description', __('site.about_subtitle') . '. ' . __('site.about_p1'))

@section('content')
<div class="py-14 md:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-start">
            
            {{-- Left: Portrait Showcase --}}
            <div class="lg:col-span-5 sticky top-28">
                <div class="rounded-3xl overflow-hidden border border-black/5 shadow-apple-card bg-white relative group aspect-[3/4]">
                    <img src="{{ asset('storage/demo/portrait-2.jpg') }}" 
                         alt="{{ __('site.author_name') }} — {{ __('site.author_role') }}" 
                         loading="eager"
                         width="800"
                         height="1067"
                         class="w-full h-full object-cover">
                    
                    <div class="absolute bottom-6 left-6 right-6 apple-glass rounded-2xl p-4 shadow-sm">
                        <span class="text-base font-bold text-apple-text block">{{ __('site.author_name') }}</span>
                        <span class="text-xs uppercase tracking-widest text-apple-accent font-mono font-semibold">{{ __('site.location') }} &bull; SIBERIA</span>
                    </div>
                </div>
            </div>

            {{-- Right: Narrative --}}
            <div class="lg:col-span-7 space-y-8 lg:pt-4">
                
                <div>
                    <span class="text-xs uppercase tracking-widest text-apple-accent font-semibold font-mono block mb-2">
                        {{ __('site.nav_about') }}
                    </span>
                    <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tightest text-apple-text mb-3">
                        {{ __('site.about_title') }}
                    </h1>
                    <p class="text-xl sm:text-2xl text-apple-textMuted font-light">
                        {{ __('site.about_subtitle') }}
                    </p>
                </div>

                <div class="space-y-6 text-base sm:text-lg text-apple-text/80 font-light leading-relaxed border-t border-black/5 pt-8">
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
                <div class="pt-6 border-t border-black/5 flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
                    <a href="{{ route('contacts.index', ['locale' => $locale]) }}" 
                       class="inline-flex justify-center items-center px-8 py-4 bg-apple-text text-white hover:bg-black text-xs uppercase tracking-widest font-bold rounded-full transition-all shadow-md">
                        {{ __('site.about_cta') }}
                    </a>
                    <a href="{{ route('portfolio.index', ['locale' => $locale]) }}" 
                       class="inline-flex justify-center items-center px-7 py-4 bg-white hover:bg-neutral-50 text-apple-text text-xs uppercase tracking-widest font-semibold rounded-full border border-black/10 shadow-sm transition-all">
                        {{ __('site.hero_portfolio') }}
                    </a>
                </div>

            </div>

        </div>

    </div>
</div>
@endsection
