<header x-data="{ mobileOpen: false }" class="sticky top-0 z-40 bg-canvas/90 backdrop-blur-md border-b border-editorial-border transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            
            {{-- Brand / Logo --}}
            <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="group flex flex-col focus:outline-none focus-visible:ring-2 focus-visible:ring-terracotta rounded-sm">
                <span class="font-serif text-2xl tracking-tight text-graphite-950 font-normal group-hover:text-terracotta transition-colors duration-200">
                    {{ __('site.author_name') }}
                </span>
                <span class="text-[0.68rem] tracking-editorial uppercase text-graphite-500 font-sans">
                    {{ __('site.author_role') }}
                </span>
            </a>

            {{-- Desktop Navigation Links --}}
            <nav class="hidden md:flex items-center space-x-8" aria-label="Main Navigation">
                <a href="{{ route('portfolio.index', ['locale' => app()->getLocale()]) }}" 
                   class="text-sm font-medium tracking-wide transition-colors {{ request()->routeIs('portfolio.*') || request()->routeIs('series.*') ? 'text-terracotta font-semibold' : 'text-graphite-700 hover:text-graphite-950' }}">
                    {{ __('site.nav_portfolio') }}
                </a>
                <a href="{{ route('pricing.index', ['locale' => app()->getLocale()]) }}" 
                   class="text-sm font-medium tracking-wide transition-colors {{ request()->routeIs('pricing.*') ? 'text-terracotta font-semibold' : 'text-graphite-700 hover:text-graphite-950' }}">
                    {{ __('site.nav_pricing') }}
                </a>
                <a href="{{ route('about.index', ['locale' => app()->getLocale()]) }}" 
                   class="text-sm font-medium tracking-wide transition-colors {{ request()->routeIs('about.*') ? 'text-terracotta font-semibold' : 'text-graphite-700 hover:text-graphite-950' }}">
                    {{ __('site.nav_about') }}
                </a>
                <a href="{{ route('contacts.index', ['locale' => app()->getLocale()]) }}" 
                   class="text-sm font-medium tracking-wide transition-colors {{ request()->routeIs('contacts.*') ? 'text-terracotta font-semibold' : 'text-graphite-700 hover:text-graphite-950' }}">
                    {{ __('site.nav_contacts') }}
                </a>
            </nav>

            {{-- Right Controls: Language Switcher + CTA --}}
            <div class="hidden md:flex items-center space-x-6">
                {{-- Language Switcher --}}
                <div class="flex items-center text-xs tracking-wider border border-editorial-border rounded-full px-2.5 py-1 bg-surface" role="group" aria-label="Language selection">
                    <a href="{{ \App\Support\LocaleHelper::alternateUrl('ru') }}" 
                       class="px-1.5 py-0.5 rounded-full transition-colors {{ app()->getLocale() === 'ru' ? 'bg-graphite-900 text-white font-medium' : 'text-graphite-600 hover:text-graphite-900' }}">
                        RU
                    </a>
                    <span class="text-graphite-300 mx-0.5">/</span>
                    <a href="{{ \App\Support\LocaleHelper::alternateUrl('en') }}" 
                       class="px-1.5 py-0.5 rounded-full transition-colors {{ app()->getLocale() === 'en' ? 'bg-graphite-900 text-white font-medium' : 'text-graphite-600 hover:text-graphite-900' }}">
                        EN
                    </a>
                </div>

                {{-- Primary CTA button --}}
                <a href="{{ route('contacts.index', ['locale' => app()->getLocale()]) }}" 
                   class="inline-flex items-center px-4 py-2 border border-graphite-900 text-xs uppercase tracking-widest font-medium text-graphite-900 hover:bg-graphite-900 hover:text-white transition-all duration-200">
                    {{ __('site.nav_discuss') }}
                </a>
            </div>

            {{-- Mobile Menu Trigger --}}
            <div class="flex items-center md:hidden space-x-3">
                {{-- Mobile Language Switcher --}}
                <div class="flex items-center text-xs tracking-wider border border-editorial-border rounded-full px-2 py-0.5 bg-surface">
                    <a href="{{ \App\Support\LocaleHelper::alternateUrl('ru') }}" 
                       class="px-1 py-0.5 {{ app()->getLocale() === 'ru' ? 'font-bold text-graphite-900' : 'text-graphite-500' }}">RU</a>
                    <span class="text-graphite-300">/</span>
                    <a href="{{ \App\Support\LocaleHelper::alternateUrl('en') }}" 
                       class="px-1 py-0.5 {{ app()->getLocale() === 'en' ? 'font-bold text-graphite-900' : 'text-graphite-500' }}">EN</a>
                </div>

                <button @click="mobileOpen = !mobileOpen" 
                        type="button" 
                        class="p-2 text-graphite-800 hover:text-graphite-950 focus:outline-none focus-visible:ring-2 focus-visible:ring-terracotta" 
                        :aria-expanded="mobileOpen.toString()" 
                        aria-label="{{ __('site.menu') }}">
                    <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7h16M4 12h16M4 17h16"></path>
                    </svg>
                    <svg x-show="mobileOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

        </div>
    </div>

    {{-- Mobile Dropdown Drawer --}}
    <div x-show="mobileOpen" 
         x-cloak 
         @click.away="mobileOpen = false"
         class="md:hidden border-b border-editorial-border bg-canvas px-6 pt-4 pb-8 space-y-4 transition-all">
        <nav class="flex flex-col space-y-4" aria-label="Mobile Navigation">
            <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" 
               @click="mobileOpen = false"
               class="text-lg font-serif text-graphite-900 hover:text-terracotta">
                {{ __('site.nav_home') }}
            </a>
            <a href="{{ route('portfolio.index', ['locale' => app()->getLocale()]) }}" 
               @click="mobileOpen = false"
               class="text-lg font-serif text-graphite-900 hover:text-terracotta">
                {{ __('site.nav_portfolio') }}
            </a>
            <a href="{{ route('pricing.index', ['locale' => app()->getLocale()]) }}" 
               @click="mobileOpen = false"
               class="text-lg font-serif text-graphite-900 hover:text-terracotta">
                {{ __('site.nav_pricing') }}
            </a>
            <a href="{{ route('about.index', ['locale' => app()->getLocale()]) }}" 
               @click="mobileOpen = false"
               class="text-lg font-serif text-graphite-900 hover:text-terracotta">
                {{ __('site.nav_about') }}
            </a>
            <a href="{{ route('contacts.index', ['locale' => app()->getLocale()]) }}" 
               @click="mobileOpen = false"
               class="text-lg font-serif text-graphite-900 hover:text-terracotta">
                {{ __('site.nav_contacts') }}
            </a>
        </nav>

        <div class="pt-4 border-t border-editorial-border">
            <a href="{{ route('contacts.index', ['locale' => app()->getLocale()]) }}" 
               @click="mobileOpen = false"
               class="block w-full text-center py-3 border border-graphite-900 bg-graphite-900 text-white text-xs uppercase tracking-widest font-medium">
                {{ __('site.nav_discuss') }}
            </a>
        </div>
    </div>
</header>
