<footer class="bg-cine-black border-t border-cine-border pt-20 pb-10 text-neutral-400 overflow-hidden relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Top Call to Action Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-12 gap-12 pb-16  border-cine-border items-start">
            
            {{-- Big Headline --}}
            <div class="md:col-span-6 space-y-4">
                <span class="text-[0.68rem] uppercase tracking-widest text-crimson font-mono font-bold block">
                    Фотосессии в Иркутске
                </span>
                <h2 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white font-display uppercase leading-tight">
                    ОБСУДИМ ВАШУ<br>
                    СЪЁМКУ?
                </h2>
                <p class="text-neutral-400 text-sm sm:text-base leading-relaxed max-w-xl">
                    Напишите, какие фотографии вам хочется получить и когда удобно сниматься. Если идеи пока нет — вместе выберем формат и место.
                </p>
                <div class="pt-2 flex flex-wrap items-center gap-3">
                    <a href="{{ \App\Models\Setting::telegramUrl() ?? 'https://t.me/romerayun' }}" 
                       target="_blank" 
                       rel="noopener" 
                       class="btn-crimson px-7 py-3.5 text-xs font-bold font-mono tracking-wider uppercase inline-flex items-center gap-2">
                        <span>НАПИСАТЬ В TELEGRAM</span>
                        <span>&rarr;</span>
                    </a>
                    <a href="{{ route('contacts.index') }}#feedback-form" 
                       class="px-6 py-3.5 bg-neutral-900 hover:bg-neutral-800 text-white font-mono text-xs uppercase tracking-wider font-bold border border-cine-border hover:border-neutral-500 transition-colors inline-flex items-center gap-2">
                        <span>ОСТАВИТЬ ЗАЯВКУ</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

            {{-- Direct Contacts & Details --}}
            <div class="md:col-span-3 space-y-4 text-xs">
                <span class="text-[0.68rem] uppercase tracking-widest text-neutral-500 font-mono font-bold block">
                    Контакты
                </span>
                <div class="space-y-3">
                    @if(\App\Models\Setting::hasTelegram())
                        <div>
                            <span class="text-neutral-500 block text-[0.68rem] uppercase font-mono">Telegram</span>
                            <a href="{{ \App\Models\Setting::telegramUrl() }}" target="_blank" rel="noopener" class="text-white hover:text-crimson font-bold text-sm transition-colors inline-flex items-center gap-1.5 mt-0.5">
                                <span>{{ \App\Models\Setting::telegramHandle() }}</span>
                                <span class="text-xs text-crimson">&nearr;</span>
                            </a>
                        </div>
                    @endif

                    <div>
                        <span class="text-neutral-500 block text-[0.68rem] uppercase font-mono">Телефон</span>
                        <a href="{{ \App\Models\Setting::phoneLink() }}" class="text-white hover:text-crimson font-bold text-sm font-mono transition-colors block mt-0.5">
                            {{ \App\Models\Setting::phoneDisplay() }}
                        </a>
                    </div>

                    <div>
                        <span class="text-neutral-500 block text-[0.68rem] uppercase font-mono">Срок ответа</span>
                        <div class="text-white font-mono text-xs flex items-center gap-2 mt-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                            <span>1–2 часа (09:00–21:00)</span>
                        </div>
                    </div>

                    <div>
                        <span class="text-neutral-500 block text-[0.68rem] uppercase font-mono">Локация</span>
                        <span class="text-white font-medium block mt-0.5">{{ __('site.location') }}</span>
                    </div>
                </div>
            </div>

            {{-- Navigation Links Column --}}
            <div class="md:col-span-3 space-y-3 text-xs">
                <span class="text-[0.68rem] uppercase tracking-widest text-neutral-500 font-mono font-bold block">
                    Навигация
                </span>
                <ul class="space-y-2 uppercase tracking-wider font-bold">
                    <li><a href="{{ route('home') }}" class="text-neutral-400 hover:text-white transition-colors">{{ __('site.nav_home') }}</a></li>
                    <li><a href="{{ route('portfolio.index') }}" class="text-neutral-400 hover:text-white transition-colors">{{ __('site.nav_portfolio') }}</a></li>
                    <li><a href="{{ route('articles.index') }}" class="text-neutral-400 hover:text-white transition-colors">{{ __('site.nav_articles') }}</a></li>
                    <li><a href="{{ route('pricing.index') }}" class="text-neutral-400 hover:text-white transition-colors">{{ __('site.nav_pricing') }}</a></li>
                    <li><a href="{{ route('about.index') }}" class="text-neutral-400 hover:text-white transition-colors">{{ __('site.nav_about') }}</a></li>
                    <li><a href="{{ route('contacts.index') }}" class="text-neutral-400 hover:text-white transition-colors">{{ __('site.nav_contacts') }}</a></li>
                </ul>
            </div>

        </div>

        {{-- Bottom Copyright and Sitemap --}}
        <div class="pt-8 mt-12 border-t border-cine-border flex flex-col sm:flex-row items-center justify-between text-xs text-neutral-500 gap-4 font-mono">
            <div>
                &copy; {{ date('Y') }} {{ __('site.author_name') }}. {{ __('site.copyright') }}
            </div>
            <div>
                <a href="{{ route('sitemap.page') }}" class="hover:text-white transition-colors">Карта сайта</a>
            </div>
        </div>

    </div>
</footer>
