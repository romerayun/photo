@extends('layouts.admin')

@section('title', 'Пакеты услуг и цены')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between border-b border-gray-200 pb-4">
        <div>
            <h1 class="text-2xl font-serif font-bold text-gray-900">Пакеты услуг и цены</h1>
            <p class="text-xs text-gray-500 mt-0.5">Редактирование форматов, условий и стоимости фотосессий.</p>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded overflow-hidden">
        <table class="w-full text-left text-sm text-gray-700">
            <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3">Порядок</th>
                    <th class="px-6 py-3">Название (RU / EN)</th>
                    <th class="px-6 py-3">Длительность</th>
                    <th class="px-6 py-3">Кадры</th>
                    <th class="px-6 py-3">Стоимость</th>
                    <th class="px-6 py-3">Статус</th>
                    <th class="px-6 py-3 text-right">Действия</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($packages as $pkg)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-mono text-xs text-gray-400">
                            {{ $pkg->sort_order }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-medium text-gray-900 block">{{ $pkg->title_ru }}</span>
                            <span class="text-xs text-gray-400 block">{{ $pkg->title_en ?: '—' }}</span>
                        </td>
                        <td class="px-6 py-4 text-xs">
                            {{ $pkg->duration_ru ?: '—' }}
                        </td>
                        <td class="px-6 py-4 text-xs">
                            {{ $pkg->photo_count_ru ?: '—' }}
                        </td>
                        <td class="px-6 py-4 text-xs font-serif text-terracotta font-semibold">
                            {{ $pkg->formattedPrice('ru') }}
                        </td>
                        <td class="px-6 py-4 text-xs">
                            @if($pkg->is_published)
                                <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[0.65rem] uppercase font-bold">Активен</span>
                            @else
                                <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-600 text-[0.65rem] uppercase">Скрыт</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right text-xs">
                            <a href="{{ route('admin.packages.edit', $pkg) }}" class="text-terracotta hover:underline font-semibold">
                                Редактировать &rarr;
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection
