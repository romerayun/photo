@extends('layouts.app')

@section('title', __('site.pricing_title') . ' — ' . __('site.author_name'))
@section('description', __('site.pricing_subtitle'))

@section('content')
<div class="bg-arch-bg text-arch-text py-16 md:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="max-w-3xl mb-14">
            <span class="text-xs uppercase tracking-widest text-crimson font-mono font-bold block mb-2">
                04 / СТОИМОСТЬ СЪЁМКИ
            </span>
            <h1 class="text-4xl sm:text-6xl font-extrabold uppercase tracking-tightest font-display text-arch-text mb-4">
                ФОРМАТЫ И ЦЕНЫ
            </h1>
            <p class="text-base text-neutral-600 font-mono max-w-2xl leading-relaxed">
                Выберите короткую съёмку или более продолжительный формат. <br> Ниже — стоимость, что входит в каждый пакет и сроки готовности фотографий.
            </p>
        </div>

        {{-- Packages in Dynamic Editorial Grid --}}
        @php
            $packagesCount = $packages->count();
            $gridColsClass = match(true) {
                $packagesCount === 1 => 'max-w-xl mx-auto',
                $packagesCount === 2 => 'md:grid-cols-2 max-w-5xl mx-auto',
                default => 'md:grid-cols-2 lg:grid-cols-3',
            };
        @endphp

        @if($packagesCount > 0)
        <div class="grid grid-cols-1 {{ $gridColsClass }} gap-8 mb-24">
            @foreach($packages as $pkg)
                <div class="bg-white p-6 sm:p-8 flex flex-col justify-between border border-arch-border shadow-card-depth relative {{ ($packagesCount > 1 && $loop->iteration === 2) ? 'ring-2 ring-crimson' : '' }}">
                    
                    @if($packagesCount > 1 && $loop->iteration === 2)
                    <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 px-4 py-1 bg-crimson text-white text-[0.65rem] uppercase tracking-widest font-bold shadow-crimson-btn font-mono whitespace-nowrap">
                        Больше времени на съёмку
                    </div>
                    @endif

                    <div>
                        {{-- Top Header --}}
                        <div class="flex items-center justify-between pb-4 mb-6 border-b border-arch-border">
                            <span class="font-mono text-xs text-neutral-400 uppercase tracking-widest font-bold">
                                ФОРМАТ 0{{ $loop->iteration }}
                            </span>
                        </div>

                        <h2 class="text-xl sm:text-2xl font-extrabold uppercase font-display text-arch-text mb-2 leading-tight break-words">
                            {{ $pkg->localizedTitle($locale) }}
                        </h2>

                        @if($pkg->localizedSubtitle($locale))
                        <p class="text-xs text-neutral-600 font-mono mb-6 leading-relaxed">
                            {{ $pkg->localizedSubtitle($locale) }}
                        </p>
                        @endif

                        {{-- Price Highlight Block --}}
                        <div class="p-5 sm:p-6 bg-neutral-950 text-white mb-8 rounded-sm">
                            <span class="text-[0.65rem] uppercase tracking-widest text-neutral-400 font-mono block mb-1">
                                Стоимость
                            </span>
                            <span class="text-xl sm:text-2xl font-extrabold font-display block leading-snug break-words">
                                {{ $pkg->formattedPrice($locale) }}
                            </span>
                            @if(is_null($pkg->price))
                            <span class="text-[0.68rem] text-crimson font-mono uppercase tracking-widest font-bold block mt-1">
                                Индивидуальный расчет
                            </span>
                            @endif
                        </div>

                        {{-- Key Parameters with Premium Icons --}}
                        <div class="space-y-2.5 mb-8">
                            {{-- Duration / Time --}}
                            @if($pkg->localizedDuration($locale))
                            <div class="flex items-start gap-3.5 p-3.5 bg-arch-bg/80 border border-arch-border rounded-sm hover:border-black/30 transition-colors">
                                <span class="w-8 h-8 rounded-full bg-white border border-arch-border flex items-center justify-center text-crimson shrink-0 shadow-xs mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <span class="text-[0.65rem] uppercase tracking-wider text-neutral-400 font-mono font-medium block">
                                        {{ __('site.duration') }}
                                    </span>
                                    <span class="text-xs sm:text-sm font-bold text-arch-text font-mono block mt-0.5 leading-snug">
                                        {{ $pkg->localizedDuration($locale) }}
                                    </span>
                                </div>
                            </div>
                            @endif

                            {{-- Photo Count / Photos --}}
                            @if($pkg->localizedPhotoCount($locale))
                            <div class="flex items-start gap-3.5 p-3.5 bg-arch-bg/80 border border-arch-border rounded-sm hover:border-black/30 transition-colors">
                                <span class="w-8 h-8 rounded-full bg-white border border-arch-border flex items-center justify-center text-crimson shrink-0 shadow-xs mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                                        <circle cx="12" cy="13" r="4"></circle>
                                    </svg>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <span class="text-[0.65rem] uppercase tracking-wider text-neutral-400 font-mono font-medium block">
                                        {{ __('site.photo_count') }}
                                    </span>
                                    <span class="text-xs sm:text-sm font-bold text-arch-text font-mono block mt-0.5 leading-snug">
                                        {{ $pkg->localizedPhotoCount($locale) }}
                                    </span>
                                </div>
                            </div>
                            @endif

                            {{-- Delivery Time / Deadline --}}
                            @if($pkg->localizedDeliveryTime($locale))
                            <div class="flex items-start gap-3.5 p-3.5 bg-arch-bg/80 border border-arch-border rounded-sm hover:border-black/30 transition-colors">
                                <span class="w-8 h-8 rounded-full bg-white border border-arch-border flex items-center justify-center text-crimson shrink-0 shadow-xs mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <span class="text-[0.65rem] uppercase tracking-wider text-neutral-400 font-mono font-medium block">
                                        {{ __('site.delivery_time') }}
                                    </span>
                                    <span class="text-xs sm:text-sm font-bold text-arch-text font-mono block mt-0.5 leading-snug">
                                        {{ $pkg->localizedDeliveryTime($locale) }}
                                    </span>
                                </div>
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
                    <div class="pt-6 space-y-2">
                        <a href="{{ route('contacts.index', ['package' => $pkg->localizedTitle($locale)]) }}#feedback-form" 
                           class="btn-crimson w-full py-3.5 text-xs font-bold text-center block">
                            <span>{{ __('site.book_package') }}</span>
                            <span>&nearr;</span>
                        </a>

                        @if(\App\Models\Setting::hasTelegram())
                            <a href="{{ \App\Models\Setting::telegramUrl('Здравствуйте, Роман! Хочу обсудить съёмку по пакету «' . $pkg->localizedTitle($locale) . '».') }}" 
                               target="_blank" 
                               rel="noopener" 
                               class="w-full py-2.5 px-3 bg-neutral-900 hover:bg-neutral-800 text-neutral-300 hover:text-white border border-arch-border text-[0.68rem] font-mono uppercase tracking-wider text-center transition-colors flex items-center justify-center gap-1.5 font-bold">
                                <svg class="w-3.5 h-3.5 text-[#229ED9]" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.75-.55 2.92-1.27 4.86-2.11 5.83-2.52 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .38z"/>
                                </svg>
                                <span>Обсудить пакет в Telegram</span>
                                <span class="text-xs">&nearr;</span>
                            </a>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>
        @else
            <div class="p-12 bg-white border border-arch-border text-center space-y-4 shadow-sm mb-24 max-w-xl mx-auto">
                <p class="text-base text-neutral-700 font-mono">
                    Пакеты услуг сейчас обновляются.
                </p>
                <p class="text-xs text-neutral-500 font-mono">
                    Свяжитесь со мной напрямую, чтобы узнать актуальные условия и стоимость съёмки.
                </p>
                <div class="pt-2">
                    <a href="{{ route('contacts.index') }}" class="btn-crimson px-7 py-3 text-xs font-bold">
                        <span>Связаться</span>
                        <span>&nearr;</span>
                    </a>
                </div>
            </div>
        @endif

        {{-- Custom Conditions Card --}}
        <div class="max-w-5xl mx-auto mb-24">
            <div class="bg-neutral-950 text-white border border-neutral-800 p-8 sm:p-12 shadow-card-depth relative overflow-hidden">
                <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-crimson/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
                    {{-- Left column: heading + description + bullets --}}
                    <div class="lg:col-span-7 space-y-5">
                        <span class="text-xs uppercase tracking-widest text-crimson font-mono font-bold block">
                            ИНДИВИДУАЛЬНЫЙ ФОРМАТ
                        </span>
                        <h3 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold uppercase font-display tracking-tight text-white leading-tight">
                            ЕСТЬ СВОЯ ИДЕЯ?
                        </h3>
                        <p class="text-xs sm:text-sm text-neutral-400 font-mono leading-relaxed">
                            Хотите попробовать необычный образ, выбрать особенное место или провести съёмку за городом? Расскажите, что задумали. Обсудим возможность съёмки, подготовку и подходящий формат.
                        </p>

                        <ul class="space-y-2.5 pt-1">
                            <li class="flex items-start gap-2.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-crimson mt-1.5 shrink-0"></span>
                                <span class="text-xs font-mono text-neutral-300">Творческая идея или необычный образ</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-crimson mt-1.5 shrink-0"></span>
                                <span class="text-xs font-mono text-neutral-300">Несколько локаций</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-crimson mt-1.5 shrink-0"></span>
                                <span class="text-xs font-mono text-neutral-300">Более продолжительная съёмка</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-crimson mt-1.5 shrink-0"></span>
                                <span class="text-xs font-mono text-neutral-300">Выезд за пределы Иркутска</span>
                            </li>
                        </ul>
                    </div>

                    {{-- Right column: pricing info + CTA --}}
                    <div class="lg:col-span-5 flex flex-col gap-4 bg-neutral-900/80 p-6 sm:p-7 border border-white/10 rounded-sm">
                        <span class="text-[0.68rem] uppercase font-mono text-neutral-400 tracking-wider font-bold">
                            СТОИМОСТЬ ПОД ВАШУ ЗАДАЧУ
                        </span>
                        <p class="text-xs text-neutral-400 font-mono leading-relaxed">
                            Пришлите описание идеи, примеры фотографий и желаемую дату. После обсуждения деталей согласуем стоимость — до бронирования.
                        </p>
                        <div class="pt-1 space-y-2.5">
                            @if(\App\Models\Setting::hasTelegram())
                                <a href="{{ \App\Models\Setting::telegramUrl('Здравствуйте, Роман! Хочу обсудить свою идею для съёмки.') }}"
                                   target="_blank"
                                   rel="noopener"
                                   class="btn-crimson w-full py-3.5 text-xs font-bold text-center block">
                                    <span>ОБСУДИТЬ ИДЕЮ В TELEGRAM</span>
                                    <span>&rarr;</span>
                                </a>
                            @endif
                            <a href="{{ route('contacts.index', ['package' => 'Своя идея']) }}#feedback-form"
                               class="w-full py-2.5 px-3 bg-white/5 hover:bg-white/10 text-neutral-200 hover:text-white border border-white/10 text-[0.68rem] font-mono uppercase tracking-wider text-center transition-colors flex items-center justify-center gap-1.5 font-bold">
                                <span>Написать через сайт</span>
                                <span class="text-xs">&nearr;</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
