<!DOCTYPE html>
<html lang="ru" class="h-full bg-[#09090B]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Вход в панель управления — Роман Юн</title>
    <meta name="robots" content="noindex, nofollow">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-[#09090B] text-white font-sans antialiased flex items-center justify-center p-4 sm:p-6 relative selection:bg-crimson selection:text-white">

    {{-- Subtle Background Light Glow --}}
    <div class="fixed inset-0 pointer-events-none overflow-hidden flex items-center justify-center">
        <div class="w-[500px] h-[500px] rounded-full bg-crimson/5 blur-[120px]"></div>
    </div>

    <div class="relative z-10 max-w-md w-full bg-[#131317] border border-white/10 rounded-xl p-8 sm:p-10 shadow-2xl backdrop-blur-sm">
        
        {{-- Brand / Header --}}
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-block group">
                <span class="font-serif text-3xl sm:text-4xl text-white tracking-tight group-hover:text-crimson transition-colors block">
                    Роман Юн
                </span>
            </a>
            <div class="inline-flex items-center gap-2 mt-2 px-3 py-1 rounded-full bg-white/5 border border-white/10">
                <span class="w-1.5 h-1.5 rounded-full bg-crimson animate-pulse"></span>
                <span class="text-[0.7rem] uppercase tracking-widest text-zinc-400 font-mono">
                    Панель управления
                </span>
            </div>
        </div>

        @if(isset($errors) && $errors->any())
            <div class="mb-6 p-4 rounded-lg bg-red-500/10 border border-red-500/30 text-red-300 text-xs flex items-start gap-2.5">
                <svg class="w-4 h-4 text-red-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="block text-xs uppercase tracking-wider text-zinc-300 font-semibold mb-1.5">
                    Электронная почта
                </label>
                <input id="email" 
                       type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus 
                       placeholder="admin@example.com"
                       class="w-full px-4 py-3 bg-[#1A1A22] border border-white/10 rounded-lg text-sm text-white placeholder-zinc-500 focus:outline-none focus:border-crimson focus:ring-1 focus:ring-crimson transition-all">
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-xs uppercase tracking-wider text-zinc-300 font-semibold">
                        Пароль
                    </label>
                </div>
                <input id="password" 
                       type="password" 
                       name="password" 
                       required 
                       placeholder="••••••••"
                       class="w-full px-4 py-3 bg-[#1A1A22] border border-white/10 rounded-lg text-sm text-white placeholder-zinc-500 focus:outline-none focus:border-crimson focus:ring-1 focus:ring-crimson transition-all">
            </div>

            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center text-xs text-zinc-400 cursor-pointer select-none">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-[#1A1A22] border-white/20 text-crimson focus:ring-crimson focus:ring-offset-0 mr-2.5">
                    Запомнить меня
                </label>
            </div>

            <div class="pt-2">
                <button type="submit" 
                        class="w-full py-3.5 px-6 bg-crimson hover:bg-crimson-hover text-white text-xs uppercase tracking-widest font-bold rounded-lg shadow-crimson-btn hover:shadow-crimson-glow transition-all duration-200 cursor-pointer flex items-center justify-center gap-2">
                    <span>Войти в систему</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </div>
        </form>

        <div class="mt-8 pt-6 border-t border-white/10 text-center text-xs text-zinc-400">
            <p>Управление доступом осуществляется через консоль сервера:</p>
            <code class="block mt-2 p-2.5 bg-black/40 border border-white/5 text-zinc-300 font-mono text-[0.72rem] rounded-md select-all">php artisan app:create-admin</code>
        </div>

        <div class="mt-4 text-center">
            <a href="{{ route('home') }}" class="text-xs text-zinc-400 hover:text-white transition-colors">
                &larr; Вернуться на сайт
            </a>
        </div>

    </div>

</body>
</html>
