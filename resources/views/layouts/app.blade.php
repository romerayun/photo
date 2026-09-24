<!DOCTYPE html>
<html lang="ru" class="scroll-smooth bg-cine-black text-white antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>@yield('title', __('site.meta_title'))</title>
    <meta name="description" content="@yield('description', __('site.meta_description'))">
    
    {{-- Canonical --}}
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Open Graph --}}
    <meta property="og:site_name" content="{{ __('site.author_name') }}">
    <meta property="og:title" content="@yield('og_title', __('site.meta_title'))">
    <meta property="og:description" content="@yield('og_description', __('site.meta_description'))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:image" content="@yield('og_image', asset('storage/demo/hero-main.jpg'))">
    <meta name="twitter:card" content="summary_large_image">

    @if(\App\Models\Setting::isDemoMode())
    <meta name="robots" content="noindex, nofollow">
    @else
    <meta name="robots" content="index, follow">
    @endif

    {{-- Structured Data (Schema.org) --}}
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Person",
      "name": "Роман Юн",
      "alternateName": "Roman Yun",
      "jobTitle": "Фотограф",
      "address": {
        "@type": "PostalAddress",
        "addressLocality": "Иркутск",
        "addressCountry": "RU"
      },
      "url": "{{ url('/') }}"
    }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-cine-black text-white font-sans antialiased selection:bg-crimson selection:text-white flex flex-col min-h-screen relative overflow-x-hidden">

    {{-- Demo Mode Notification Banner --}}
    @if(\App\Models\Setting::isDemoMode())
    <aside class="w-full bg-cine-surface border-b border-cine-border py-2 px-4 text-xs text-cine-muted z-50">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2 text-center sm:text-left">
            <div class="flex items-center gap-2.5">
                <span class="inline-block w-2 h-2 rounded-full bg-crimson shadow-crimson-glow animate-pulse" aria-hidden="true"></span>
                <span class="font-bold text-white tracking-wide uppercase text-[0.68rem]">{{ __('site.demo_badge') }}:</span>
                <span class="text-neutral-400 text-xs">{{ __('site.demo_banner_text') }}</span>
            </div>
            <a href="{{ route('admin.login') }}" class="text-crimson hover:text-white text-xs font-bold transition-colors shrink-0 underline underline-offset-4">
                Панель управления &rarr;
            </a>
        </div>
    </aside>
    @endif

    {{-- Avant-Garde Header --}}
    @include('components.navbar')

    {{-- Main Content --}}
    <main id="main-content" class="flex-grow">
        @yield('content')
    </main>

    {{-- Monumental Footer --}}
    @include('components.footer')

    @stack('scripts')
</body>
</html>
