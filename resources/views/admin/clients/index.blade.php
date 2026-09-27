@extends('layouts.admin')

@section('title', 'Клиенты (CRM)')

@section('content')
<div class="space-y-6" x-data="{
    isCreateModalOpen: false,
    newClient: {
        name: '',
        phone: '',
        social_link: '',
        email: '',
        notes: ''
    }
}">

    {{-- Top Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-serif font-bold text-slate-900">Клиенты</h1>
                <span class="px-2.5 py-0.5 rounded-full bg-slate-900 text-white text-xs font-semibold">
                    {{ $clients->total() }} {{ trans_choice('клиент|клиента|клиентов', $clients->total()) }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">Единая база клиентов: контакты, профили MAX, история съёмок и быстрый переход в календарь.</p>
        </div>

        <div class="flex items-center gap-3">
            <button type="button" 
                    @click="isCreateModalOpen = true"
                    class="px-4 py-2 bg-crimson hover:bg-crimson/90 text-white text-xs uppercase tracking-wider font-bold rounded-lg shadow-sm hover:shadow transition-all inline-flex items-center gap-1.5 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span>Добавить клиента</span>
            </button>
        </div>
    </div>

    {{-- Search Form --}}
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 bg-white p-3.5 rounded-xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.clients.index') }}" class="flex-1 flex items-center gap-2">
            <div class="relative flex-1">
                <input type="text" 
                       name="search" 
                       value="{{ $search }}" 
                       placeholder="Поиск по имени, номеру телефона, нику в соцсети..."
                       class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-900 focus:bg-white focus:outline-none focus:border-slate-900 transition-colors">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-black text-white text-xs font-semibold rounded-lg transition-colors cursor-pointer">
                Найти
            </button>
            @if($search)
                <a href="{{ route('admin.clients.index') }}" class="px-3 py-2 text-xs text-slate-500 hover:text-slate-900 border border-slate-200 rounded-lg transition-colors">
                    Сбросить
                </a>
            @endif
        </form>
    </div>

    {{-- Clients Table --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        @if($clients->isEmpty())
            <div class="text-center py-12 px-4">
                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <h3 class="text-sm font-bold text-slate-900">Клиенты не найдены</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Создайте нового клиента или измените поисковый запрос.</p>
                <div class="mt-4">
                    <button type="button" @click="isCreateModalOpen = true" class="px-3.5 py-2 bg-slate-900 text-white text-xs font-semibold rounded-lg hover:bg-black transition-colors cursor-pointer">
                        Добавить первого клиента
                    </button>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[0.7rem]">
                        <tr>
                            <th class="px-5 py-3.5">Клиент</th>
                            <th class="px-5 py-3.5">Контакты</th>
                            <th class="px-5 py-3.5">MAX Бот</th>
                            <th class="px-5 py-3.5 text-center">Съёмок</th>
                            <th class="px-5 py-3.5">Последняя съёмка</th>
                            <th class="px-5 py-3.5 text-right">Действия</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($clients as $c)
                            @php
                                $lastShoot = $c->shoots->first();
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-5 py-4 font-bold text-slate-900">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-700 font-bold flex items-center justify-center text-xs shrink-0 border border-slate-200">
                                            {{ mb_substr($c->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.clients.show', $c) }}" class="hover:text-crimson font-serif text-sm block">
                                                {{ $c->name }}
                                            </a>
                                            @if($c->notes)
                                                <span class="text-[0.68rem] text-slate-400 truncate max-w-xs block font-sans">{{ $c->notes }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-slate-700">
                                    @if($c->phone)
                                        <div><a href="tel:{{ $c->phone_clean }}" class="text-neutral-900 font-mono hover:underline">{{ $c->phone }}</a></div>
                                    @endif
                                    @if($c->social_link)
                                        <div class="text-[0.7rem] text-slate-400 font-mono mt-0.5">{{ $c->social_link }}</div>
                                    @endif
                                    @if(!$c->phone && !$c->social_link)
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    @if($c->max_connected_at)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[0.68rem] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <span>Привязан</span>
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-[0.7rem]">Не подключен</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-center font-bold text-slate-800">
                                    <span class="px-2.5 py-1 rounded-md bg-slate-100 font-mono text-xs">
                                        {{ $c->shoots_count }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-slate-600">
                                    @if($lastShoot)
                                        <div class="font-medium text-slate-900">{{ $lastShoot->shoot_date->format('d.m.Y') }}</div>
                                        <div class="text-[0.7rem] text-slate-400 truncate max-w-[140px]">{{ $lastShoot->location ?: 'Локация не указана' }}</div>
                                    @else
                                        <span class="text-slate-400">Нет съёмок</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('admin.clients.show', $c) }}" 
                                           class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition-colors">
                                            Карточка
                                        </a>
                                        <a href="{{ route('admin.shoots.index') }}?client_id={{ $c->id }}" 
                                           class="px-2.5 py-1 rounded bg-neutral-900 hover:bg-black text-white font-semibold transition-colors">
                                            + Съёмка
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($clients->hasPages())
                <div class="p-4 border-t border-slate-200 bg-slate-50">
                    {{ $clients->links() }}
                </div>
            @endif
        @endif
    </div>

    {{-- Create Client Modal --}}
    <div x-show="isCreateModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
         @keydown.escape.window="isCreateModalOpen = false">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-neutral-100 text-left"
             @click.away="isCreateModalOpen = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-5">
                <h3 class="text-lg font-serif font-bold text-slate-900">Новый клиент</h3>
                <button type="button" @click="isCreateModalOpen = false" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-100 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.clients.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Имя / ФИО <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" required placeholder="Например: Алина Соколова"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-900 focus:bg-white focus:outline-none focus:border-slate-900">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Телефон
                    </label>
                    <input type="tel" name="phone" placeholder="+7 999 000-00-00"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-900 focus:bg-white focus:outline-none focus:border-slate-900">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Соцсеть / Мессенджер
                    </label>
                    <input type="text" name="social_link" placeholder="@telegram, vk.com/..."
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-900 focus:bg-white focus:outline-none focus:border-slate-900">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Заметки
                    </label>
                    <textarea name="notes" rows="2" placeholder="Особые пожелания, предпочтения по съёмкам..."
                              class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs text-slate-900 focus:bg-white focus:outline-none focus:border-slate-900"></textarea>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" @click="isCreateModalOpen = false" class="px-4 py-2 text-xs font-semibold text-slate-500 hover:text-slate-800 cursor-pointer">
                        Отмена
                    </button>
                    <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-black text-white text-xs font-bold uppercase tracking-wider rounded-lg transition-colors cursor-pointer">
                        Сохранить
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
