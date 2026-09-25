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

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Breadcrumb / Back --}}
        <div class="mb-6 flex items-center justify-between text-xs text-neutral-500">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-neutral-600 hover:text-crimson font-mono uppercase tracking-wider text-[0.75rem] transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>На главную</span>
            </a>
            <span class="font-mono text-[0.7rem] uppercase tracking-wider text-neutral-400">ID: {{ substr(md5($shoot->id), 0, 8) }}</span>
        </div>

        {{-- Main Shoot Card (Light Architectural Style) --}}
        <div class="bg-white border border-arch-border rounded-2xl md:rounded-3xl overflow-hidden shadow-card-depth">
            
            {{-- Card Header --}}
            <div class="p-6 sm:p-10 border-b border-arch-border relative overflow-hidden bg-gradient-to-br from-white via-arch-bg/40 to-white">
                {{-- Accent decorative blush --}}
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-crimson/5 rounded-full blur-3xl pointer-events-none"></div>

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

                <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold uppercase tracking-tight font-display text-arch-text leading-[1.08]">
                    {{ $shoot->client_name }}
                </h1>
                <p class="text-xs sm:text-sm text-neutral-600 font-mono mt-3 max-w-2xl leading-relaxed">
                    Вся актуальная информация о нашей фотосессии: дата, тайминг, адрес локации, референсы и рекомендации по подготовке.
                </p>
            </div>

            {{-- Highlight Key Details Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 divide-y sm:divide-y-0 sm:divide-x divide-arch-border border-b border-arch-border bg-arch-bg/40">
                
                {{-- Date & Time Box --}}
                <div class="p-6 sm:p-8 flex flex-col justify-between space-y-4">
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
                    <div class="pt-2 flex flex-wrap gap-2 items-center" x-data="{
                        maxLoading: false,
                        maxConnected: {{ $shoot->max_connected_at ? 'true' : 'false' }},
                        async connectMax() {
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
                                if (data.success && data.deep_link) {
                                    window.open(data.deep_link, '_blank');
                                } else {
                                    alert('Не удалось сформировать ссылку для MAX. Попробуйте еще раз.');
                                }
                            } catch (e) {
                                alert('Произошла ошибка при подключении к MAX');
                            } finally {
                                this.maxLoading = false;
                            }
                        }
                    }">
                        <a href="{{ route('shoots.share.ics', ['token' => $shoot->share_token]) }}" 
                           class="px-3.5 py-2 rounded-xl bg-white hover:bg-neutral-50 text-xs font-mono uppercase tracking-wider text-arch-text font-bold transition-colors inline-flex items-center gap-1.5 border border-arch-border shadow-2xs hover:border-neutral-400">
                            <svg class="w-3.5 h-3.5 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>В календарь (.ics)</span>
                        </a>

                        <template x-if="maxConnected">
                            <span class="px-3.5 py-2 rounded-xl bg-emerald-50 text-emerald-800 text-xs font-mono uppercase tracking-wider font-bold inline-flex items-center gap-1.5 border border-emerald-200 shadow-2xs">
                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Напоминания в MAX подключены</span>
                            </span>
                        </template>

                        <template x-if="!maxConnected">
                            <button type="button"
                                    @click="connectMax()"
                                    :disabled="maxLoading"
                                    class="px-3.5 py-2 rounded-xl bg-neutral-900 hover:bg-black text-white text-xs font-mono uppercase tracking-wider font-bold transition-all inline-flex items-center gap-1.5 border border-neutral-800 shadow-sm disabled:opacity-50 cursor-pointer">
                                <svg class="w-3.5 h-3.5 text-amber-400 animate-pulse" fill="currentColor" viewBox="0 0 24 24"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2zm-2 1H8v-6c0-2.48 1.51-4.5 4-4.5s4 2.02 4 4.5v6z"/></svg>
                                <span x-text="maxLoading ? 'Генерация ссылки...' : 'Получать напоминания в MAX'"></span>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Location Box --}}
                <div class="p-6 sm:p-8 flex flex-col justify-between space-y-4">
                    <div>
                        <span class="text-[0.68rem] uppercase tracking-widest text-neutral-400 font-mono font-bold block mb-1">Место проведения / Локация</span>
                        <div class="text-lg sm:text-xl font-bold font-display uppercase tracking-tight text-arch-text leading-snug">
                            {{ $shoot->location ?: 'Локация уточняется / на согласовании' }}
                        </div>
                        @if($shoot->price)
                            <div class="text-xs sm:text-sm font-mono text-neutral-600 mt-2">
                                Стоимость: <span class="font-extrabold font-display text-arch-text text-base sm:text-lg">{{ number_format($shoot->price, 0, '', ' ') }} ₽</span>
                            </div>
                        @endif
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

            {{-- CONTRACT & PREPAYMENT / CONFIRMED BOOKING SECTION --}}
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
            }">

                {{-- 1. CONFIRMED BOOKING BANNER (Shown when booking is confirmed) --}}
                <div x-show="bookingConfirmed"
                     class="p-6 sm:p-10 border-b border-arch-border bg-gradient-to-br from-emerald-50/70 via-white to-emerald-50/40">
                    <div class="rounded-2xl p-6 sm:p-8 bg-white border-2 border-emerald-300 shadow-sm space-y-6 relative overflow-hidden">
                        {{-- Subtle background glow --}}
                        <div class="absolute -right-8 -bottom-8 w-40 h-40 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none"></div>

                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-3.5">
                                <div class="w-12 h-12 rounded-2xl bg-emerald-100 border border-emerald-300 flex items-center justify-center text-emerald-700 shrink-0 shadow-2xs">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div>
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[0.68rem] font-mono uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200 font-bold mb-1">
                                        <span>Бронь подтверждена</span>
                                    </div>
                                    <h2 class="text-xl sm:text-2xl font-extrabold uppercase tracking-tight font-display text-arch-text">
                                        Бронирование подтверждено!
                                    </h2>
                                </div>
                            </div>

                            <div class="text-xs font-mono text-neutral-500 sm:text-right">
                                <span class="block text-[0.68rem] uppercase tracking-wider text-neutral-400 font-bold">Договор-оферта</span>
                                <span class="font-bold text-neutral-700">{{ $shoot->contract_number }}</span>
                            </div>
                        </div>

                        {{-- Confirmation Details Grid --}}
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 p-4 rounded-xl bg-arch-bg/80 border border-arch-border text-arch-text font-sans">
                            <div class="space-y-0.5">
                                <span class="text-[0.68rem] uppercase tracking-widest text-neutral-400 font-mono font-bold block">Предоплата</span>
                                <div class="text-lg sm:text-xl font-extrabold font-display text-emerald-700 flex items-center gap-1.5">
                                    <span>{{ number_format($shoot->prepayment_amount, 0, '', ' ') }} ₽</span>
                                    <span class="text-xs font-mono font-bold px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800">Внесена</span>
                                </div>
                            </div>
                            <div class="space-y-0.5 border-t sm:border-t-0 sm:border-l border-arch-border pt-3 sm:pt-0 sm:pl-4">
                                <span class="text-[0.68rem] uppercase tracking-widest text-neutral-400 font-mono font-bold block">Остаток к оплате</span>
                                <div class="text-lg sm:text-xl font-extrabold font-display text-arch-text">
                                    {{ number_format($shoot->remainder_amount, 0, '', ' ') }} ₽
                                </div>
                                <span class="text-[0.68rem] font-mono text-neutral-500 block">в день съёмки</span>
                            </div>
                            <div class="space-y-0.5 border-t sm:border-t-0 sm:border-l border-arch-border pt-3 sm:pt-0 sm:pl-4">
                                <span class="text-[0.68rem] uppercase tracking-widest text-neutral-400 font-mono font-bold block">Дата и время съёмки</span>
                                <div class="text-base font-bold font-display text-arch-text">
                                    {{ $shoot->shoot_date->translatedFormat('d F Y') }}
                                </div>
                                <span class="text-[0.68rem] font-mono text-crimson font-bold block">{{ substr($shoot->start_time, 0, 5) }} (Иркутск)</span>
                            </div>
                        </div>

                        {{-- Friendly status message --}}
                        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-start gap-3 text-xs sm:text-sm text-emerald-900 leading-relaxed font-sans">
                            <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                <p class="font-semibold text-emerald-950">
                                    Предоплата успешно получена, дата и время забронированы за вами.
                                </p>
                                <p class="text-xs text-emerald-800 font-mono mt-1" x-show="bookingConfirmedAt" x-text="'Подтверждено фотографом: ' + bookingConfirmedAt"></p>
                                <p class="text-xs text-emerald-700 font-mono mt-1.5">
                                    Остаток стоимости ({{ number_format($shoot->remainder_amount, 0, '', ' ') }} ₽) оплачивается в день съёмки. До встречи!
                                </p>
                            </div>
                        </div>

                        {{-- Document & receipt quick links --}}
                        <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-arch-border text-xs font-mono">
                            <button type="button"
                                    @click="contractModalOpen = true"
                                    class="text-neutral-600 hover:text-arch-text underline decoration-neutral-300 underline-offset-4 cursor-pointer inline-flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Открыть договор-оферту (№ {{ $shoot->contract_number }})</span>
                            </button>

                            <template x-if="receiptUrl">
                                <a :href="receiptUrl" target="_blank"
                                   class="text-emerald-700 hover:text-emerald-900 underline decoration-emerald-300 underline-offset-4 inline-flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span>Посмотреть загруженный чек</span>
                                </a>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- 2. CONTRACT & PREPAYMENT SECTION (Hidden when booking is confirmed) --}}
                <div x-show="!bookingConfirmed"
                     class="p-6 sm:p-10 border-b border-arch-border bg-white space-y-6">
                
                {{-- Section Title --}}
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <span class="text-[0.68rem] uppercase tracking-widest text-crimson font-mono font-bold block mb-1">
                            Условия бронирования
                        </span>
                        <h2 class="text-xl sm:text-2xl font-extrabold uppercase tracking-tight font-display text-arch-text">
                            ДОГОВОР И ПРЕДОПЛАТА
                        </h2>
                    </div>

                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[0.68rem] font-mono uppercase tracking-wider bg-arch-bg border border-arch-border text-neutral-600 font-bold">
                        <span>Оферта {{ $shoot->contract_number }}</span>
                    </div>
                </div>

                {{-- Pricing Breakdown Card --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 p-5 rounded-2xl bg-arch-bg border border-arch-border text-arch-text">
                    {{-- Full price --}}
                    <div class="space-y-0.5">
                        <span class="text-[0.68rem] uppercase tracking-widest text-neutral-400 font-mono font-bold block">
                            Стоимость съёмки
                        </span>
                        <div class="text-xl sm:text-2xl font-extrabold font-display text-arch-text">
                            {{ number_format($shoot->price ?? 3500, 0, '', ' ') }} ₽
                        </div>
                    </div>

                    {{-- Prepayment --}}
                    <div class="space-y-0.5 border-t sm:border-t-0 sm:border-l border-arch-border pt-3 sm:pt-0 sm:pl-4">
                        <span class="text-[0.68rem] uppercase tracking-widest text-crimson font-mono font-bold block">
                            Предоплата (бронь)
                        </span>
                        <div class="text-xl sm:text-2xl font-extrabold font-display text-crimson">
                            {{ number_format($shoot->prepayment_amount, 0, '', ' ') }} ₽
                        </div>
                    </div>

                    {{-- Remainder --}}
                    <div class="space-y-0.5 border-t sm:border-t-0 sm:border-l border-arch-border pt-3 sm:pt-0 sm:pl-4">
                        <span class="text-[0.68rem] uppercase tracking-widest text-neutral-400 font-mono font-bold block">
                            Остаток
                        </span>
                        <div class="text-xl sm:text-2xl font-extrabold font-display text-arch-text">
                            {{ number_format($shoot->remainder_amount, 0, '', ' ') }} ₽
                        </div>
                        <span class="text-[0.7rem] font-mono text-neutral-500 block">в день съёмки</span>
                    </div>
                </div>

                {{-- Explanatory note with Deadline --}}
                <div class="p-4 rounded-xl bg-amber-500/5 border border-amber-500/20 text-xs sm:text-sm text-neutral-700 leading-relaxed font-sans">
                    <p>
                        Для подтверждения бронирования ознакомьтесь с договором и внесите предоплату до 
                        <strong class="font-bold text-neutral-900 font-mono underline decoration-amber-500/50 decoration-2 underline-offset-2">
                            {{ $shoot->prepayment_deadline['formatted'] }}
                        </strong>. 
                        Предоплата входит в общую стоимость съёмки.
                    </p>
                </div>

                {{-- Contract Action and Acceptance --}}
                <div class="space-y-4 pt-1">
                    {{-- Open / Download Contract Button --}}
                    <div>
                        <button type="button" 
                                @click="contractModalOpen = true" 
                                class="px-5 py-3 rounded-xl bg-white hover:bg-neutral-50 text-arch-text border border-arch-border hover:border-neutral-400 text-xs font-mono uppercase tracking-wider font-bold transition-all shadow-2xs inline-flex items-center gap-2 cursor-pointer group">
                            <svg class="w-4 h-4 text-crimson group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Открыть и скачать договор</span>
                            <span class="text-[0.65rem] text-neutral-400 font-normal lowercase">(№ {{ $shoot->contract_number }})</span>
                        </button>
                    </div>

                    {{-- Agreement Checkbox --}}
                    <div class="flex items-start gap-3 p-3.5 rounded-xl border transition-colors select-none"
                         :class="contractAccepted || receiptUploaded ? 'bg-emerald-50/50 border-emerald-200' : 'bg-neutral-50 border-arch-border'">
                        <div class="flex items-center h-5 mt-0.5">
                            <input type="checkbox" 
                                   id="contract_acceptance_check" 
                                   x-model="contractAccepted" 
                                   :checked="receiptUploaded || contractAccepted"
                                   :disabled="receiptUploaded"
                                   class="w-4 h-4 rounded text-crimson focus:ring-crimson border-neutral-300"
                                   :class="receiptUploaded ? 'opacity-70 cursor-not-allowed' : 'cursor-pointer'">
                        </div>
                        <label for="contract_acceptance_check" class="text-xs sm:text-sm leading-snug"
                               :class="receiptUploaded ? 'text-emerald-800' : 'text-neutral-800 cursor-pointer'">
                            <template x-if="receiptUploaded">
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Договор-оферта № {{ $shoot->contract_number }} принят(а). Чек загружен.</span>
                                </span>
                            </template>
                            <template x-if="!receiptUploaded">
                                <span>
                                    Я ознакомился с 
                                    <button type="button" 
                                            @click="contractModalOpen = true" 
                                            class="text-crimson font-bold underline underline-offset-2 hover:text-crimson/80 cursor-pointer">
                                        договором-офертой № {{ $shoot->contract_number }}
                                    </button> 
                                    и условиями съёмки и принимаю их.
                                </span>
                            </template>
                        </label>
                    </div>

                    {{-- Prepayment Button (toggles collapse) --}}
                    <div>
                        <button type="button" 
                                @click="paymentOpen = !paymentOpen; if(!contractAccepted) contractAccepted = true" 
                                class="w-full sm:w-auto px-8 py-4 rounded-xl bg-crimson hover:bg-crimson/90 text-white font-mono uppercase tracking-wider text-xs font-bold transition-all shadow-crimson-btn hover:shadow-lg flex items-center justify-center gap-2 cursor-pointer group">
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
                         class="rounded-2xl border-2 border-crimson/20 bg-gradient-to-br from-white via-arch-bg to-white p-5 sm:p-7 space-y-5 shadow-md">
                        
                        <div class="flex items-center justify-between border-b border-arch-border pb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-crimson"></span>
                                <h3 class="text-sm sm:text-base font-extrabold uppercase tracking-tight font-display text-arch-text">
                                    Реквизиты для внесения предоплаты
                                </h3>
                            </div>
                            <span class="text-xs font-mono font-bold text-crimson">СБП / Перевод</span>
                        </div>

                        {{-- Payment instructions --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs sm:text-sm">
                            <div class="space-y-3">
                                <div>
                                    <span class="text-[0.68rem] uppercase font-mono tracking-widest text-neutral-400 font-bold block mb-1">
                                        Сумма к переводу:
                                    </span>
                                    <div class="text-2xl font-extrabold font-display text-crimson">
                                        {{ number_format($shoot->prepayment_amount, 0, '', ' ') }} ₽
                                    </div>
                                </div>

                                <div>
                                    <span class="text-[0.68rem] uppercase font-mono tracking-widest text-neutral-400 font-bold block mb-1">
                                        Банки получателя (СБП):
                                    </span>
                                    <div class="font-medium text-neutral-800">
                                        Т-Банк (Тинькофф) / Сбербанк / Альфа-Банк
                                    </div>
                                    <span class="text-[0.72rem] text-neutral-500 font-mono">Получатель: Роман Александрович Ю.</span>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <div>
                                    <span class="text-[0.68rem] uppercase font-mono tracking-widest text-neutral-400 font-bold block mb-1">
                                        Номер телефона для перевода:
                                    </span>
                                    <div class="flex items-center gap-2">
                                        <span class="text-base sm:text-lg font-mono font-bold text-neutral-900 bg-white px-3 py-1.5 rounded-lg border border-arch-border">
                                            {{ $photographerPhone }}
                                        </span>
                                        <button type="button" 
                                                @click="copyPhone('{{ preg_replace('/[^\d+]/', '', $photographerPhone) }}')"
                                                class="px-3 py-2 bg-neutral-900 hover:bg-neutral-800 text-white rounded-lg text-xs font-mono font-bold transition-all cursor-pointer inline-flex items-center gap-1 shadow-sm">
                                            <span x-text="copiedPhone ? '✓ Скопировано' : 'Скопировать'"></span>
                                        </button>
                                    </div>
                                </div>

                                <div>
                                    <span class="text-[0.68rem] uppercase font-mono tracking-widest text-neutral-400 font-bold block mb-1">
                                        Назначение платежа:
                                    </span>
                                    <div class="text-xs font-mono text-neutral-700 bg-white p-2 rounded-lg border border-arch-border">
                                        Предоплата за съёмку ({{ $shoot->client_name }})
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Receipt Upload Section --}}
                        <div class="pt-4 border-t border-arch-border space-y-3">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="w-2 h-2 rounded-full" :class="receiptUploaded ? 'bg-emerald-500' : 'bg-amber-500 animate-pulse'"></span>
                                <h4 class="text-xs sm:text-sm font-extrabold uppercase tracking-tight font-display text-arch-text">
                                    Загрузка чека об оплате
                                </h4>
                            </div>

                            {{-- Already uploaded state --}}
                            <template x-if="receiptUploaded && receiptUrl">
                                <div class="space-y-3">
                                    <div class="rounded-xl border-2 border-emerald-200 bg-emerald-50/50 p-4 flex flex-col sm:flex-row items-start sm:items-center gap-4">
                                        {{-- Receipt thumbnail/preview --}}
                                        <div class="shrink-0">
                                            <template x-if="receiptUrl && !receiptUrl.endsWith('.pdf')">
                                                <button type="button"
                                                        @click="receiptLightbox = true"
                                                        class="block w-20 h-20 rounded-lg overflow-hidden border border-emerald-200 shadow-sm cursor-pointer hover:shadow-md transition-shadow group relative">
                                                    <img :src="receiptUrl" alt="Чек" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                                                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                                                        <svg class="w-5 h-5 text-white opacity-0 group-hover:opacity-100 transition-opacity drop-shadow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/></svg>
                                                    </div>
                                                </button>
                                            </template>
                                            <template x-if="receiptUrl && receiptUrl.endsWith('.pdf')">
                                                <a :href="receiptUrl" target="_blank"
                                                   class="w-20 h-20 rounded-lg border border-emerald-200 bg-white shadow-sm flex flex-col items-center justify-center gap-1 hover:shadow-md transition-shadow">
                                                    <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                    <span class="text-[0.6rem] font-mono font-bold text-neutral-500 uppercase">PDF</span>
                                                </a>
                                            </template>
                                        </div>

                                        {{-- Receipt info --}}
                                        <div class="flex-1 min-w-0 space-y-1">
                                            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[0.68rem] font-mono uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200 font-bold">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                <span>Чек загружен</span>
                                            </div>
                                            <p class="text-xs font-mono text-neutral-700 truncate" x-text="receiptName"></p>
                                            <p class="text-[0.68rem] font-mono text-neutral-500" x-text="'Загружен: ' + receiptUploadedAt"></p>
                                        </div>

                                        {{-- Re-upload button --}}
                                        <div class="shrink-0">
                                            <label class="px-3.5 py-2 rounded-xl bg-white hover:bg-neutral-50 text-xs font-mono uppercase tracking-wider text-arch-text font-bold transition-all cursor-pointer inline-flex items-center gap-1.5 border border-arch-border shadow-2xs hover:border-neutral-400">
                                                <svg class="w-3.5 h-3.5 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                                <span>Загрузить другой</span>
                                                <input type="file" accept="image/*,.pdf" @change="uploadReceipt($event)" class="hidden">
                                            </label>
                                        </div>
                                    </div>

                                    <p class="text-xs text-emerald-700 font-mono leading-relaxed">
                                        ✓ Роман получил ваш чек и скоро подтвердит бронирование. Спасибо!
                                    </p>
                                </div>
                            </template>

                            {{-- Upload zone (not yet uploaded or no receipt) --}}
                            <template x-if="!receiptUploaded || !receiptUrl">
                                <div class="space-y-3">
                                    <label class="block cursor-pointer group"
                                           x-on:dragover.prevent="$el.classList.add('border-crimson', 'bg-crimson/5')"
                                           x-on:dragleave.prevent="$el.classList.remove('border-crimson', 'bg-crimson/5')"
                                           x-on:drop.prevent="$el.classList.remove('border-crimson', 'bg-crimson/5'); const dt = $event.dataTransfer; if (dt.files.length) { const input = $el.querySelector('input[type=file]'); const dataTransfer = new DataTransfer(); dataTransfer.items.add(dt.files[0]); input.files = dataTransfer.files; input.dispatchEvent(new Event('change')); }">
                                        <div class="rounded-xl border-2 border-dashed border-arch-border bg-arch-bg/60 p-6 sm:p-8 text-center transition-all group-hover:border-neutral-400 group-hover:bg-neutral-50"
                                             :class="receiptUploading ? 'opacity-60 pointer-events-none' : ''">
                                            
                                            {{-- Upload icon --}}
                                            <div class="w-14 h-14 mx-auto rounded-2xl bg-white border border-arch-border text-neutral-400 flex items-center justify-center shadow-2xs mb-3 group-hover:text-crimson transition-colors">
                                                <template x-if="!receiptUploading">
                                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                                </template>
                                                <template x-if="receiptUploading">
                                                    <svg class="w-7 h-7 animate-spin text-crimson" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                                                </template>
                                            </div>

                                            <template x-if="!receiptUploading">
                                                <div>
                                                    <p class="text-sm font-bold text-arch-text font-display uppercase tracking-tight">
                                                        Загрузите скриншот или фото чека
                                                    </p>
                                                    <p class="text-xs text-neutral-500 font-mono mt-1">
                                                        Перетащите файл сюда или нажмите для выбора
                                                    </p>
                                                    <p class="text-[0.68rem] text-neutral-400 font-mono mt-2">
                                                        JPG, PNG, WebP, HEIC, PDF · до 10 МБ
                                                    </p>
                                                </div>
                                            </template>
                                            <template x-if="receiptUploading">
                                                <div>
                                                    <p class="text-sm font-bold text-crimson font-display uppercase tracking-tight">
                                                        Загружаем чек...
                                                    </p>
                                                    <p class="text-xs text-neutral-500 font-mono mt-1">
                                                        Пожалуйста, подождите
                                                    </p>
                                                </div>
                                            </template>

                                            <input type="file" accept="image/*,.pdf" @change="uploadReceipt($event)" class="hidden">
                                        </div>
                                    </label>

                                    {{-- Error message --}}
                                    <template x-if="receiptError">
                                        <div class="rounded-lg bg-rose-50 border border-rose-200 p-3 flex items-start gap-2">
                                            <svg class="w-4 h-4 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.072 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                            <p class="text-xs font-mono text-rose-700" x-text="receiptError"></p>
                                        </div>
                                    </template>

                                    <p class="text-xs text-neutral-600 font-mono leading-relaxed">
                                        После перевода предоплаты загрузите скриншот или фото квитанции — Роман сразу подтвердит бронь.
                                    </p>
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

                    {{-- Footnote disclaimer --}}
                    <p class="text-[0.72rem] text-neutral-500 font-mono leading-relaxed italic pt-1">
                        * Внесение предоплаты означает принятие договора-оферты. Договор считается заключённым с момента поступления предоплаты.
                    </p>
                </div>
                </div>

                {{-- CONTRACT MODAL (STUB / PREVIEW) --}}
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
                                <span>Печать / Сохранить</span>
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

            {{-- PHOTOSHOOT RESULTS / GALLERY SECTION --}}
            <div class="p-6 sm:p-8 border-b border-arch-border relative overflow-hidden bg-white">
                @if(!empty($shoot->gallery_link))
                    {{-- Ready Photos with Gallery Link (Visible whenever link is added) --}}
                    <div class="rounded-2xl p-6 sm:p-8 bg-gradient-to-br from-emerald-50 via-white to-emerald-50/40 border border-emerald-200 relative overflow-hidden shadow-xs">
                        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-emerald-500/5 rounded-full blur-2xl pointer-events-none"></div>

                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                            <div class="space-y-2">
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[0.68rem] font-mono uppercase tracking-widest bg-emerald-100 text-emerald-800 border border-emerald-200 font-bold">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Фотографии готовы</span>
                                </div>
                                <h3 class="text-xl sm:text-2xl font-extrabold uppercase tracking-tight font-display text-arch-text">Результат вашей съёмки</h3>
                                <p class="text-xs sm:text-sm text-neutral-600 font-mono max-w-xl leading-relaxed">
                                    Вся серия обработана и загружена в облачную галерею в максимальном качестве. Вы можете смотреть кадры онлайн и скачать весь архив.
                                </p>
                            </div>

                            <div class="shrink-0 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                                <a href="{{ $shoot->clean_gallery_url }}" target="_blank" class="px-7 py-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs uppercase tracking-wider transition-all shadow-md hover:shadow-emerald-600/30 flex items-center justify-center gap-2 font-mono group">
                                    <svg class="w-4 h-4 text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    <span>Смотреть и скачать фото</span>
                                    <svg class="w-3.5 h-3.5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @elseif($shoot->status === 'completed')
                    {{-- Completed but link pending --}}
                    <div class="rounded-2xl p-6 sm:p-8 bg-blue-50/60 border border-blue-100 text-center space-y-3">
                        <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 mx-auto flex items-center justify-center">
                            <svg class="w-6 h-6 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </div>
                        <h3 class="text-lg sm:text-xl font-extrabold uppercase tracking-tight font-display text-arch-text">Съёмка проведена &bull; Фотографии в обработке</h3>
                        <p class="text-xs sm:text-sm text-neutral-600 font-mono max-w-md mx-auto leading-relaxed">
                            Роман отбирает и обрабатывает кадры. Как только серия будет загружена, здесь сразу появится ссылка на скачивание.
                        </p>
                    </div>
                @else
                    {{-- Status NOT completed & no link: "Здесь будет результат съёмки" --}}
                    <div class="rounded-2xl p-6 sm:p-8 border-2 border-dashed border-arch-border bg-arch-bg/60 text-center space-y-3 relative overflow-hidden">
                        <div class="w-12 h-12 rounded-2xl bg-white border border-arch-border text-neutral-500 mx-auto flex items-center justify-center shadow-2xs">
                            <svg class="w-6 h-6 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>

                        <div class="space-y-1">
                            <span class="text-[0.68rem] uppercase font-mono tracking-widest text-neutral-400 font-bold block">Результаты фотосессии</span>
                            <h3 class="text-lg sm:text-xl font-extrabold uppercase tracking-tight font-display text-arch-text">Здесь будет результат съёмки</h3>
                        </div>

                        <p class="text-xs sm:text-sm text-neutral-600 font-mono max-w-lg mx-auto leading-relaxed">
                            После проведения съёмки и завершения обработки сюда будет добавлена прямая ссылка на персональную онлайн-галерею, где вы сможете просмотреть и скачать все готовые фотографии в оригинальном качестве.
                        </p>

                        <div class="pt-2 inline-flex items-center gap-2 text-xs font-mono text-neutral-500">
                            <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                            <span>Статус: {{ $shoot->status_label }} &bull; Дата съёмки: {{ $shoot->shoot_date->translatedFormat('d F Y') }}</span>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Description / Concept / Preparation --}}
            @if($shoot->description)
                <div class="p-6 sm:p-8 border-b border-arch-border space-y-3 bg-white">
                    <span class="text-[0.68rem] uppercase tracking-widest text-neutral-400 font-mono font-bold block">Концепт и детали съёмки</span>
                    <div class="text-xs sm:text-sm text-neutral-700 leading-relaxed whitespace-pre-line bg-arch-bg p-5 rounded-2xl border border-arch-border font-sans">
                        {{ $shoot->description }}
                    </div>
                </div>
            @endif

            {{-- Attached Files / Moodboard References --}}
            @if($shoot->files->count() > 0)
                <div class="p-6 sm:p-8 border-b border-arch-border space-y-4 bg-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-[0.68rem] uppercase tracking-widest text-neutral-400 font-mono font-bold block">Материалы и референсы</span>
                            <h3 class="text-lg sm:text-xl font-extrabold uppercase tracking-tight font-display text-arch-text mt-0.5">Мудборд и файлы съёмки</h3>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full bg-arch-bg text-neutral-700 text-xs font-mono font-semibold border border-arch-border">{{ $shoot->files->count() }} шт.</span>
                    </div>

                    {{-- Images Grid (Visual Moodboard) --}}
                    @php
                        $images = $shoot->files->filter->is_image;
                        $otherFiles = $shoot->files->reject->is_image;
                    @endphp

                    @if($images->count() > 0)
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach($images as $file)
                                <button type="button" 
                                        @click="openLightbox('{{ $file->url }}', '{{ addslashes($file->original_name) }}')"
                                        class="group relative aspect-square rounded-xl overflow-hidden bg-neutral-100 border border-arch-border block w-full text-left cursor-pointer focus:outline-none focus:ring-2 focus:ring-crimson shadow-2xs hover:shadow-md transition-all">
                                    <img src="{{ $file->url }}" alt="{{ $file->original_name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    
                                    {{-- Hover info overlay --}}
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-2.5">
                                        <span class="text-[0.68rem] text-white font-mono truncate">{{ $file->original_name }}</span>
                                    </div>

                                    {{-- Zoom icon indicator --}}
                                    <div class="absolute top-2 right-2 p-1.5 rounded-full bg-black/60 text-white opacity-0 group-hover:opacity-100 transition-opacity backdrop-blur-xs shadow-md">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/></svg>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    @endif

                    {{-- Non-Image Documents (PDF, brief, etc.) --}}
                    @if($otherFiles->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-2">
                            @foreach($otherFiles as $file)
                                <div class="p-3 rounded-xl border border-arch-border bg-arch-bg hover:bg-neutral-100 transition-colors flex items-center justify-between gap-3 shadow-2xs">
                                    <div class="flex items-center gap-3 truncate">
                                        <div class="w-10 h-10 rounded-lg bg-white text-arch-text border border-arch-border flex items-center justify-center shrink-0 uppercase text-xs font-mono font-bold shadow-2xs">
                                            {{ $file->extension }}
                                        </div>
                                        <div class="truncate">
                                            <span class="text-xs font-mono font-bold text-arch-text truncate block">{{ $file->original_name }}</span>
                                            <span class="text-[0.68rem] text-neutral-500 font-mono">{{ $file->formatted_size }}</span>
                                        </div>
                                    </div>
                                    <a href="{{ $file->url }}" download class="px-3.5 py-1.5 bg-white hover:bg-neutral-50 text-xs font-mono uppercase tracking-wider text-arch-text border border-arch-border rounded-lg transition-colors font-bold shrink-0 shadow-2xs">
                                        Скачать
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            {{-- Photographer Contact Bar --}}
            <div class="p-6 sm:p-8 bg-gradient-to-r from-crimson/5 via-arch-bg to-white flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-t border-arch-border">
                <div>
                    <span class="text-[0.68rem] uppercase tracking-widest text-neutral-400 font-mono font-bold block mb-1">Ваш фотограф</span>
                    <div class="text-xl sm:text-2xl font-extrabold uppercase tracking-tight font-display text-arch-text">Роман Юн</div>
                    <p class="text-xs text-neutral-500 font-mono mt-1 leading-relaxed">Если у вас возникнут вопросы или захотите скорректировать образ — напишите мне.</p>
                </div>

                <div class="flex flex-wrap items-center justify-end sm:justify-end gap-2.5 shrink-0">
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
                <div class="mt-3.5 text-center text-xs sm:text-sm text-white/90 font-mono px-4 max-w-xl truncate bg-white/10 py-1.5 rounded-full border border-white/10 backdrop-blur-xs" x-text="lightboxTitle"></div>
            </template>
        </div>
    </div>

</div>
@endsection
