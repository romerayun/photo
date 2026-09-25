@extends('layouts.admin')

@section('title', 'Редактировать SEO — ' . $seo->path)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-mono font-bold px-2 py-0.5 rounded bg-slate-200 text-slate-700">
                    {{ $seo->path }}
                </span>
                @if($seo->is_auto_generated)
                    <span class="text-[0.68rem] px-2 py-0.5 rounded font-bold bg-blue-50 text-blue-700 border border-blue-200">
                        Авто-сформировано
                    </span>
                @else
                    <span class="text-[0.68rem] px-2 py-0.5 rounded font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Ручная настройка
                    </span>
                @endif
            </div>
            <h1 class="text-2xl font-serif font-bold text-slate-900">SEO настройки страницы</h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Страница: <a href="{{ url($seo->path === '/' ? '' : $seo->path) }}" target="_blank" class="text-neutral-900 font-bold hover:underline">{{ url($seo->path === '/' ? '' : $seo->path) }} &nearr;</a>
            </p>
        </div>
        <a href="{{ route('admin.seo.index') }}" class="px-3.5 py-1.5 rounded-lg border border-slate-300 text-xs uppercase tracking-wider font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
            &larr; Назад к списку
        </a>
    </div>

    <form method="POST" action="{{ route('admin.seo.update', $seo) }}" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Search Engine Display (Snippet Preview) --}}
        <div class="bg-white p-6 border border-slate-200 rounded-xl shadow-sm space-y-3">
            <h2 class="text-xs uppercase tracking-wider font-bold text-slate-500">
                Предпросмотр в поисковой выдаче (Google / Яндекс)
            </h2>
            <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg max-w-2xl font-sans">
                <div class="text-[13px] text-slate-500 truncate mb-0.5 font-mono">
                    {{ url($seo->path === '/' ? '' : $seo->path) }}
                </div>
                <div id="preview-title" class="text-lg text-[#1a0dab] font-medium leading-snug hover:underline cursor-pointer line-clamp-1">
                    {{ $seo->title ?: 'Заголовок страницы' }}
                </div>
                <div id="preview-desc" class="text-xs text-[#4d5156] mt-1 line-clamp-2 leading-relaxed">
                    {{ $seo->description ?: 'Описание страницы для сниппета в поисковике...' }}
                </div>
            </div>
        </div>

        {{-- Meta Tags Block --}}
        <div class="bg-white p-6 sm:p-8 border border-slate-200 rounded-xl shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-slate-900">Основные мета-теги</h2>
                <p class="text-xs text-slate-500 mt-0.5">Влияют на ранжирование и внешний вид сниппета в результатах поиска.</p>
            </div>

            <div class="space-y-4">
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="title" class="block text-xs uppercase tracking-wider font-bold text-slate-700">
                            Meta Title <span class="text-rose-600">*</span>
                        </label>
                        <span id="title-counter" class="text-[0.7rem] font-mono text-slate-400">0 / 65 символов</span>
                    </div>
                    <input type="text" name="title" id="title" value="{{ old('title', $seo->title) }}" required
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="description" class="block text-xs uppercase tracking-wider font-bold text-slate-700">
                            Meta Description
                        </label>
                        <span id="desc-counter" class="text-[0.7rem] font-mono text-slate-400">0 / 160 символов</span>
                    </div>
                    <textarea name="description" id="description" rows="3"
                              class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900 leading-relaxed">{{ old('description', $seo->description) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label for="canonical" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1.5">
                            Canonical URL (канонический адрес)
                        </label>
                        <input type="url" name="canonical" id="canonical" value="{{ old('canonical', $seo->canonical) }}"
                               placeholder="https://..."
                               class="w-full px-4 py-2 border border-slate-300 rounded-lg text-xs font-mono text-slate-900 focus:outline-none focus:border-neutral-900">
                    </div>

                    <div>
                        <label for="robots" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1.5">
                            Meta Robots
                        </label>
                        <select name="robots" id="robots" class="w-full px-4 py-2 border border-slate-300 rounded-lg text-xs text-slate-900 bg-white focus:outline-none focus:border-neutral-900">
                            <option value="index, follow" {{ old('robots', $seo->robots) === 'index, follow' ? 'selected' : '' }}>index, follow (индексировать и переходить)</option>
                            <option value="noindex, follow" {{ old('robots', $seo->robots) === 'noindex, follow' ? 'selected' : '' }}>noindex, follow (не индексировать, переходить по ссылкам)</option>
                            <option value="noindex, nofollow" {{ old('robots', $seo->robots) === 'noindex, nofollow' ? 'selected' : '' }}>noindex, nofollow (закрыть от поиска)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Open Graph Social Block --}}
        <div class="bg-white p-6 sm:p-8 border border-slate-200 rounded-xl shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-slate-900">Социальные сети (Open Graph / Telegram / VK)</h2>
                <p class="text-xs text-slate-500 mt-0.5">Отображаются при репосте ссылки в мессенджеры и социальные платформы. Если не заполнены, используются значения Title и Description.</p>
            </div>

            <div class="space-y-4">
                <div>
                    <label for="og_title" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1.5">
                        OG Title
                    </label>
                    <input type="text" name="og_title" id="og_title" value="{{ old('og_title', $seo->og_title) }}"
                           placeholder="Оставьте пустым для использования Meta Title"
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-neutral-900">
                </div>

                <div>
                    <label for="og_description" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1.5">
                        OG Description
                    </label>
                    <textarea name="og_description" id="og_description" rows="2"
                              placeholder="Оставьте пустым для использования Meta Description"
                              class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-neutral-900 leading-relaxed">{{ old('og_description', $seo->og_description) }}</textarea>
                </div>

                <div>
                    <label for="og_image" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1.5">
                        OG Image URL (ссылка на изображение обложки)
                    </label>
                    <input type="text" name="og_image" id="og_image" value="{{ old('og_image', $seo->og_image) }}"
                           placeholder="https://..."
                           class="w-full px-4 py-2 border border-slate-300 rounded-lg text-xs font-mono text-slate-900 focus:outline-none focus:border-neutral-900">
                    @if($seo->og_image)
                        <div class="mt-2 w-32 aspect-[3/2] rounded border border-slate-200 overflow-hidden bg-slate-100">
                            <img src="{{ $seo->og_image }}" alt="" class="w-full h-full object-cover">
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="border-t border-slate-200 pt-6 flex items-center justify-between">
            <a href="{{ route('admin.seo.index') }}" class="text-xs uppercase tracking-wider text-slate-500 hover:text-slate-800 font-semibold">
                Отмена
            </a>
            <button type="submit" class="px-8 py-3 bg-neutral-900 hover:bg-neutral-800 text-white text-xs uppercase tracking-wider font-bold rounded-lg shadow-sm hover:shadow transition-all inline-flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span>Сохранить SEO настройки</span>
            </button>
        </div>

    </form>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const titleInput = document.getElementById('title');
        const descInput = document.getElementById('description');
        const previewTitle = document.getElementById('preview-title');
        const previewDesc = document.getElementById('preview-desc');
        const titleCounter = document.getElementById('title-counter');
        const descCounter = document.getElementById('desc-counter');

        function updateCounters() {
            if (titleInput && previewTitle && titleCounter) {
                const len = titleInput.value.length;
                titleCounter.textContent = len + ' / 65 символов';
                titleCounter.className = len > 70 ? 'text-[0.7rem] font-mono text-rose-500 font-bold' : 'text-[0.7rem] font-mono text-slate-400';
                previewTitle.textContent = titleInput.value.trim() || 'Заголовок страницы';
            }
            if (descInput && previewDesc && descCounter) {
                const len = descInput.value.length;
                descCounter.textContent = len + ' / 160 символов';
                descCounter.className = len > 170 ? 'text-[0.7rem] font-mono text-rose-500 font-bold' : 'text-[0.7rem] font-mono text-slate-400';
                previewDesc.textContent = descInput.value.trim() || 'Описание страницы для сниппета в поисковике...';
            }
        }

        if (titleInput) titleInput.addEventListener('input', updateCounters);
        if (descInput) descInput.addEventListener('input', updateCounters);
        updateCounters();
    });
</script>
@endsection
