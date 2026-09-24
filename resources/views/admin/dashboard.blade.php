@extends('layouts.admin')

@section('title', 'Обзор')

@section('content')
<div class="space-y-8">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-200 pb-5">
        <div>
            <h1 class="text-2xl font-serif font-bold text-gray-900">Добро пожаловать в панель управления</h1>
            <p class="text-xs text-gray-500 mt-1">Здесь вы можете управлять сериями, загружать фотографии, менять цены и настраивать контакты.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.series.create') }}" class="px-4 py-2 bg-graphite-900 text-white text-xs uppercase tracking-wider font-medium hover:bg-terracotta transition-colors rounded">
                + Добавить серию
            </a>
        </div>
    </div>

    {{-- Status Alerts --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        
        {{-- Demo Mode Status --}}
        <div class="p-4 rounded border {{ $isDemo ? 'bg-amber-50 border-amber-200 text-amber-900' : 'bg-emerald-50 border-emerald-200 text-emerald-900' }}">
            <div class="flex items-start justify-between">
                <div>
                    <span class="font-medium text-sm block">
                        {{ $isDemo ? 'Сайт работает в демонстрационном режиме' : 'Публичный рабочий режим активен' }}
                    </span>
                    <p class="text-xs mt-1 leading-relaxed">
                        {{ $isDemo 
                            ? 'На сайте отображаются демонстрационные серии с пометкой Demo, поисковая индексация закрыта. Переключите режим в Настройках, когда будете готовы опубликовать свои реальные работы.' 
                            : 'Сайт открыт для посетителей и поисковых систем. Отображаются только ваши опубликованные серии.' 
                        }}
                    </p>
                </div>
                <a href="{{ route('admin.settings.index') }}" class="text-xs font-semibold underline underline-offset-4 shrink-0 ml-4">
                    Настроить
                </a>
            </div>
        </div>

        {{-- Contact Configuration Status --}}
        <div class="p-4 rounded border {{ (!$hasTelegram || !$hasPhone) ? 'bg-blue-50 border-blue-200 text-blue-900' : 'bg-emerald-50 border-emerald-200 text-emerald-900' }}">
            <div class="flex items-start justify-between">
                <div>
                    <span class="font-medium text-sm block">
                        {{ (!$hasTelegram || !$hasPhone) ? 'Контакты требуют настройки' : 'Контакты настроены' }}
                    </span>
                    <p class="text-xs mt-1 leading-relaxed">
                        Telegram: <strong>{{ $hasTelegram ? 'Указан' : 'Не заполнен' }}</strong> &bull; Телефон: <strong>{{ $hasPhone ? 'Указан' : 'Не заполнен' }}</strong>.
                    </p>
                </div>
                <a href="{{ route('admin.settings.index') }}" class="text-xs font-semibold underline underline-offset-4 shrink-0 ml-4">
                    Изменить
                </a>
            </div>
        </div>

    </div>

    {{-- Statistics Grid --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-5 bg-white border border-gray-200 rounded">
            <span class="text-xs uppercase tracking-wider text-gray-500 font-medium">Всего серий</span>
            <span class="text-3xl font-serif font-bold text-gray-900 block mt-2">{{ $stats['total_series'] }}</span>
            <span class="text-[0.7rem] text-gray-400 mt-1 block">Опубликовано: {{ $stats['published_series'] }}</span>
        </div>

        <div class="p-5 bg-white border border-gray-200 rounded">
            <span class="text-xs uppercase tracking-wider text-gray-500 font-medium">Фотографий в базе</span>
            <span class="text-3xl font-serif font-bold text-gray-900 block mt-2">{{ $stats['total_photos'] }}</span>
            <span class="text-[0.7rem] text-gray-400 mt-1 block">В оригинальном качестве</span>
        </div>

        <div class="p-5 bg-white border border-gray-200 rounded">
            <span class="text-xs uppercase tracking-wider text-gray-500 font-medium">Пакетов услуг</span>
            <span class="text-3xl font-serif font-bold text-gray-900 block mt-2">{{ $stats['total_packages'] }}</span>
            <span class="text-[0.7rem] text-gray-400 mt-1 block">Редактируемые цены</span>
        </div>

        <div class="p-5 bg-white border border-gray-200 rounded">
            <span class="text-xs uppercase tracking-wider text-gray-500 font-medium">Категорий</span>
            <span class="text-3xl font-serif font-bold text-gray-900 block mt-2">{{ $stats['total_categories'] }}</span>
            <span class="text-[0.7rem] text-gray-400 mt-1 block">Для фильтрации</span>
        </div>
    </div>

    {{-- Recent Series Table --}}
    <div class="bg-white border border-gray-200 rounded overflow-hidden">
        <div class="p-5 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-900">Последние серии</h2>
            <a href="{{ route('admin.series.index') }}" class="text-xs text-terracotta hover:underline font-medium">Смотреть все &rarr;</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-700">
                <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3">Обложка</th>
                        <th class="px-6 py-3">Название (RU / EN)</th>
                        <th class="px-6 py-3">Категория</th>
                        <th class="px-6 py-3">Кадров</th>
                        <th class="px-6 py-3">Статус</th>
                        <th class="px-6 py-3 text-right">Действия</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($recentSeries as $series)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3">
                                <img src="{{ $series->cover_url }}" alt="" class="w-12 h-9 object-cover rounded bg-gray-100">
                            </td>
                            <td class="px-6 py-3">
                                <span class="font-medium text-gray-900 block">{{ $series->title_ru }}</span>
                                <span class="text-xs text-gray-400 block">{{ $series->title_en ?: '—' }}</span>
                            </td>
                            <td class="px-6 py-3 text-xs">
                                {{ $series->category ? $series->category->name_ru : '—' }}
                            </td>
                            <td class="px-6 py-3 text-xs font-mono">
                                {{ $series->photos->count() }}
                            </td>
                            <td class="px-6 py-3 text-xs">
                                @if($series->is_demo)
                                    <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 text-[0.68rem] uppercase font-bold">Demo</span>
                                @elseif($series->is_published)
                                    <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[0.68rem] uppercase font-bold">Опубликовано</span>
                                @else
                                    <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-600 text-[0.68rem] uppercase">Черновик</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-right text-xs">
                                <a href="{{ route('admin.series.edit', $series) }}" class="text-terracotta hover:underline font-medium">Редактировать</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400">Серий пока нет.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
