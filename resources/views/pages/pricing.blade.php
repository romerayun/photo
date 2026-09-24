@extends('layouts.app')

@section('title', __('site.pricing_title') . ' — ' . __('site.author_name'))
@section('description', __('site.pricing_subtitle'))

@section('content')
<div class="py-14 md:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="max-w-3xl mb-14">
            <span class="text-xs uppercase tracking-widest text-apple-accent font-semibold font-mono block mb-2">
                {{ __('site.pricing_title') }}
            </span>
            <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tightest text-apple-text mb-4">
                {{ __('site.pricing_subtitle') }}
            </h1>
            <p class="text-base sm:text-lg text-apple-textMuted font-light max-w-2xl leading-relaxed">
                Форматы съёмок подбираются под вашу задачу: от камерного портрета для себя до многочасового репортажного сопровождения событий в Иркутске.
            </p>
        </div>

        {{-- 3 Packages in Apple Light Pro Cards --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-24">
            @foreach($packages as $pkg)
                <div class="bg-white rounded-3xl p-8 sm:p-10 flex flex-col justify-between border border-black/5 shadow-apple-card apple-card-hover relative {{ $loop->iteration === 2 ? 'ring-2 ring-apple-accent' : '' }}">
                    
                    @if($loop->iteration === 2)
                    <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 px-4 py-1 rounded-full bg-apple-accent text-white text-[0.65rem] uppercase tracking-widest font-bold shadow-md">
                        Рекомендуемый выбор
                    </div>
                    @endif

                    <div>
                        {{-- Top Header --}}
                        <div class="flex items-center justify-between pb-4 mb-4 border-b border-black/5">
                            <span class="font-mono text-xs text-neutral-400 uppercase tracking-widest">
                                TIER {{ sprintf('%02d', $loop->iteration) }}
                            </span>
                        </div>

                        <h2 class="text-3xl font-extrabold text-apple-text mb-2">
                            {{ $pkg->localizedTitle($locale) }}
                        </h2>

                        @if($pkg->localizedSubtitle($locale))
                        <p class="text-xs text-apple-textMuted font-light mb-6 leading-relaxed">
                            {{ $pkg->localizedSubtitle($locale) }}
                        </p>
                        @endif

                        {{-- Price Highlight Block --}}
                        <div class="p-6 rounded-2xl bg-apple-bg border border-black/5 mb-8">
                            <span class="text-xs uppercase tracking-widest text-neutral-400 font-mono block mb-1">
                                Стоимость
                            </span>
                            <span class="text-3xl sm:text-4xl font-extrabold text-apple-text block">
                                {{ $pkg->formattedPrice($locale) }}
                            </span>
                            @if(is_null($pkg->price))
                            <span class="text-[0.68rem] text-apple-accent font-mono uppercase tracking-widest font-semibold block mt-1">
                                Индивидуальный расчет
                            </span>
                            @endif
                        </div>

                        {{-- Key Parameters List --}}
                        <div class="space-y-3 mb-8 text-xs text-apple-text">
                            @if($pkg->localizedDuration($locale))
                            <div class="flex items-center justify-between py-1.5 border-b border-black/5">
                                <span class="text-apple-textMuted uppercase tracking-wider font-mono">{{ __('site.duration') }}</span>
                                <span class="font-bold text-apple-text">{{ $pkg->localizedDuration($locale) }}</span>
                            </div>
                            @endif

                            @if($pkg->localizedPhotoCount($locale))
                            <div class="flex items-center justify-between py-1.5 border-b border-black/5">
                                <span class="text-apple-textMuted uppercase tracking-wider font-mono">{{ __('site.photo_count') }}</span>
                                <span class="font-bold text-apple-text">{{ $pkg->localizedPhotoCount($locale) }}</span>
                            </div>
                            @endif

                            @if($pkg->localizedDeliveryTime($locale))
                            <div class="flex items-center justify-between py-1.5 border-b border-black/5">
                                <span class="text-apple-textMuted uppercase tracking-wider font-mono">{{ __('site.delivery_time') }}</span>
                                <span class="font-bold text-apple-text">{{ $pkg->localizedDeliveryTime($locale) }}</span>
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
                            <ul class="space-y-2.5 text-xs text-apple-text font-light">
                                @foreach($includesList as $item)
                                    <li class="flex items-start gap-2.5">
                                        <svg class="w-4 h-4 text-apple-accent shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        <div class="mb-8 p-4 rounded-xl bg-apple-bg border border-black/5 text-xs text-apple-textMuted font-light italic">
                            {{ $pkg->localizedExtraConditions($locale) }}
                        </div>
                        @endif
                    </div>

                    {{-- CTA --}}
                    <div class="pt-6">
                        <a href="{{ route('contacts.index', ['locale' => $locale]) }}" 
                           class="block w-full text-center py-3.5 {{ $loop->iteration === 2 ? 'bg-apple-text text-white hover:bg-black' : 'bg-apple-bg text-apple-text hover:bg-neutral-200 border border-black/5' }} text-xs uppercase tracking-widest font-bold rounded-full transition-all shadow-sm">
                            {{ __('site.book_package') }}
                        </a>
                    </div>

                </div>
            @endforeach
        </div>

        {{-- Apple-Style Accordion FAQ --}}
        <div class="max-w-3xl mx-auto pt-14 border-t border-black/5">
            <div class="mb-10 text-center">
                <span class="text-xs uppercase tracking-widest text-apple-accent font-semibold font-mono block mb-2">
                    FAQ
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-apple-text">
                    {{ __('site.faq_title') }}
                </h2>
            </div>

            @if($faqs->count() > 0)
                <div class="space-y-4" x-data="{ active: null }">
                    @foreach($faqs as $index => $faq)
                        <div class="bg-white rounded-2xl border border-black/5 shadow-sm overflow-hidden">
                            <button @click="active = (active === {{ $index }} ? null : {{ $index }})" 
                                    class="w-full py-5 px-6 text-left flex items-center justify-between focus:outline-none">
                                <span class="text-base font-semibold text-apple-text">
                                    {{ $faq->localizedQuestion($locale) }}
                                </span>
                                <span class="text-xl text-neutral-400 font-light ml-4" x-text="active === {{ $index }} ? '−' : '+'"></span>
                            </button>
                            <div x-show="active === {{ $index }}" x-cloak class="px-6 pb-6 text-sm text-apple-textMuted font-light leading-relaxed border-t border-black/5 pt-4">
                                {{ $faq->localizedAnswer($locale) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-8 rounded-3xl bg-white border border-black/5 shadow-sm text-center space-y-4">
                    <p class="text-sm text-apple-textMuted font-light">
                        {{ __('site.faq_empty') }}
                    </p>
                    <p class="text-xs text-neutral-400">
                        Если у вас есть вопросы по подготовке, локациям или таймингу — напишите мне напрямую в Telegram.
                    </p>
                    <div>
                        <a href="{{ route('contacts.index', ['locale' => $locale]) }}" 
                           class="inline-flex px-7 py-3 bg-apple-text text-white rounded-full text-xs uppercase tracking-widest font-bold hover:bg-black transition-colors shadow-sm">
                            Задать вопрос
                        </a>
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
