@extends('layouts.app')

@section('title', __('site.nav_contacts') . ' — ' . __('site.author_name'))
@section('description', __('site.contacts_subtitle'))

@section('content')
<div class="bg-arch-bg text-arch-text py-16 md:py-24">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="mb-14 text-center md:text-left">
            <span class="text-xs uppercase tracking-widest text-crimson font-mono font-bold block mb-2">
                06 / CONTACTS
            </span>
            <h1 class="text-4xl sm:text-6xl font-extrabold uppercase tracking-tightest font-display text-arch-text mb-4">
                {{ __('site.contacts_title') }}
            </h1>
            <p class="text-base text-neutral-600 font-mono max-w-2xl leading-relaxed">
                {{ __('site.contacts_subtitle') }}
            </p>
        </div>

        {{-- Contact Cards --}}
        <div class="space-y-6">

            {{-- 1. Telegram Card --}}
            @if($telegramUrl)
                <div class="bg-white p-8 sm:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6 border border-arch-border shadow-card-depth">
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="text-[0.65rem] uppercase tracking-widest bg-crimson text-white px-3 py-1 font-bold font-mono">
                                ПРЯМАЯ СВЯЗЬ
                            </span>
                            <span class="text-xs font-mono text-neutral-500 font-bold">TELEGRAM</span>
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-extrabold uppercase font-display text-arch-text">
                            {{ $telegramHandle ?: 'Telegram' }}
                        </h2>
                        <p class="text-xs text-neutral-600 font-mono">
                            {{ __('site.telegram_desc') }}
                        </p>
                    </div>

                    <a href="{{ $telegramUrl }}" 
                       target="_blank" 
                       rel="noopener" 
                       class="btn-crimson px-8 py-4 text-xs font-bold shrink-0">
                        <span>{{ __('site.telegram_btn') }}</span>
                        <span>&nearr;</span>
                    </a>
                </div>
            @endif

            {{-- 2. Phone Card --}}
            @if($phoneLink && $phoneDisplay)
                <div class="bg-white p-8 sm:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6 border border-arch-border shadow-card-depth">
                    <div class="space-y-2">
                        <span class="text-xs uppercase tracking-widest text-neutral-400 font-mono block">
                            {{ __('site.phone_title') }}
                        </span>
                        <h2 class="text-2xl sm:text-3xl font-extrabold uppercase font-display text-arch-text">
                            {{ $phoneDisplay }}
                        </h2>
                        <p class="text-xs text-neutral-600 font-mono">
                            Для звонков и срочных вопросов по текущим съёмкам
                        </p>
                    </div>

                    <a href="{{ $phoneLink }}" 
                       class="px-8 py-4 bg-arch-bg hover:bg-neutral-200 text-arch-text text-xs uppercase tracking-widest font-bold border border-arch-border transition-all shrink-0 font-mono">
                        Позвонить &rarr;
                    </a>
                </div>
            @endif

            {{-- 3. City & Location Card --}}
            <div class="bg-white p-8 sm:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6 border border-arch-border shadow-card-depth">
                <div class="space-y-2">
                    <span class="text-xs uppercase tracking-widest text-neutral-400 font-mono block">
                        {{ __('site.city_title') }}
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold uppercase font-display text-arch-text">
                        {{ $city }}
                    </h2>
                    <p class="text-xs text-neutral-600 font-mono">
                        Локации в Иркутске, на побережье Байкала и выездные проекты по области.
                    </p>
                </div>
                <div class="text-xs font-mono text-crimson uppercase tracking-widest px-4 py-2 border border-crimson/30 shrink-0 font-bold">
                    IRKUTSK &bull; TIMEZONE UTC+8
                </div>
            </div>

            {{-- Missing contacts prompt for demo mode --}}
            @if(!$telegramUrl && !$phoneLink)
                <div class="p-8 border border-dashed border-crimson/40 bg-crimson/5 space-y-3 font-mono">
                    <div class="flex items-center gap-2 text-crimson font-bold text-sm">
                        <span class="w-2.5 h-2.5 rounded-full bg-crimson animate-pulse"></span>
                        <span>Контакты ожидают настройки автором</span>
                    </div>
                    <p class="text-xs text-neutral-600 leading-relaxed font-light">
                        Владелец сайта ещё не внёс номер телефона и Telegram в панели управления. Для проверки работы сайта авторизуйтесь в административной панели и укажите свой контактный Telegram и телефон.
                    </p>
                    <div class="pt-2">
                        <a href="{{ route('admin.login') }}" class="text-xs uppercase tracking-widest text-crimson font-bold hover:underline">
                            Перейти к настройке контактов в панели управления &rarr;
                        </a>
                    </div>
                </div>
            @endif

        </div>

    </div>
</div>
@endsection
