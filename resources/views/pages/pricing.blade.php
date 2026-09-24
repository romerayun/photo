@extends('layouts.app')

@section('title', __('site.pricing_title') . ' — ' . __('site.author_name'))
@section('description', __('site.pricing_subtitle'))

@section('content')
<div class="bg-arch-bg text-arch-text py-16 md:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="max-w-3xl mb-14">
            <span class="text-xs uppercase tracking-widest text-crimson font-mono font-bold block mb-2">
                04 / СТОИМОСТЬ И ПАКЕТЫ
            </span>
            <h1 class="text-4xl sm:text-6xl font-extrabold uppercase tracking-tightest font-display text-arch-text mb-4">
                {{ __('site.pricing_subtitle') }}
            </h1>
            <p class="text-base text-neutral-600 font-mono max-w-2xl leading-relaxed">
                Форматы съёмок подбираются под вашу задачу: от камерного портрета для себя до многочасового репортажного сопровождения событий в Иркутске.
            </p>
        </div>

        {{-- 3 Packages in Editorial Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-24">
            @foreach($packages as $pkg)
                <div class="bg-white p-8 sm:p-10 flex flex-col justify-between border border-arch-border shadow-card-depth relative {{ $loop->iteration === 2 ? 'ring-2 ring-crimson' : '' }}">
                    
                    @if($loop->iteration === 2)
                    <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 px-4 py-1 bg-crimson text-white text-[0.65rem] uppercase tracking-widest font-bold shadow-crimson-btn font-mono">
                        Популярный выбор
                    </div>
                    @endif

                    <div>
                        {{-- Top Header --}}
                        <div class="flex items-center justify-between pb-4 mb-6 border-b border-arch-border">
                            <span class="font-mono text-xs text-neutral-400 uppercase tracking-widest font-bold">
                                ФОРМАТ 0{{ $loop->iteration }}
                            </span>
                        </div>

                        <h2 class="text-3xl font-extrabold uppercase font-display text-arch-text mb-2">
                            {{ $pkg->localizedTitle($locale) }}
                        </h2>

                        @if($pkg->localizedSubtitle($locale))
                        <p class="text-xs text-neutral-600 font-mono mb-6 leading-relaxed">
                            {{ $pkg->localizedSubtitle($locale) }}
                        </p>
                        @endif

                        {{-- Price Highlight Block --}}
                        <div class="p-6 bg-neutral-950 text-white mb-8 rounded-sm">
                            <span class="text-[0.65rem] uppercase tracking-widest text-neutral-400 font-mono block mb-1">
                                Стоимость
                            </span>
                            <span class="text-3xl font-extrabold font-display block">
                                {{ $pkg->formattedPrice($locale) }}
                            </span>
                            @if(is_null($pkg->price))
                            <span class="text-[0.68rem] text-crimson font-mono uppercase tracking-widest font-bold block mt-1">
                                Индивидуальный расчет
                            </span>
                            @endif
                        </div>

                        {{-- Key Parameters List --}}
                        <div class="space-y-3 mb-8 text-xs font-mono text-arch-text">
                            @if($pkg->localizedDuration($locale))
                            <div class="flex items-center justify-between py-1.5 border-b border-arch-border">
                                <span class="text-neutral-500 uppercase tracking-wider">{{ __('site.duration') }}</span>
                                <span class="font-bold text-arch-text">{{ $pkg->localizedDuration($locale) }}</span>
                            </div>
                            @endif

                            @if($pkg->localizedPhotoCount($locale))
                            <div class="flex items-center justify-between py-1.5 border-b border-arch-border">
                                <span class="text-neutral-500 uppercase tracking-wider">{{ __('site.photo_count') }}</span>
                                <span class="font-bold text-arch-text">{{ $pkg->localizedPhotoCount($locale) }}</span>
                            </div>
                            @endif

                            @if($pkg->localizedDeliveryTime($locale))
                            <div class="flex items-center justify-between py-1.5 border-b border-arch-border">
                                <span class="text-neutral-500 uppercase tracking-wider">{{ __('site.delivery_time') }}</span>
                                <span class="font-bold text-arch-text">{{ $pkg->localizedDeliveryTime($locale) }}</span>
                            </div>
                            @endif
                        </div>

                        {{-- What is included --}}
                        @php
                            $includesList = $pkg->getIncludesList($locale);
                        @endphp
                        @if(count($includesList) > 0)
                        <div class="mb-8">
                            <span class="text-xs uppercase tracking-widest text-neutral-400 font-bold font-mono block mb-3">
                                {{ __('site.includes') }}:
                            </span>
                            <ul class="space-y-2 text-xs font-mono text-neutral-700">
                                @foreach($includesList as $item)
                                    <li class="flex items-start gap-2">
                                        <span class="text-crimson font-bold">&bull;</span>
                                        <span>{{ $item }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        {{-- Extra conditions --}}
                        @if($pkg->localizedExtraConditions($locale))
                        <div class="mb-8 p-4 bg-arch-bg border border-arch-border text-xs text-neutral-600 font-mono italic">
                            {{ $pkg->localizedExtraConditions($locale) }}
                        </div>
                        @endif
                    </div>

                    {{-- CTA --}}
                    <div class="pt-6">
                        <a href="{{ route('contacts.index') }}" 
                           class="btn-crimson w-full py-4 text-xs font-bold">
                            <span>{{ __('site.book_package') }}</span>
                            <span>&nearr;</span>
                        </a>
                    </div>

                </div>
            @endforeach
        </div>

        {{-- FAQ Accordion --}}
        <div class="max-w-3xl mx-auto pt-14 border-t border-arch-border">
            <div class="mb-10 text-center">
                <span class="text-xs uppercase tracking-widest text-crimson font-mono font-bold block mb-2">
                    05 / ЧАСТЫЕ ВОПРОСЫ
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold uppercase tracking-tight font-display text-arch-text">
                    {{ __('site.faq_title') }}
                </h2>
            </div>

            @if($faqs->count() > 0)
                <div class="space-y-4" x-data="{ active: null }">
                    @foreach($faqs as $index => $faq)
                        <div class="bg-white border border-arch-border shadow-sm">
                            <button @click="active = (active === {{ $index }} ? null : {{ $index }})" 
                                    class="w-full py-5 px-6 text-left flex items-center justify-between focus:outline-none">
                                <span class="text-base font-bold text-arch-text font-display uppercase tracking-tight">
                                    {{ $faq->localizedQuestion($locale) }}
                                </span>
                                <span class="text-xl text-crimson font-bold ml-4" x-text="active === {{ $index }} ? '−' : '+'"></span>
                            </button>
                            <div x-show="active === {{ $index }}" x-cloak class="px-6 pb-6 text-sm text-neutral-600 font-mono leading-relaxed border-t border-arch-border pt-4">
                                {{ $faq->localizedAnswer($locale) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-8 bg-white border border-arch-border text-center space-y-4 shadow-sm">
                    <p class="text-sm text-neutral-600 font-mono">
                        {{ __('site.faq_empty') }}
                    </p>
                    <p class="text-xs text-neutral-500 font-mono">
                        Если у вас есть вопросы по подготовке, локациям или таймингу — напишите мне напрямую в Telegram.
                    </p>
                    <div>
                        <a href="{{ route('contacts.index') }}" 
                           class="btn-crimson px-7 py-3 text-xs font-bold">
                            <span>Задать вопрос</span>
                            <span>&nearr;</span>
                        </a>
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
