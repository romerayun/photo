<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Управление сайтом') — Роман Юн</title>
    <meta name="robots" content="noindex, nofollow">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased min-h-screen flex flex-col">

    {{-- Top Admin Bar --}}
    <header class="bg-graphite-950 text-white border-b border-gray-800 sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            
            <div class="flex items-center space-x-6">
                <a href="{{ route('admin.dashboard') }}" class="font-serif text-xl tracking-tight text-white hover:text-terracotta transition-colors">
                    Роман Юн &bull; <span class="text-xs font-sans uppercase tracking-widest text-gray-400">Панель управления</span>
                </a>

                <nav class="hidden md:flex items-center space-x-4 text-xs uppercase tracking-wider text-gray-300">
                    <a href="{{ route('admin.dashboard') }}" class="px-3 py-1.5 rounded hover:text-white {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800 text-white' : '' }}">Обзор</a>
                    <a href="{{ route('admin.series.index') }}" class="px-3 py-1.5 rounded hover:text-white {{ request()->routeIs('admin.series.*') ? 'bg-gray-800 text-white' : '' }}">Серии</a>
                    <a href="{{ route('admin.categories.index') }}" class="px-3 py-1.5 rounded hover:text-white {{ request()->routeIs('admin.categories.*') ? 'bg-gray-800 text-white' : '' }}">Категории</a>
                    <a href="{{ route('admin.packages.index') }}" class="px-3 py-1.5 rounded hover:text-white {{ request()->routeIs('admin.packages.*') ? 'bg-gray-800 text-white' : '' }}">Пакеты</a>
                    <a href="{{ route('admin.settings.index') }}" class="px-3 py-1.5 rounded hover:text-white {{ request()->routeIs('admin.settings.*') ? 'bg-gray-800 text-white' : '' }}">Настройки</a>
                </nav>
            </div>

            <div class="flex items-center space-x-4">
                <a href="{{ url('/ru') }}" target="_blank" class="text-xs uppercase tracking-wider text-gray-400 hover:text-white flex items-center gap-1">
                    <span>Сайт</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </a>

                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="text-xs uppercase tracking-wider text-gray-400 hover:text-red-400 transition-colors">
                        Выйти
                    </button>
                </form>
            </div>

        </div>
    </header>

    {{-- Main Container --}}
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        {{-- Flash messages --}}
        @if(session('success'))
            <div class="mb-6 p-4 rounded bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 rounded bg-red-50 border border-red-200 text-red-800 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-white border-t border-gray-200 py-4 text-center text-xs text-gray-500">
        Управление портфолио &bull; Роман Юн &bull; {{ date('Y') }}
    </footer>

    @stack('scripts')
</body>
</html>
