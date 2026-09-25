<header x-data="{ mobileOpen: false }" class="sticky top-0 z-40 bg-cine-black/90 backdrop-blur-xl border-b border-cine-border transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            
            {{-- Brand / Monogram & Name --}}
            <a href="{{ route('home') }}" 
               class="group flex items-center gap-3 focus:outline-none">
                <div class="w-9 h-9 bg-crimson flex items-center justify-center text-xs font-extrabold text-white tracking-wider shadow-crimson-btn">
                    RY
                </div>
                <div class="flex flex-col">
                    <span class="text-base font-extrabold tracking-tight text-white font-display group-hover:text-crimson transition-colors">
                        {{ __('site.author_name') }}
                    </span>
                    <span class="text-[0.62rem] uppercase tracking-widest text-neutral-400 font-mono">
                        {{ __('site.location') }} &bull; ФОТОГРАФ
                    </span>
                </div>
            </a>

            {{-- Desktop Navigation Links --}}
            <nav class="hidden md:flex items-center space-x-8 text-xs uppercase tracking-widest font-bold" aria-label="Main Navigation">
                <a href="{{ route('portfolio.index') }}" 
                   class="transition-colors hover:text-crimson {{ request()->routeIs('portfolio.*') || request()->routeIs('series.*') ? 'text-crimson' : 'text-neutral-300' }}">
                    {{ __('site.nav_portfolio') }}
                </a>
                <a href="{{ route('articles.index') }}" 
                   class="transition-colors hover:text-crimson {{ request()->routeIs('articles.*') ? 'text-crimson' : 'text-neutral-300' }}">
                    {{ __('site.nav_articles') }}
                </a>
                <a href="{{ route('pricing.index') }}" 
                   class="transition-colors hover:text-crimson {{ request()->routeIs('pricing.*') ? 'text-crimson' : 'text-neutral-300' }}">
                    {{ __('site.nav_pricing') }}
                </a>
                <a href="{{ route('about.index') }}" 
                   class="transition-colors hover:text-crimson {{ request()->routeIs('about.*') ? 'text-crimson' : 'text-neutral-300' }}">
                    {{ __('site.nav_about') }}
                </a>
                <a href="{{ route('contacts.index') }}" 
                   class="transition-colors hover:text-crimson {{ request()->routeIs('contacts.*') ? 'text-crimson' : 'text-neutral-300' }}">
                    {{ __('site.nav_contacts') }}
                </a>
            </nav>

            {{-- Right Controls: Direct CTA to Telegram --}}
            <div class="hidden md:flex items-center space-x-3">
                <a href="{{ \App\Models\Setting::hasTelegram() ? \App\Models\Setting::telegramUrl('Здравствуйте, Роман! Хочу обсудить съёмку.') : route('contacts.index') }}" 
                   @if(\App\Models\Setting::hasTelegram()) target="_blank" rel="noopener" @endif
                   class="btn-crimson px-5 py-2.5 text-xs tracking-wider font-bold">
                    <span>{{ __('site.nav_discuss') }}</span>
                    <span>&nearr;</span>
                </a>
            </div>

            {{-- Mobile Menu Trigger --}}
            <div class="flex items-center md:hidden space-x-3">
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
            <a href="{{ route('home') }}" 
               @click="mobileOpen = false"
               class="text-white hover:text-crimson py-1">
                {{ __('site.nav_home') }}
            </a>
            <a href="{{ route('portfolio.index') }}" 
               @click="mobileOpen = false"
               class="text-white hover:text-crimson py-1">
                {{ __('site.nav_portfolio') }}
            </a>
            <a href="{{ route('articles.index') }}" 
               @click="mobileOpen = false"
               class="text-white hover:text-crimson py-1 {{ request()->routeIs('articles.*') ? 'text-crimson' : '' }}">
                {{ __('site.nav_articles') }}
            </a>
            <a href="{{ route('pricing.index') }}" 
               @click="mobileOpen = false"
               class="text-white hover:text-crimson py-1">
                {{ __('site.nav_pricing') }}
            </a>
            <a href="{{ route('about.index') }}" 
               @click="mobileOpen = false"
               class="text-white hover:text-crimson py-1">
                {{ __('site.nav_about') }}
            </a>
            <a href="{{ route('contacts.index') }}" 
               @click="mobileOpen = false"
               class="text-white hover:text-crimson py-1">
                {{ __('site.nav_contacts') }}
            </a>
        </nav>

        <div class="pt-4 border-t border-cine-border">
            <a href="{{ \App\Models\Setting::hasTelegram() ? \App\Models\Setting::telegramUrl('Здравствуйте, Роман! Хочу обсудить съёмку.') : route('contacts.index') }}" 
               @if(\App\Models\Setting::hasTelegram()) target="_blank" rel="noopener" @endif
               @click="mobileOpen = false"
               class="btn-crimson block w-full text-center py-3 text-xs tracking-wider font-bold">
                <span>{{ __('site.nav_discuss') }}</span>
                <span>&nearr;</span>
            </a>
        </div>
    </div>
</header>
