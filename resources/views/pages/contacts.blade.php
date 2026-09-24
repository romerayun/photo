@extends('layouts.app')

@section('title', __('site.nav_contacts') . ' — ' . __('site.author_name'))
@section('description', __('site.contacts_subtitle'))

@section('content')
<div class="py-12 md:py-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="mb-14 text-center md:text-left">
            <span class="text-xs uppercase tracking-widest text-terracotta font-medium block mb-2">
                {{ __('site.nav_contacts') }}
            </span>
            <h1 class="font-serif text-4xl sm:text-5xl text-graphite-950 editorial-heading mb-4">
                {{ __('site.contacts_title') }}
            </h1>
            <p class="text-base text-graphite-600 font-light max-w-2xl leading-relaxed">
                {{ __('site.contacts_subtitle') }}
            </p>
        </div>

        {{-- Contact Cards --}}
        <div class="space-y-6">

            {{-- 1. Telegram Card (Primary Channel) --}}
            @if($telegramUrl)
                <div class="bg-surface border border-editorial-border p-8 md:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6 hover:border-terracotta transition-colors">
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="text-xs uppercase tracking-widest bg-terracotta text-white px-2 py-0.5 font-medium">
                                Основной способ
                            </span>
                            <span class="text-xs font-mono text-graphite-400">TELEGRAM</span>
                        </div>
                        <h2 class="font-serif text-2xl sm:text-3xl text-graphite-950">
                            {{ $telegramHandle ?: 'Telegram' }}
                        </h2>
                        <p class="text-xs text-graphite-600 font-light">
                            {{ __('site.telegram_desc') }}
                        </p>
                    </div>

                    <a href="{{ $telegramUrl }}" 
                       target="_blank" 
                       rel="noopener" 
                       class="inline-flex justify-center items-center px-8 py-4 bg-graphite-900 text-white text-xs uppercase tracking-widest font-medium hover:bg-terracotta transition-colors shadow-sm shrink-0">
                        {{ __('site.telegram_btn') }} &rarr;
                    </a>
                </div>
            @endif

            {{-- 2. Phone Card --}}
            @if($phoneLink && $phoneDisplay)
                <div class="bg-canvas border border-editorial-border p-8 md:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6 hover:border-editorial-border/80 transition-colors">
                    <div class="space-y-2">
                        <span class="text-xs uppercase tracking-widest text-graphite-400 font-mono block">
                            {{ __('site.phone_title') }}
                        </span>
                        <h2 class="font-serif text-2xl sm:text-3xl text-graphite-950">
                            {{ $phoneDisplay }}
                        </h2>
                        <p class="text-xs text-graphite-600 font-light">
                            Для срочных звонков и вопросов по текущим проектам
                        </p>
                    </div>

                    <a href="{{ $phoneLink }}" 
                       class="inline-flex justify-center items-center px-8 py-4 border border-graphite-900 text-graphite-900 text-xs uppercase tracking-widest font-medium hover:bg-graphite-900 hover:text-white transition-colors shrink-0">
                        Позвонить
                    </a>
                </div>
            @endif

            {{-- 3. City & Location Card --}}
            <div class="bg-canvas border border-editorial-border p-8 md:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2">
                    <span class="text-xs uppercase tracking-widest text-graphite-400 font-mono block">
                        {{ __('site.city_title') }}
                    </span>
                    <h2 class="font-serif text-2xl sm:text-3xl text-graphite-950">
                        {{ $city }}
                    </h2>
                    <p class="text-xs text-graphite-600 font-light">
                        Возможны выездные съёмки по Байкалу и Иркутской области по предварительной договорённости.
                    </p>
                </div>
                <div class="text-xs font-mono text-graphite-500 uppercase tracking-wider shrink-0">
                    IRKUTSK &bull; TIMEZONE UTC+8
                </div>
            </div>

            {{-- Missing contacts banner for demo mode --}}
            @if(!$telegramUrl && !$phoneLink)
                <div class="p-8 border border-dashed border-terracotta/40 bg-terracotta/5 rounded-sm space-y-3">
                    <div class="flex items-center gap-2 text-terracotta font-medium text-sm">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Контакты ожидают заполнения автором</span>
                    </div>
                    <p class="text-xs text-graphite-600 leading-relaxed font-light">
                        Владелец сайта ещё не внёс контактные данные в панели управления. Для демонстрации работы сайта вы можете авторизоваться в административной панели и указать свой актуальный Telegram-аккаунт и номер телефона.
                    </p>
                    <div class="pt-2">
                        <a href="{{ route('admin.login') }}" class="text-xs uppercase tracking-widest text-terracotta font-semibold hover:underline">
                            Перейти к настройке контактов в панели управления &rarr;
                        </a>
                    </div>
                </div>
            @endif

        </div>

    </div>
</div>
@endsection
