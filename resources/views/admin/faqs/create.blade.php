@extends('layouts.admin')

@section('title', 'Добавить вопрос FAQ')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-serif font-bold text-slate-900">Новый вопрос FAQ</h1>
            <p class="text-xs text-slate-500 mt-0.5">Добавьте вопрос и ответ для страницы цен и условий съёмки.</p>
        </div>
        <a href="{{ route('admin.faqs.index') }}" class="px-3.5 py-1.5 rounded-lg border border-slate-300 text-xs uppercase tracking-wider font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
            &larr; Назад к списку
        </a>
    </div>

    <form method="POST" action="{{ route('admin.faqs.store') }}" class="space-y-6 bg-white p-6 sm:p-8 border border-slate-200 rounded-xl shadow-sm">
        @csrf

        {{-- Question RU --}}
        <div>
            <label for="question_ru" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1.5">
                Вопрос (на русском) *
            </label>
            <input type="text" name="question_ru" id="question_ru" value="{{ old('question_ru') }}" required
                   placeholder="Например: Как забронировать дату съёмки?"
                   class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900">
        </div>

        {{-- Answer RU --}}
        <div>
            <label for="answer_ru" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1.5">
                Ответ (на русском)
            </label>
            <textarea name="answer_ru" id="answer_ru" rows="4"
                      placeholder="Подробный и понятный ответ для клиента..."
                      class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900">{{ old('answer_ru') }}</textarea>
        </div>

        {{-- Collapsible English Fields --}}
        <div class="pt-2 border-t border-slate-100" x-data="{ openEn: {{ (old('question_en') || old('answer_en')) ? 'true' : 'false' }} }">
            <button type="button" @click="openEn = !openEn" class="text-xs uppercase tracking-wider font-bold text-slate-500 hover:text-slate-900 flex items-center gap-1.5 focus:outline-none">
                <span x-text="openEn ? '−' : '+'"></span>
                <span>Версия на английском (необязательно)</span>
            </button>

            <div x-show="openEn" x-cloak class="mt-4 space-y-4 bg-slate-50 p-4 rounded-lg border border-slate-200">
                <div>
                    <label for="question_en" class="block text-xs uppercase tracking-wider font-bold text-slate-600 mb-1.5">
                        Вопрос (EN)
                    </label>
                    <input type="text" name="question_en" id="question_en" value="{{ old('question_en') }}"
                           placeholder="How can I book a photoshoot?"
                           class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-neutral-900 bg-white">
                </div>

                <div>
                    <label for="answer_en" class="block text-xs uppercase tracking-wider font-bold text-slate-600 mb-1.5">
                        Ответ (EN)
                    </label>
                    <textarea name="answer_en" id="answer_en" rows="3"
                              placeholder="English answer description..."
                              class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-neutral-900 bg-white">{{ old('answer_en') }}</textarea>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
            {{-- Sort order --}}
            <div>
                <label for="sort_order" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1.5">
                    Порядковый номер
                </label>
                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}"
                       class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900">
                <p class="text-[0.7rem] text-slate-400 mt-1">Чем меньше число, тем выше вопрос в списке.</p>
            </div>

            {{-- Published / Draft status --}}
            <div class="flex items-center sm:pt-7">
                <label class="inline-flex items-center gap-3 cursor-pointer select-none">
                    <input type="checkbox" name="is_draft" value="1" {{ old('is_draft', false) ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-slate-300 text-neutral-900 focus:ring-neutral-900">
                    <div>
                        <span class="text-sm font-semibold text-slate-800">Сохранить как черновик</span>
                        <p class="text-[0.7rem] text-slate-400">Если отмечено, вопрос не будет виден на сайте.</p>
                    </div>
                </label>
            </div>
        </div>

        {{-- Actions --}}
        <div class="pt-4 border-t border-slate-200 flex items-center justify-end gap-3">
            <a href="{{ route('admin.faqs.index') }}" class="px-5 py-2.5 border border-slate-300 text-xs uppercase tracking-wider font-semibold text-slate-700 hover:bg-slate-50 rounded-lg transition-colors">
                Отмена
            </a>
            <button type="submit" class="px-6 py-2.5 bg-neutral-900 hover:bg-neutral-800 text-white text-xs uppercase tracking-wider font-bold rounded-lg shadow-sm hover:shadow transition-all cursor-pointer">
                Сохранить вопрос
            </button>
        </div>

    </form>

</div>
@endsection
