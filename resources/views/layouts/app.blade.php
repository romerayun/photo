<!DOCTYPE html>
<html lang="ru" class="scroll-smooth bg-cine-black text-white antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $currentPath = \App\Models\SeoMeta::normalizePath(request()->path());
        $seoMeta = \App\Models\SeoMeta::findByPath($currentPath);
    @endphp
    <title>{{ ($seoMeta && $seoMeta->title) ? $seoMeta->title : (app()->view->getSections()['title'] ?? __('site.meta_title')) }}</title>
    <meta name="description" content="{{ ($seoMeta && $seoMeta->description) ? $seoMeta->description : (app()->view->getSections()['description'] ?? __('site.meta_description')) }}">
    
    {{-- Canonical --}}
    <link rel="canonical" href="{{ ($seoMeta && $seoMeta->canonical) ? $seoMeta->canonical : (app()->view->getSections()['canonical'] ?? url()->current()) }}">

    {{-- Open Graph --}}
    <meta property="og:site_name" content="{{ __('site.author_name') }}">
    <meta property="og:title" content="{{ ($seoMeta && $seoMeta->og_title) ? $seoMeta->og_title : (($seoMeta && $seoMeta->title) ? $seoMeta->title : (app()->view->getSections()['og_title'] ?? (app()->view->getSections()['title'] ?? __('site.meta_title')))) }}">
    <meta property="og:description" content="{{ ($seoMeta && $seoMeta->og_description) ? $seoMeta->og_description : (($seoMeta && $seoMeta->description) ? $seoMeta->description : (app()->view->getSections()['og_description'] ?? (app()->view->getSections()['description'] ?? __('site.meta_description')))) }}">
    <meta property="og:url" content="{{ app()->view->getSections()['og_url'] ?? url()->current() }}">
    <meta property="og:type" content="{{ app()->view->getSections()['og_type'] ?? 'website' }}">
    <meta property="og:image" content="{{ ($seoMeta && $seoMeta->og_image) ? $seoMeta->og_image : (app()->view->getSections()['og_image'] ?? asset('storage/demo/hero-main.jpg')) }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ ($seoMeta && $seoMeta->og_title) ? $seoMeta->og_title : (($seoMeta && $seoMeta->title) ? $seoMeta->title : (app()->view->getSections()['og_title'] ?? (app()->view->getSections()['title'] ?? __('site.meta_title')))) }}">
    <meta name="twitter:description" content="{{ ($seoMeta && $seoMeta->og_description) ? $seoMeta->og_description : (($seoMeta && $seoMeta->description) ? $seoMeta->description : (app()->view->getSections()['og_description'] ?? (app()->view->getSections()['description'] ?? __('site.meta_description')))) }}">
    <meta name="twitter:image" content="{{ ($seoMeta && $seoMeta->og_image) ? $seoMeta->og_image : (app()->view->getSections()['og_image'] ?? asset('storage/demo/hero-main.jpg')) }}">

    <meta name="robots" content="{{ ($seoMeta && $seoMeta->robots) ? $seoMeta->robots : (app()->view->getSections()['robots'] ?? 'index, follow') }}">
    <meta name="yandex-verification" content="1969fa24207a6a63">

    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="32x32">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

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
