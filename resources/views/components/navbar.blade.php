<header x-data="{ mobileOpen: false }" class="sticky top-0 z-40 transition-all duration-300 px-4 sm:px-6 lg:px-8 py-3">
    <div class="max-w-7xl mx-auto">
        <div class="apple-glass rounded-full px-4 sm:px-6 py-2.5 sm:py-3 flex items-center justify-between shadow-apple-card border border-white/10">
            
            {{-- Brand / Logo --}}
            <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" 
               class="group flex items-center gap-3 focus:outline-none focus-visible:ring-2 focus-visible:ring-apple-blue rounded-full">
                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-neutral-800 to-neutral-700 border border-white/20 flex items-center justify-center text-xs font-bold text-white shadow-inner">
                    RY
                </div>
                <div class="flex flex-col">
                    <span class="text-sm font-semibold tracking-tight text-white group-hover:text-apple-blue transition-colors duration-200">
                        {{ __('site.author_name') }}
                    </span>
                    <span class="text-[0.62rem] uppercase tracking-widest text-apple-textMuted font-mono">
                        {{ __('site.location') }} &bull; PHOTO
                    </span>
                </div>
            </a>

            {{-- Desktop Navigation (Apple Segmented Style) --}}
            <nav class="hidden md:flex items-center space-x-1 bg-neutral-900/60 p-1 rounded-full border border-white/5" aria-label="Main Navigation">
                <a href="{{ route('portfolio.index', ['locale' => app()->getLocale()]) }}" 
                   class="px-4 py-1.5 text-xs font-medium rounded-full transition-all duration-200 {{ request()->routeIs('portfolio.*') || request()->routeIs('series.*') ? 'bg-white/15 text-white font-semibold shadow-sm' : 'text-apple-textMuted hover:text-white hover:bg-white/5' }}">
                    {{ __('site.nav_portfolio') }}
                </a>
                <a href="{{ route('pricing.index', ['locale' => app()->getLocale()]) }}" 
                   class="px-4 py-1.5 text-xs font-medium rounded-full transition-all duration-200 {{ request()->routeIs('pricing.*') ? 'bg-white/15 text-white font-semibold shadow-sm' : 'text-apple-textMuted hover:text-white hover:bg-white/5' }}">
                    {{ __('site.nav_pricing') }}
                </a>
                <a href="{{ route('about.index', ['locale' => app()->getLocale()]) }}" 
                   class="px-4 py-1.5 text-xs font-medium rounded-full transition-all duration-200 {{ request()->routeIs('about.*') ? 'bg-white/15 text-white font-semibold shadow-sm' : 'text-apple-textMuted hover:text-white hover:bg-white/5' }}">
                    {{ __('site.nav_about') }}
                </a>
                <a href="{{ route('contacts.index', ['locale' => app()->getLocale()]) }}" 
                   class="px-4 py-1.5 text-xs font-medium rounded-full transition-all duration-200 {{ request()->routeIs('contacts.*') ? 'bg-white/15 text-white font-semibold shadow-sm' : 'text-apple-textMuted hover:text-white hover:bg-white/5' }}">
                    {{ __('site.nav_contacts') }}
                </a>
            </nav>

            {{-- Right Controls: Language Switcher + Apple Pill CTA --}}
            <div class="hidden md:flex items-center space-x-4">
                {{-- Language Toggle (iOS Segmented Style) --}}
                <div class="flex items-center bg-neutral-900/80 p-0.5 rounded-full border border-white/10 text-[0.7rem] font-medium" role="group" aria-label="Language selection">
                    <a href="{{ \App\Support\LocaleHelper::alternateUrl('ru') }}" 
                       class="px-2.5 py-1 rounded-full transition-all {{ app()->getLocale() === 'ru' ? 'bg-white text-black font-semibold shadow' : 'text-neutral-400 hover:text-white' }}">
                        RU
                    </a>
                    <a href="{{ \App\Support\LocaleHelper::alternateUrl('en') }}" 
                       class="px-2.5 py-1 rounded-full transition-all {{ app()->getLocale() === 'en' ? 'bg-white text-black font-semibold shadow' : 'text-neutral-400 hover:text-white' }}">
                        EN
                    </a>
                </div>

                {{-- Primary Apple CTA Button --}}
                <a href="{{ route('contacts.index', ['locale' => app()->getLocale()]) }}" 
                   class="inline-flex items-center justify-center px-5 py-2 bg-white text-black hover:bg-neutral-200 active:scale-95 text-xs uppercase tracking-wider font-semibold rounded-full transition-all duration-200 shadow-[0_0_20px_rgba(255,255,255,0.2)]">
                    {{ __('site.nav_discuss') }}
                </a>
            </div>

            {{-- Mobile Trigger --}}
            <div class="flex items-center md:hidden space-x-2">
                {{-- Mobile Language Switcher --}}
                <div class="flex items-center bg-neutral-900/80 p-0.5 rounded-full border border-white/10 text-[0.68rem] font-medium">
                    <a href="{{ \App\Support\LocaleHelper::alternateUrl('ru') }}" 
                       class="px-2 py-0.5 rounded-full {{ app()->getLocale() === 'ru' ? 'bg-white text-black font-bold' : 'text-neutral-400' }}">RU</a>
                    <a href="{{ \App\Support\LocaleHelper::alternateUrl('en') }}" 
                       class="px-2 py-0.5 rounded-full {{ app()->getLocale() === 'en' ? 'bg-white text-black font-bold' : 'text-neutral-400' }}">EN</a>
                </div>

                <button @click="mobileOpen = !mobileOpen" 
                        type="button" 
                        class="p-2 text-neutral-300 hover:text-white focus:outline-none" 
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

    {{-- Mobile Overlay Menu --}}
    <div x-show="mobileOpen" 
         x-cloak 
         @click.away="mobileOpen = false"
         class="md:hidden mt-3 mx-auto max-w-7xl apple-glass rounded-3xl p-6 space-y-4 shadow-2xl border border-white/15">
        <nav class="flex flex-col space-y-3">
            <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" 
               @click="mobileOpen = false"
               class="text-base font-medium text-white hover:text-apple-blue py-1">
                {{ __('site.nav_home') }}
            </a>
            <a href="{{ route('portfolio.index', ['locale' => app()->getLocale()]) }}" 
               @click="mobileOpen = false"
               class="text-base font-medium text-white hover:text-apple-blue py-1">
                {{ __('site.nav_portfolio') }}
            </a>
            <a href="{{ route('pricing.index', ['locale' => app()->getLocale()]) }}" 
               @click="mobileOpen = false"
               class="text-base font-medium text-white hover:text-apple-blue py-1">
                {{ __('site.nav_pricing') }}
            </a>
            <a href="{{ route('about.index', ['locale' => app()->getLocale()]) }}" 
               @click="mobileOpen = false"
               class="text-base font-medium text-white hover:text-apple-blue py-1">
                {{ __('site.nav_about') }}
            </a>
            <a href="{{ route('contacts.index', ['locale' => app()->getLocale()]) }}" 
               @click="mobileOpen = false"
               class="text-base font-medium text-white hover:text-apple-blue py-1">
                {{ __('site.nav_contacts') }}
            </a>
        </nav>

        <div class="pt-4 border-t border-white/10">
            <a href="{{ route('contacts.index', ['locale' => app()->getLocale()]) }}" 
               @click="mobileOpen = false"
               class="block w-full text-center py-3 bg-white text-black font-semibold text-xs uppercase tracking-widest rounded-full shadow-lg">
                {{ __('site.nav_discuss') }}
            </a>
        </div>
    </div>
</header>
