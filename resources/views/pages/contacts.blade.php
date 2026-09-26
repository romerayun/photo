@extends('layouts.app')

@section('title', __('site.nav_contacts') . ' — ' . __('site.author_name'))
@section('description', __('site.contacts_subtitle'))

@section('content')
<div class="bg-arch-bg text-arch-text py-16 md:py-24">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="mb-12 text-center md:text-left">
            <span class="text-xs uppercase tracking-widest text-crimson font-mono font-bold block mb-2">
                06 / СВЯЗЬ И БРОНИРОВАНИЕ
            </span>
            <h1 class="text-4xl sm:text-6xl font-extrabold uppercase tracking-tightest font-display text-arch-text mb-4">
                {{ __('site.contacts_title') }}
            </h1>
            <p class="text-base text-neutral-600 font-mono max-w-2xl leading-relaxed">
                {{ __('site.contacts_subtitle') }}
            </p>
        </div>

        {{-- Response Time Guarantee Badge --}}
        <div class="mb-10 p-5 bg-white border border-arch-border shadow-sm flex items-center gap-3 font-mono text-xs">
            <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse shrink-0"></span>
            <div>
                <span class="font-bold text-arch-text uppercase tracking-wider block">
                    Срок ответа: в течение 1–2 часов
                </span>
                <span class="text-neutral-500 text-[0.72rem] leading-relaxed">
                    Ежедневно с 09:00 до 21:00 (Иркутск, UTC+8 / МСК+5). Во время съёмок на Байкале вне зоны сети — сразу по возвращении.
                </span>
            </div>
        </div>

        {{-- Selected Package Banner (if user came from Pricing or Home) --}}
        @if(!empty($selectedPackage))
            <div class="mb-10 p-5 sm:p-6 bg-neutral-950 text-white border-l-4 border-crimson flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-card-depth">
                <div>
                    <span class="text-[0.65rem] font-mono uppercase tracking-widest text-crimson font-bold block mb-1">
                        Выбранный формат съёмки
                    </span>
                    <h2 class="text-xl sm:text-2xl font-bold uppercase font-display text-white">
                        {{ $selectedPackage }}
                    </h2>
                    <p class="text-xs text-neutral-400 font-mono mt-1">
                        Название пакета уже подставлено в форму заявки и в текст сообщения для Telegram.
                    </p>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    @if($telegramUrl)
                        <a href="{{ $telegramUrl }}" 
                           target="_blank" 
                           rel="noopener" 
                           class="btn-crimson px-5 py-3 text-xs font-bold font-mono">
                            <span>Написать в Telegram</span>
                            <span>&nearr;</span>
                        </a>
                    @endif
                    <a href="{{ route('contacts.index') }}" 
                       class="text-xs text-neutral-400 hover:text-white font-mono uppercase tracking-wider underline">
                        Сбросить выбор
                    </a>
                </div>
            </div>
        @endif

        {{-- Success / Error Alerts --}}
        @if(session('contact_success'))
            <div class="mb-10 p-6 bg-emerald-950 border border-emerald-700 text-emerald-100 flex items-start gap-4">
                <span class="text-emerald-400 font-bold text-xl">&check;</span>
                <div class="space-y-1 font-mono">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-emerald-300">Заявка успешно отправлена!</h3>
                    <p class="text-xs text-emerald-200 leading-relaxed">{{ session('contact_success') }}</p>
                </div>
            </div>
        @endif

        @if(session('contact_error'))
            <div class="mb-10 p-6 bg-rose-950 border border-rose-700 text-rose-100 flex items-start gap-4">
                <span class="text-rose-400 font-bold text-xl">&cross;</span>
                <div class="space-y-1 font-mono">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-rose-300">Ошибка отправки</h3>
                    <p class="text-xs text-rose-200 leading-relaxed">{{ session('contact_error') }}</p>
                </div>
            </div>
        @endif

        {{-- Main Two-Column Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">

            {{-- Left Column: Direct Contacts & Channels --}}
            <div class="lg:col-span-5 space-y-6">

                {{-- Telegram Card --}}
                @if($telegramUrl)
                    <div class="bg-white p-6 sm:p-8 border border-arch-border shadow-card-depth space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-[0.65rem] uppercase tracking-widest bg-crimson text-white px-3 py-1 font-bold font-mono">
                                БЫСТРЫЙ ОТВЕТ
                            </span>
                            <span class="text-xs font-mono text-neutral-400 font-bold">TELEGRAM</span>
                        </div>
                        <div>
                            <h2 class="text-2xl font-extrabold uppercase font-display text-arch-text">
                                {{ $telegramHandle ?: 'Telegram' }}
                            </h2>
                            <p class="text-xs text-neutral-600 font-mono mt-1">
                                Удобно обсудить референсы, детали съёмки, тайминг и задать любые вопросы.
                            </p>
                        </div>
                        <a href="{{ $telegramUrl }}" 
                           target="_blank" 
                           rel="noopener" 
                           class="btn-crimson w-full py-3.5 text-xs font-bold text-center block">
                            <span>{{ $selectedPackage ? 'Обсудить пакет в Telegram' : 'Написать в Telegram' }}</span>
                            <span>&nearr;</span>
                        </a>
                    </div>
                @endif

                {{-- Phone Card --}}
                @if($phoneLink && $phoneDisplay)
                    <div class="bg-white p-6 sm:p-8 border border-arch-border shadow-card-depth space-y-4">
                        <span class="text-xs uppercase tracking-widest text-neutral-400 font-mono block">
                            Телефон для связи
                        </span>
                        <div>
                            <h2 class="text-2xl font-extrabold uppercase font-display text-arch-text">
                                {{ $phoneDisplay }}
                            </h2>
                            <p class="text-xs text-neutral-600 font-mono mt-1">
                                Для звонков, срочных согласований и вопросов по съёмкам на текущий день.
                            </p>
                        </div>
                        <a href="{{ $phoneLink }}" 
                           class="w-full py-3 bg-arch-bg hover:bg-neutral-200 text-arch-text text-xs uppercase tracking-widest font-bold border border-arch-border transition-colors block text-center font-mono">
                            Позвонить &rarr;
                        </a>
                    </div>
                @endif

                {{-- Location & Geography Card --}}
                <div class="bg-white p-6 sm:p-8 border border-arch-border shadow-card-depth space-y-3 font-mono">
                    <span class="text-xs uppercase tracking-widest text-neutral-400 block font-bold">
                        География съёмок
                    </span>
                    <h3 class="text-xl font-bold uppercase font-display text-arch-text">
                        {{ $city }} и Байкал
                    </h3>
                    <p class="text-xs text-neutral-600 leading-relaxed">
                        Студии и городские локации Иркутска, Листвянка, Большое Голоустное, Малое Море, Ольхон и выездные проекты.
                    </p>
                    <div class="pt-2 text-[0.7rem] text-crimson uppercase tracking-wider font-bold">
                        ЕЖЕДНЕВНО С 09:00 ДО 21:00 (UTC+8)
                    </div>
                </div>

            </div>

            {{-- Right Column: Feedback & Booking Form --}}
            <div id="feedback-form" class="lg:col-span-7 bg-white p-8 sm:p-10 border border-arch-border shadow-card-depth">
                
                {{-- Inline Success / Error Alerts directly above the form --}}
                @if(session('contact_success'))
                    <div class="mb-6 p-5 bg-emerald-50 border border-emerald-500 text-emerald-900 rounded-sm flex items-start gap-3">
                        <span class="text-emerald-600 font-bold text-lg leading-none">&check;</span>
                        <div class="space-y-1 font-mono">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-800">Заявка успешно отправлена!</h3>
                            <p class="text-xs text-emerald-700 leading-relaxed">{{ session('contact_success') }}</p>
                        </div>
                    </div>
                @endif
                @if(session('contact_error'))
                    <div class="mb-6 p-5 bg-rose-50 border border-rose-500 text-rose-900 rounded-sm flex items-start gap-3">
                        <span class="text-rose-600 font-bold text-lg leading-none">&cross;</span>
                        <div class="space-y-1 font-mono">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-rose-800">Ошибка отправки</h3>
                            <p class="text-xs text-rose-700 leading-relaxed">{{ session('contact_error') }}</p>
                        </div>
                    </div>
                @endif

                <div class="mb-8 border-b border-arch-border pb-6">
                    <span class="text-xs uppercase tracking-widest text-crimson font-mono font-bold block mb-1">
                        ФОРМА ОБРАТНОЙ СВЯЗИ
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold uppercase font-display text-arch-text">
                        Оставить заявку на съёмку
                    </h2>
                    <p class="text-xs text-neutral-600 font-mono mt-2 leading-relaxed">
                        Заполните форму, и я свяжусь с вами выбранным способом в течение 1–2 часов с подробной информацией.
                    </p>
                </div>

                <form action="{{ route('contacts.send') }}" method="POST" class="space-y-6 relative" id="booking-contact-form" x-data="{ submitting: false }" @submit="submitting = true;">
                    @csrf

                    {{-- Honeypot anti-spam field --}}
                    <input type="text" name="_hp" value="" style="display:none !important;" tabindex="-1" autocomplete="off">
                    {{-- Full Form Submitting Overlay --}}
                    <div x-show="submitting" 
                         x-cloak 
                         class="absolute inset-0 bg-white/85 backdrop-blur-[2px] z-40 flex flex-col items-center justify-center p-6 text-center rounded-sm">
                        <div class="w-12 h-12 rounded-full border-2 border-crimson border-t-transparent animate-spin mb-4"></div>
                        <h4 class="text-base font-extrabold font-display uppercase tracking-tight text-arch-text">Отправляем вашу заявку</h4>
                        <p class="text-xs font-mono text-neutral-500 mt-1 max-w-xs">Пожалуйста, не закрывайте страницу, сохраняем данные...</p>
                    </div>


                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-xs uppercase tracking-widest font-mono font-bold text-arch-text mb-2">
                            Ваше имя <span class="text-crimson">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name') }}" 
                               required 
                               placeholder="Как к вам обращаться"
                               class="w-full px-4 py-3 bg-arch-bg border border-arch-border text-arch-text text-sm font-mono focus:border-crimson focus:bg-white focus:outline-none transition-colors @error('name') border-crimson @enderror">
                        @error('name')
                            <p class="text-xs text-crimson font-mono mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Preferred Contact Method & Masked Value --}}
                    @php
                        $oldMethod = old('contact_method', 'phone');
                        $oldValue = old('contact_value', '');
                    @endphp
                    <div x-data="{
                            method: '{{ $oldMethod }}',
                            methodOpen: false,
                            value: '{{ addslashes($oldValue) }}',
                            methods: [
                                { id: 'phone', label: 'Телефон (Звонок / SMS)', shortLabel: 'Телефон', placeholder: '+7 (999) 000-00-00', helper: 'Введите номер телефона для звонка или SMS' },
                                { id: 'telegram', label: 'Telegram (@username)', shortLabel: 'Telegram', placeholder: '@username', helper: 'Укажите никнейм в Telegram' },
                                { id: 'max', label: 'MAX (Мессенджер)', shortLabel: 'MAX', placeholder: 'ID или номер в MAX', helper: 'Укажите ID или номер в мессенджере MAX' },
                                { id: 'email', label: 'Электронная почта', shortLabel: 'Email', placeholder: 'name@example.com', helper: 'Для ответа на электронную почту' }
                            ],
                            selectMethod(m) {
                                this.method = m.id;
                                this.methodOpen = false;
                                if (m.id === 'phone' && this.value) {
                                    this.value = this.formatPhone(this.value);
                                }
                            },
                            handleInput(e) {
                                let val = e.target.value;
                                if (this.method === 'phone') {
                                    this.value = this.formatPhone(val);
                                } else {
                                    this.value = val;
                                }
                                e.target.value = this.value;
                            },
                            formatPhone(input) {
                                if (!input) return '';
                                // Keep only digits
                                let digits = input.replace(/\D/g, '');
                                if (!digits.length) return '';

                                // If user started typing 8 or 7, normalize to 7
                                if (digits[0] === '8' || digits[0] === '7') {
                                    digits = digits.substring(1);
                                }

                                // Cap at 10 national digits
                                digits = digits.substring(0, 10);

                                let result = '+7';
                                if (digits.length > 0) {
                                    result += ' (' + digits.substring(0, 3);
                                }
                                if (digits.length >= 3) {
                                    result += ') ' + digits.substring(3, 6);
                                }
                                if (digits.length >= 6) {
                                    result += '-' + digits.substring(6, 8);
                                }
                                if (digits.length >= 8) {
                                    result += '-' + digits.substring(8, 10);
                                }
                                return result;
                            },
                            get current() {
                                return this.methods.find(m => m.id === this.method) || this.methods[0];
                            }
                         }"
                         class="grid grid-cols-1 sm:grid-cols-12 gap-5 items-start">

                        {{-- Left Column: Styled Contact Method Dropdown (Same Aesthetic as Package Dropdown) --}}
                        <div class="sm:col-span-5 relative" @click.away="methodOpen = false">
                            <label class="block text-xs uppercase tracking-widest font-mono font-bold text-arch-text mb-2">
                                Способ связи <span class="text-crimson">*</span>
                            </label>

                            <input type="hidden" name="contact_method" :value="method">

                            {{-- Dropdown Trigger Button --}}
                            <button type="button" 
                                    @click="methodOpen = !methodOpen" 
                                    class="w-full px-4 py-3 bg-arch-bg hover:bg-white border text-left flex items-center justify-between transition-all focus:outline-none"
                                    :class="methodOpen ? 'border-crimson bg-white ring-1 ring-crimson shadow-sm' : 'border-arch-border hover:border-neutral-400'">
                                <div class="flex items-center gap-2.5 overflow-hidden">
                                    <span class="w-2.5 h-2.5 rounded-full shrink-0 bg-crimson"></span>
                                    <span class="truncate font-mono text-sm font-bold text-arch-text uppercase tracking-tight"
                                          x-text="current.shortLabel">
                                    </span>
                                </div>
                                <svg class="w-4 h-4 text-neutral-500 transition-transform duration-200 shrink-0"
                                     :class="methodOpen ? 'rotate-180 text-crimson' : ''"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            {{-- Stylized Dropdown Menu --}}
                            <div x-show="methodOpen" 
                                 x-cloak 
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 -translate-y-1 scale-[0.99]"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave-end="opacity-0 -translate-y-1 scale-[0.99]"
                                 class="absolute z-30 left-0 right-0 mt-1.5 bg-white border border-arch-border shadow-card-depth divide-y divide-arch-border">
                                <template x-for="m in methods" :key="m.id">
                                    <div @click="selectMethod(m)"
                                         class="p-3.5 hover:bg-neutral-950 group cursor-pointer transition-colors"
                                         :class="method === m.id ? 'bg-neutral-900 text-white' : 'bg-white text-arch-text'">
                                        <div class="flex items-center justify-between">
                                            <div class="space-y-0.5">
                                                <div class="text-xs font-bold font-mono uppercase tracking-wider group-hover:text-white"
                                                     :class="method === m.id ? 'text-white' : 'text-arch-text'"
                                                     x-text="m.label"></div>
                                                <div class="text-[0.65rem] font-mono text-neutral-500 group-hover:text-neutral-400"
                                                     x-text="m.helper"></div>
                                            </div>
                                            <span x-show="method === m.id" class="text-crimson font-bold text-sm ml-2">&check;</span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Right Column: Contact Value Input with Adaptive Mask & Placeholder --}}
                        <div class="sm:col-span-7">
                            <label for="contact_value_input" class="block text-xs uppercase tracking-widest font-mono font-bold text-arch-text mb-2">
                                <span x-text="current.shortLabel + ' для связи'">Данные для связи</span> <span class="text-crimson">*</span>
                            </label>
                            <input :type="method === 'email' ? 'email' : 'text'" 
                                   name="contact_value" 
                                   id="contact_value_input" 
                                   :value="value"
                                   @input="handleInput($event)"
                                   required 
                                   :placeholder="current.placeholder"
                                   class="w-full px-4 py-3 bg-arch-bg border border-arch-border text-arch-text text-sm font-mono focus:border-crimson focus:bg-white focus:outline-none transition-colors @error('contact_value') border-crimson @enderror">
                            <span class="text-[0.68rem] text-neutral-400 font-mono mt-1 block" x-text="current.helper"></span>
                            @error('contact_value')
                                <p class="text-xs text-crimson font-mono mt-1">{{ $message }}</p>
                            @enderror
                            @error('contact_method')
                                <p class="text-xs text-crimson font-mono mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    {{-- Package Selection (Custom Stylized Dropdown) --}}
                    @php
                        $initialPackage = old('package', $selectedPackage);
                    @endphp
                    <div x-data="{ 
                            open: false, 
                            selected: '{{ addslashes($initialPackage) }}',
                            selectedLabel: '{{ addslashes($initialPackage ?: '— Выберите формат или укажите свой —') }}',
                            selectPackage(val, label) {
                                this.selected = val;
                                this.selectedLabel = label;
                                this.open = false;
                            }
                         }" 
                         @click.away="open = false" 
                         class="relative">
                        
                        <label class="block text-xs uppercase tracking-widest font-mono font-bold text-arch-text mb-2">
                            Формат съёмки / Пакет
                        </label>

                        {{-- Hidden input for standard form submission --}}
                        <input type="hidden" name="package" :value="selected" value="{{ $initialPackage }}">

                        {{-- Stylized Trigger Button --}}
                        <button type="button" 
                                @click="open = !open" 
                                class="w-full px-4 py-3.5 bg-arch-bg hover:bg-white border text-left flex items-center justify-between transition-all focus:outline-none"
                                :class="open ? 'border-crimson bg-white ring-1 ring-crimson shadow-sm' : 'border-arch-border hover:border-neutral-400'">
                            
                            <div class="flex items-center gap-3 overflow-hidden">
                                <span class="w-2.5 h-2.5 rounded-full shrink-0 transition-colors"
                                      :class="selected ? 'bg-crimson' : 'bg-neutral-300'"></span>
                                <span class="truncate font-mono text-sm"
                                      :class="selected ? 'font-bold text-arch-text uppercase tracking-tight' : 'text-neutral-500 font-normal'"
                                      x-text="selectedLabel">
                                    {{ $initialPackage ?: '— Выберите формат или укажите свой —' }}
                                </span>
                            </div>

                            <div class="flex items-center gap-2 pl-3 shrink-0">
                                <span x-show="selected" 
                                      x-cloak
                                      @click.stop="selectPackage('', '— Выберите формат или укажите свой —')" 
                                      title="Очистить выбор"
                                      class="text-xs text-neutral-400 hover:text-crimson p-1 font-mono">
                                    &times;
                                </span>
                                <svg class="w-4 h-4 text-neutral-500 transition-transform duration-200"
                                     :class="open ? 'rotate-180 text-crimson' : ''"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </button>

                        {{-- Stylized Dropdown Menu --}}
                        <div x-show="open" 
                             x-cloak 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1 scale-[0.99]"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 -translate-y-1 scale-[0.99]"
                             class="absolute z-30 left-0 right-0 mt-1.5 bg-white border border-arch-border shadow-card-depth divide-y divide-arch-border max-h-80 overflow-y-auto">
                            
                            {{-- Option: Reset / Clear --}}
                            <div @click="selectPackage('', '— Выберите формат или укажите свой —')"
                                 class="p-3.5 hover:bg-neutral-50 cursor-pointer flex items-center justify-between text-xs font-mono transition-colors"
                                 :class="selected === '' ? 'bg-neutral-100 font-bold text-arch-text selected' : 'text-neutral-500'">
                                <span>— Не выбран (уточнить в сообщении) —</span>
                                <span x-show="selected === ''" class="text-crimson font-bold">&check;</span>
                            </div>

                            {{-- Packages list --}}
                            @if($packages->count() > 0)
                                @foreach($packages as $pkg)
                                    @php
                                        $pkgTitle = $pkg->localizedTitle($locale);
                                        $pkgPrice = $pkg->formattedPrice($locale);
                                        $pkgDuration = $pkg->localizedDuration($locale);
                                        $pkgPhotos = $pkg->localizedPhotoCount($locale);
                                    @endphp
                                    <div @click="selectPackage('{{ addslashes($pkgTitle) }}', '{{ addslashes($pkgTitle) }}')"
                                         class="p-4 hover:bg-neutral-950 group cursor-pointer transition-colors"
                                         :class="selected === '{{ addslashes($pkgTitle) }}' ? 'bg-neutral-900 text-white selected' : 'bg-white text-arch-text'">
                                        
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="space-y-1">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-[0.62rem] font-mono uppercase tracking-widest text-crimson font-bold">
                                                        ФОРМАТ 0{{ $loop->iteration }}
                                                    </span>
                                                    @if($pkg->localizedSubtitle($locale))
                                                        <span class="text-[0.65rem] text-neutral-400 group-hover:text-neutral-300 font-mono hidden sm:inline">
                                                            &bull; {{ $pkg->localizedSubtitle($locale) }}
                                                        </span>
                                                    @endif
                                                </div>

                                                <h4 class="text-sm font-extrabold uppercase font-display tracking-tight group-hover:text-white"
                                                    :class="selected === '{{ addslashes($pkgTitle) }}' ? 'text-white' : 'text-arch-text'">
                                                    {{ $pkgTitle }}
                                                </h4>

                                                @if($pkgDuration || $pkgPhotos)
                                                    <div class="text-[0.68rem] text-neutral-500 group-hover:text-neutral-300 font-mono flex items-center gap-2 pt-0.5">
                                                        @if($pkgDuration) <span>{{ $pkgDuration }}</span> @endif
                                                        @if($pkgDuration && $pkgPhotos) <span>&bull;</span> @endif
                                                        @if($pkgPhotos) <span>{{ $pkgPhotos }}</span> @endif
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="flex items-center gap-3 shrink-0 pt-0.5">
                                                <span class="px-2.5 py-1 text-xs font-mono font-bold rounded-sm border transition-colors"
                                                      :class="selected === '{{ addslashes($pkgTitle) }}' 
                                                            ? 'bg-neutral-800 text-white border-neutral-700' 
                                                            : 'bg-arch-bg text-arch-text border-arch-border group-hover:bg-neutral-800 group-hover:text-white group-hover:border-neutral-700'">
                                                    {{ $pkgPrice }}
                                                </span>
                                                <span x-show="selected === '{{ addslashes($pkgTitle) }}'" 
                                                      x-cloak 
                                                      class="text-crimson font-bold text-sm">
                                                    &check;
                                                </span>
                                            </div>
                                        </div>

                                    </div>
                                @endforeach
                            @endif

                            {{-- Option: Custom project --}}
                            <div @click="selectPackage('Индивидуальный проект', 'Индивидуальный проект')"
                                 class="p-4 hover:bg-neutral-950 group cursor-pointer transition-colors"
                                 :class="selected === 'Индивидуальный проект' ? 'bg-neutral-900 text-white selected' : 'bg-white text-arch-text'">
                                <div class="flex items-center justify-between">
                                    <div class="space-y-0.5">
                                        <span class="text-[0.62rem] font-mono uppercase tracking-widest text-crimson font-bold block">
                                            СПЕЦИАЛЬНЫЙ ЗАПРОС
                                        </span>
                                        <h4 class="text-sm font-extrabold uppercase font-display tracking-tight group-hover:text-white"
                                            :class="selected === 'Индивидуальный проект' ? 'text-white' : 'text-arch-text'">
                                            Индивидуальный проект / Другое
                                        </h4>
                                        <p class="text-[0.68rem] text-neutral-500 group-hover:text-neutral-300 font-mono">
                                            Выездные серии, нестандартный тайминг, контент для брендов
                                        </p>
                                    </div>
                                    <span x-show="selected === 'Индивидуальный проект'" 
                                          x-cloak 
                                          class="text-crimson font-bold text-sm ml-3">
                                        &check;
                                    </span>
                                </div>
                            </div>

                        </div>

                    </div>

                    {{-- Message / Details --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="message" class="block text-xs uppercase tracking-widest font-mono font-bold text-arch-text">
                                Сообщение / пожелания к съёмке
                            </label>
                            <span class="text-[0.68rem] text-neutral-400 font-mono">Необязательно</span>
                        </div>
                        <textarea name="message" 
                                  id="message" 
                                  rows="4" 
                                  placeholder="Расскажите о вашей идее: желаемые даты, локация, количество участников или стиль кадра..."
                                  class="w-full px-4 py-3 bg-arch-bg border border-arch-border text-arch-text text-sm font-mono focus:border-crimson focus:bg-white focus:outline-none transition-colors @error('message') border-crimson @enderror">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="text-xs text-crimson font-mono mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Submit Button & Response Time note --}}
                    <div class="pt-2 space-y-4">
                        <button type="submit" 
                                :disabled="submitting"
                                :class="submitting ? 'opacity-85 cursor-wait' : ''"
                                class="btn-crimson w-full py-4 text-xs font-bold uppercase tracking-wider font-mono shadow-crimson-btn relative overflow-hidden transition-all">
                            
                            {{-- Normal State Content --}}
                            <span x-show="!submitting" class="inline-flex items-center justify-center gap-2">
                                <span>Отправить заявку</span>
                                <span>&rarr;</span>
                            </span>

                            {{-- Submitting Loading State Content --}}
                            <span x-show="submitting" x-cloak class="inline-flex items-center justify-center gap-2.5">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="animate-pulse">Отправка заявки... Пожалуйста, подождите</span>
                            </span>
                        </button>

                        <div class="flex items-center justify-between text-[0.68rem] text-neutral-500 font-mono">
                            <span x-show="!submitting">⏱ Ответ в течение 1–2 часов</span>
                            <span x-show="submitting" x-cloak class="text-crimson font-bold animate-pulse">Передаём данные на сервер...</span>
                            <span>Конфиденциальность гарантирована</span>
                        </div>
                    </div>

                </form>

            </div>

        </div>

    </div>
</div>
@endsection
