@extends('layouts.admin')

@section('title', 'Клиенты в MAX и массовая рассылка')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">

    {{-- Header & Tabs --}}
    <div class="border-b border-slate-200 pb-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                    <h1 class="text-2xl font-serif font-bold text-slate-900">Бот MAX (@se14454241_bot)</h1>
                </div>
                <p class="text-xs text-slate-500 mt-1">Список привязанных клиентов и отправка массовых сообщений/рассылок.</p>
            </div>
            
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.max_bot.index') }}" 
                   class="px-3.5 py-2 rounded-lg text-xs font-bold uppercase tracking-wider bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors">
                    Кнопки и тексты
                </a>
                <a href="{{ route('admin.max_bot.clients') }}" 
                   class="px-3.5 py-2 rounded-lg text-xs font-bold uppercase tracking-wider bg-neutral-900 text-white shadow-sm">
                    Клиенты и рассылка
                </a>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div>
                <span class="text-xs uppercase tracking-wider font-bold text-slate-400 block">Уникальных клиентов</span>
                <span class="text-2xl font-bold text-slate-900">{{ $uniqueSubscribersCount }}</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <span class="text-xs uppercase tracking-wider font-bold text-slate-400 block">Всего привязок съёмок</span>
                <span class="text-2xl font-bold text-slate-900">{{ $shoots->count() }}</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
            </div>
            <div>
                <span class="text-xs uppercase tracking-wider font-bold text-slate-400 block">Статус рассылки</span>
                <span class="text-sm font-bold text-emerald-600 flex items-center gap-1.5 mt-1">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Готов к отправке
                </span>
            </div>
        </div>
    </div>

    {{-- Broadcast Form --}}
    <form method="POST" action="{{ route('admin.max_bot.broadcast') }}" id="broadcast-form" class="space-y-6">
        @csrf

        <div class="bg-white p-6 sm:p-8 border border-slate-200 rounded-xl shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-neutral-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        <span>Создать массовую рассылку</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Сообщение придет клиентам в мессенджер MAX от имени вашего бота.</p>
                </div>

                <div class="flex items-center gap-4 text-xs font-semibold">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="radio" name="target" value="all" checked id="target-all"
                               class="text-neutral-900 focus:ring-neutral-900">
                        <span class="ml-1.5 text-slate-700">Всем подписчикам ({{ $uniqueSubscribersCount }})</span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="radio" name="target" value="selected" id="target-selected"
                               class="text-neutral-900 focus:ring-neutral-900">
                        <span class="ml-1.5 text-slate-700">Выбранным в таблице</span>
                    </label>
                </div>
            </div>

            <div>
                <label for="broadcast-message" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1.5">
                    Текст рассылки *
                </label>
                <textarea name="message" id="broadcast-message" rows="5" required
                          placeholder="Здравствуйте, {client_name}! Хочу сообщить о сезонной акции..."
                          class="w-full px-4 py-3 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900 leading-relaxed font-sans">{{ old('message') }}</textarea>
                
                <div class="mt-3 p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-2 text-slate-500">
                            <span>Подстановка имени:</span>
                            <code class="px-2 py-0.5 rounded bg-white border border-slate-200 text-crimson font-mono cursor-pointer select-all" title="Кликните, чтобы выделить">{client_name}</code>
                            <span class="text-slate-400">|</span>
                            <span>Форматирование: <b>**жирный**</b>, <i>*курсив*</i></span>
                        </div>

                        <div class="flex items-center gap-3 font-semibold text-slate-700">
                            <span class="text-slate-500 uppercase tracking-wider text-[0.7rem]">Кнопки:</span>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" name="attach_buttons" value="none" class="text-neutral-900 focus:ring-neutral-900">
                                <span class="ml-1 text-slate-600">Без кнопок</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" name="attach_buttons" value="all" class="text-neutral-900 focus:ring-neutral-900">
                                <span class="ml-1 text-slate-600">Все кнопки меню</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" name="attach_buttons" value="custom" checked class="text-neutral-900 focus:ring-neutral-900">
                                <span class="ml-1 text-slate-900 font-bold">Выбрать нужные</span>
                            </label>
                        </div>
                    </div>

                    {{-- Button Checkboxes --}}
                    <div id="buttons-selector" class="pt-3 border-t border-slate-200 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
                        @foreach($availableButtons as $bKey => $bTitle)
                            <label class="flex items-center p-2.5 rounded-lg bg-white border border-slate-200 hover:border-slate-300 transition-colors cursor-pointer select-none">
                                <input type="checkbox" name="selected_buttons[]" value="{{ $bKey }}"
                                       class="w-4 h-4 rounded border-slate-300 text-neutral-900 focus:ring-neutral-900 mr-2.5">
                                <span class="text-xs font-medium text-slate-800 truncate">{{ $bTitle }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" 
                        onclick="return confirm('Вы уверены, что хотите отправить это сообщение в MAX?');"
                        class="px-8 py-3 bg-neutral-900 hover:bg-neutral-800 text-white text-xs uppercase tracking-wider font-bold rounded-lg shadow-sm hover:shadow transition-all inline-flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    <span>Запустить рассылку</span>
                </button>
            </div>
        </div>

        {{-- Clients Table --}}
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden space-y-0">
            <div class="p-5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Список привязанных клиентов ({{ $shoots->count() }})</h3>
                    <p class="text-xs text-slate-500">Клиенты, которые перешли из карточки съёмки и подключили напоминания в MAX.</p>
                </div>

                <div class="text-xs font-semibold text-slate-600">
                    <label class="inline-flex items-center cursor-pointer select-none">
                        <input type="checkbox" id="select-all-checkbox" class="w-4 h-4 rounded border-slate-300 text-neutral-900 focus:ring-neutral-900 mr-2">
                        <span>Выбрать всех</span>
                    </label>
                </div>
            </div>

            @if($shoots->isEmpty())
                <div class="p-12 text-center text-slate-400">
                    <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <p class="text-sm font-medium text-slate-600">Пока нет привязанных клиентов</p>
                    <p class="text-xs text-slate-400 mt-1">Когда клиент нажмет «Получать напоминания в MAX» на странице съёмки, он появится в этой таблице.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50/75 border-b border-slate-200 text-slate-500 uppercase tracking-wider font-semibold text-[0.7rem]">
                            <tr>
                                <th class="w-10 px-4 py-3 text-center">
                                    #
                                </th>
                                <th class="px-4 py-3">Клиент</th>
                                <th class="px-4 py-3">Дата съёмки</th>
                                <th class="px-4 py-3">Телефон / Контакт</th>
                                <th class="px-4 py-3">MAX ID / Chat ID</th>
                                <th class="px-4 py-3">Подключен</th>
                                <th class="px-4 py-3 text-right">Карточка</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($shoots as $shoot)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="px-4 py-3.5 text-center">
                                        <input type="checkbox" name="shoot_ids[]" value="{{ $shoot->id }}"
                                               class="client-checkbox w-4 h-4 rounded border-slate-300 text-neutral-900 focus:ring-neutral-900">
                                    </td>
                                    <td class="px-4 py-3.5 font-bold text-slate-900">
                                        {{ $shoot->client_name }}
                                    </td>
                                    <td class="px-4 py-3.5">
                                        {{ $shoot->shoot_date ? $shoot->shoot_date->format('d.m.Y') : '—' }}
                                        <span class="text-slate-400 text-[0.7rem] block">{{ substr($shoot->start_time, 0, 5) }}</span>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        @if($shoot->phone)
                                            <a href="tel:{{ $shoot->phone }}" class="text-neutral-900 hover:underline">{{ $shoot->phone }}</a>
                                        @else
                                            <span class="text-slate-400">—</span>
                                        @endif
                                        @if($shoot->social_link)
                                            <div class="text-[0.7rem] text-slate-400 truncate max-w-[140px]">{{ $shoot->social_link }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 font-mono text-[0.75rem] text-slate-600">
                                        <div><span class="text-slate-400">User:</span> {{ $shoot->max_user_id ?: '—' }}</div>
                                        <div><span class="text-slate-400">Chat:</span> {{ $shoot->max_chat_id ?: '—' }}</div>
                                    </td>
                                    <td class="px-4 py-3.5 text-slate-500">
                                        {{ $shoot->max_connected_at ? $shoot->max_connected_at->format('d.m.Y H:i') : '—' }}
                                    </td>
                                    <td class="px-4 py-3.5 text-right">
                                        <a href="{{ route('shoots.share', ['token' => $shoot->share_token]) }}" target="_blank"
                                           class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition-colors inline-flex items-center gap-1">
                                            <span>Открыть</span>
                                            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </form>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAllCheckbox = document.getElementById('select-all-checkbox');
    const clientCheckboxes = document.querySelectorAll('.client-checkbox');
    const targetAllRadio = document.getElementById('target-all');
    const targetSelectedRadio = document.getElementById('target-selected');

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function () {
            clientCheckboxes.forEach(cb => cb.checked = selectAllCheckbox.checked);
            if (selectAllCheckbox.checked && targetSelectedRadio) {
                targetSelectedRadio.checked = true;
            }
        });
    }

    clientCheckboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            if (targetSelectedRadio && cb.checked) {
                targetSelectedRadio.checked = true;
            }
        });
    });

    const attachRadios = document.querySelectorAll('input[name="attach_buttons"]');
    const buttonsSelector = document.getElementById('buttons-selector');

    attachRadios.forEach(radio => {
        radio.addEventListener('change', function () {
            if (buttonsSelector) {
                buttonsSelector.style.display = this.value === 'custom' ? 'grid' : 'none';
            }
        });
    });
});
</script>
@endsection
