@extends('layouts.admin')

@section('title', 'SEO управление страницами')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div>
            <h1 class="text-2xl font-serif font-bold text-slate-900">SEO настройки страниц</h1>
            <p class="text-xs text-slate-500 mt-1">
                Автоматическое управление Meta Title, Description, Open Graph и Robots для всех страниц сайта. При добавлении новых серий или статей страницы появляются здесь автоматически.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <form method="POST" action="{{ route('admin.seo.sync') }}">
                @csrf
                <button type="submit" class="px-4 py-2.5 bg-neutral-900 hover:bg-neutral-800 text-white text-xs uppercase tracking-wider font-bold rounded-lg shadow-sm hover:shadow transition-all inline-flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>Синхронизировать страницы</span>
                </button>
            </form>
        </div>
    </div>

    {{-- Quick Stat Badges --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-4 bg-white border border-slate-200 rounded-xl shadow-sm">
            <span class="text-[0.7rem] uppercase tracking-wider text-slate-400 font-bold block">Всего в базе SEO</span>
            <span class="text-2xl font-serif font-bold text-slate-900 mt-1 block">{{ $stats['total'] }}</span>
        </div>
        <div class="p-4 bg-white border border-slate-200 rounded-xl shadow-sm">
            <span class="text-[0.7rem] uppercase tracking-wider text-slate-400 font-bold block">Основные разделы</span>
            <span class="text-2xl font-serif font-bold text-slate-900 mt-1 block">{{ $stats['static'] }}</span>
        </div>
        <div class="p-4 bg-white border border-slate-200 rounded-xl shadow-sm">
            <span class="text-[0.7rem] uppercase tracking-wider text-slate-400 font-bold block">Серии съёмок</span>
            <span class="text-2xl font-serif font-bold text-slate-900 mt-1 block">{{ $stats['series'] }}</span>
        </div>
        <div class="p-4 bg-white border border-slate-200 rounded-xl shadow-sm">
            <span class="text-[0.7rem] uppercase tracking-wider text-slate-400 font-bold block">Статьи блога</span>
            <span class="text-2xl font-serif font-bold text-slate-900 mt-1 block">{{ $stats['articles'] }}</span>
        </div>
    </div>

    {{-- Filters & Search --}}
    <div class="bg-white p-4 border border-slate-200 rounded-xl shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-2 overflow-x-auto text-xs font-semibold">
            <a href="{{ route('admin.seo.index', ['search' => $currentSearch]) }}" 
               class="px-3 py-1.5 rounded-lg border transition-colors {{ empty($currentType) ? 'bg-neutral-900 text-white border-neutral-900' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100' }}">
                Все ({{ $stats['total'] }})
            </a>
            <a href="{{ route('admin.seo.index', ['type' => 'static', 'search' => $currentSearch]) }}" 
               class="px-3 py-1.5 rounded-lg border transition-colors {{ $currentType === 'static' ? 'bg-neutral-900 text-white border-neutral-900' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100' }}">
                Основные страницы ({{ $stats['static'] }})
            </a>
            <a href="{{ route('admin.seo.index', ['type' => 'series', 'search' => $currentSearch]) }}" 
               class="px-3 py-1.5 rounded-lg border transition-colors {{ $currentType === 'series' ? 'bg-neutral-900 text-white border-neutral-900' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100' }}">
                Серии ({{ $stats['series'] }})
            </a>
            <a href="{{ route('admin.seo.index', ['type' => 'articles', 'search' => $currentSearch]) }}" 
               class="px-3 py-1.5 rounded-lg border transition-colors {{ $currentType === 'articles' ? 'bg-neutral-900 text-white border-neutral-900' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100' }}">
                Статьи ({{ $stats['articles'] }})
            </a>
        </div>

        <form method="GET" action="{{ route('admin.seo.index') }}" class="flex items-center gap-2">
            @if($currentType)
                <input type="hidden" name="type" value="{{ $currentType }}">
            @endif
            <div class="relative w-full sm:w-64">
                <input type="text" name="search" value="{{ $currentSearch }}" placeholder="Поиск по URL или title..." 
                       class="w-full text-xs pl-8 pr-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-neutral-900">
                <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            @if($currentSearch)
                <a href="{{ route('admin.seo.index', ['type' => $currentType]) }}" class="px-2.5 py-2 text-xs text-slate-500 hover:text-slate-800">
                    Сброс
                </a>
            @endif
        </form>
    </div>

    {{-- SEO Entries Table --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[0.7rem]">
                        <th class="py-3 px-4">URL страницы</th>
                        <th class="py-3 px-4">SEO Title</th>
                        <th class="py-3 px-4">Meta Description</th>
                        <th class="py-3 px-4">Статус генерации</th>
                        <th class="py-3 px-4">Robots</th>
                        <th class="py-3 px-4 text-right">Действия</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($metas as $meta)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            {{-- Path --}}
                            <td class="py-3 px-4 align-top">
                                <a href="{{ url($meta->path === '/' ? '' : $meta->path) }}" target="_blank" class="font-mono text-xs font-bold text-neutral-900 hover:text-crimson flex items-center gap-1 group">
                                    <span>{{ $meta->path }}</span>
                                    <svg class="w-3 h-3 text-slate-400 group-hover:text-crimson transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </a>
                            </td>

                            {{-- Title --}}
                            <td class="py-3 px-4 align-top max-w-xs">
                                <span class="font-semibold text-slate-800 line-clamp-2">{{ $meta->title ?: '—' }}</span>
                            </td>

                            {{-- Description --}}
                            <td class="py-3 px-4 align-top max-w-sm text-slate-500">
                                <span class="line-clamp-2 leading-relaxed">{{ $meta->description ?: '—' }}</span>
                            </td>

                            {{-- Type / Auto status --}}
                            <td class="py-3 px-4 align-top whitespace-nowrap">
                                @if($meta->is_auto_generated)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[0.68rem] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        Авто-генерация
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[0.68rem] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Индивидуальный
                                    </span>
                                @endif
                            </td>

                            {{-- Robots --}}
                            <td class="py-3 px-4 align-top whitespace-nowrap">
                                <span class="font-mono text-[0.7rem] {{ str_contains($meta->robots ?? '', 'noindex') ? 'text-amber-700 bg-amber-50' : 'text-slate-600 bg-slate-100' }} px-1.5 py-0.5 rounded">
                                    {{ $meta->robots ?? 'index, follow' }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="py-3 px-4 align-top text-right whitespace-nowrap space-x-2">
                                <a href="{{ route('admin.seo.edit', $meta) }}" 
                                   class="inline-flex items-center px-2.5 py-1.5 bg-slate-100 hover:bg-neutral-900 hover:text-white rounded text-xs font-bold text-slate-700 transition-colors">
                                    Редактировать
                                </a>
                                @if(!$meta->is_auto_generated)
                                    <form method="POST" action="{{ route('admin.seo.reset', $meta) }}" class="inline-block" onsubmit="return confirm('Сбросить кастомные настройки для {{ $meta->path }} к автоматически сформированным?');">
                                        @csrf
                                        <button type="submit" class="text-xs text-slate-400 hover:text-rose-600 transition-colors underline cursor-pointer">
                                            Сброс
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">
                                Страницы не найдены. Нажмите «Синхронизировать страницы» вверху для обновления списка.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($metas->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $metas->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
