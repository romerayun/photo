@extends('layouts.app')

@section('title', $series->localizedTitle($locale) . ' — ' . __('site.author_name'))
@section('description', $series->localizedDescription($locale) ?: __('site.meta_description'))
@section('og_image', $series->cover_url)

@section('content')

@php
    $lightboxData = $series->photos->map(function ($photo) use ($locale) {
        return [
            'id' => $photo->id,
            'url' => $photo->url,
            'alt' => $photo->localizedAlt($locale) ?: $photo->series->localizedTitle($locale),
            'caption' => $photo->localizedCaption($locale),
            'width' => $photo->width,
            'height' => $photo->height,
        ];
    })->values();
@endphp

<article x-data="lightbox(@js($lightboxData))" 
         @keydown.escape.window="close()" 
         @keydown.arrow-right.window="if(isOpen) next()" 
         @keydown.arrow-left.window="if(isOpen) prev()"
         class="bg-arch-bg text-arch-text py-16 md:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Breadcrumb --}}
        <div class="mb-8">
            <a href="{{ route('portfolio.index') }}" 
               class="text-xs uppercase tracking-widest text-neutral-500 hover:text-crimson inline-flex items-center gap-1.5 transition-colors font-mono font-bold">
                <span>&larr;</span>
                <span>{{ __('site.back_to_portfolio') }}</span>
            </a>
        </div>

        {{-- Series Master Header --}}
        <div class="border-b border-arch-border pb-12 mb-16">
            <div class="max-w-4xl space-y-4">
                
                {{-- HUD Tags --}}
                <div class="flex flex-wrap items-center gap-3 text-xs uppercase tracking-widest text-neutral-500 font-mono">
                    @if($series->category)
                    <span class="text-crimson font-bold">{{ $series->category->localizedName($locale) }}</span>
                    <span>&bull;</span>
                    @endif

                    @if($series->localizedLocation($locale))
                    <span>{{ $series->localizedLocation($locale) }}</span>
                    <span>&bull;</span>
                    @endif

                    @if($series->shooting_date)
                    <span>{{ $series->shooting_date }}</span>
                    <span>&bull;</span>
                    @endif

                    <span>{{ $series->photos->count() }} кадров</span>

                    @if($series->is_demo)
                    <span class="bg-crimson/10 text-crimson px-2.5 py-0.5 text-[0.65rem] font-bold">
                        ДЕМО
                    </span>
                    @endif
                </div>

                <h1 class="text-4xl sm:text-6xl md:text-7xl font-extrabold uppercase tracking-tightest font-display text-arch-text">
                    {{ $series->localizedTitle($locale) }}
                </h1>

                @if($series->localizedDescription($locale))
                <p class="text-base sm:text-lg text-neutral-700 leading-relaxed max-w-2xl font-mono pt-2">
                    {{ $series->localizedDescription($locale) }}
                </p>
                @endif

            </div>
        </div>

        {{-- High-Fashion Photo Stream --}}
        <div class="space-y-12 md:space-y-16">
            @foreach($series->photos as $index => $photo)
                @php
                    $isWide = $photo->width && $photo->height && ($photo->width / $photo->height > 1.2);
                @endphp

                <figure class="relative group cursor-pointer {{ $isWide ? 'max-w-6xl mx-auto' : 'max-w-4xl mx-auto' }}" 
                        @click="open({{ $index }})"
                        role="button"
                        tabindex="0"
                        @keydown.enter="open({{ $index }})"
                        @keydown.space.prevent="open({{ $index }})"
                        aria-label="Открыть фото в увеличенном размере">
                    
                    <div class="overflow-hidden bg-white border border-arch-border shadow-card-depth relative">
                        <img src="{{ $photo->url }}" 
                             alt="{{ $photo->localizedAlt($locale) ?: $series->localizedTitle($locale) }}" 
                             loading="{{ $index < 2 ? 'eager' : 'lazy' }}"
                             width="{{ $photo->width ?: 1200 }}"
                             height="{{ $photo->height ?: 800 }}"
                             class="w-full h-auto object-cover transition-transform duration-700 ease-out group-hover:scale-[1.01]">
                        
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-end justify-end p-6 opacity-0 group-hover:opacity-100">
                            <span class="bg-crimson text-white text-xs px-4 py-2 font-mono uppercase tracking-wider font-bold">
                                Увеличить &plus;
                            </span>
                        </div>
                    </div>

                    @if($photo->localizedCaption($locale))
                    <figcaption class="mt-3 text-xs text-neutral-500 font-mono italic text-center">
                        {{ $photo->localizedCaption($locale) }}
                    </figcaption>
                    @endif
                </figure>
            @endforeach
        </div>

        {{-- Bottom CTA & Next Series --}}
        <div class="mt-24 pt-16 border-t border-arch-border">
            <div class="flex flex-col md:flex-row items-center justify-between gap-8 text-center md:text-left">
                
                <div class="space-y-2">
                    <span class="text-xs uppercase tracking-widest text-crimson font-mono font-bold block">
                        Понравилась эта серия?
                    </span>
                    <h3 class="text-2xl sm:text-3xl font-extrabold uppercase font-display text-arch-text">
                        {{ __('site.similar_cta') }}
                    </h3>
                    <p class="text-xs text-neutral-600 font-mono">
                        Напишите мне в Telegram или позвоните, чтобы обсудить детали и локации в Иркутске.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-4 shrink-0">
                    <a href="{{ route('contacts.index') }}" 
                       class="btn-crimson px-8 py-4 text-xs font-bold">
                        <span>{{ __('site.similar_cta') }}</span>
                        <span>&nearr;</span>
                    </a>

                    @if($nextSeries)
                    <a href="{{ route('series.show', ['slug' => $nextSeries->slug]) }}" 
                       class="px-7 py-4 bg-white border border-arch-border text-arch-text text-xs uppercase tracking-widest font-bold hover:border-black transition-colors inline-flex items-center gap-2 font-mono">
                        <span>{{ __('site.next_series') }}</span>
                        <span>&rarr;</span>
                    </a>
                    @endif
                </div>

            </div>
        </div>

    </div>

    {{-- Darkroom Lightbox Modal --}}
    <div x-show="isOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/95 backdrop-blur-2xl p-4 sm:p-8"
         role="dialog"
         aria-modal="true"
         aria-label="Просмотр фотографии">
        
        <button @click="close()" 
                type="button" 
                class="absolute top-6 right-6 text-white hover:text-crimson p-3 rounded-full bg-white/10 hover:bg-white/20 transition-colors z-50"
                aria-label="{{ __('site.lightbox_close') }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <button @click="prev()" 
                type="button" 
                class="absolute left-6 top-1/2 -translate-y-1/2 text-white hover:text-crimson p-3.5 rounded-full bg-white/10 hover:bg-white/20 transition-colors z-50"
                aria-label="{{ __('site.lightbox_prev') }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>

        <button @click="next()" 
                type="button" 
                class="absolute right-6 top-1/2 -translate-y-1/2 text-white hover:text-crimson p-3.5 rounded-full bg-white/10 hover:bg-white/20 transition-colors z-50"
                aria-label="{{ __('site.lightbox_next') }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>

        <div class="relative max-w-full max-h-[88vh] flex flex-col items-center justify-center select-none" @click.away="close()">
            <img :src="currentPhoto().url" 
                 :alt="currentPhoto().alt || ''" 
                 class="max-w-full max-h-[82vh] object-contain shadow-2xl transition-all duration-300">
            
            <div class="mt-4 flex items-center justify-between w-full text-xs text-white/80 font-mono px-4">
                <span x-text="currentPhoto().caption || currentPhoto().alt || ''"></span>
                <span class="bg-crimson px-3 py-1 font-bold text-white uppercase tracking-wider" x-text="(currentIndex + 1) + ' / ' + photos.length"></span>
            </div>
        </div>

    </div>

</article>
@endsection
