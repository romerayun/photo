<!DOCTYPE html>
<html lang="ru" class="h-full bg-slate-100 text-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Управление сайтом') — Роман Юн</title>
    <meta name="robots" content="noindex, nofollow">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="min-h-full bg-slate-100 text-slate-900 font-sans antialiased flex flex-col selection:bg-crimson selection:text-white">

    {{-- Top Admin Bar --}}
    <header class="bg-[#111114] text-white border-b border-white/10 sticky top-0 z-30 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            
            <div class="flex items-center space-x-6 sm:space-x-8">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 font-serif text-lg sm:text-xl tracking-tight text-white hover:text-crimson transition-colors">
                    <span class="w-2 h-2 rounded-full bg-crimson"></span>
                    <span>Роман Юн</span>
                    <span class="hidden sm:inline-block text-zinc-500 text-xs font-sans">&bull;</span>
                    <span class="hidden sm:inline-block text-[0.7rem] font-sans uppercase tracking-widest text-zinc-400 font-mono">Панель управления</span>
                </a>

                <nav class="hidden md:flex items-center space-x-1 text-xs uppercase tracking-wider text-zinc-300 font-semibold">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="px-3.5 py-2 rounded-lg transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-white/15 text-white shadow-sm' : 'hover:bg-white/5 hover:text-white' }}">
                        Обзор
                    </a>
                    <a href="{{ route('admin.shoots.index') }}" 
                       class="px-3.5 py-2 rounded-lg transition-colors {{ request()->routeIs('admin.shoots.*') ? 'bg-white/15 text-white shadow-sm' : 'hover:bg-white/5 hover:text-white' }}">
                        Календарь
                    </a>
                    <a href="{{ route('admin.series.index') }}" 
                       class="px-3.5 py-2 rounded-lg transition-colors {{ request()->routeIs('admin.series.*') ? 'bg-white/15 text-white shadow-sm' : 'hover:bg-white/5 hover:text-white' }}">
                        Серии
                    </a>
                    <a href="{{ route('admin.articles.index') }}" 
                       class="px-3.5 py-2 rounded-lg transition-colors {{ request()->routeIs('admin.articles.*') ? 'bg-white/15 text-white shadow-sm' : 'hover:bg-white/5 hover:text-white' }}">
                        Статьи
                    </a>
                    <a href="{{ route('admin.categories.index') }}" 
                       class="px-3.5 py-2 rounded-lg transition-colors {{ request()->routeIs('admin.categories.*') ? 'bg-white/15 text-white shadow-sm' : 'hover:bg-white/5 hover:text-white' }}">
                        Категории
                    </a>
                    <a href="{{ route('admin.packages.index') }}" 
                       class="px-3.5 py-2 rounded-lg transition-colors {{ request()->routeIs('admin.packages.*') ? 'bg-white/15 text-white shadow-sm' : 'hover:bg-white/5 hover:text-white' }}">
                        Пакеты
                    </a>
                    <a href="{{ route('admin.settings.index') }}" 
                       class="px-3.5 py-2 rounded-lg transition-colors {{ request()->routeIs('admin.settings.*') ? 'bg-white/15 text-white shadow-sm' : 'hover:bg-white/5 hover:text-white' }}">
                        Настройки
                    </a>
                </nav>
            </div>

            <div class="flex items-center space-x-3 sm:space-x-4">
                <a href="{{ route('home') }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-xs uppercase tracking-wider text-zinc-300 hover:text-white flex items-center gap-1.5 transition-colors border border-white/5">
                    <span>Сайт</span>
                    <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </a>

                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 rounded-lg text-xs uppercase tracking-wider text-zinc-400 hover:text-rose-400 hover:bg-rose-500/10 transition-colors font-semibold cursor-pointer">
                        Выйти
                    </button>
                </form>
            </div>

        </div>

        {{-- Mobile Nav Bar --}}
        <div class="md:hidden border-t border-white/10 px-4 py-2.5 flex items-center justify-between overflow-x-auto text-[0.72rem] uppercase tracking-wider text-zinc-300 gap-1">
            <a href="{{ route('admin.dashboard') }}" class="px-2.5 py-1 rounded {{ request()->routeIs('admin.dashboard') ? 'bg-white/15 text-white' : '' }}">Обзор</a>
            <a href="{{ route('admin.shoots.index') }}" class="px-2.5 py-1 rounded {{ request()->routeIs('admin.shoots.*') ? 'bg-white/15 text-white' : '' }}">Календарь</a>
            <a href="{{ route('admin.series.index') }}" class="px-2.5 py-1 rounded {{ request()->routeIs('admin.series.*') ? 'bg-white/15 text-white' : '' }}">Серии</a>
            <a href="{{ route('admin.articles.index') }}" class="px-2.5 py-1 rounded {{ request()->routeIs('admin.articles.*') ? 'bg-white/15 text-white' : '' }}">Статьи</a>
            <a href="{{ route('admin.categories.index') }}" class="px-2.5 py-1 rounded {{ request()->routeIs('admin.categories.*') ? 'bg-white/15 text-white' : '' }}">Категории</a>
            <a href="{{ route('admin.packages.index') }}" class="px-2.5 py-1 rounded {{ request()->routeIs('admin.packages.*') ? 'bg-white/15 text-white' : '' }}">Пакеты</a>
            <a href="{{ route('admin.settings.index') }}" class="px-2.5 py-1 rounded {{ request()->routeIs('admin.settings.*') ? 'bg-white/15 text-white' : '' }}">Настройки</a>
        </div>
    </header>

    {{-- Main Container --}}
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        {{-- Flash messages --}}
        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-sm flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center shrink-0 text-emerald-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 text-sm shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center shrink-0 text-rose-600 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-rose-950 mb-1">Пожалуйста, исправьте следующие ошибки:</div>
                        <ul class="list-disc list-inside space-y-1 text-xs text-rose-800">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-white border-t border-slate-200 py-5 text-center text-xs text-slate-500">
        Управление портфолио &bull; Роман Юн &bull; {{ date('Y') }}
    </footer>

    @stack('scripts')
</body>
</html>
