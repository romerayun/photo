@extends('layouts.admin')

@section('title', 'Частые вопросы (FAQ)')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-serif font-bold text-slate-900">Частые вопросы (FAQ)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Управление вопросами и ответами, которые выводятся в блоке FAQ на странице цен и условий съёмки.</p>
        </div>
        <a href="{{ route('admin.faqs.create') }}" class="px-4 py-2.5 bg-neutral-900 hover:bg-neutral-800 text-white text-xs uppercase tracking-wider font-bold rounded-lg shadow-sm hover:shadow transition-all inline-flex items-center gap-2 self-start sm:self-auto cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            <span>Добавить вопрос</span>
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 border-b border-slate-200 font-semibold">
                    <tr>
                        <th class="px-6 py-3.5 w-20">Порядок</th>
                        <th class="px-6 py-3.5">Вопрос и ответ</th>
                        <th class="px-6 py-3.5 w-36">Статус</th>
                        <th class="px-6 py-3.5 text-right w-52">Действия</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($faqs as $faq)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4 font-mono text-xs text-slate-400 font-bold align-top">
                                #{{ $faq->sort_order }}
                            </td>
                            <td class="px-6 py-4 align-top">
                                <div class="font-bold text-slate-900 text-sm">
                                    {{ $faq->question_ru }}
                                </div>
                                @if($faq->question_en)
                                    <div class="text-xs text-slate-400 mt-0.5 font-mono">
                                        EN: {{ $faq->question_en }}
                                    </div>
                                @endif

                                @if($faq->answer_ru)
                                    <div class="text-xs text-slate-600 mt-2 line-clamp-2 leading-relaxed">
                                        {{ $faq->answer_ru }}
                                    </div>
                                @else
                                    <div class="text-xs text-amber-600 italic mt-2">
                                        Ответ ещё не заполнен
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs align-top">
                                @if(!$faq->is_draft)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[0.68rem] uppercase font-bold tracking-wider">
                                        Опубликован
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[0.68rem] uppercase font-bold tracking-wider">
                                        Черновик
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-xs align-top">
                                <div class="inline-flex items-center gap-2">
                                    <form method="POST" action="{{ route('admin.faqs.toggle', $faq) }}" class="inline-block">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1.5 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-100 font-semibold transition-colors cursor-pointer" title="{{ $faq->is_draft ? 'Опубликовать' : 'В черновик' }}">
                                            {{ $faq->is_draft ? 'Опубликовать' : 'В черновик' }}
                                        </button>
                                    </form>

                                    <a href="{{ route('admin.faqs.edit', $faq) }}" class="px-3.5 py-1.5 rounded-lg bg-neutral-900 hover:bg-neutral-800 text-white font-semibold transition-colors shadow-sm">
                                        Изменить
                                    </a>

                                    <form method="POST" action="{{ route('admin.faqs.destroy', $faq) }}" class="inline-block" onsubmit="return confirm('Удалить этот вопрос?');">
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
                            <td colspan="4" class="px-6 py-12 text-center text-slate-400 text-sm">
                                Вопросов пока нет. Создайте первый вопрос для блока FAQ.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
