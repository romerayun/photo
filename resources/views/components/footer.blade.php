<footer class="bg-apple-black border-t border-white/10 mt-28 pt-16 pb-12 text-apple-textMuted">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 md:grid-cols-12 gap-10 pb-14 border-b border-white/10">
            
            {{-- Col 1: Brand & Statement --}}
            <div class="md:col-span-5 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-7 h-7 rounded-full bg-white text-black text-xs font-bold flex items-center justify-center">
                        RY
                    </div>
                    <span class="text-xl font-bold tracking-tight text-white block">
                        {{ __('site.author_name') }}
                    </span>
                </div>
                <p class="text-xs text-apple-textMuted max-w-sm leading-relaxed">
                    {{ __('site.footer_tagline') }}
                </p>
                <div class="inline-flex items-center gap-2 text-[0.68rem] uppercase tracking-widest text-neutral-500 font-mono">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span>{{ __('site.location') }} &bull; AVAILABLE FOR BOOKING</span>
                </div>
            </div>

            {{-- Col 2: Navigation --}}
            <div class="md:col-span-3 space-y-3">
                <span class="text-[0.68rem] uppercase tracking-widest text-neutral-400 font-semibold block">
                    Навигация
                </span>
                <ul class="space-y-2 text-xs">
                    <li><a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="hover:text-white transition-colors">{{ __('site.nav_home') }}</a></li>
                    <li><a href="{{ route('portfolio.index', ['locale' => app()->getLocale()]) }}" class="hover:text-white transition-colors">{{ __('site.nav_portfolio') }}</a></li>
                    <li><a href="{{ route('pricing.index', ['locale' => app()->getLocale()]) }}" class="hover:text-white transition-colors">{{ __('site.nav_pricing') }}</a></li>
                    <li><a href="{{ route('about.index', ['locale' => app()->getLocale()]) }}" class="hover:text-white transition-colors">{{ __('site.nav_about') }}</a></li>
                    <li><a href="{{ route('contacts.index', ['locale' => app()->getLocale()]) }}" class="hover:text-white transition-colors">{{ __('site.nav_contacts') }}</a></li>
                </ul>
            </div>

            {{-- Col 3: Direct Concierge Connect --}}
            <div class="md:col-span-4 space-y-3">
                <span class="text-[0.68rem] uppercase tracking-widest text-neutral-400 font-semibold block">
                    Прямая связь
                </span>
                <div class="space-y-2.5 text-xs">
                    @if(\App\Models\Setting::hasTelegram())
                        <div>
                            <a href="{{ \App\Models\Setting::telegramUrl() }}" target="_blank" rel="noopener" class="text-white hover:text-apple-blue inline-flex items-center gap-2 transition-colors">
                                <span class="w-2 h-2 rounded-full bg-apple-blue"></span>
                                <span>Telegram:</span>
                                <span class="font-semibold underline underline-offset-4">{{ \App\Models\Setting::telegramHandle() }}</span>
                            </a>
                        </div>
                    @endif

                    @if(\App\Models\Setting::hasPhone())
                        <div>
                            <a href="{{ \App\Models\Setting::phoneLink() }}" class="text-white hover:text-apple-blue inline-flex items-center gap-2 transition-colors">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span>Телефон:</span>
                                <span class="font-semibold underline underline-offset-4">{{ \App\Models\Setting::phoneDisplay() }}</span>
                            </a>
                        </div>
                    @endif

                    @if(!\App\Models\Setting::hasTelegram() && !\App\Models\Setting::hasPhone())
                        <p class="text-[0.75rem] text-neutral-500 italic">
                            {{ __('site.contacts_not_configured') }}
                        </p>
                    @endif

                    <div class="pt-2">
                        <a href="{{ route('contacts.index', ['locale' => app()->getLocale()]) }}" class="text-[0.7rem] uppercase tracking-widest text-apple-blue hover:text-white font-semibold inline-flex items-center gap-1 transition-colors">
                            <span>{{ __('site.hero_cta') }}</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>

        {{-- Bottom Copyright & Admin link --}}
        <div class="pt-8 flex flex-col sm:flex-row items-center justify-between text-[0.72rem] text-neutral-500 gap-4">
            <div>
                &copy; {{ date('Y') }} {{ __('site.author_name') }}. {{ __('site.copyright') }}
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('sitemap') }}" class="hover:text-white transition-colors">Sitemap</a>
                <span>&bull;</span>
                <a href="{{ route('admin.login') }}" class="hover:text-white transition-colors">Вход для автора</a>
            </div>
        </div>

    </div>
</footer>
