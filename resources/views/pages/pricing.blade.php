@extends('layouts.app')

@section('title', __('site.pricing_title') . ' — ' . __('site.author_name'))
@section('description', __('site.pricing_subtitle'))

@section('content')
<div class="py-12 md:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="max-w-3xl mb-16">
            <span class="text-xs uppercase tracking-widest text-terracotta font-medium block mb-2">
                {{ __('site.pricing_title') }}
            </span>
            <h1 class="font-serif text-4xl sm:text-5xl text-graphite-950 editorial-heading mb-4">
                {{ __('site.pricing_subtitle') }}
            </h1>
            <p class="text-sm sm:text-base text-graphite-600 font-light max-w-2xl leading-relaxed">
                Форматы съёмок подбираются под вашу задачу: от камерного портрета для себя до многочасового репортажного сопровождения событий в Иркутске.
            </p>
        </div>

        {{-- 3 Packages in Editorial Format (not SaaS!) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-24">
            @foreach($packages as $pkg)
                <div class="bg-canvas border border-editorial-border p-8 md:p-10 flex flex-col justify-between hover:border-terracotta/70 transition-all duration-300 relative group">
                    
                    <div>
                        {{-- Top Number & Title --}}
                        <div class="flex items-baseline justify-between border-b border-editorial-border pb-4 mb-6">
                            <span class="font-mono text-xs text-graphite-400">
                                FORMAT / {{ sprintf('%02d', $loop->iteration) }}
                            </span>
                            @if($loop->iteration === 2)
                            <span class="text-[0.65rem] uppercase tracking-widest bg-terracotta/10 text-terracotta px-2.5 py-0.5 rounded-full font-medium">
                                Популярный выбор
                            </span>
                            @endif
                        </div>

                        <h2 class="font-serif text-3xl text-graphite-950 mb-3">
                            {{ $pkg->localizedTitle($locale) }}
                        </h2>

                        @if($pkg->localizedSubtitle($locale))
                        <p class="text-xs text-graphite-600 font-light mb-6 leading-relaxed">
                            {{ $pkg->localizedSubtitle($locale) }}
                        </p>
                        @endif

                        {{-- Price Block --}}
                        <div class="bg-surface p-5 border border-editorial-border mb-8">
                            <span class="text-xs uppercase tracking-widest text-graphite-400 block mb-1">
                                Стоимость
                            </span>
                            <span class="font-serif text-3xl text-terracotta block font-normal">
                                {{ $pkg->formattedPrice($locale) }}
                            </span>
                            @if(is_null($pkg->price))
                            <span class="text-[0.68rem] text-graphite-500 italic block mt-1">
                                Окончательная стоимость согласовывается под вашу задачу
                            </span>
                            @endif
                        </div>

                        {{-- Key Parameters --}}
                        <div class="space-y-3 mb-8 text-xs">
                            @if($pkg->localizedDuration($locale))
                            <div class="flex items-baseline justify-between pb-2 border-b border-editorial-line">
                                <span class="text-graphite-500 uppercase tracking-wider">{{ __('site.duration') }}</span>
                                <span class="font-medium text-graphite-900 text-right">{{ $pkg->localizedDuration($locale) }}</span>
                            </div>
                            @endif

                            @if($pkg->localizedPhotoCount($locale))
                            <div class="flex items-baseline justify-between pb-2 border-b border-editorial-line">
                                <span class="text-graphite-500 uppercase tracking-wider">{{ __('site.photo_count') }}</span>
                                <span class="font-medium text-graphite-900 text-right">{{ $pkg->localizedPhotoCount($locale) }}</span>
                            </div>
                            @endif

                            @if($pkg->localizedDeliveryTime($locale))
                            <div class="flex items-baseline justify-between pb-2 border-b border-editorial-line">
                                <span class="text-graphite-500 uppercase tracking-wider">{{ __('site.delivery_time') }}</span>
                                <span class="font-medium text-graphite-900 text-right">{{ $pkg->localizedDeliveryTime($locale) }}</span>
                            </div>
                            @endif
                        </div>

                        {{-- What is included --}}
                        @php
                            $includesList = $pkg->getIncludesList($locale);
                        @endphp
                        @if(count($includesList) > 0)
                        <div class="mb-8">
                            <span class="text-xs uppercase tracking-widest text-graphite-400 block mb-3 font-medium">
                                {{ __('site.includes') }}:
                            </span>
                            <ul class="space-y-2 text-xs text-graphite-700 font-light">
                                @foreach($includesList as $item)
                                    <li class="flex items-start gap-2">
                                        <span class="text-terracotta mt-0.5">&bull;</span>
                                        <span>{{ $item }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        {{-- Extra conditions if present --}}
                        @if($pkg->localizedExtraConditions($locale))
                        <div class="mb-8 p-3 bg-surface text-xs text-graphite-600 font-light italic">
                            {{ $pkg->localizedExtraConditions($locale) }}
                        </div>
                        @endif
                    </div>

                    {{-- CTA --}}
                    <div class="pt-6">
                        <a href="{{ route('contacts.index', ['locale' => $locale]) }}" 
                           class="block w-full text-center py-3.5 bg-graphite-900 text-white text-xs uppercase tracking-widest font-medium hover:bg-terracotta transition-colors shadow-sm">
                            {{ __('site.book_package') }}
                        </a>
                    </div>

                </div>
            @endforeach
        </div>

        {{-- Compact FAQ Section --}}
        <div class="max-w-3xl mx-auto pt-12 border-t border-editorial-border">
            <div class="mb-10 text-center">
                <span class="text-xs uppercase tracking-widest text-terracotta font-medium block mb-2">
                    FAQ
                </span>
                <h2 class="font-serif text-3xl text-graphite-950">
                    {{ __('site.faq_title') }}
                </h2>
            </div>

            @if($faqs->count() > 0)
                <div class="space-y-4" x-data="{ active: null }">
                    @foreach($faqs as $index => $faq)
                        <div class="border border-editorial-border bg-surface">
                            <button @click="active = (active === {{ $index }} ? null : {{ $index }})" 
                                    class="w-full py-4 px-6 text-left flex items-center justify-between focus:outline-none focus-visible:ring-2 focus-visible:ring-terracotta">
                                <span class="font-serif text-lg text-graphite-950 font-normal">
                                    {{ $faq->localizedQuestion($locale) }}
                                </span>
                                <span class="text-xl text-graphite-500 font-light ml-4" x-text="active === {{ $index }} ? '−' : '+'"></span>
                            </button>
                            <div x-show="active === {{ $index }}" x-cloak class="px-6 pb-5 text-sm text-graphite-600 font-light leading-relaxed border-t border-editorial-border/50 pt-3">
                                {{ $faq->localizedAnswer($locale) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                {{-- Clean explanation that FAQ terms are drafts and pending owner completion --}}
                <div class="p-8 bg-surface border border-editorial-border text-center space-y-4">
                    <p class="text-sm text-graphite-600 font-light">
                        {{ __('site.faq_empty') }}
                    </p>
                    <p class="text-xs text-graphite-500">
                        Если у вас есть вопросы по подготовке, локациям или таймингу — напишите мне напрямую.
                    </p>
                    <div>
                        <a href="{{ route('contacts.index', ['locale' => $locale]) }}" 
                           class="inline-flex px-6 py-2.5 bg-graphite-900 text-white text-xs uppercase tracking-widest font-medium hover:bg-terracotta transition-colors">
                            Задать вопрос
                        </a>
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
