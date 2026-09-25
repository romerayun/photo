@extends('layouts.admin')

@section('title', 'Пакеты услуг и цены')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-serif font-bold text-slate-900">Пакеты услуг и цены</h1>
            <p class="text-xs text-slate-500 mt-0.5">Управление форматами съёмок, включёнными услугами и стоимостью.</p>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 border-b border-slate-200 font-semibold">
                    <tr>
                        <th class="px-6 py-3.5">Порядок</th>
                        <th class="px-6 py-3.5">Название пакета</th>
                        <th class="px-6 py-3.5">Длительность</th>
                        <th class="px-6 py-3.5">Кадры</th>
                        <th class="px-6 py-3.5">Стоимость</th>
                        <th class="px-6 py-3.5">Статус</th>
                        <th class="px-6 py-3.5 text-right">Действия</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($packages as $pkg)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4 font-mono text-xs text-slate-400 font-bold">
                                #{{ $pkg->sort_order }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-900 block text-sm">{{ $pkg->title_ru }}</span>
                                @if($pkg->subtitle_ru)
                                    <span class="text-xs text-slate-500 block mt-0.5">{{ $pkg->subtitle_ru }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600">
                                {{ $pkg->duration_ru ?: '—' }}
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600 font-mono">
                                {{ $pkg->photo_count_ru ?: '—' }}
                            </td>
                            <td class="px-6 py-4 text-xs font-serif text-slate-900 font-bold text-sm">
                                {{ $pkg->formattedPrice('ru') }}
                            </td>
                            <td class="px-6 py-4 text-xs">
                                @if($pkg->is_published)
                                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[0.68rem] uppercase font-bold tracking-wider">Активен</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[0.68rem] uppercase font-medium tracking-wider">Скрыт</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-xs">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.packages.edit', $pkg) }}" class="px-3.5 py-1.5 rounded-lg bg-neutral-900 hover:bg-neutral-800 text-white font-semibold transition-colors shadow-sm">
                                        Редактировать
                                    </a>
                                    <form method="POST" action="{{ route('admin.packages.destroy', $pkg) }}" class="inline-block" onsubmit="return confirm('Вы уверены, что хотите удалить пакет «{{ $pkg->title_ru }}»?');">
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
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-sm">
                                Пакетов пока нет.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
