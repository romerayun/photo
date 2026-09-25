@extends('layouts.admin')

@section('title', 'Статьи и блог')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-serif font-bold text-slate-900">Статьи и материалы</h1>
            <p class="text-xs text-slate-500 mt-0.5">Управление публикациями, текстами, фотографиями и комментариями читателей.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('articles.index') }}" target="_blank" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 text-xs uppercase tracking-wider font-bold rounded-lg transition-all inline-flex items-center gap-1.5">
                <span>Смотреть раздел</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
            <a href="{{ route('admin.articles.create') }}" class="px-5 py-2.5 bg-neutral-900 hover:bg-neutral-800 text-white text-xs uppercase tracking-wider font-bold rounded-lg shadow-sm hover:shadow transition-all inline-flex items-center gap-2 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span>Написать статью</span>
            </a>
        </div>
    </div>

    {{-- Filter / Search bar --}}
    <div class="flex items-center justify-between gap-4 bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.articles.index') }}" class="flex items-center gap-2 w-full max-w-md">
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}" 
                   placeholder="Поиск по названию или анонсу..." 
                   class="w-full text-xs px-3.5 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-neutral-900">
            <button type="submit" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg transition-colors shrink-0">
                Найти
            </button>
            @if(request('search'))
                <a href="{{ route('admin.articles.index') }}" class="text-xs text-rose-600 hover:underline shrink-0">Сбросить</a>
            @endif
        </form>

        <span class="text-xs text-slate-500 font-mono hidden sm:inline">
            Всего: {{ $articles->total() }} {{ trans_choice('статья|статьи|статей', $articles->total(), [], 'ru') }}
        </span>
    </div>

    {{-- Table --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 border-b border-slate-200 font-semibold">
                    <tr>
                        <th class="px-6 py-3.5">Обложка</th>
                        <th class="px-6 py-3.5">Название статьи</th>
                        <th class="px-6 py-3.5">Дата публикации</th>
                        <th class="px-6 py-3.5 text-center">Просмотры</th>
                        <th class="px-6 py-3.5 text-center">Комментарии</th>
                        <th class="px-6 py-3.5">Статус</th>
                        <th class="px-6 py-3.5 text-right">Действия</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($articles as $art)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4">
                                <img src="{{ $art->cover_url }}" alt="" class="w-16 h-11 object-cover rounded-md bg-slate-100 border border-slate-200 shadow-sm">
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.articles.edit', $art) }}" class="font-bold text-slate-900 hover:text-crimson transition-colors block text-sm">
                                    {{ $art->title }}
                                </a>
                                <span class="text-[0.7rem] text-slate-400 font-mono mt-0.5 block">/articles/{{ $art->slug }}</span>
                            </td>
                            <td class="px-6 py-4 text-xs font-mono text-slate-600">
                                {{ $art->formatted_date }}
                            </td>
                            <td class="px-6 py-4 text-xs font-mono font-bold text-slate-800 text-center">
                                👁 {{ $art->views_count }}
                            </td>
                            <td class="px-6 py-4 text-xs font-mono font-bold text-slate-800 text-center">
                                <span class="px-2 py-0.5 rounded-full {{ $art->comments_count > 0 ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-400' }}">
                                    💬 {{ $art->comments_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($art->is_published)
                                    <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[0.68rem] uppercase font-bold tracking-wider">
                                        Опубликована
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 text-[0.68rem] uppercase font-bold tracking-wider">
                                        Черновик
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('articles.show', $art->slug) }}" target="_blank" class="p-1.5 text-slate-400 hover:text-slate-700 transition-colors inline-block" title="Открыть на сайте">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                                <a href="{{ route('admin.articles.edit', $art) }}" class="p-1.5 text-indigo-600 hover:text-indigo-800 transition-colors inline-block" title="Редактировать">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('admin.articles.destroy', $art) }}" method="POST" class="inline-block" onsubmit="return confirm('Вы действительно хотите удалить статью «{{ $art->title }}» и все её комментарии?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-rose-500 hover:text-rose-700 transition-colors cursor-pointer" title="Удалить">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 font-mono text-xs">
                                Статей пока нет. Создайте первую статью с помощью кнопки выше.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($articles->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $articles->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
