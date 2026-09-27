@extends('layouts.admin')

@section('title', 'Настройки бота MAX')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">

    <div class="border-b border-slate-200 pb-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                    <h1 class="text-2xl font-serif font-bold text-slate-900">Бот MAX (@se14454241_bot)</h1>
                </div>
                <p class="text-xs text-slate-500 mt-1">Управление интерактивными кнопками, приветствием и рассылками для клиентов.</p>
            </div>
            
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.max_bot.index') }}" 
                   class="px-3.5 py-2 rounded-lg text-xs font-bold uppercase tracking-wider bg-neutral-900 text-white shadow-sm">
                    Кнопки и тексты
                </a>
                <a href="{{ route('admin.max_bot.clients') }}" 
                   class="px-3.5 py-2 rounded-lg text-xs font-bold uppercase tracking-wider bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors">
                    Клиенты и рассылка
                </a>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.max_bot.update') }}" class="space-y-8">
        @csrf

        {{-- 1. Welcome Message --}}
        <div class="bg-white p-6 sm:p-8 border border-slate-200 rounded-xl shadow-sm space-y-4">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-slate-900">1. Приветственное сообщение при подключении</h2>
                <p class="text-xs text-slate-500 mt-0.5">Это сообщение клиент получает сразу после перехода в бот по кнопке «Подключить напоминания». Доступны теги подстановки: <code class="text-crimson font-mono">{client_name}</code>, <code class="text-crimson font-mono">{date}</code>, <code class="text-crimson font-mono">{time}</code>, <code class="text-crimson font-mono">{location}</code>.</p>
            </div>

            <div>
                <label for="welcome_text" class="block text-xs uppercase tracking-wider font-bold text-slate-700 mb-1.5">
                    Текст приветствия *
                </label>
                <textarea name="welcome_text" id="welcome_text" rows="6" required
                          class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900 font-sans leading-relaxed">{{ old('welcome_text', $settings['welcome_text']) }}</textarea>
            </div>
        </div>

        {{-- 2. Standard Buttons & Responses --}}
        <div class="bg-white p-6 sm:p-8 border border-slate-200 rounded-xl shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-slate-900">2. Стандартные кнопки и ответы</h2>
                <p class="text-xs text-slate-500 mt-0.5">Включайте или отключайте кнопки, меняйте названия и тексты автоматических ответов.</p>
            </div>

            <div class="space-y-6 divide-y divide-slate-100">

                {{-- Button 1: Web Card --}}
                <div class="pt-4 first:pt-0 space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="flex items-center cursor-pointer select-none">
                            <input type="checkbox" name="btn_card_enabled" value="1" {{ old('btn_card_enabled', $settings['btn_card_enabled']) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-slate-300 text-neutral-900 focus:ring-neutral-900 mr-2.5">
                            <span class="text-sm font-bold text-slate-900">Кнопка «Карточка съёмки» (Ссылка)</span>
                        </label>
                        <span class="text-[0.7rem] px-2 py-0.5 rounded bg-blue-50 text-blue-700 font-semibold border border-blue-200">Ссылка на сайт</span>
                    </div>
                    <div class="pl-6.5 max-w-md">
                        <label for="btn_card_text" class="block text-[0.7rem] uppercase tracking-wider font-bold text-slate-500 mb-1">
                            Текст на кнопке
                        </label>
                        <input type="text" name="btn_card_text" id="btn_card_text" value="{{ old('btn_card_text', $settings['btn_card_text']) }}" required
                               class="w-full px-3.5 py-2 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-neutral-900">
                    </div>
                </div>

                {{-- Button 2: Shoot Details --}}
                <div class="pt-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="flex items-center cursor-pointer select-none">
                            <input type="checkbox" name="btn_details_enabled" value="1" {{ old('btn_details_enabled', $settings['btn_details_enabled']) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-slate-300 text-neutral-900 focus:ring-neutral-900 mr-2.5">
                            <span class="text-sm font-bold text-slate-900">Кнопка «Детали съёмки»</span>
                        </label>
                        <span class="text-[0.7rem] px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 font-semibold border border-emerald-200">Автоматически из базы</span>
                    </div>
                    <div class="pl-6.5 max-w-md">
                        <label for="btn_details_text" class="block text-[0.7rem] uppercase tracking-wider font-bold text-slate-500 mb-1">
                            Текст на кнопке
                        </label>
                        <input type="text" name="btn_details_text" id="btn_details_text" value="{{ old('btn_details_text', $settings['btn_details_text']) }}" required
                               class="w-full px-3.5 py-2 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-neutral-900">
                        <span class="text-[0.7rem] text-slate-400 mt-1 block">Ответ формируется автоматически из карточки: дата, время, локация, стоимость, статус оплаты.</span>
                    </div>
                </div>

                {{-- Button 3: Shoot Tips --}}
                <div class="pt-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="flex items-center cursor-pointer select-none">
                            <input type="checkbox" name="btn_tips_enabled" value="1" {{ old('btn_tips_enabled', $settings['btn_tips_enabled']) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-slate-300 text-neutral-900 focus:ring-neutral-900 mr-2.5">
                            <span class="text-sm font-bold text-slate-900">Кнопка «Памятка подготовки к съёмке»</span>
                        </label>
                        <span class="text-[0.7rem] px-2 py-0.5 rounded bg-purple-50 text-purple-700 font-semibold border border-purple-200">Текстовый ответ</span>
                    </div>
                    <div class="pl-6.5 space-y-3">
                        <div class="max-w-md">
                            <label for="btn_tips_text" class="block text-[0.7rem] uppercase tracking-wider font-bold text-slate-500 mb-1">
                                Текст на кнопке
                            </label>
                            <input type="text" name="btn_tips_text" id="btn_tips_text" value="{{ old('btn_tips_text', $settings['btn_tips_text']) }}" required
                                   class="w-full px-3.5 py-2 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-neutral-900">
                        </div>
                        <div>
                            <label for="tips_response" class="block text-[0.7rem] uppercase tracking-wider font-bold text-slate-500 mb-1">
                                Текст памятки (что отвечает бот при нажатии) *
                            </label>
                            <textarea name="tips_response" id="tips_response" rows="6" required
                                      class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-neutral-900 leading-relaxed">{{ old('tips_response', $settings['tips_response']) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Button 4: Contacts --}}
                <div class="pt-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="flex items-center cursor-pointer select-none">
                            <input type="checkbox" name="btn_contacts_enabled" value="1" {{ old('btn_contacts_enabled', $settings['btn_contacts_enabled']) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-slate-300 text-neutral-900 focus:ring-neutral-900 mr-2.5">
                            <span class="text-sm font-bold text-slate-900">Кнопка «Контакты фотографа»</span>
                        </label>
                        <span class="text-[0.7rem] px-2 py-0.5 rounded bg-amber-50 text-amber-700 font-semibold border border-amber-200">Текстовый ответ</span>
                    </div>
                    <div class="pl-6.5 space-y-3">
                        <div class="max-w-md">
                            <label for="btn_contacts_text" class="block text-[0.7rem] uppercase tracking-wider font-bold text-slate-500 mb-1">
                                Текст на кнопке
                            </label>
                            <input type="text" name="btn_contacts_text" id="btn_contacts_text" value="{{ old('btn_contacts_text', $settings['btn_contacts_text']) }}" required
                                   class="w-full px-3.5 py-2 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-neutral-900">
                        </div>
                        <div>
                            <label for="contacts_response" class="block text-[0.7rem] uppercase tracking-wider font-bold text-slate-500 mb-1">
                                Текст контактов (что отвечает бот при нажатии) *
                            </label>
                            <textarea name="contacts_response" id="contacts_response" rows="5" required
                                      class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-neutral-900 leading-relaxed">{{ old('contacts_response', $settings['contacts_response']) }}</textarea>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- 3. Custom Buttons Builder --}}
        <div class="bg-white p-6 sm:p-8 border border-slate-200 rounded-xl shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">3. Дополнительные пользовательские кнопки</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Вы можете добавить любые свои кнопки: ссылку на Яндекс.Карты, реквизиты, ссылку на ВК или портфолио.</p>
                </div>
                <button type="button" id="add-custom-btn" class="px-3 py-1.5 bg-neutral-100 hover:bg-neutral-200 text-neutral-800 text-xs font-semibold rounded-lg transition-colors cursor-pointer flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>Добавить кнопку</span>
                </button>
            </div>

            <div id="custom-buttons-container" class="space-y-4">
                @forelse($settings['custom_buttons'] as $index => $cBtn)
                    <div class="custom-button-card p-4 border border-slate-200 rounded-xl bg-slate-50 relative space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Кнопка #{{ $index + 1 }}</span>
                            <button type="button" class="remove-custom-btn text-rose-500 hover:text-rose-700 text-xs font-semibold cursor-pointer">Удалить</button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[0.7rem] uppercase font-bold text-slate-500 mb-1">Название кнопки *</label>
                                <input type="text" name="custom_buttons[{{ $index }}][title]" value="{{ $cBtn['title'] ?? '' }}" required
                                       placeholder="Например: 📍 Студия на карте"
                                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm bg-white focus:outline-none focus:border-neutral-900">
                            </div>
                            <div>
                                <label class="block text-[0.7rem] uppercase font-bold text-slate-500 mb-1">Тип действия</label>
                                <select name="custom_buttons[{{ $index }}][type]" class="custom-type-select w-full px-3 py-2 border border-slate-300 rounded-lg text-sm bg-white focus:outline-none focus:border-neutral-900">
                                    <option value="link" {{ ($cBtn['type'] ?? '') === 'link' ? 'selected' : '' }}>Открыть ссылку в браузере</option>
                                    <option value="text" {{ ($cBtn['type'] ?? '') === 'text' ? 'selected' : '' }}>Ответить текстом в чат</option>
                                </select>
                            </div>
                        </div>

                        <div class="custom-url-field {{ ($cBtn['type'] ?? '') === 'text' ? 'hidden' : '' }}">
                            <label class="block text-[0.7rem] uppercase font-bold text-slate-500 mb-1">URL адрес ссылки</label>
                            <input type="text" name="custom_buttons[{{ $index }}][url]" value="{{ $cBtn['url'] ?? '' }}"
                                   placeholder="https://yandex.ru/maps/..."
                                   class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm bg-white focus:outline-none focus:border-neutral-900">
                        </div>

                        <div class="custom-reply-field {{ ($cBtn['type'] ?? '') === 'text' ? '' : 'hidden' }}">
                            <label class="block text-[0.7rem] uppercase font-bold text-slate-500 mb-1">Текст ответа бота в чат</label>
                            <textarea name="custom_buttons[{{ $index }}][reply]" rows="3"
                                      placeholder="Текст, который бот пришлет клиенту..."
                                      class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm bg-white focus:outline-none focus:border-neutral-900 leading-relaxed">{{ $cBtn['reply'] ?? '' }}</textarea>
                        </div>

                        <div class="pt-2 border-t border-slate-200/80">
                            <label class="flex items-center cursor-pointer select-none">
                                <input type="checkbox" name="custom_buttons[{{ $index }}][in_menu]" value="1" {{ (!isset($cBtn['in_menu']) || !empty($cBtn['in_menu'])) ? 'checked' : '' }}
                                       class="w-4 h-4 rounded border-slate-300 text-neutral-900 focus:ring-neutral-900 mr-2">
                                <span class="text-xs font-semibold text-slate-700">Показывать в основном постоянном меню бота (приветствие, ответы на сообщения)</span>
                            </label>
                            <span class="text-[0.7rem] text-slate-400 block pl-6 mt-0.5">Если выключить — кнопка не будет захламлять обычный диалог бота, но будет доступна для выбора в разовых рассылках.</span>
                        </div>
                    </div>
                @empty
                    <div id="no-custom-buttons-msg" class="text-xs text-slate-400 py-3 text-center border border-dashed border-slate-200 rounded-lg">
                        Пока нет добавленных дополнительных кнопок. Нажмите «Добавить кнопку» выше, чтобы создать новую.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="flex justify-end pt-2">
            <button type="submit" class="px-9 py-3.5 bg-neutral-900 hover:bg-neutral-800 text-white text-xs uppercase tracking-wider font-bold rounded-lg shadow-sm hover:shadow transition-all inline-flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span>Сохранить настройки бота</span>
            </button>
        </div>

    </form>
</div>

<template id="custom-button-template">
    <div class="custom-button-card p-4 border border-slate-200 rounded-xl bg-slate-50 relative space-y-3">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-700 custom-btn-num">Кнопка</span>
            <button type="button" class="remove-custom-btn text-rose-500 hover:text-rose-700 text-xs font-semibold cursor-pointer">Удалить</button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <label class="block text-[0.7rem] uppercase font-bold text-slate-500 mb-1">Название кнопки *</label>
                <input type="text" data-name="title" required
                       placeholder="Например: 📍 Студия на карте"
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm bg-white focus:outline-none focus:border-neutral-900">
            </div>
            <div>
                <label class="block text-[0.7rem] uppercase font-bold text-slate-500 mb-1">Тип действия</label>
                <select data-name="type" class="custom-type-select w-full px-3 py-2 border border-slate-300 rounded-lg text-sm bg-white focus:outline-none focus:border-neutral-900">
                    <option value="link">Открыть ссылку в браузере</option>
                    <option value="text">Ответить текстом в чат</option>
                </select>
            </div>
        </div>

        <div class="custom-url-field">
            <label class="block text-[0.7rem] uppercase font-bold text-slate-500 mb-1">URL адрес ссылки</label>
            <input type="text" data-name="url"
                   placeholder="https://yandex.ru/maps/..."
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm bg-white focus:outline-none focus:border-neutral-900">
        </div>

        <div class="custom-reply-field hidden">
            <label class="block text-[0.7rem] uppercase font-bold text-slate-500 mb-1">Текст ответа бота в чат</label>
            <textarea data-name="reply" rows="3"
                      placeholder="Текст, который бот пришлет клиенту..."
                      class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm bg-white focus:outline-none focus:border-neutral-900 leading-relaxed"></textarea>
        </div>

        <div class="pt-2 border-t border-slate-200/80">
            <label class="flex items-center cursor-pointer select-none">
                <input type="checkbox" data-name="in_menu" value="1" checked
                       class="w-4 h-4 rounded border-slate-300 text-neutral-900 focus:ring-neutral-900 mr-2">
                <span class="text-xs font-semibold text-slate-700">Показывать в основном постоянном меню бота (приветствие, ответы на сообщения)</span>
            </label>
            <span class="text-[0.7rem] text-slate-400 block pl-6 mt-0.5">Если выключить — кнопка не будет захламлять обычный диалог бота, но будет доступна для выбора в разовых рассылках.</span>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('custom-buttons-container');
    const addBtn = document.getElementById('add-custom-btn');
    const template = document.getElementById('custom-button-template');
    const noMsg = document.getElementById('no-custom-buttons-msg');

    function updateIndexes() {
        const cards = container.querySelectorAll('.custom-button-card');
        cards.forEach((card, idx) => {
            const numSpan = card.querySelector('.custom-btn-num');
            if (numSpan) numSpan.textContent = `Кнопка #${idx + 1}`;
            
            card.querySelectorAll('[data-name]').forEach(input => {
                const field = input.getAttribute('data-name');
                input.setAttribute('name', `custom_buttons[${idx}][${field}]`);
            });
        });
        if (noMsg) {
            noMsg.style.display = cards.length === 0 ? 'block' : 'none';
        }
    }

    addBtn.addEventListener('click', function () {
        const clone = template.content.cloneNode(true);
        container.appendChild(clone);
        updateIndexes();
    });

    container.addEventListener('click', function (e) {
        if (e.target.closest('.remove-custom-btn')) {
            e.target.closest('.custom-button-card').remove();
            updateIndexes();
        }
    });

    container.addEventListener('change', function (e) {
        if (e.target.classList.contains('custom-type-select')) {
            const card = e.target.closest('.custom-button-card');
            const urlField = card.querySelector('.custom-url-field');
            const replyField = card.querySelector('.custom-reply-field');
            if (e.target.value === 'text') {
                urlField.classList.add('hidden');
                replyField.classList.remove('hidden');
            } else {
                urlField.classList.remove('hidden');
                replyField.classList.add('hidden');
            }
        }
    });
});
</script>
@endsection
