<!DOCTYPE html>
<html lang="ru" class="scroll-smooth bg-cine-black text-white antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('site.meta_title'))</title>
    <meta name="description" content="@yield('description', __('site.meta_description'))">
    
    {{-- Canonical --}}
    <link rel="canonical" href="@yield('canonical', url()->current())">

    {{-- Open Graph --}}
    <meta property="og:site_name" content="{{ __('site.author_name') }}">
    <meta property="og:title" content="@yield('og_title', __('site.meta_title'))">
    <meta property="og:description" content="@yield('og_description', __('site.meta_description'))">
    <meta property="og:url" content="@yield('og_url', url()->current())">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:image" content="@yield('og_image', asset('storage/demo/hero-main.jpg'))">
    <meta name="twitter:card" content="summary_large_image">

    <meta name="robots" content="@yield('robots', 'index, follow')">

    @stack('meta_links')

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
    @stack('styles')
</head>
<body class="bg-cine-black text-white font-sans antialiased selection:bg-crimson selection:text-white flex flex-col min-h-screen relative overflow-x-hidden">

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
