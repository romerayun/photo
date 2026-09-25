@extends('layouts.admin')

@section('title', 'Серии съёмок')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-serif font-bold text-slate-900">Серии съёмок</h1>
            <p class="text-xs text-slate-500 mt-0.5">Управление фотоисториями, загрузка кадров, порядок и видимость на сайте.</p>
        </div>
        <a href="{{ route('admin.series.create') }}" class="px-5 py-2.5 bg-neutral-900 hover:bg-neutral-800 text-white text-xs uppercase tracking-wider font-bold rounded-lg shadow-sm hover:shadow transition-all inline-flex items-center gap-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            <span>Создать новую серию</span>
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 border-b border-slate-200 font-semibold">
                    <tr>
                        <th class="px-6 py-3.5">Порядок</th>
                        <th class="px-6 py-3.5">Обложка</th>
                        <th class="px-6 py-3.5">Название серии</th>
                        <th class="px-6 py-3.5">Категория</th>
                        <th class="px-6 py-3.5">Кадров</th>
                        <th class="px-6 py-3.5">Статусы</th>
                        <th class="px-6 py-3.5 text-right">Действия</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($series as $s)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4 font-mono text-xs text-slate-400 font-bold">
                                #{{ $s->sort_order }}
                            </td>
                            <td class="px-6 py-4">
                                <img src="{{ $s->cover_url }}" alt="" class="w-16 h-11 object-cover rounded-md bg-slate-100 border border-slate-200 shadow-sm">
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-900 block text-sm">{{ $s->title_ru }}</span>
                                <span class="text-[0.7rem] text-slate-400 font-mono mt-0.5 block">/series/{{ $s->slug }}</span>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600">
                                {{ $s->category ? $s->category->name_ru : '—' }}
                            </td>
                            <td class="px-6 py-4 text-xs font-mono font-bold text-slate-800">
                                {{ $s->photos->count() }}
                            </td>
                            <td class="px-6 py-4 space-x-1">
                                @if($s->is_demo)
                                    <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[0.65rem] uppercase font-bold tracking-wider">Demo</span>
                                @endif
                                @if($s->is_featured)
                                    <span class="px-2 py-0.5 rounded-full bg-purple-100 text-purple-800 text-[0.65rem] uppercase font-bold tracking-wider">На главной</span>
                                @endif
                                @if($s->is_published)
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[0.65rem] uppercase font-bold tracking-wider">Опубликована</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[0.65rem] uppercase font-medium tracking-wider">Черновик</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-xs">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('series.show', ['slug' => $s->slug]) }}" target="_blank" class="px-2.5 py-1.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition-colors">
                                        Сайт &nearr;
                                    </a>
                                    <a href="{{ route('admin.series.edit', $s) }}" class="px-3 py-1.5 rounded bg-neutral-900 hover:bg-neutral-800 text-white font-semibold transition-colors shadow-sm">
                                        Редактировать
                                    </a>
                                    <form method="POST" action="{{ route('admin.series.destroy', $s) }}" class="inline-block" onsubmit="return confirm('Удалить серию? Все загруженные фотографии также будут удалены.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1.5 rounded text-rose-600 hover:bg-rose-50 hover:text-rose-800 font-semibold transition-colors cursor-pointer">
                                            Удалить
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                Серий пока нет. Нажмите «Создать новую серию».
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($series->hasPages())
        <div class="p-4 border-t border-slate-200 bg-slate-50">
            {{ $series->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
