<footer class="bg-surface border-t border-editorial-border mt-20 pt-16 pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-12 pb-16 border-b border-editorial-border">
            
            {{-- Col 1: Brand & Philosophy --}}
            <div class="md:col-span-5 space-y-4">
                <span class="font-serif text-2xl tracking-tight text-graphite-950 block">
                    {{ __('site.author_name') }}
                </span>
                <p class="text-sm text-graphite-600 max-w-sm leading-relaxed">
                    {{ __('site.footer_tagline') }}
                </p>
                <div class="text-xs uppercase tracking-widest text-graphite-500 pt-2">
                    {{ __('site.location') }} &bull; {{ date('Y') }}
                </div>
            </div>

            {{-- Col 2: Navigation --}}
            <div class="md:col-span-3 space-y-3">
                <span class="text-xs uppercase tracking-widest text-graphite-400 font-medium block">
                    Навигация
                </span>
                <ul class="space-y-2 text-sm text-graphite-700">
                    <li><a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="hover:text-terracotta transition-colors">{{ __('site.nav_home') }}</a></li>
                    <li><a href="{{ route('portfolio.index', ['locale' => app()->getLocale()]) }}" class="hover:text-terracotta transition-colors">{{ __('site.nav_portfolio') }}</a></li>
                    <li><a href="{{ route('pricing.index', ['locale' => app()->getLocale()]) }}" class="hover:text-terracotta transition-colors">{{ __('site.nav_pricing') }}</a></li>
                    <li><a href="{{ route('about.index', ['locale' => app()->getLocale()]) }}" class="hover:text-terracotta transition-colors">{{ __('site.nav_about') }}</a></li>
                    <li><a href="{{ route('contacts.index', ['locale' => app()->getLocale()]) }}" class="hover:text-terracotta transition-colors">{{ __('site.nav_contacts') }}</a></li>
                </ul>
            </div>

            {{-- Col 3: Communication --}}
            <div class="md:col-span-4 space-y-3">
                <span class="text-xs uppercase tracking-widest text-graphite-400 font-medium block">
                    Связь
                </span>
                <div class="space-y-2 text-sm">
                    @if(\App\Models\Setting::hasTelegram())
                        <div>
                            <a href="{{ \App\Models\Setting::telegramUrl() }}" target="_blank" rel="noopener" class="text-graphite-800 hover:text-terracotta inline-flex items-center gap-1.5 transition-colors">
                                <span>Telegram:</span>
                                <span class="font-medium underline underline-offset-4">{{ \App\Models\Setting::telegramHandle() }}</span>
                            </a>
                        </div>
                    @endif

                    @if(\App\Models\Setting::hasPhone())
                        <div>
                            <a href="{{ \App\Models\Setting::phoneLink() }}" class="text-graphite-800 hover:text-terracotta inline-flex items-center gap-1.5 transition-colors">
                                <span>Телефон:</span>
                                <span class="font-medium underline underline-offset-4">{{ \App\Models\Setting::phoneDisplay() }}</span>
                            </a>
                        </div>
                    @endif

                    @if(!\App\Models\Setting::hasTelegram() && !\App\Models\Setting::hasPhone())
                        <p class="text-xs text-graphite-500 italic">
                            {{ __('site.contacts_not_configured') }}
                        </p>
                    @endif

                    <div class="pt-2">
                        <a href="{{ route('contacts.index', ['locale' => app()->getLocale()]) }}" class="text-xs uppercase tracking-widest text-terracotta hover:text-terracotta-dark font-medium underline underline-offset-4">
                            {{ __('site.hero_cta') }} &rarr;
                        </a>
                    </div>
                </div>
            </div>

        </div>

        {{-- Bottom Copyright & Admin link --}}
        <div class="pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-graphite-500 gap-4">
            <div>
                &copy; {{ date('Y') }} {{ __('site.author_name') }}. {{ __('site.copyright') }}
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('sitemap') }}" class="hover:text-graphite-800 transition-colors">Sitemap</a>
                <span>&bull;</span>
                <a href="{{ route('admin.login') }}" class="hover:text-graphite-800 transition-colors">Вход для автора</a>
            </div>
        </div>
    </div>
</footer>
