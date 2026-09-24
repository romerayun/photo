@extends('layouts.app')

@section('title', __('site.pricing_title') . ' — ' . __('site.author_name'))
@section('description', __('site.pricing_subtitle'))

@section('content')
<div class="py-16 md:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="max-w-3xl mb-16">
            <span class="text-xs uppercase tracking-widest text-apple-blue font-semibold font-mono block mb-2">
                {{ __('site.pricing_title') }}
            </span>
            <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tightest apple-text-gradient mb-4">
                {{ __('site.pricing_subtitle') }}
            </h1>
            <p class="text-base sm:text-lg text-neutral-400 font-light max-w-2xl leading-relaxed">
                Форматы съёмок подбираются под вашу задачу: от камерного портрета для себя до многочасового репортажного сопровождения событий в Иркутске.
            </p>
        </div>

        {{-- 3 Packages in Apple Pro Titanium Tier Cards --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-28">
            @foreach($packages as $pkg)
                <div class="apple-card-glow rounded-3xl p-8 sm:p-10 flex flex-col justify-between relative {{ $loop->iteration === 2 ? 'border-apple-blue/50 ring-1 ring-apple-blue/50' : '' }}">
                    
                    @if($loop->iteration === 2)
                    <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 px-4 py-1 rounded-full bg-apple-blue text-white text-[0.65rem] uppercase tracking-widest font-bold shadow-md">
                        Рекомендуемый выбор
                    </div>
                    @endif

                    <div>
                        {{-- Top Header --}}
                        <div class="flex items-center justify-between pb-4 mb-4 border-b border-white/10">
                            <span class="font-mono text-xs text-neutral-400 uppercase tracking-widest">
                                TIER {{ sprintf('%02d', $loop->iteration) }}
                            </span>
                        </div>

                        <h2 class="text-3xl font-extrabold text-white mb-2">
                            {{ $pkg->localizedTitle($locale) }}
                        </h2>

                        @if($pkg->localizedSubtitle($locale))
                        <p class="text-xs text-neutral-400 font-light mb-6 leading-relaxed">
                            {{ $pkg->localizedSubtitle($locale) }}
                        </p>
                        @endif

                        {{-- Price Highlight Block --}}
                        <div class="p-6 rounded-2xl bg-neutral-900/90 border border-white/5 mb-8">
                            <span class="text-xs uppercase tracking-widest text-neutral-500 font-mono block mb-1">
                                Стоимость
                            </span>
                            <span class="text-3xl sm:text-4xl font-extrabold text-white block">
                                {{ $pkg->formattedPrice($locale) }}
                            </span>
                            @if(is_null($pkg->price))
                            <span class="text-[0.68rem] text-apple-blue font-mono uppercase tracking-widest block mt-1">
                                Индивидуальный расчет
                            </span>
                            @endif
                        </div>

                        {{-- Key Parameters List --}}
                        <div class="space-y-3 mb-8 text-xs text-neutral-300">
                            @if($pkg->localizedDuration($locale))
                            <div class="flex items-center justify-between py-1.5 border-b border-white/5">
                                <span class="text-neutral-500 uppercase tracking-wider font-mono">{{ __('site.duration') }}</span>
                                <span class="font-semibold text-white">{{ $pkg->localizedDuration($locale) }}</span>
                            </div>
                            @endif

                            @if($pkg->localizedPhotoCount($locale))
                            <div class="flex items-center justify-between py-1.5 border-b border-white/5">
                                <span class="text-neutral-500 uppercase tracking-wider font-mono">{{ __('site.photo_count') }}</span>
                                <span class="font-semibold text-white">{{ $pkg->localizedPhotoCount($locale) }}</span>
                            </div>
                            @endif

                            @if($pkg->localizedDeliveryTime($locale))
                            <div class="flex items-center justify-between py-1.5 border-b border-white/5">
                                <span class="text-neutral-500 uppercase tracking-wider font-mono">{{ __('site.delivery_time') }}</span>
                                <span class="font-semibold text-white">{{ $pkg->localizedDeliveryTime($locale) }}</span>
                            </div>
                            @endif
                        </div>

                        {{-- What is included --}}
                        @php
                            $includesList = $pkg->getIncludesList($locale);
                        @endphp
                        @if(count($includesList) > 0)
                        <div class="mb-8">
                            <span class="text-xs uppercase tracking-widest text-neutral-400 font-semibold font-mono block mb-3">
                                {{ __('site.includes') }}:
                            </span>
                            <ul class="space-y-2.5 text-xs text-neutral-300 font-light">
                                @foreach($includesList as $item)
                                    <li class="flex items-start gap-2.5">
                                        <svg class="w-4 h-4 text-apple-blue shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span>{{ $item }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        {{-- Extra conditions --}}
                        @if($pkg->localizedExtraConditions($locale))
                        <div class="mb-8 p-4 rounded-xl bg-white/5 text-xs text-neutral-400 font-light italic">
                            {{ $pkg->localizedExtraConditions($locale) }}
                        </div>
                        @endif
                    </div>

                    {{-- CTA --}}
                    <div class="pt-6">
                        <a href="{{ route('contacts.index', ['locale' => $locale]) }}" 
                           class="block w-full text-center py-3.5 {{ $loop->iteration === 2 ? 'bg-white text-black hover:bg-neutral-200' : 'bg-white/10 text-white hover:bg-white/20 border border-white/15' }} text-xs uppercase tracking-widest font-bold rounded-full transition-all">
                            {{ __('site.book_package') }}
                        </a>
                    </div>

                </div>
            @endforeach
        </div>

        {{-- Apple-Style FAQ Accordion --}}
        <div class="max-w-3xl mx-auto pt-16 border-t border-white/10">
            <div class="mb-12 text-center">
                <span class="text-xs uppercase tracking-widest text-apple-blue font-semibold font-mono block mb-2">
                    FAQ
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-white">
                    {{ __('site.faq_title') }}
                </h2>
            </div>

            @if($faqs->count() > 0)
                <div class="space-y-4" x-data="{ active: null }">
                    @foreach($faqs as $index => $faq)
                        <div class="apple-card-glow rounded-2xl overflow-hidden">
                            <button @click="active = (active === {{ $index }} ? null : {{ $index }})" 
                                    class="w-full py-5 px-6 text-left flex items-center justify-between focus:outline-none">
                                <span class="text-base font-semibold text-white">
                                    {{ $faq->localizedQuestion($locale) }}
                                </span>
                                <span class="text-xl text-neutral-400 font-light ml-4" x-text="active === {{ $index }} ? '−' : '+'"></span>
                            </button>
                            <div x-show="active === {{ $index }}" x-cloak class="px-6 pb-6 text-sm text-neutral-400 font-light leading-relaxed border-t border-white/5 pt-4">
                                {{ $faq->localizedAnswer($locale) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-8 rounded-3xl apple-card-glow text-center space-y-4">
                    <p class="text-sm text-neutral-300 font-light">
                        {{ __('site.faq_empty') }}
                    </p>
                    <p class="text-xs text-neutral-400">
                        Если у вас есть вопросы по подготовке, локациям или таймингу — напишите мне напрямую в Telegram.
                    </p>
                    <div>
                        <a href="{{ route('contacts.index', ['locale' => $locale]) }}" 
                           class="inline-flex px-7 py-3 bg-white text-black rounded-full text-xs uppercase tracking-widest font-bold hover:bg-neutral-200 transition-colors">
                            Задать вопрос
                        </a>
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
