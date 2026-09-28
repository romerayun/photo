@extends('layouts.app')

@section('title', 'Информация о съёмке для ' . $shoot->client_name . ' — Роман Юн')
@section('robots', 'noindex, nofollow')

@section('content')
<div x-data="{
    lightboxOpen: false,
    lightboxUrl: '',
    lightboxTitle: '',
    openLightbox(url, title = '') {
        this.lightboxUrl = url;
        this.lightboxTitle = title;
        this.lightboxOpen = true;
        document.body.style.overflow = 'hidden';
    },
    closeLightbox() {
        this.lightboxOpen = false;
        this.lightboxUrl = '';
        this.lightboxTitle = '';
        document.body.style.overflow = '';
    }
}" class="bg-arch-bg text-arch-text min-h-screen py-10 sm:py-16">

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Breadcrumb / Back --}}
        <div class="mb-6 flex items-center justify-between text-xs text-neutral-500">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-neutral-600 hover:text-crimson font-mono uppercase tracking-wider text-[0.75rem] transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>На главную</span>
            </a>
            <span class="font-mono text-[0.7rem] uppercase tracking-wider text-neutral-400">ID: {{ substr(md5($shoot->id . $shoot->share_token), 0, 8) }}</span>
        </div>

        {{-- TOP CARD: Shoot Hero & Essential Info (Date, Time, Location, Calendar) --}}
        <div class="bg-white border border-arch-border rounded-2xl md:rounded-3xl overflow-hidden shadow-card-depth mb-6">
            
            {{-- Hero Header --}}
            <div class="p-6 sm:p-8 lg:p-10 border-b border-arch-border relative overflow-hidden bg-gradient-to-br from-white via-arch-bg/40 to-white">
                <div class="absolute -right-20 -top-20 w-72 h-72 bg-crimson/5 rounded-full blur-3xl pointer-events-none"></div>

                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[0.68rem] font-mono uppercase tracking-widest bg-arch-bg text-neutral-700 border border-arch-border shadow-2xs font-bold">
                        <span class="w-2 h-2 rounded-full bg-crimson animate-pulse"></span>
                        <span>Персональная карточка съёмки</span>
                    </span>

                    @if($shoot->booking_confirmed_at && $shoot->status === 'planned')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono uppercase tracking-wider font-bold bg-emerald-50 text-emerald-700 border border-emerald-300">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span>Бронь подтверждена</span>
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-mono uppercase tracking-wider font-bold {{ $shoot->status === 'completed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($shoot->status === 'cancelled' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-blue-50 text-blue-700 border border-blue-200') }}">
                            {{ $shoot->status_label }}
                        </span>
                    @endif
                </div>

                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                    <div>
                        <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold uppercase tracking-tight font-display text-arch-text leading-[1.08]">
                            {{ $shoot->client_name }}
                        </h1>
                        <p class="text-xs sm:text-sm text-neutral-600 font-mono mt-2.5 max-w-2xl leading-relaxed">
                            Вся актуальная информация о фотосессии: дата, тайминг, локация, договор и готовые материалы.
                        </p>
                    </div>

                    @if($shoot->price)
                        <div class="md:text-right shrink-0">
                            <span class="text-[0.68rem] uppercase tracking-widest text-neutral-400 font-mono font-bold block mb-0.5">Стоимость съёмки</span>
                            <div class="text-2xl sm:text-3xl font-extrabold font-display text-arch-text">
                                {{ number_format($shoot->price, 0, '', ' ') }} ₽
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Key Details Grid: Date & Time + Location & Navigation --}}
            <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-arch-border bg-arch-bg/40">
                
                {{-- Date & Time Box --}}
                <div class="p-6 sm:p-7 flex flex-col justify-between space-y-4">
                    <div>
                        <span class="text-[0.68rem] uppercase tracking-widest text-neutral-400 font-mono font-bold block mb-1">Дата и время</span>
                        <div class="text-xl sm:text-2xl font-extrabold uppercase font-display tracking-tight text-arch-text">
                            {{ $shoot->shoot_date->translatedFormat('d F Y') }}
                        </div>
                        <div class="text-xs sm:text-sm font-mono text-neutral-600 mt-1.5 flex items-center gap-2">
                            <span class="text-crimson font-bold">{{ substr($shoot->start_time, 0, 5) }} – {{ $shoot->end_time ?: '...' }}</span>
                            <span class="text-neutral-300">&bull;</span>
                            <span>{{ $shoot->duration_label }}</span>
                        </div>
                    </div>

                    {{-- Add to calendar & MAX reminders --}}
                    <div class="pt-1 flex flex-wrap gap-2 items-center" x-data="{
                        maxLoading: false,
                        maxModalOpen: false,
                        copied: false,
                        maxConnected: {{ $shoot->max_connected_at ? 'true' : 'false' }},
                        maxData: {
                            deep_link: '',
                            app_link: '',
                            start_command: '',
                            bot_username: 'se14454241_bot'
                        },
                        async initMaxConnect() {
                            if (this.maxLoading) return;
                            this.maxLoading = true;
                            try {
                                const response = await fetch('{{ route('shoots.share.max_link', ['token' => $shoot->share_token]) }}', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    }
                                });
                                const data = await response.json();
                                if (data.success) {
                                    this.maxData = data;
                                    this.maxModalOpen = true;
                                } else {
                                    alert('Не удалось сформировать данные для MAX. Попробуйте еще раз.');
                                }
                            } catch (e) {
                                alert('Произошла ошибка при формировании ссылки MAX');
                            } finally {
                                this.maxLoading = false;
                            }
                        },
                        copyCommand() {
                            if (!this.maxData.start_command) return;
                            navigator.clipboard.writeText(this.maxData.start_command);
                            this.copied = true;
                            setTimeout(() => { this.copied = false; }, 2500);
                        },
                        async disconnectMax() {
                            if (!confirm('Отключить напоминания в MAX для этой съёмки?')) return;
                            try {
                                const response = await fetch('{{ route('shoots.share.max_disconnect', ['token' => $shoot->share_token]) }}', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    }
                                });
                                const data = await response.json();
                                if (data.success) {
                                    this.maxConnected = false;
                                }
                            } catch (e) {
                                alert('Не удалось отключить напоминания');
                            }
                        }
                    }">
                        <a href="{{ route('shoots.share.ics', ['token' => $shoot->share_token]) }}" 
                           class="px-3.5 py-2 rounded-xl bg-white hover:bg-neutral-50 text-xs font-mono uppercase tracking-wider text-arch-text font-bold transition-colors inline-flex items-center gap-1.5 border border-arch-border shadow-2xs hover:border-neutral-400">
                            <svg class="w-3.5 h-3.5 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>В календарь (.ics)</span>
                        </a>

                        <template x-if="maxConnected">
                            <div class="inline-flex items-center gap-2">
                                <span class="px-3 py-2 rounded-xl bg-emerald-50 text-emerald-800 text-xs font-mono uppercase tracking-wider font-bold inline-flex items-center gap-1.5 border border-emerald-200 shadow-2xs">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>MAX подключен</span>
                                </span>
                                <button type="button"
                                        @click="disconnectMax()"
                                        title="Отвязать напоминания"
                                        class="px-2.5 py-2 rounded-xl bg-neutral-100 hover:bg-rose-50 text-neutral-400 hover:text-rose-600 text-xs font-mono transition-colors border border-neutral-200 hover:border-rose-200">
                                    <span class="sr-only">Отключить</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </template>

                        <template x-if="!maxConnected">
                            <button type="button"
                                    @click="initMaxConnect()"
                                    :disabled="maxLoading"
                                    class="px-3.5 py-2 rounded-xl bg-neutral-900 hover:bg-black text-white text-xs font-mono uppercase tracking-wider font-bold transition-all inline-flex items-center gap-1.5 border border-neutral-800 shadow-sm disabled:opacity-50 cursor-pointer">
                                <svg class="w-3.5 h-3.5 text-amber-400 animate-pulse" fill="currentColor" viewBox="0 0 24 24"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2zm-2 1H8v-6c0-2.48 1.51-4.5 4-4.5s4 2.02 4 4.5v6z"/></svg>
                                <span x-text="maxLoading ? 'Генерация...' : 'Напоминания в MAX'"></span>
                            </button>
                        </template>

                        {{-- Modal: Choice for with app / without app --}}
                        <div x-show="maxModalOpen" 
                             x-cloak 
                             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
                             @keydown.escape.window="maxModalOpen = false">
                            <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-neutral-100 text-left"
                                 @click.away="maxModalOpen = false">
                                
                                <div class="flex items-start justify-between gap-4 mb-5">
                                    <div class="flex items-center gap-3.5 min-w-0">
                                        <div class="w-12 h-12 rounded-2xl overflow-hidden shadow-sm shrink-0 border border-neutral-100 bg-black flex items-center justify-center">
                                            <img src="{{ asset('images/max-logo.png') }}" alt="MAX" class="w-full h-full object-cover">
                                        </div>
                                        <div class="min-w-0">
                                            <h3 class="text-base font-bold uppercase tracking-tight text-neutral-900 font-sans truncate">
                                                Напоминания о съёмке
                                            </h3>
                                            <p class="text-xs text-neutral-500 font-sans mt-0.5">Выберите способ запуска бота</p>
                                        </div>
                                    </div>
                                    
                                    <button type="button" 
                                            @click="maxModalOpen = false" 
                                            aria-label="Закрыть"
                                            class="text-neutral-400 hover:text-neutral-800 p-2 -mr-1 -mt-1 rounded-full hover:bg-neutral-100 transition-colors shrink-0 cursor-pointer">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>

                                <div class="space-y-3">
                                    <a :href="maxData.app_link" 
                                       class="group flex items-center justify-between p-4 rounded-2xl bg-neutral-900 hover:bg-black text-white transition-all shadow-sm">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center text-amber-400 shrink-0">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17 1.01L7 1c-1.1 0-2 .9-2 2v18c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V3c0-1.1-.9-1.99-2-1.99zM17 19H7V5h10v14z"/></svg>
                                            </div>
                                            <div>
                                                <span class="font-bold text-sm block">Открыть в приложении MAX</span>
                                                <span class="text-xs text-neutral-400 block mt-0.5">Если у вас установлено приложение</span>
                                            </div>
                                        </div>
                                        <svg class="w-4 h-4 text-neutral-400 group-hover:translate-x-1 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>

                                    <a :href="maxData.web_link || maxData.deep_link" 
                                       target="_blank"
                                       class="group flex items-center justify-between p-4 rounded-2xl border border-neutral-200 bg-neutral-50/70 hover:bg-neutral-100 text-neutral-900 transition-all">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 border border-blue-100">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                                            </div>
                                            <div>
                                                <span class="font-bold text-sm block">Открыть в веб-версии (Web)</span>
                                                <span class="text-xs text-neutral-500 block mt-0.5">Без установки приложения</span>
                                            </div>
                                        </div>
                                        <svg class="w-4 h-4 text-neutral-400 group-hover:translate-x-1 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                </div>

                                <div class="mt-5 pt-3 border-t border-neutral-100 flex justify-center">
                                    <button type="button" 
                                            @click="maxModalOpen = false" 
                                            class="text-xs text-neutral-400 hover:text-neutral-700 font-semibold transition-colors py-1 cursor-pointer">
                                        Отмена
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Location Box --}}
                <div class="p-6 sm:p-7 flex flex-col justify-between space-y-4">
                    <div>
                        <span class="text-[0.68rem] uppercase tracking-widest text-neutral-400 font-mono font-bold block mb-1">Место проведения / Локация</span>
                        <div class="text-lg sm:text-xl font-bold font-display uppercase tracking-tight text-arch-text leading-snug">
                            {{ $shoot->location ?: 'Локация уточняется / на согласовании' }}
                        </div>
                    </div>

                    @if($shoot->location)
                        <div>
                            <a href="https://yandex.ru/maps/?text={{ urlencode($shoot->location) }}" 
                               target="_blank" 
                               class="px-3.5 py-2 rounded-xl bg-white hover:bg-neutral-50 text-xs font-mono uppercase tracking-wider text-arch-text font-bold transition-colors inline-flex items-center gap-1.5 border border-arch-border shadow-2xs hover:border-neutral-400">
                                <svg class="w-3.5 h-3.5 text-crimson" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>Открыть на Яндекс Картах</span>
                            </a>
                        </div>
                    @endif
                </div>

            </div>

        </div>

        {{-- 2-COLUMN DASHBOARD GRID --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

            {{-- LEFT COLUMN: Concept, Details & Moodboard --}}
            <div class="space-y-6">

                {{-- Description / Concept Card --}}
                @if($shoot->description)
                    <div class="bg-white border border-arch-border rounded-2xl md:rounded-3xl p-6 sm:p-8 shadow-card-depth space-y-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-crimson"></span>
                            <span class="text-[0.68rem] uppercase tracking-widest text-neutral-400 font-mono font-bold block">
                                Концепт и детали съёмки
                            </span>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-extrabold uppercase tracking-tight font-display text-arch-text">
                            Подготовка к фотосессии
                        </h2>
                        <div class="text-xs sm:text-sm text-neutral-700 leading-relaxed whitespace-pre-line bg-arch-bg/70 p-5 rounded-2xl border border-arch-border font-sans mt-3">
                            {{ $shoot->description }}
                        </div>
                    </div>
                @endif

                {{-- Moodboard & Materials Card --}}
                @if($shoot->files->count() > 0)
                    <div class="bg-white border border-arch-border rounded-2xl md:rounded-3xl p-6 sm:p-8 shadow-card-depth space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-[0.68rem] uppercase tracking-widest text-neutral-400 font-mono font-bold block">Материалы и референсы</span>
                                <h3 class="text-xl sm:text-2xl font-extrabold uppercase tracking-tight font-display text-arch-text mt-0.5">Мудборд съёмки</h3>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full bg-arch-bg text-neutral-700 text-xs font-mono font-semibold border border-arch-border">{{ $shoot->files->count() }} шт.</span>
                        </div>

                        @php
                            $images = $shoot->files->filter->is_image;
                            $otherFiles = $shoot->files->reject->is_image;
                        @endphp

                        @if($images->count() > 0)
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 pt-1">
                                @foreach($images as $file)
                                    <button type="button" 
                                            @click="openLightbox('{{ $file->url }}', '{{ addslashes($file->original_name) }}')"
                                            class="group relative aspect-square rounded-xl overflow-hidden bg-neutral-100 border border-arch-border block w-full text-left cursor-pointer focus:outline-none focus:ring-2 focus:ring-crimson shadow-xs hover:shadow-md transition-all">
                                        <img src="{{ $file->url }}" alt="{{ $file->original_name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-2.5">
                                            <span class="text-[0.68rem] text-white font-mono truncate">{{ $file->original_name }}</span>
                                        </div>

                                        <div class="absolute top-2 right-2 p-1.5 rounded-full bg-black/60 text-white opacity-0 group-hover:opacity-100 transition-opacity backdrop-blur-sm shadow-md">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/></svg>
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        @endif

                        @if($otherFiles->count() > 0)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-2">
                                @foreach($otherFiles as $file)
                                    <div class="p-3 rounded-xl border border-arch-border bg-arch-bg hover:bg-neutral-100 transition-colors flex items-center justify-between gap-3 shadow-xs">
                                        <div class="flex items-center gap-3 truncate">
                                            <div class="w-9 h-9 rounded-lg bg-white text-arch-text border border-arch-border flex items-center justify-center shrink-0 uppercase text-xs font-mono font-bold shadow-xs">
                                                {{ $file->extension }}
                                            </div>
                                            <div class="truncate">
                                                <span class="text-xs font-mono font-bold text-arch-text truncate block">{{ $file->original_name }}</span>
                                                <span class="text-[0.65rem] text-neutral-500 font-mono">{{ $file->formatted_size }}</span>
                                            </div>
                                        </div>
                                        <a href="{{ $file->url }}" download class="px-3 py-1.5 bg-white hover:bg-neutral-50 text-xs font-mono uppercase tracking-wider text-arch-text border border-arch-border rounded-lg transition-colors font-bold shrink-0 shadow-xs">
                                            Скачать
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                {{-- If no description and no files, show a neat placeholder card --}}
                @if(!$shoot->description && $shoot->files->count() === 0)
                    <div class="bg-white border border-arch-border rounded-2xl md:rounded-3xl p-6 sm:p-8 shadow-card-depth space-y-4">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-neutral-400"></span>
                            <span class="text-[0.68rem] uppercase tracking-widest text-neutral-400 font-mono font-bold block">
                                Концепт и референсы
                            </span>
                        </div>
                        <div class="p-6 rounded-2xl border-2 border-dashed border-arch-border bg-arch-bg/50 text-center space-y-3">
                            <div class="w-12 h-12 rounded-2xl bg-white border border-arch-border text-neutral-400 mx-auto flex items-center justify-center shadow-xs">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div class="space-y-1">
                                <h3 class="text-base font-extrabold uppercase tracking-tight font-display text-arch-text">Подготовка в процессе</h3>
                                <p class="text-xs text-neutral-500 font-sans max-w-sm mx-auto leading-relaxed">
                                    Роман добавит сюда референсы образов, мудборд и рекомендации по подготовке к съёмке.
                                </p>
                            </div>
                            @if($photographerTelegram)
                                <div class="pt-2">
                                    <a href="{{ $photographerTelegram }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-mono font-bold text-neutral-700 hover:text-crimson transition-colors">
                                        <span>Предложить свои референсы</span>
                                        <span>&rarr;</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

            </div>

            {{-- RIGHT COLUMN: Top = Contract & Prepayment, Bottom = Photoshoot Results / Gallery --}}
            <div class="space-y-6">

                {{-- Right Card 1: CONTRACT & PREPAYMENT / BOOKING --}}
                <div x-data="{
                    bookingConfirmed: {{ $shoot->booking_confirmed_at ? 'true' : 'false' }},
                    bookingConfirmedAt: {{ $shoot->booking_confirmed_at ? "'" . e($shoot->booking_confirmed_at->timezone('Asia/Irkutsk')->translatedFormat('d F Y, H:i')) . "'" : 'null' }},
                    contractAccepted: {{ ($shoot->receipt_path || $shoot->booking_confirmed_at) ? 'true' : 'false' }},
                    paymentOpen: false,
                    contractModalOpen: false,
                    copiedPhone: false,
                    receiptUrl: {{ $shoot->receipt_url ? "'" . e($shoot->receipt_url) . "'" : 'null' }},
                    receiptName: {{ $shoot->receipt_original_name ? "'" . e($shoot->receipt_original_name) . "'" : 'null' }},
                    receiptUploadedAt: {{ $shoot->receipt_uploaded_at ? "'" . e($shoot->receipt_uploaded_at->timezone('Asia/Irkutsk')->translatedFormat('d F Y, H:i')) . "'" : 'null' }},
                    receiptUploading: false,
                    receiptUploaded: {{ $shoot->receipt_path ? 'true' : 'false' }},
                    receiptError: null,
                    receiptLightbox: false,
                    copyPhone(number) {
                        navigator.clipboard.writeText(number);
                        this.copiedPhone = true;
                        setTimeout(() => this.copiedPhone = false, 2500);
                    },
                    async uploadReceipt(event) {
                        const file = event.target.files[0];
                        if (!file) return;

                        const maxSize = 10 * 1024 * 1024;
                        if (file.size > maxSize) {
                            this.receiptError = 'Файл слишком большой (максимум 10 МБ)';
                            return;
                        }

                        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/heic', 'application/pdf'];
                        if (!allowedTypes.includes(file.type)) {
                            this.receiptError = 'Допустимые форматы: JPG, PNG, WebP, HEIC, PDF';
                            return;
                        }

                        this.receiptError = null;
                        this.receiptUploading = true;

                        const formData = new FormData();
                        formData.append('receipt', file);

                        try {
                            const csrfToken = document.querySelector('meta[name=csrf-token]')?.content || '';
                            const response = await fetch('{{ route('shoots.share.receipt', $shoot->share_token) }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json',
                                },
                                body: formData,
                            });

                            const data = await response.json();

                            if (response.ok && data.success) {
                                this.receiptUrl = data.receipt_url;
                                this.receiptName = data.original_name;
                                this.receiptUploadedAt = data.uploaded_at;
                                this.receiptUploaded = true;
                                this.receiptError = null;
                            } else if (data.errors) {
                                const firstError = Object.values(data.errors).flat()[0];
                                this.receiptError = firstError || 'Ошибка валидации файла.';
                            } else {
                                this.receiptError = data.message || 'Ошибка загрузки. Попробуйте ещё раз.';
                            }
                        } catch (err) {
                            this.receiptError = 'Ошибка сети. Проверьте подключение и попробуйте ещё раз.';
                        } finally {
                            this.receiptUploading = false;
                        }
                    }
                }" class="bg-white border border-arch-border rounded-2xl md:rounded-3xl overflow-hidden shadow-card-depth">

                    {{-- 1. CONFIRMED BOOKING BANNER (Shown when booking is confirmed) --}}
                    <div x-show="bookingConfirmed"
                         class="p-6 sm:p-8 bg-gradient-to-br from-emerald-50/80 via-white to-emerald-50/40 space-y-5">
                        
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-100 border border-emerald-300 flex items-center justify-center text-emerald-700 shrink-0 shadow-2xs">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div class="min-w-0">
                                <div class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[0.65rem] font-mono uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200 font-bold mb-0.5">
                                    <span>Бронирование подтверждено</span>
                                </div>
                                <h2 class="text-xl sm:text-2xl font-extrabold uppercase tracking-tight font-display text-arch-text truncate">
                                    Бронирование подтверждено!
                                </h2>
                            </div>
                        </div>

                        {{-- Confirmation Details Grid --}}
                        <div class="grid grid-cols-2 gap-3 p-4 rounded-xl bg-arch-bg/80 border border-arch-border text-arch-text font-sans text-xs">
                            <div class="space-y-0.5">
                                <span class="text-[0.65rem] uppercase tracking-widest text-neutral-400 font-mono font-bold block">Предоплата</span>
                                <div class="text-lg font-extrabold font-display text-emerald-700 flex items-center gap-1.5">
                                    <span>{{ number_format($shoot->prepayment_amount, 0, '', ' ') }} ₽</span>
                                    <span class="text-[0.62rem] font-mono font-bold px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800">Внесена</span>
                                </div>
                            </div>
                            <div class="space-y-0.5 border-l border-arch-border pl-4">
                                <span class="text-[0.65rem] uppercase tracking-widest text-neutral-400 font-mono font-bold block">Остаток</span>
                                <div class="text-lg font-extrabold font-display text-arch-text">
                                    {{ number_format($shoot->remainder_amount, 0, '', ' ') }} ₽
                                </div>
                                <span class="text-[0.68rem] font-mono text-neutral-500 block">в день съёмки</span>
                            </div>
                        </div>

                        {{-- Friendly status message --}}
                        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-start gap-3 text-xs sm:text-sm text-emerald-950 leading-relaxed font-sans">
                            <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                <p class="font-semibold">Предоплата успешно получена, дата и время забронированы за вами.</p>
                                <p class="text-xs text-emerald-800 font-mono mt-1" x-show="bookingConfirmedAt" x-text="'Подтверждено фотографом: ' + bookingConfirmedAt"></p>
                            </div>
                        </div>

                        {{-- Quick links --}}
                        <div class="pt-2 border-t border-arch-border flex flex-wrap items-center justify-between gap-3 text-xs font-mono">
                            <button type="button"
                                    @click="contractModalOpen = true"
                                    class="text-neutral-600 hover:text-arch-text underline decoration-neutral-300 underline-offset-4 cursor-pointer inline-flex items-center gap-1.5 text-left">
                                <svg class="w-4 h-4 text-neutral-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Договор (№ {{ $shoot->contract_number }})</span>
                            </button>

                            <template x-if="receiptUrl">
                                <a :href="receiptUrl" target="_blank"
                                   class="text-emerald-700 hover:text-emerald-900 underline decoration-emerald-300 underline-offset-4 inline-flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span>Загруженный чек</span>
                                </a>
                            </template>
                        </div>
                    </div>

                    {{-- 2. CONTRACT & PREPAYMENT SECTION (Hidden when booking is confirmed) --}}
                    <div x-show="!bookingConfirmed"
                         class="p-6 sm:p-8 space-y-5">
                    
                        {{-- Section Title --}}
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <span class="text-[0.68rem] uppercase tracking-widest text-crimson font-mono font-bold block mb-1">
                                    ДОГОВОР И ПРЕДОПЛАТА
                                </span>
                                <h2 class="text-xl sm:text-2xl font-extrabold uppercase tracking-tight font-display text-arch-text">
                                    Договор и предоплата
                                </h2>
                            </div>

                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[0.65rem] font-mono uppercase tracking-wider bg-arch-bg border border-arch-border text-neutral-600 font-bold shrink-0">
                                № {{ $shoot->contract_number }}
                            </span>
                        </div>

                        {{-- Pricing Breakdown Card --}}
                        <div class="grid grid-cols-3 gap-2 p-4 rounded-xl bg-arch-bg border border-arch-border text-arch-text text-center sm:text-left">
                            <div class="space-y-0.5">
                                <span class="text-[0.65rem] uppercase tracking-widest text-neutral-400 font-mono font-bold block">
                                    Стоимость съёмки
                                </span>
                                <div class="text-sm sm:text-base font-extrabold font-display text-arch-text">
                                    {{ number_format($shoot->price ?? 3500, 0, '', ' ') }} ₽
                                </div>
                            </div>

                            <div class="space-y-0.5 border-l border-arch-border pl-2 sm:pl-3">
                                <span class="text-[0.65rem] uppercase tracking-widest text-crimson font-mono font-bold block">
                                    Предоплата (бронь)
                                </span>
                                <div class="text-sm sm:text-base font-extrabold font-display text-crimson">
                                    {{ number_format($shoot->prepayment_amount, 0, '', ' ') }} ₽
                                </div>
                            </div>

                            <div class="space-y-0.5 border-l border-arch-border pl-2 sm:pl-3">
                                <span class="text-[0.65rem] uppercase tracking-widest text-neutral-400 font-mono font-bold block">
                                    Остаток
                                </span>
                                <div class="text-sm sm:text-base font-extrabold font-display text-arch-text">
                                    {{ number_format($shoot->remainder_amount, 0, '', ' ') }} ₽
                                </div>
                                <span class="text-[0.65rem] font-mono text-neutral-400 block">в день съёмки</span>
                            </div>
                        </div>

                        {{-- Explanatory note with Deadline --}}
                        <div class="p-3.5 rounded-xl bg-amber-500/5 border border-amber-500/20 text-xs text-neutral-700 leading-relaxed font-sans">
                            <p>
                                Для подтверждения бронирования ознакомьтесь с договором и внесите предоплату до 
                                <strong class="font-bold text-neutral-900 font-mono underline decoration-amber-500/50 underline-offset-2">
                                    {{ $shoot->prepayment_deadline['formatted'] }}
                                </strong>. 
                                Входит в общую стоимость съёмки.
                            </p>
                        </div>

                        {{-- Contract Action and Acceptance --}}
                        <div class="space-y-3.5">
                            <div>
                                <button type="button" 
                                        @click="contractModalOpen = true" 
                                        class="w-full px-4 py-2.5 rounded-xl bg-white hover:bg-neutral-50 text-arch-text border border-arch-border hover:border-neutral-400 text-xs font-mono uppercase tracking-wider font-bold transition-all shadow-2xs inline-flex items-center justify-center gap-2 cursor-pointer group">
                                    <svg class="w-4 h-4 text-crimson group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span>Открыть и скачать договор</span>
                                    <span class="text-[0.65rem] text-neutral-400 font-normal lowercase">(№ {{ $shoot->contract_number }})</span>
                                </button>
                            </div>

                            {{-- Agreement Checkbox --}}
                            <div class="flex items-start gap-2.5 p-3 rounded-xl border transition-colors select-none"
                                 :class="contractAccepted || receiptUploaded ? 'bg-emerald-50/50 border-emerald-200' : 'bg-neutral-50 border-arch-border'">
                                <div class="flex items-center h-4 mt-0.5">
                                    <input type="checkbox" 
                                           id="contract_acceptance_check" 
                                           x-model="contractAccepted" 
                                           :checked="receiptUploaded || contractAccepted"
                                           :disabled="receiptUploaded"
                                           class="w-4 h-4 rounded text-crimson focus:ring-crimson border-neutral-300"
                                           :class="receiptUploaded ? 'opacity-70 cursor-not-allowed' : 'cursor-pointer'">
                                </div>
                                <label for="contract_acceptance_check" class="text-xs leading-snug"
                                       :class="receiptUploaded ? 'text-emerald-800' : 'text-neutral-800 cursor-pointer'">
                                    <template x-if="receiptUploaded">
                                        <span class="inline-flex items-center gap-1 font-medium">
                                            <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            <span>Договор принят. Чек загружен.</span>
                                        </span>
                                    </template>
                                    <template x-if="!receiptUploaded">
                                        <span>
                                            Я ознакомлен(а) с 
                                            <button type="button" 
                                                    @click="contractModalOpen = true" 
                                                    class="text-crimson font-bold underline underline-offset-2 hover:text-crimson/80 cursor-pointer">
                                                договором-офертой № {{ $shoot->contract_number }}
                                            </button> 
                                            и принимаю условия.
                                        </span>
                                    </template>
                                </label>
                            </div>

                            {{-- Prepayment Button --}}
                            <div>
                                <button type="button" 
                                        @click="paymentOpen = !paymentOpen; if(!contractAccepted) contractAccepted = true" 
                                        class="w-full px-5 py-3.5 rounded-xl bg-crimson hover:bg-crimson/90 text-white font-mono uppercase tracking-wider text-xs font-bold transition-all shadow-crimson-btn hover:shadow-lg flex items-center justify-center gap-2 cursor-pointer group">
                                    <span>Внести предоплату {{ number_format($shoot->prepayment_amount, 0, '', ' ') }} ₽</span>
                                    <svg class="w-4 h-4 transition-transform duration-200" 
                                         :class="paymentOpen ? 'rotate-180' : ''" 
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                            </div>

                            {{-- Payment Details Collapse --}}
                            <div x-show="paymentOpen" 
                                 x-cloak 
                                 x-transition:enter="transition ease-out duration-250"
                                 x-transition:enter-start="opacity-0 -translate-y-2"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 -translate-y-2"
                                 class="rounded-2xl border-2 border-crimson/20 bg-gradient-to-br from-white via-arch-bg to-white p-4 sm:p-5 space-y-4 shadow-md">
                                
                                <div class="flex items-center justify-between border-b border-arch-border pb-2.5">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-crimson"></span>
                                        <h3 class="text-xs sm:text-sm font-extrabold uppercase tracking-tight font-display text-arch-text">
                                            Реквизиты для внесения предоплаты
                                        </h3>
                                    </div>
                                    <span class="text-[0.65rem] font-mono font-bold text-crimson">СБП / Перевод</span>
                                </div>

                                <p class="text-[0.68rem] text-neutral-500 font-mono">
                                    Внесение предоплаты означает принятие договора-оферты.
                                </p>

                                <div class="space-y-3 text-xs">
                                    <div>
                                        <span class="text-[0.65rem] uppercase font-mono tracking-widest text-neutral-400 font-bold block mb-0.5">
                                            Сумма:
                                        </span>
                                        <div class="text-xl font-extrabold font-display text-crimson">
                                            {{ number_format($shoot->prepayment_amount, 0, '', ' ') }} ₽
                                        </div>
                                    </div>

                                    <div>
                                        <span class="text-[0.65rem] uppercase font-mono tracking-widest text-neutral-400 font-bold block mb-0.5">
                                            Банки получателя (СБП):
                                        </span>
                                        <div class="font-medium text-neutral-800">
                                            Т-Банк (Тинькофф) / Сбер / Альфа
                                        </div>
                                        <span class="text-[0.68rem] text-neutral-500 font-mono">Получатель: Роман Александрович Ю.</span>
                                    </div>

                                    <div>
                                        <span class="text-[0.65rem] uppercase font-mono tracking-widest text-neutral-400 font-bold block mb-1">
                                            Номер телефона:
                                        </span>
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-mono font-bold text-neutral-900 bg-white px-2.5 py-1.5 rounded-lg border border-arch-border flex-1">
                                                {{ $photographerPhone }}
                                            </span>
                                            <button type="button" 
                                                    @click="copyPhone('{{ preg_replace('/[^\d+]/', '', $photographerPhone) }}')"
                                                    class="px-2.5 py-1.5 bg-neutral-900 hover:bg-neutral-800 text-white rounded-lg text-xs font-mono font-bold transition-all cursor-pointer inline-flex items-center gap-1 shadow-sm shrink-0">
                                                <span x-text="copiedPhone ? '✓' : 'Копировать'"></span>
                                            </button>
                                        </div>
                                    </div>

                                    <div>
                                        <span class="text-[0.65rem] uppercase font-mono tracking-widest text-neutral-400 font-bold block mb-0.5">
                                            Назначение платежа:
                                        </span>
                                        <div class="text-[0.7rem] font-mono text-neutral-700 bg-white p-2 rounded-lg border border-arch-border">
                                            Предоплата за съёмку ({{ $shoot->client_name }})
                                        </div>
                                    </div>
                                </div>

                                {{-- Receipt Upload Section --}}
                                <div class="pt-3 border-t border-arch-border space-y-2.5">
                                    <div class="flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full" :class="receiptUploaded ? 'bg-emerald-500' : 'bg-amber-500 animate-pulse'"></span>
                                        <h4 class="text-xs font-extrabold uppercase tracking-tight font-display text-arch-text">
                                            Загрузка чека об оплате
                                        </h4>
                                    </div>

                                    {{-- Already uploaded state --}}
                                    <template x-if="receiptUploaded && receiptUrl">
                                        <div class="space-y-2">
                                            <div class="rounded-xl border border-emerald-200 bg-emerald-50/50 p-3 flex items-center gap-3">
                                                <div class="shrink-0">
                                                    <template x-if="receiptUrl && !receiptUrl.endsWith('.pdf')">
                                                        <button type="button"
                                                                @click="receiptLightbox = true"
                                                                class="block w-14 h-14 rounded-lg overflow-hidden border border-emerald-200 shadow-xs cursor-pointer hover:shadow-sm">
                                                            <img :src="receiptUrl" alt="Чек" class="w-full h-full object-cover">
                                                        </button>
                                                    </template>
                                                    <template x-if="receiptUrl && receiptUrl.endsWith('.pdf')">
                                                        <a :href="receiptUrl" target="_blank"
                                                           class="w-14 h-14 rounded-lg border border-emerald-200 bg-white shadow-xs flex flex-col items-center justify-center gap-0.5">
                                                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                            <span class="text-[0.55rem] font-mono font-bold text-neutral-500 uppercase">PDF</span>
                                                        </a>
                                                    </template>
                                                </div>

                                                <div class="flex-1 min-w-0 space-y-0.5">
                                                    <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.62rem] font-mono uppercase bg-emerald-100 text-emerald-800 font-bold">
                                                        <span>Чек загружен</span>
                                                    </div>
                                                    <p class="text-[0.7rem] font-mono text-neutral-700 truncate" x-text="receiptName"></p>
                                                </div>

                                                <div class="shrink-0">
                                                    <label class="p-2 rounded-xl bg-white hover:bg-neutral-50 text-xs font-mono text-arch-text border border-arch-border cursor-pointer inline-flex items-center justify-center" title="Загрузить другой">
                                                        <svg class="w-3.5 h-3.5 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                                        <input type="file" accept="image/*,.pdf" @change="uploadReceipt($event)" class="hidden">
                                                    </label>
                                                </div>
                                            </div>

                                            <p class="text-[0.68rem] text-emerald-700 font-mono leading-relaxed">
                                                ✓ Роман получил ваш чек и скоро подтвердит бронирование.
                                            </p>
                                        </div>
                                    </template>

                                    {{-- Upload zone --}}
                                    <template x-if="!receiptUploaded || !receiptUrl">
                                        <div class="space-y-2">
                                            <label class="block cursor-pointer group"
                                                   x-on:dragover.prevent="$el.classList.add('border-crimson', 'bg-crimson/5')"
                                                   x-on:dragleave.prevent="$el.classList.remove('border-crimson', 'bg-crimson/5')"
                                                   x-on:drop.prevent="$el.classList.remove('border-crimson', 'bg-crimson/5'); const dt = $event.dataTransfer; if (dt.files.length) { const input = $el.querySelector('input[type=file]'); const dataTransfer = new DataTransfer(); dataTransfer.items.add(dt.files[0]); input.files = dataTransfer.files; input.dispatchEvent(new Event('change')); }">
                                                <div class="rounded-xl border-2 border-dashed border-arch-border bg-arch-bg/60 p-4 sm:p-5 text-center transition-all group-hover:border-neutral-400 group-hover:bg-neutral-50"
                                                     :class="receiptUploading ? 'opacity-60 pointer-events-none' : ''">
                                                    
                                                    <div class="w-10 h-10 mx-auto rounded-xl bg-white border border-arch-border text-neutral-400 flex items-center justify-center shadow-2xs mb-2 group-hover:text-crimson transition-colors">
                                                        <template x-if="!receiptUploading">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                                        </template>
                                                        <template x-if="receiptUploading">
                                                            <svg class="w-5 h-5 animate-spin text-crimson" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                                                        </template>
                                                    </div>

                                                    <template x-if="!receiptUploading">
                                                        <div>
                                                            <p class="text-xs font-bold text-arch-text font-display uppercase tracking-tight">
                                                                Загрузите скриншот или фото чека
                                                            </p>
                                                            <p class="text-[0.68rem] text-neutral-400 font-mono mt-1">
                                                                JPG, PNG, WebP, PDF · до 10 МБ
                                                            </p>
                                                        </div>
                                                    </template>
                                                    <template x-if="receiptUploading">
                                                        <div>
                                                            <p class="text-xs font-bold text-crimson font-display uppercase tracking-tight">
                                                                Загружаем...
                                                            </p>
                                                        </div>
                                                    </template>

                                                    <input type="file" accept="image/*,.pdf" @change="uploadReceipt($event)" class="hidden">
                                                </div>
                                            </label>

                                            <template x-if="receiptError">
                                                <div class="rounded-lg bg-rose-50 border border-rose-200 p-2.5 flex items-start gap-1.5">
                                                    <svg class="w-3.5 h-3.5 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.072 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                                    <p class="text-[0.7rem] font-mono text-rose-700" x-text="receiptError"></p>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    {{-- Receipt lightbox --}}
                                    <div x-show="receiptLightbox"
                                         x-cloak
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0"
                                         x-transition:enter-end="opacity-100"
                                         x-transition:leave="transition ease-in duration-150"
                                         x-transition:leave-start="opacity-100"
                                         x-transition:leave-end="opacity-0"
                                         @click.self="receiptLightbox = false"
                                         @keydown.escape.window="receiptLightbox = false"
                                         class="fixed inset-0 z-[140] flex items-center justify-center bg-black/85 backdrop-blur-sm p-4"
                                         style="display: none;">
                                        <div class="relative max-w-3xl max-h-[85vh] w-full">
                                            <button type="button" @click="receiptLightbox = false" class="absolute -top-3 -right-3 z-10 w-9 h-9 rounded-full bg-white/90 text-neutral-800 flex items-center justify-center shadow-lg hover:bg-white transition cursor-pointer">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                            <img :src="receiptUrl" alt="Чек об оплате" class="w-full h-full object-contain rounded-2xl shadow-2xl">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <p class="text-[0.68rem] text-neutral-400 font-mono leading-relaxed italic pt-0.5">
                                * Внесение предоплаты означает принятие договора-оферты.
                            </p>
                        </div>
                    </div>

                    {{-- CONTRACT MODAL --}}
                    <div x-show="contractModalOpen" 
                         style="display: none;"
                         x-cloak 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         @keydown.escape.window="contractModalOpen = false"
                         class="fixed inset-0 z-[130] flex items-center justify-center bg-black/80 backdrop-blur-xs p-4 sm:p-6"
                         role="dialog"
                         aria-modal="true">
                        
                        <div @click.away="contractModalOpen = false" 
                             class="bg-white rounded-2xl sm:rounded-3xl shadow-2xl max-w-2xl w-full max-h-[88vh] overflow-hidden flex flex-col border border-arch-border">
                            
                            {{-- Modal Header --}}
                            <div class="px-6 py-4 border-b border-arch-border bg-arch-bg flex items-center justify-between">
                                <div>
                                    <span class="text-[0.65rem] font-mono uppercase tracking-widest text-crimson font-bold block">
                                        Документ
                                    </span>
                                    <h3 class="text-base sm:text-lg font-extrabold uppercase font-display text-arch-text">
                                        Договор-оферта № {{ $shoot->contract_number }}
                                    </h3>
                                </div>
                                <button type="button" 
                                        @click="contractModalOpen = false" 
                                        class="text-neutral-400 hover:text-neutral-900 p-1.5 rounded-lg transition-colors cursor-pointer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>

                            {{-- Modal Body: Contract Text --}}
                            <div class="p-6 overflow-y-auto space-y-4 text-xs sm:text-sm text-neutral-700 leading-relaxed font-sans custom-scrollbar">
                                <div class="text-center pb-2 border-b border-arch-border">
                                    <h4 class="font-extrabold font-display uppercase tracking-tight text-arch-text text-sm sm:text-base">
                                        ДОГОВОР-ОФЕРТА НА ОКАЗАНИЕ ФОТОГРАФИЧЕСКИХ УСЛУГ
                                    </h4>
                                    <span class="text-xs font-mono text-neutral-500 mt-1 block">
                                        г. Иркутск &bull; Дата размещения: {{ $shoot->created_at ? $shoot->created_at->translatedFormat('d F Y') : date('d.m.Y') }}
                                    </span>
                                </div>

                                <p>
                                    <strong>1. Стороны договора:</strong> Фотограф Роман Юн (далее — <em>«Исполнитель»</em>) и заказчик <strong>{{ $shoot->client_name }}</strong> (далее — <em>«Заказчик»</em>) заключили настоящий Договор о нижеследующем.
                                </p>

                                <p>
                                    <strong>2. Предмет договора:</strong> Исполнитель обязуется оказать фотографические услуги, а Заказчик обязуется принять и оплатить их на условиях настоящей оферты:
                                </p>
                                <ul class="list-disc list-inside space-y-1 pl-2 font-mono text-xs text-neutral-800 bg-arch-bg p-3.5 rounded-xl border border-arch-border">
                                    <li><strong>Дата съёмки:</strong> {{ $shoot->shoot_date->translatedFormat('d F Y') }}</li>
                                    <li><strong>Время начала:</strong> {{ substr($shoot->start_time, 0, 5) }} ({{ $shoot->duration_label }})</li>
                                    <li><strong>Место съёмки:</strong> {{ $shoot->location ?: 'По согласованию сторон' }}</li>
                                    <li><strong>Стоимость съёмки:</strong> {{ number_format($shoot->price ?? 3500, 0, '', ' ') }} ₽</li>
                                    <li><strong>Размер предоплаты (задатка):</strong> {{ number_format($shoot->prepayment_amount, 0, '', ' ') }} ₽</li>
                                    <li><strong>Остаток к оплате:</strong> {{ number_format($shoot->remainder_amount, 0, '', ' ') }} ₽ — оплачивается в день съёмки</li>
                                </ul>

                                <p>
                                    <strong>3. Бронирование и оплата:</strong> Дата и время считаются окончательно забронированными с момента внесения Заказчиком предоплаты в размере <strong>{{ number_format($shoot->prepayment_amount, 0, '', ' ') }} ₽</strong>. Предоплата засчитывается в общую стоимость услуг.
                                </p>

                                <p>
                                    <strong>4. Сроки и передача материалов:</strong> Готовые обработанные фотографии передаются в оригинальном высоком разрешении через персональную онлайн-галерею в срок до 7–14 календарных дней с даты проведения съёмки.
                                </p>

                                <p>
                                    <strong>5. Перенос и отмена съёмки:</strong> Перенос съёмки на другую дату без потери суммы бронирования допускается с уведомлением Исполнителя не позднее чем за 3 дня до запланированной даты.
                                </p>

                                <p class="text-xs text-neutral-500 font-mono pt-2 border-t border-arch-border">
                                    Настоящий договор имеет юридическую силу в соответствии со ст. 435 и 438 Гражданского кодекса РФ. Акцептом оферты является факт внесения предоплаты.
                                </p>
                            </div>

                            {{-- Modal Footer --}}
                            <div class="px-6 py-4 border-t border-arch-border bg-arch-bg/60 flex items-center justify-between gap-3">
                                <button type="button" 
                                        onclick="window.print()" 
                                        class="px-4 py-2 rounded-xl bg-white hover:bg-neutral-50 text-arch-text border border-arch-border font-mono text-xs uppercase tracking-wider font-bold transition-all shadow-2xs inline-flex items-center gap-1.5 cursor-pointer">
                                    <svg class="w-4 h-4 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    <span>Печать</span>
                                </button>

                                <button type="button" 
                                        @click="contractModalOpen = false; contractAccepted = true" 
                                        class="px-5 py-2 rounded-xl bg-neutral-900 hover:bg-neutral-800 text-white font-mono text-xs uppercase tracking-wider font-bold transition-all cursor-pointer shadow-sm">
                                    Принять и закрыть
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Right Card 2: PHOTOSHOOT RESULTS / GALLERY SECTION (Ниже справа) --}}
                <div class="bg-white border border-arch-border rounded-2xl md:rounded-3xl overflow-hidden shadow-card-depth p-6 sm:p-8 relative">
                    @if(!empty($shoot->gallery_link))
                        {{-- Ready Photos with Gallery Link --}}
                        <div class="rounded-2xl p-6 bg-gradient-to-br from-emerald-50 via-white to-emerald-50/40 border border-emerald-200 relative overflow-hidden shadow-xs space-y-4">
                            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-emerald-500/5 rounded-full blur-2xl pointer-events-none"></div>

                            <div class="space-y-2">
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[0.68rem] font-mono uppercase tracking-widest bg-emerald-100 text-emerald-800 border border-emerald-200 font-bold">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Готовые фото</span>
                                </div>
                                <h3 class="text-xl sm:text-2xl font-extrabold uppercase tracking-tight font-display text-arch-text">
                                    Результат вашей съёмки
                                </h3>
                                <p class="text-xs text-neutral-600 font-mono leading-relaxed">
                                    Вся серия обработана и доступна в персональной онлайн-галерее в максимальном качестве.
                                </p>
                            </div>

                            <div>
                                <a href="{{ $shoot->clean_gallery_url }}" target="_blank" class="w-full px-5 py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs uppercase tracking-wider transition-all shadow-md hover:shadow-emerald-600/30 flex items-center justify-center gap-2 font-mono group">
                                    <svg class="w-4 h-4 text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    <span>Смотреть и скачать фото</span>
                                    <svg class="w-3.5 h-3.5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                            </div>
                        </div>
                    @elseif($shoot->status === 'completed')
                        {{-- Completed but link pending --}}
                        <div class="rounded-2xl p-6 bg-blue-50/60 border border-blue-100 text-center space-y-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 mx-auto flex items-center justify-center">
                                <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            </div>
                            <h3 class="text-base font-extrabold uppercase tracking-tight font-display text-arch-text">
                                Фотографии в обработке
                            </h3>
                            <p class="text-xs text-neutral-600 font-mono leading-relaxed">
                                Съёмка завершена. Роман отбирает и обрабатывает кадры. Скоро здесь появится ссылка на готовую серию.
                            </p>
                        </div>
                    @else
                        {{-- Pending photoshoot --}}
                        <div class="rounded-2xl p-6 border-2 border-dashed border-arch-border bg-arch-bg/40 text-center space-y-3 relative overflow-hidden">
                            <div class="w-10 h-10 rounded-xl bg-white border border-arch-border text-neutral-500 mx-auto flex items-center justify-center shadow-2xs">
                                <svg class="w-5 h-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>

                            <div class="space-y-1">
                                <span class="text-[0.65rem] uppercase font-mono tracking-widest text-neutral-400 font-bold block">Здесь будет результат съёмки</span>
                                <h3 class="text-base font-extrabold uppercase tracking-tight font-display text-arch-text">Готовые фото появятся здесь</h3>
                            </div>

                            <p class="text-xs text-neutral-600 font-mono leading-relaxed">
                                После проведения съёмки и обработки в этом блоке появится прямая ссылка на онлайн-галерею для скачивания кадров.
                            </p>

                            <div class="pt-1 inline-flex items-center gap-1.5 text-[0.68rem] font-mono text-neutral-500">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                <span>{{ $shoot->status_label }}</span>
                            </div>
                        </div>
                    @endif
                </div>

            </div>

        </div>

        {{-- BOTTOM: Photographer Contact Card (Across full width) --}}
        <div class="mt-6 bg-white border border-arch-border rounded-2xl md:rounded-3xl overflow-hidden shadow-card-depth p-6 sm:p-8 bg-gradient-to-r from-crimson/5 via-arch-bg to-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-[0.68rem] uppercase tracking-widest text-neutral-400 font-mono font-bold block mb-1">Ваш фотограф</span>
                <div class="text-xl sm:text-2xl font-extrabold uppercase tracking-tight font-display text-arch-text">Роман Юн</div>
                <p class="text-xs text-neutral-500 font-mono mt-1 leading-relaxed">Если у вас возникнут вопросы или захотите скорректировать образ — напишите мне.</p>
            </div>

            <div class="flex flex-wrap items-center justify-start sm:justify-end gap-2.5 shrink-0">
                @if($photographerTelegram)
                    <a href="{{ $photographerTelegram }}" target="_blank" class="px-4 py-2.5 rounded-xl bg-[#2AABEE] hover:bg-[#229ED9] text-white text-xs font-mono uppercase tracking-wider font-bold transition-all shadow-md inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221l-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.121l-6.871 4.326-2.962-.924c-.643-.204-.657-.643.136-.953l11.57-4.461c.537-.196 1.006.128.832.946z"/></svg>
                        <span>Написать в Telegram</span>
                    </a>
                @endif

                @if($photographerPhone)
                    <a href="tel:{{ preg_replace('/[^\d+]/', '', $photographerPhone) }}" class="px-4 py-2.5 rounded-xl bg-white hover:bg-neutral-50 text-arch-text text-xs font-mono uppercase tracking-wider font-bold transition-all border border-arch-border shadow-xs inline-flex items-center gap-2">
                        <svg class="w-4 h-4 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <span>Позвонить</span>
                    </a>
                @endif
            </div>
        </div>

    </div>

    {{-- Darkroom Lightbox Modal --}}
    <div x-show="lightboxOpen" 
         style="display: none;"
         x-cloak 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="closeLightbox()"
         class="fixed inset-0 z-[120] flex items-center justify-center bg-black/95 backdrop-blur-md p-4 sm:p-8"
         role="dialog"
         aria-modal="true"
         aria-label="Просмотр референса">
        
        {{-- Close button --}}
        <button type="button" 
                @click="closeLightbox()" 
                class="absolute top-4 right-4 sm:top-6 sm:right-6 text-white/80 hover:text-white p-2.5 rounded-full bg-white/10 hover:bg-white/20 transition-all z-50 cursor-pointer shadow-lg"
                title="Закрыть (Esc)">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        {{-- Download button in Lightbox --}}
        <a :href="lightboxUrl" 
           :download="lightboxTitle || 'reference'" 
           target="_blank"
           class="absolute top-4 right-16 sm:top-6 sm:right-20 text-white/80 hover:text-white px-3.5 py-2 rounded-full bg-white/10 hover:bg-white/20 transition-all z-50 cursor-pointer text-xs font-semibold inline-flex items-center gap-1.5 shadow-lg"
           title="Скачать изображение">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            <span class="hidden sm:inline">Скачать</span>
        </a>

        {{-- Fullscreen Image Preview --}}
        <div class="relative max-w-full max-h-[90vh] flex flex-col items-center justify-center select-none" 
             @click.away="closeLightbox()">
            <img :src="lightboxUrl" 
                 :alt="lightboxTitle" 
                 class="max-w-full max-h-[82vh] object-contain rounded-xl shadow-2xl ring-1 ring-white/10">
            
            <template x-if="lightboxTitle">
                <div class="mt-3.5 text-center text-xs sm:text-sm text-white/90 font-mono px-4 max-w-xl truncate bg-white/10 py-1.5 rounded-full border border-white/10 backdrop-blur-sm" x-text="lightboxTitle"></div>
            </template>
        </div>
    </div>

</div>
</div>
@endsection
