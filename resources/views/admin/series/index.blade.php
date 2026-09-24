@extends('layouts.admin')

@section('title', 'Серии съёмок')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-serif font-bold text-gray-900">Серии съёмок</h1>
            <p class="text-xs text-gray-500 mt-0.5">Управление историями, обложками, фотографиями и публикацией.</p>
        </div>
        <a href="{{ route('admin.series.create') }}" class="px-4 py-2 bg-graphite-900 text-white text-xs uppercase tracking-wider font-medium hover:bg-terracotta transition-colors rounded">
            + Создать новую серию
        </a>
    </div>

    <div class="bg-white border border-gray-200 rounded overflow-hidden">
        <table class="w-full text-left text-sm text-gray-700">
            <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3">Порядок</th>
                    <th class="px-6 py-3">Обложка</th>
                    <th class="px-6 py-3">Название (RU / EN)</th>
                    <th class="px-6 py-3">Категория</th>
                    <th class="px-6 py-3">Кадров</th>
                    <th class="px-6 py-3">Статусы</th>
                    <th class="px-6 py-3 text-right">Действия</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($series as $s)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-mono text-xs text-gray-400">
                            {{ $s->sort_order }}
                        </td>
                        <td class="px-6 py-4">
                            <img src="{{ $s->cover_url }}" alt="" class="w-14 h-10 object-cover rounded bg-gray-100">
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-medium text-gray-900 block">{{ $s->title_ru }}</span>
                            <span class="text-xs text-gray-400 block">{{ $s->title_en ?: '—' }}</span>
                            <span class="text-[0.68rem] text-gray-400 font-mono">/series/{{ $s->slug }}</span>
                        </td>
                        <td class="px-6 py-4 text-xs">
                            {{ $s->category ? $s->category->name_ru : '—' }}
                        </td>
                        <td class="px-6 py-4 text-xs font-mono">
                            {{ $s->photos->count() }}
                        </td>
                        <td class="px-6 py-4 space-x-1">
                            @if($s->is_demo)
                                <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 text-[0.65rem] uppercase font-bold">Demo</span>
                            @endif
                            @if($s->is_featured)
                                <span class="px-2 py-0.5 rounded bg-purple-100 text-purple-800 text-[0.65rem] uppercase font-bold">На главной</span>
                            @endif
                            @if($s->is_published)
                                <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[0.65rem] uppercase font-bold">Опубликована</span>
                            @else
                                <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-600 text-[0.65rem] uppercase">Черновик</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right text-xs space-x-3">
                            <a href="{{ route('series.show', ['slug' => $s->slug]) }}" target="_blank" class="text-gray-500 hover:text-gray-900">Просмотр</a>
                            <a href="{{ route('admin.series.edit', $s) }}" class="text-terracotta hover:underline font-semibold">Редактировать</a>
                            <form method="POST" action="{{ route('admin.series.destroy', $s) }}" class="inline-block" onsubmit="return confirm('Удалить серию? Все загруженные фотографии также будут удалены.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700">Удалить</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                            Серий пока нет. Нажмите «Создать новую серию».
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($series->hasPages())
        <div class="p-4 border-t border-gray-200">
            {{ $series->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
