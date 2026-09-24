<footer class="bg-cine-black border-t border-cine-border pt-20 pb-10 text-neutral-400 overflow-hidden relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Top Call to Action Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-12 gap-12 pb-16 border-b border-cine-border items-start">
            
            {{-- Big Headline --}}
            <div class="md:col-span-6 space-y-4">
                <span class="text-[0.68rem] uppercase tracking-widest text-crimson font-mono font-bold block">
                    {{ __('site.location') }} &bull; НАЧАТЬ ПРОЕКТ
                </span>
                <h2 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white font-display uppercase leading-tight">
                    Готовы начать?<br>
                    <span class="text-neutral-400">Напишите мне сегодня.</span>
                </h2>
                <div class="pt-2">
                    <a href="{{ route('contacts.index') }}" 
                       class="btn-crimson px-8 py-4 text-xs font-bold">
                        <span>{{ __('site.hero_cta') }}</span>
                        <span>&nearr;</span>
                    </a>
                </div>
            </div>

            {{-- Direct Contacts & Details --}}
            <div class="md:col-span-3 space-y-4 text-xs">
                <span class="text-[0.68rem] uppercase tracking-widest text-neutral-500 font-mono font-bold block">
                    Прямая связь
                </span>
                <div class="space-y-3">
                    @if(\App\Models\Setting::hasTelegram())
                        <div>
                            <span class="text-neutral-500 block text-[0.68rem] uppercase font-mono">Telegram</span>
                            <a href="{{ \App\Models\Setting::telegramUrl() }}" target="_blank" rel="noopener" class="text-white hover:text-crimson font-bold text-sm transition-colors">
                                {{ \App\Models\Setting::telegramHandle() }} &nearr;
                            </a>
                        </div>
                    @endif

                    @if(\App\Models\Setting::hasPhone())
                        <div>
                            <span class="text-neutral-500 block text-[0.68rem] uppercase font-mono">Телефон</span>
                            <a href="{{ \App\Models\Setting::phoneLink() }}" class="text-white hover:text-crimson font-bold text-sm transition-colors">
                                {{ \App\Models\Setting::phoneDisplay() }}
                            </a>
                        </div>
                    @endif

                    <div>
                        <span class="text-neutral-500 block text-[0.68rem] uppercase font-mono">Локация</span>
                        <span class="text-white font-medium">{{ __('site.location') }}, Сибирь</span>
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
                <a href="{{ route('sitemap') }}" class="hover:text-white transition-colors">Карта сайта</a>
            </div>
        </div>

    </div>
</footer>
