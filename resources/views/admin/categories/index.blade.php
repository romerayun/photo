@extends('layouts.admin')

@section('title', 'Категории съёмок')

@section('content')
<div class="space-y-8">

    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-serif font-bold text-slate-900">Категории съёмок</h1>
            <p class="text-xs text-slate-500 mt-0.5">Разделы съёмок на главной странице и фильтры в портфолио: название, описание и фотография обложки.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-sm rounded-xl">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8 items-start">
        
        {{-- List of Categories --}}
        <div class="xl:col-span-8 space-y-6">
            @forelse($categories as $category)
                <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm hover:border-slate-300 transition-colors">
                    <form id="update-cat-{{ $category->id }}" method="POST" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                            
                            {{-- Photo preview & upload --}}
                            <div class="md:col-span-4 space-y-3">
                                <label class="block text-xs uppercase tracking-wider font-bold text-slate-700">
                                    Фотография обложки
                                </label>
                                <div class="relative w-full aspect-[3/2] rounded-lg overflow-hidden bg-slate-100 border border-slate-200 shadow-inner">
                                    <img src="{{ $category->image_url }}" alt="{{ $category->name_ru }}" class="w-full h-full object-cover">
                                    @if($category->image)
                                        <span class="absolute top-2 right-2 px-2 py-0.5 bg-black/75 text-[0.65rem] text-white font-mono rounded">
                                            Своё фото
                                        </span>
                                    @else
                                        <span class="absolute top-2 right-2 px-2 py-0.5 bg-neutral-800/80 text-[0.65rem] text-neutral-300 font-mono rounded">
                                            Демо-кадр
                                        </span>
                                    @endif
                                </div>
                                
                                <div>
                                    <input type="file" name="image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                                </div>

                                @if($category->image)
                                    <div class="flex items-center gap-2 pt-1">
                                        <input type="checkbox" name="remove_image" id="remove_image_{{ $category->id }}" value="1" class="rounded border-slate-300 text-crimson focus:ring-crimson text-xs">
                                        <label for="remove_image_{{ $category->id }}" class="text-xs text-rose-600 font-medium cursor-pointer">
                                            Удалить загруженное фото
                                        </label>
                                    </div>
                                @endif

                                <div class="pt-2 text-xs text-slate-500 font-mono">
                                    Серий в портфолио: <span class="font-bold text-slate-900">{{ $category->series_count }}</span>
                                </div>
                            </div>

                            {{-- Category fields --}}
                            <div class="md:col-span-8 space-y-4">
                                <div>
                                    <label class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1">
                                        Название категории *
                                    </label>
                                    <input type="text" name="name_ru" value="{{ old('name_ru', $category->name_ru) }}" required class="w-full px-3.5 py-2 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900">
                                </div>

                                <div>
                                    <label class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1">
                                        Краткое описание
                                    </label>
                                    <textarea name="description_ru" rows="2" placeholder="Например: Индивидуальные портреты для себя, съёмка в студии или на прогулке" class="w-full px-3.5 py-2 border border-slate-300 rounded-lg text-xs text-slate-900 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900 leading-relaxed">{{ old('description_ru', $category->description_ru) }}</textarea>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1">
                                            Slug (URL) *
                                        </label>
                                        <input type="text" name="slug" value="{{ old('slug', $category->slug) }}" required class="w-full px-3 py-1.5 border border-slate-300 rounded-lg text-xs font-mono text-slate-900 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900">
                                    </div>
                                    <div>
                                        <label class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1">
                                            Порядок
                                        </label>
                                        <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}" class="w-full px-3 py-1.5 border border-slate-300 rounded-lg text-xs font-mono text-slate-900 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900">
                                    </div>
                                </div>

                                <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                                    <button type="submit" form="update-cat-{{ $category->id }}" class="px-5 py-2.5 bg-neutral-900 hover:bg-neutral-800 text-white text-xs uppercase tracking-wider font-bold rounded-lg shadow-sm hover:shadow transition-all inline-flex items-center gap-1.5 cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        <span>Сохранить</span>
                                    </button>

                                    <button type="submit" form="delete-cat-{{ $category->id }}" class="text-xs text-rose-600 hover:text-rose-800 hover:underline font-bold transition-colors cursor-pointer">
                                        Удалить категорию
                                    </button>
                                </div>
                            </div>

                        </div>
                    </form>

                    <form id="delete-cat-{{ $category->id }}" method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Удалить категорию «{{ $category->name_ru }}»? Все серии останутся в базе без категории.');" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            @empty
                <div class="bg-white border border-slate-200 rounded-xl p-8 text-center text-slate-400">
                    Категорий пока нет.
                </div>
            @endforelse
        </div>

        {{-- Add New Category Form --}}
        <div class="xl:col-span-4 bg-white p-6 border border-slate-200 rounded-xl sticky top-20 shadow-sm">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                <svg class="w-4 h-4 text-crimson" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span>Добавить категорию</span>
            </h2>
            
            <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label for="new_name_ru" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1">
                        Название категории *
                    </label>
                    <input type="text" name="name_ru" id="new_name_ru" required placeholder="Например: Портреты"
                           class="w-full px-3.5 py-2 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900">
                </div>

                <div>
                    <label for="new_desc_ru" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1">
                        Краткое описание
                    </label>
                    <textarea name="description_ru" id="new_desc_ru" rows="2" placeholder="Небольшое описание направления съёмок"
                              class="w-full px-3.5 py-2 border border-slate-300 rounded-lg text-xs text-slate-900 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900 leading-relaxed"></textarea>
                </div>

                <div>
                    <label for="new_image" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1">
                        Фотография обложки
                    </label>
                    <input type="file" name="image" id="new_image" accept="image/*"
                           class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="new_slug" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1">
                            Slug (URL)
                        </label>
                        <input type="text" name="slug" id="new_slug" placeholder="portraits"
                               class="w-full px-3 py-1.5 border border-slate-300 rounded-lg text-xs font-mono text-slate-900 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900">
                    </div>
                    <div>
                        <label for="new_sort_order" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1">
                            Порядок
                        </label>
                        <input type="number" name="sort_order" id="new_sort_order" value="10"
                               class="w-full px-3 py-1.5 border border-slate-300 rounded-lg text-xs font-mono text-slate-900 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900">
                    </div>
                </div>

                <div class="pt-3">
                    <button type="submit" class="w-full py-3 bg-neutral-900 hover:bg-neutral-800 text-white text-xs uppercase tracking-wider font-bold rounded-lg shadow-sm hover:shadow transition-all inline-flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        <span>Добавить категорию</span>
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>
@endsection
