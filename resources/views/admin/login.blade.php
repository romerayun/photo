<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Вход в панель управления — Роман Юн</title>
    <meta name="robots" content="noindex, nofollow">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-canvas text-graphite-900 font-sans antialiased min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-white border border-editorial-border p-8 shadow-sm">
        
        <div class="text-center mb-8">
            <span class="font-serif text-3xl text-graphite-950 block">Роман Юн</span>
            <span class="text-xs uppercase tracking-widest text-graphite-500 font-mono mt-1 block">Панель управления сайтом</span>
        </div>

        @if($errors->any())
            <div class="mb-6 p-4 rounded bg-red-50 border border-red-200 text-red-800 text-xs">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login') }}" class="space-y-6">
            @csrf

            <div>
                <label for="email" class="block text-xs uppercase tracking-wider text-graphite-700 font-medium mb-1">
                    Электронная почта
                </label>
                <input id="email" 
                       type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus 
                       class="w-full px-4 py-2.5 border border-editorial-border focus:border-terracotta focus:ring-1 focus:ring-terracotta text-sm bg-canvas">
            </div>

            <div>
                <label for="password" class="block text-xs uppercase tracking-wider text-graphite-700 font-medium mb-1">
                    Пароль
                </label>
                <input id="password" 
                       type="password" 
                       name="password" 
                       required 
                       class="w-full px-4 py-2.5 border border-editorial-border focus:border-terracotta focus:ring-1 focus:ring-terracotta text-sm bg-canvas">
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center text-graphite-600">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 text-terracotta focus:ring-terracotta mr-2">
                    Запомнить меня
                </label>
            </div>

            <div>
                <button type="submit" 
                        class="w-full py-3 bg-graphite-900 text-white text-xs uppercase tracking-widest font-medium hover:bg-terracotta transition-colors">
                    Войти в систему
                </button>
            </div>
        </form>

        <div class="mt-8 pt-6 border-t border-editorial-border text-center text-xs text-graphite-500">
            <p>Регистрация доступна только через защищенную команду консоли:</p>
            <code class="block mt-2 p-2 bg-gray-100 text-graphite-800 font-mono text-[0.7rem] rounded">php artisan app:create-admin</code>
        </div>

    </div>

</body>
</html>
