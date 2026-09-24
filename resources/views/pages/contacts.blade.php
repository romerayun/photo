@extends('layouts.app')

@section('title', __('site.nav_contacts') . ' — ' . __('site.author_name'))
@section('description', __('site.contacts_subtitle'))

@section('content')
<div class="py-16 md:py-24">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="mb-14 text-center md:text-left">
            <span class="text-xs uppercase tracking-widest text-apple-blue font-semibold font-mono block mb-2">
                {{ __('site.nav_contacts') }}
            </span>
            <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tightest apple-text-gradient mb-4">
                {{ __('site.contacts_title') }}
            </h1>
            <p class="text-base sm:text-lg text-neutral-400 font-light max-w-2xl leading-relaxed">
                {{ __('site.contacts_subtitle') }}
            </p>
        </div>

        {{-- Contact Cards --}}
        <div class="space-y-6">

            {{-- 1. Telegram Card (Primary Channel) --}}
            @if($telegramUrl)
                <div class="apple-card-glow rounded-3xl p-8 sm:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="text-[0.65rem] uppercase tracking-widest bg-apple-blue text-white px-3 py-1 rounded-full font-bold">
                                Основной канал
                            </span>
                            <span class="text-xs font-mono text-neutral-400">FAST RESPONSE</span>
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-bold text-white">
                            {{ $telegramHandle ?: 'Telegram' }}
                        </h2>
                        <p class="text-xs text-neutral-400 font-light">
                            {{ __('site.telegram_desc') }}
                        </p>
                    </div>

                    <a href="{{ $telegramUrl }}" 
                       target="_blank" 
                       rel="noopener" 
                       class="inline-flex justify-center items-center px-8 py-4 bg-white text-black hover:bg-neutral-200 text-xs uppercase tracking-widest font-bold rounded-full transition-all shadow-[0_0_25px_rgba(255,255,255,0.25)] shrink-0">
                        {{ __('site.telegram_btn') }} &rarr;
                    </a>
                </div>
            @endif

            {{-- 2. Phone Card --}}
            @if($phoneLink && $phoneDisplay)
                <div class="apple-card-glow rounded-3xl p-8 sm:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="space-y-2">
                        <span class="text-xs uppercase tracking-widest text-neutral-400 font-mono block">
                            {{ __('site.phone_title') }}
                        </span>
                        <h2 class="text-2xl sm:text-3xl font-bold text-white">
                            {{ $phoneDisplay }}
                        </h2>
                        <p class="text-xs text-neutral-400 font-light">
                            Для звонков и срочных вопросов по текущим съёмкам
                        </p>
                    </div>

                    <a href="{{ $phoneLink }}" 
                       class="inline-flex justify-center items-center px-8 py-4 bg-white/10 hover:bg-white/20 text-white text-xs uppercase tracking-widest font-bold rounded-full border border-white/15 transition-all shrink-0">
                        Позвонить
                    </a>
                </div>
            @endif

            {{-- 3. City & Location Card --}}
            <div class="apple-card-glow rounded-3xl p-8 sm:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2">
                    <span class="text-xs uppercase tracking-widest text-neutral-400 font-mono block">
                        {{ __('site.city_title') }}
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-bold text-white">
                        {{ $city }}
                    </h2>
                    <p class="text-xs text-neutral-400 font-light">
                        Локации в Иркутске, на побережье Байкала и выездные проекты по области.
                    </p>
                </div>
                <div class="text-xs font-mono text-apple-blue uppercase tracking-widest px-4 py-2 rounded-full bg-apple-blue/10 border border-apple-blue/20 shrink-0 font-semibold">
                    IRKUTSK &bull; UTC+8
                </div>
            </div>

            {{-- Missing contacts prompt for demo mode --}}
            @if(!$telegramUrl && !$phoneLink)
                <div class="p-8 rounded-3xl border border-dashed border-apple-blue/40 bg-apple-blue/5 space-y-3">
                    <div class="flex items-center gap-2 text-apple-blue font-semibold text-sm">
                        <span class="w-2.5 h-2.5 rounded-full bg-apple-blue animate-pulse"></span>
                        <span>Контакты ожидают настройки автором</span>
                    </div>
                    <p class="text-xs text-neutral-400 leading-relaxed font-light">
                        Владелец сайта ещё не внёс номер телефона и Telegram в панели управления. Для проверки работы сайта авторизуйтесь в административной панели и укажите свой контактный Telegram и телефон.
                    </p>
                    <div class="pt-2">
                        <a href="{{ route('admin.login') }}" class="text-xs uppercase tracking-widest text-apple-blue font-bold hover:underline">
                            Перейти к настройке контактов в панели управления &rarr;
                        </a>
                    </div>
                </div>
            @endif

        </div>

    </div>
</div>
@endsection
