@extends('layouts.admin')

@section('title', 'Категории съёмок')

@section('content')
<div class="space-y-8">

    <div class="flex items-center justify-between border-b border-gray-200 pb-4">
        <div>
            <h1 class="text-2xl font-serif font-bold text-gray-900">Категории съёмок</h1>
            <p class="text-xs text-gray-500 mt-0.5">Разделы для группировки и фильтрации портфолио на русском и английском языках.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        {{-- List & Edit Table --}}
        <div class="lg:col-span-8 bg-white border border-gray-200 rounded overflow-hidden">
            <table class="w-full text-left text-sm text-gray-700">
                <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3">Порядок</th>
                        <th class="px-6 py-3">Название (RU)</th>
                        <th class="px-6 py-3">Название (EN)</th>
                        <th class="px-6 py-3">Slug</th>
                        <th class="px-6 py-3">Серий</th>
                        <th class="px-6 py-3 text-right">Действия</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($categories as $category)
                        <tr class="hover:bg-gray-50">
                            <form method="POST" action="{{ route('admin.categories.update', $category) }}">
                                @csrf
                                @method('PUT')
                                <td class="px-6 py-3">
                                    <input type="number" name="sort_order" value="{{ $category->sort_order }}" class="w-16 px-2 py-1 border border-gray-300 rounded text-xs font-mono">
                                </td>
                                <td class="px-6 py-3">
                                    <input type="text" name="name_ru" value="{{ $category->name_ru }}" required class="w-full px-2 py-1 border border-gray-300 rounded text-xs">
                                </td>
                                <td class="px-6 py-3">
                                    <input type="text" name="name_en" value="{{ $category->name_en }}" required class="w-full px-2 py-1 border border-gray-300 rounded text-xs">
                                </td>
                                <td class="px-6 py-3">
                                    <input type="text" name="slug" value="{{ $category->slug }}" required class="w-full px-2 py-1 border border-gray-300 rounded text-xs font-mono">
                                </td>
                                <td class="px-6 py-3 text-xs font-mono">
                                    {{ $category->series_count }}
                                </td>
                                <td class="px-6 py-3 text-right text-xs space-x-2 shrink-0">
                                    <button type="submit" class="text-terracotta hover:underline font-semibold">Сохранить</button>
                            </form>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline-block" onsubmit="return confirm('Удалить категорию? Серии останутся без категории.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700">Удалить</button>
                                    </form>
                                </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400">Категорий нет.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Add New Category Form --}}
        <div class="lg:col-span-4 bg-white p-6 border border-gray-200 rounded self-start">
            <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-900 mb-4">Добавить категорию</h2>
            
            <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="new_name_ru" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                        Название на русском *
                    </label>
                    <input type="text" name="name_ru" id="new_name_ru" required placeholder="Например: Пейзажи"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">
                </div>

                <div>
                    <label for="new_name_en" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                        Название на английском (EN) *
                    </label>
                    <input type="text" name="name_en" id="new_name_en" required placeholder="e.g. Landscapes"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">
                </div>

                <div>
                    <label for="new_slug" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                        Slug (URL)
                    </label>
                    <input type="text" name="slug" id="new_slug" placeholder="landscapes"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm font-mono text-xs focus:border-terracotta focus:ring-1 focus:ring-terracotta">
                </div>

                <div>
                    <label for="new_sort_order" class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                        Порядок сортировки
                    </label>
                    <input type="number" name="sort_order" id="new_sort_order" value="10"
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:border-terracotta focus:ring-1 focus:ring-terracotta">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-2.5 bg-graphite-900 text-white text-xs uppercase tracking-wider font-medium hover:bg-terracotta transition-colors rounded">
                        Добавить категорию
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>
@endsection
