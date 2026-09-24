<header x-data="{ mobileOpen: false }" class="sticky top-0 z-40 bg-cine-black/90 backdrop-blur-xl border-b border-cine-border transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            
            {{-- Brand / Monogram & Name --}}
            <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" 
               class="group flex items-center gap-3 focus:outline-none">
                <div class="w-9 h-9 bg-crimson flex items-center justify-center text-xs font-extrabold text-white tracking-wider shadow-crimson-btn">
                    RY
                </div>
                <div class="flex flex-col">
                    <span class="text-base font-extrabold tracking-tight text-white font-display group-hover:text-crimson transition-colors">
                        {{ __('site.author_name') }}
                    </span>
                    <span class="text-[0.62rem] uppercase tracking-widest text-neutral-400 font-mono">
                        {{ __('site.location') }} &bull; PORTFOLIO
                    </span>
                </div>
            </a>

            {{-- Desktop Navigation Links --}}
            <nav class="hidden md:flex items-center space-x-8 text-xs uppercase tracking-widest font-bold" aria-label="Main Navigation">
                <a href="{{ route('portfolio.index', ['locale' => app()->getLocale()]) }}" 
                   class="transition-colors hover:text-crimson {{ request()->routeIs('portfolio.*') || request()->routeIs('series.*') ? 'text-crimson' : 'text-neutral-300' }}">
                    {{ __('site.nav_portfolio') }}
                </a>
                <a href="{{ route('pricing.index', ['locale' => app()->getLocale()]) }}" 
                   class="transition-colors hover:text-crimson {{ request()->routeIs('pricing.*') ? 'text-crimson' : 'text-neutral-300' }}">
                    {{ __('site.nav_pricing') }}
                </a>
                <a href="{{ route('about.index', ['locale' => app()->getLocale()]) }}" 
                   class="transition-colors hover:text-crimson {{ request()->routeIs('about.*') ? 'text-crimson' : 'text-neutral-300' }}">
                    {{ __('site.nav_about') }}
                </a>
                <a href="{{ route('contacts.index', ['locale' => app()->getLocale()]) }}" 
                   class="transition-colors hover:text-crimson {{ request()->routeIs('contacts.*') ? 'text-crimson' : 'text-neutral-300' }}">
                    {{ __('site.nav_contacts') }}
                </a>
            </nav>

            {{-- Right Controls: Language Switcher + Crimson CTA --}}
            <div class="hidden md:flex items-center space-x-6">
                {{-- Language Toggle --}}
                <div class="flex items-center border border-white/15 px-2 py-1 text-[0.7rem] font-mono tracking-widest uppercase">
                    <a href="{{ \App\Support\LocaleHelper::alternateUrl('ru') }}" 
                       class="px-1.5 py-0.5 transition-colors {{ app()->getLocale() === 'ru' ? 'bg-crimson text-white font-bold' : 'text-neutral-400 hover:text-white' }}">
                        RU
                    </a>
                    <span class="text-neutral-600 mx-1">/</span>
                    <a href="{{ \App\Support\LocaleHelper::alternateUrl('en') }}" 
                       class="px-1.5 py-0.5 transition-colors {{ app()->getLocale() === 'en' ? 'bg-crimson text-white font-bold' : 'text-neutral-400 hover:text-white' }}">
                        EN
                    </a>
                </div>

                {{-- Primary Red CTA Button --}}
                <a href="{{ route('contacts.index', ['locale' => app()->getLocale()]) }}" 
                   class="btn-crimson px-6 py-3 text-xs tracking-wider font-bold">
                    <span>{{ __('site.nav_discuss') }}</span>
                    <span>&nearr;</span>
                </a>
            </div>

            {{-- Mobile Menu Trigger --}}
            <div class="flex items-center md:hidden space-x-3">
                <div class="flex items-center border border-white/15 px-2 py-1 text-[0.68rem] font-mono">
                    <a href="{{ \App\Support\LocaleHelper::alternateUrl('ru') }}" 
                       class="px-1 {{ app()->getLocale() === 'ru' ? 'text-crimson font-bold' : 'text-neutral-400' }}">RU</a>
                    <span class="text-neutral-600">/</span>
                    <a href="{{ \App\Support\LocaleHelper::alternateUrl('en') }}" 
                       class="px-1 {{ app()->getLocale() === 'en' ? 'text-crimson font-bold' : 'text-neutral-400' }}">EN</a>
                </div>

                <button @click="mobileOpen = !mobileOpen" 
                        type="button" 
                        class="p-2 text-white hover:text-crimson focus:outline-none" 
                        :aria-expanded="mobileOpen.toString()" 
                        aria-label="{{ __('site.menu') }}">
                    <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16"></path>
                    </svg>
                    <svg x-show="mobileOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

        </div>
    </div>

    {{-- Mobile Menu Dropdown --}}
    <div x-show="mobileOpen" 
         x-cloak 
         @click.away="mobileOpen = false"
         class="md:hidden border-b border-cine-border bg-cine-surface px-6 pt-4 pb-8 space-y-4">
        <nav class="flex flex-col space-y-3 font-display uppercase tracking-wider text-sm font-bold">
            <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" 
               @click="mobileOpen = false"
               class="text-white hover:text-crimson py-1">
                {{ __('site.nav_home') }}
            </a>
            <a href="{{ route('portfolio.index', ['locale' => app()->getLocale()]) }}" 
               @click="mobileOpen = false"
               class="text-white hover:text-crimson py-1">
                {{ __('site.nav_portfolio') }}
            </a>
            <a href="{{ route('pricing.index', ['locale' => app()->getLocale()]) }}" 
               @click="mobileOpen = false"
               class="text-white hover:text-crimson py-1">
                {{ __('site.nav_pricing') }}
            </a>
            <a href="{{ route('about.index', ['locale' => app()->getLocale()]) }}" 
               @click="mobileOpen = false"
               class="text-white hover:text-crimson py-1">
                {{ __('site.nav_about') }}
            </a>
            <a href="{{ route('contacts.index', ['locale' => app()->getLocale()]) }}" 
               @click="mobileOpen = false"
               class="text-white hover:text-crimson py-1">
                {{ __('site.nav_contacts') }}
            </a>
        </nav>

        <div class="pt-4 border-t border-cine-border">
            <a href="{{ route('contacts.index', ['locale' => app()->getLocale()]) }}" 
               @click="mobileOpen = false"
               class="btn-crimson block w-full text-center py-3 text-xs tracking-wider">
                <span>{{ __('site.nav_discuss') }}</span>
                <span>&nearr;</span>
            </a>
        </div>
    </div>
</header>
