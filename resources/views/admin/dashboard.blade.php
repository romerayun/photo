@extends('layouts.admin')

@section('title', 'Обзор')

@section('content')
<div class="space-y-8">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div>
            <h1 class="text-2xl font-serif font-bold text-slate-900">Добро пожаловать в панель управления</h1>
            <p class="text-xs text-slate-500 mt-1">Управляйте сериями фотографий, категориями, ценами и настройками сайта.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.series.create') }}" class="px-5 py-2.5 bg-neutral-900 hover:bg-neutral-800 text-white text-xs uppercase tracking-wider font-bold rounded-lg shadow-sm hover:shadow transition-all inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span>Добавить серию</span>
            </a>
        </div>
    </div>

    {{-- Status Alerts --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        
        {{-- Demo Mode Status --}}
        <div class="p-5 rounded-xl border {{ $isDemo ? 'bg-amber-50/80 border-amber-200 text-amber-900' : 'bg-emerald-50/80 border-emerald-200 text-emerald-900' }} shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <span class="font-bold text-sm block">
                        {{ $isDemo ? 'Сайт работает в демонстрационном режиме' : 'Публичный рабочий режим активен' }}
                    </span>
                    <p class="text-xs mt-1.5 leading-relaxed text-slate-700">
                        {{ $isDemo 
                            ? 'На сайте отображаются демонстрационные серии с пометкой Demo, поисковая индексация закрыта. Переключите режим в Настройках, когда будете готовы опубликовать свои реальные работы.' 
                            : 'Сайт открыт для посетителей и поисковых систем. Отображаются только ваши опубликованные серии.' 
                        }}
                    </p>
                </div>
                <a href="{{ route('admin.settings.index') }}" class="text-xs font-bold underline underline-offset-4 shrink-0 ml-4 hover:text-black">
                    Настроить
                </a>
            </div>
        </div>

        {{-- Contact Configuration Status --}}
        <div class="p-5 rounded-xl border {{ (!$hasTelegram || !$hasPhone) ? 'bg-blue-50/80 border-blue-200 text-blue-900' : 'bg-emerald-50/80 border-emerald-200 text-emerald-900' }} shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <span class="font-bold text-sm block">
                        {{ (!$hasTelegram || !$hasPhone) ? 'Контакты требуют настройки' : 'Контакты настроены' }}
                    </span>
                    <p class="text-xs mt-1.5 leading-relaxed text-slate-700">
                        Telegram: <strong>{{ $hasTelegram ? 'Указан' : 'Не заполнен' }}</strong> &bull; Телефон: <strong>{{ $hasPhone ? 'Указан' : 'Не заполнен' }}</strong>.
                    </p>
                </div>
                <a href="{{ route('admin.settings.index') }}" class="text-xs font-bold underline underline-offset-4 shrink-0 ml-4 hover:text-black">
                    Изменить
                </a>
            </div>
        </div>

    </div>

    {{-- Statistics Grid --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="p-5 bg-white border border-slate-200 rounded-xl shadow-sm">
            <span class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Всего серий</span>
            <span class="text-3xl font-serif font-bold text-slate-900 block mt-2">{{ $stats['total_series'] }}</span>
            <span class="text-[0.7rem] text-slate-400 mt-1 block">Опубликовано: {{ $stats['published_series'] }}</span>
        </div>

        <div class="p-5 bg-white border border-slate-200 rounded-xl shadow-sm">
            <span class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Статей</span>
            <span class="text-3xl font-serif font-bold text-slate-900 block mt-2">{{ $stats['total_articles'] }}</span>
            <span class="text-[0.7rem] text-slate-400 mt-1 block">Опубликовано: {{ $stats['published_articles'] }}</span>
        </div>

        <div class="p-5 bg-white border border-slate-200 rounded-xl shadow-sm">
            <span class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Комментариев</span>
            <span class="text-3xl font-serif font-bold text-slate-900 block mt-2">{{ $stats['total_comments'] }}</span>
            <span class="text-[0.7rem] text-slate-400 mt-1 block">От читателей</span>
        </div>

        <div class="p-5 bg-white border border-slate-200 rounded-xl shadow-sm">
            <span class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Фотографий</span>
            <span class="text-3xl font-serif font-bold text-slate-900 block mt-2">{{ $stats['total_photos'] }}</span>
            <span class="text-[0.7rem] text-slate-400 mt-1 block">В оригинале</span>
        </div>

        <div class="p-5 bg-white border border-slate-200 rounded-xl shadow-sm">
            <span class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Пакетов</span>
            <span class="text-3xl font-serif font-bold text-slate-900 block mt-2">{{ $stats['total_packages'] }}</span>
            <span class="text-[0.7rem] text-slate-400 mt-1 block">Услуги и цены</span>
        </div>

        <div class="p-5 bg-white border border-slate-200 rounded-xl shadow-sm">
            <span class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Съёмок</span>
            <span class="text-3xl font-serif font-bold text-slate-900 block mt-2">{{ $stats['total_shoots'] }}</span>
            <span class="text-[0.7rem] text-slate-400 mt-1 block">Предстоит: {{ $stats['upcoming_shoots'] }}</span>
        </div>
    </div>

    {{-- Upcoming Shoots Section --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-2.5 h-2.5 rounded-full bg-crimson animate-pulse"></div>
                <h2 class="text-base font-bold text-slate-900">Ближайшие съёмки</h2>
                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold">{{ $upcomingShoots->count() }}</span>
            </div>
            <a href="{{ route('admin.shoots.index') }}" class="text-xs text-crimson hover:underline font-semibold flex items-center gap-1">
                <span>Календарь съёмок</span>
                <span>&rarr;</span>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50 text-[0.7rem] uppercase tracking-wider text-slate-500 border-b border-slate-200 font-semibold">
                    <tr>
                        <th class="px-6 py-3.5">Дата и время</th>
                        <th class="px-6 py-3.5">Клиент</th>
                        <th class="px-6 py-3.5">Контакты</th>
                        <th class="px-6 py-3.5">Локация / Описание</th>
                        <th class="px-6 py-3.5">Статус</th>
                        <th class="px-6 py-3.5 text-right">Действие</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($upcomingShoots as $shoot)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                <span class="font-semibold text-slate-900 block">{{ $shoot->shoot_date->translatedFormat('d F Y') }}</span>
                                <span class="text-xs font-mono text-slate-500">{{ substr($shoot->start_time, 0, 5) }} &bull; {{ $shoot->duration_label }}</span>
                            </td>
                            <td class="px-6 py-3.5">
                                <span class="font-semibold text-slate-900 block">{{ $shoot->client_name }}</span>
                                @if($shoot->price)
                                    <span class="text-xs text-emerald-600 font-mono">{{ number_format($shoot->price, 0, '', ' ') }} ₽</span>
                                @endif
                            </td>
                            <td class="px-6 py-3.5 text-xs">
                                <div class="space-y-1">
                                    @if($shoot->phone)
                                        <a href="tel:{{ $shoot->phone_clean }}" class="text-slate-800 hover:text-crimson font-mono block">{{ $shoot->phone }}</a>
                                    @endif
                                    @if($shoot->social_link)
                                        <a href="{{ $shoot->social_url }}" target="_blank" class="text-blue-600 hover:underline block truncate max-w-[140px]">{{ $shoot->social_link }}</a>
                                    @endif
                                    @if(!$shoot->phone && !$shoot->social_link)
                                        <span class="text-slate-400 italic">—</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-3.5 text-xs text-slate-600 max-w-xs">
                                @if($shoot->location)
                                    <span class="font-semibold text-slate-800 block mb-0.5">{{ $shoot->location }}</span>
                                @endif
                                <p class="line-clamp-1 text-slate-500">{{ $shoot->description ?? '—' }}</p>
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-[0.68rem] uppercase font-bold tracking-wider {{ $shoot->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : ($shoot->status === 'cancelled' ? 'bg-rose-100 text-rose-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ $shoot->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 text-right text-xs">
                                <a href="{{ route('admin.shoots.index') }}" class="px-3 py-1.5 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold transition-colors inline-block">
                                    В календарь
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400">
                                Предстоящих съёмок пока нет. <a href="{{ route('admin.shoots.index') }}" class="text-crimson font-semibold hover:underline">Запланировать в календаре &rarr;</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recent Series Table --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-900">Последние серии</h2>
            <a href="{{ route('admin.series.index') }}" class="text-xs text-crimson hover:underline font-semibold flex items-center gap-1">
                <span>Смотреть все</span>
                <span>&rarr;</span>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 border-b border-slate-200 font-semibold">
                    <tr>
                        <th class="px-6 py-3.5">Обложка</th>
                        <th class="px-6 py-3.5">Название серии</th>
                        <th class="px-6 py-3.5">Категория</th>
                        <th class="px-6 py-3.5">Кадров</th>
                        <th class="px-6 py-3.5">Статус</th>
                        <th class="px-6 py-3.5 text-right">Действия</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($recentSeries as $series)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-3.5">
                                <img src="{{ $series->cover_url }}" alt="" class="w-14 h-10 object-cover rounded-md bg-slate-100 border border-slate-200">
                            </td>
                            <td class="px-6 py-3.5">
                                <span class="font-semibold text-slate-900 block">{{ $series->title_ru }}</span>
                                <span class="text-[0.7rem] text-slate-400 font-mono">/series/{{ $series->slug }}</span>
                            </td>
                            <td class="px-6 py-3.5 text-xs text-slate-600">
                                {{ $series->category ? $series->category->name_ru : '—' }}
                            </td>
                            <td class="px-6 py-3.5 text-xs font-mono font-semibold text-slate-700">
                                {{ $series->photos->count() }}
                            </td>
                            <td class="px-6 py-3.5 text-xs">
                                @if($series->is_demo)
                                    <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[0.68rem] uppercase font-bold tracking-wider">Demo</span>
                                @elseif($series->is_published)
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[0.68rem] uppercase font-bold tracking-wider">Опубликовано</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[0.68rem] uppercase font-medium tracking-wider">Черновик</span>
                                @endif
                            </td>
                            <td class="px-6 py-3.5 text-right text-xs">
                                <a href="{{ route('admin.series.edit', $series) }}" class="px-3 py-1.5 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold transition-colors inline-block">
                                    Редактировать
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400">Серий пока нет.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recent Articles Table --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-900">Последние статьи блога</h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.articles.create') }}" class="text-xs text-neutral-900 hover:text-crimson font-bold flex items-center gap-1">
                    <span>+ Создать статью</span>
                </a>
                <span class="text-slate-300">&bull;</span>
                <a href="{{ route('admin.articles.index') }}" class="text-xs text-crimson hover:underline font-semibold flex items-center gap-1">
                    <span>Все статьи ({{ $stats['total_articles'] }})</span>
                    <span>&rarr;</span>
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 border-b border-slate-200 font-semibold">
                    <tr>
                        <th class="px-6 py-3.5">Обложка</th>
                        <th class="px-6 py-3.5">Название</th>
                        <th class="px-6 py-3.5">Дата</th>
                        <th class="px-6 py-3.5 text-center">Просмотры</th>
                        <th class="px-6 py-3.5 text-center">Комментарии</th>
                        <th class="px-6 py-3.5">Статус</th>
                        <th class="px-6 py-3.5 text-right">Действия</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($recentArticles as $art)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-3.5">
                                <img src="{{ $art->cover_url }}" alt="" class="w-14 h-10 object-cover rounded-md bg-slate-100 border border-slate-200">
                            </td>
                            <td class="px-6 py-3.5">
                                <span class="font-semibold text-slate-900 block">{{ $art->title }}</span>
                                <span class="text-[0.7rem] text-slate-400 font-mono">/articles/{{ $art->slug }}</span>
                            </td>
                            <td class="px-6 py-3.5 text-xs font-mono text-slate-600">
                                {{ $art->formatted_date }}
                            </td>
                            <td class="px-6 py-3.5 text-xs font-mono font-bold text-slate-800 text-center">
                                👁 {{ $art->views_count }}
                            </td>
                            <td class="px-6 py-3.5 text-xs font-mono font-bold text-slate-800 text-center">
                                💬 {{ $art->comments_count }}
                            </td>
                            <td class="px-6 py-3.5 text-xs">
                                @if($art->is_published)
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[0.68rem] uppercase font-bold tracking-wider">Опубликовано</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[0.68rem] uppercase font-medium tracking-wider">Черновик</span>
                                @endif
                            </td>
                            <td class="px-6 py-3.5 text-right text-xs space-x-1">
                                <a href="{{ route('admin.articles.edit', $art) }}" class="px-3 py-1.5 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold transition-colors inline-block">
                                    Редактировать
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-400">Статей пока нет.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
