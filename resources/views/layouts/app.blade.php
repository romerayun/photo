<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth bg-apple-bg text-apple-text antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>@yield('title', __('site.meta_title'))</title>
    <meta name="description" content="@yield('description', __('site.meta_description'))">
    
    {{-- Canonical & Hreflang --}}
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="alternate" hreflang="ru" href="{{ \App\Support\LocaleHelper::alternateUrl('ru') }}">
    <link rel="alternate" hreflang="en" href="{{ \App\Support\LocaleHelper::alternateUrl('en') }}">
    <link rel="alternate" hreflang="x-default" href="{{ \App\Support\LocaleHelper::alternateUrl('ru') }}">

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
<body class="bg-apple-bg text-apple-text font-sans antialiased selection:bg-apple-accent selection:text-white flex flex-col min-h-screen relative overflow-x-hidden">

    {{-- Demo Mode Notification Pill --}}
    @if(\App\Models\Setting::isDemoMode())
    <aside class="w-full bg-apple-surface/90 border-b border-black/5 py-2 px-4 text-xs text-apple-textMuted z-50">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2 text-center sm:text-left">
            <div class="flex items-center gap-2.5">
                <span class="inline-block w-2 h-2 rounded-full bg-apple-accent shadow-[0_0_8px_#B07D53] animate-pulse" aria-hidden="true"></span>
                <span class="font-semibold text-apple-text tracking-wide uppercase text-[0.68rem]">{{ __('site.demo_badge') }}:</span>
                <span class="text-apple-textMuted text-xs">{{ __('site.demo_banner_text') }}</span>
            </div>
            <a href="{{ route('admin.login') }}" class="text-apple-accent hover:text-apple-accentHover text-xs font-semibold transition-colors shrink-0 underline underline-offset-4">
                Панель управления &rarr;
            </a>
        </div>
    </aside>
    @endif

    {{-- Apple Floating Island Header --}}
    @include('components.navbar')

    {{-- Main Content --}}
    <main id="main-content" class="flex-grow">
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('components.footer')

    @stack('scripts')
</body>
</html>
