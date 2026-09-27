@extends('layouts.admin')

@section('title', 'Клиент: ' . $client->name)

@section('content')
<div class="space-y-6">

    {{-- Breadcrumb & Back --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.clients.index') }}" class="text-xs text-slate-500 hover:text-slate-900 inline-flex items-center gap-1.5 font-semibold">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            <span>Назад к списку клиентов</span>
        </a>

        <form method="POST" action="{{ route('admin.clients.destroy', $client) }}" onsubmit="return confirm('Удалить карточку клиента? Все съёмки в календаре сохранятся, но будут отвязаны от этого клиента.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-xs text-rose-500 hover:text-rose-700 font-semibold cursor-pointer">
                Удалить клиента
            </button>
        </form>
    </div>

    {{-- Main Grid: Left Client Profile, Right Shoots History --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: Edit Client Card --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <div class="flex items-center gap-3.5 border-b border-slate-100 pb-4 mb-5">
                    <div class="w-12 h-12 rounded-2xl bg-slate-900 text-white font-serif text-lg font-bold flex items-center justify-center shrink-0">
                        {{ mb_substr($client->name, 0, 1) }}
                    </div>
                    <div>
                        <h2 class="text-lg font-serif font-bold text-slate-900">{{ $client->name }}</h2>
                        <span class="text-xs text-slate-400 font-mono">ID #{{ $client->id }}</span>
                    </div>
                </div>

                {{-- Status MAX connection --}}
                <div class="mb-5 p-3.5 rounded-xl border {{ $client->max_connected_at ? 'bg-emerald-50/70 border-emerald-200' : 'bg-slate-50 border-slate-200' }}">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider {{ $client->max_connected_at ? 'text-emerald-800' : 'text-slate-600' }}">Бот MAX</span>
                        @if($client->max_connected_at)
                            <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[0.68rem] font-bold">Подключен</span>
                        @else
                            <span class="text-[0.68rem] text-slate-400">Не подключен</span>
                        @endif
                    </div>
                    @if($client->max_connected_at)
                        <div class="mt-2 text-[0.72rem] font-mono text-slate-600 space-y-0.5">
                            <div>Chat ID: {{ $client->max_chat_id ?: '—' }}</div>
                            <div>User ID: {{ $client->max_user_id ?: '—' }}</div>
                            <div class="text-[0.68rem] text-slate-400">Дата: {{ $client->max_connected_at->format('d.m.Y H:i') }}</div>
                        </div>
                    @else
                        <p class="text-[0.7rem] text-slate-500 mt-1">Клиент подключится к боту автоматически при открытии ссылки из карточки съёмки.</p>
                    @endif
                </div>

                {{-- Update Form --}}
                <form method="POST" action="{{ route('admin.clients.update', $client) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Имя / ФИО *
                        </label>
                        <input type="text" name="name" value="{{ old('name', $client->name) }}" required
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs text-slate-900 focus:bg-white focus:outline-none focus:border-slate-900">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Номер телефона
                        </label>
                        <input type="tel" name="phone" value="{{ old('phone', $client->phone) }}"
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs text-slate-900 focus:bg-white focus:outline-none focus:border-slate-900">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Соцсеть / Контакт
                        </label>
                        <input type="text" name="social_link" value="{{ old('social_link', $client->social_link) }}"
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs text-slate-900 focus:bg-white focus:outline-none focus:border-slate-900">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Email
                        </label>
                        <input type="email" name="email" value="{{ old('email', $client->email) }}"
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs text-slate-900 focus:bg-white focus:outline-none focus:border-slate-900">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Заметки
                        </label>
                        <textarea name="notes" rows="3"
                                  class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs text-slate-900 focus:bg-white focus:outline-none focus:border-slate-900">{{ old('notes', $client->notes) }}</textarea>
                    </div>

                    <button type="submit" class="w-full py-2 bg-slate-900 hover:bg-black text-white text-xs font-bold uppercase tracking-wider rounded-lg transition-colors cursor-pointer">
                        Сохранить изменения
                    </button>
                </form>
            </div>
        </div>

        {{-- Right: Shoots History & Schedule --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
                    <div>
                        <h3 class="text-base font-serif font-bold text-slate-900">Съёмки клиента ({{ $client->shoots->count() }})</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Все прошедшие и запланированные фотосессии.</p>
                    </div>

                    <a href="{{ route('admin.shoots.index') }}?client_id={{ $client->id }}" 
                       class="px-3.5 py-1.5 bg-crimson hover:bg-crimson/90 text-white text-xs font-bold uppercase tracking-wider rounded-lg inline-flex items-center gap-1.5 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Запланировать съёмку</span>
                    </a>
                </div>

                @if($client->shoots->isEmpty())
                    <div class="text-center py-10 text-slate-400 text-xs">
                        У этого клиента пока нет привязанных съёмок в календаре.
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($client->shoots as $shoot)
                            <div class="p-4 rounded-xl border border-slate-200 hover:border-slate-300 transition-all bg-slate-50/50 hover:bg-slate-50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-sm text-slate-900">
                                            {{ $shoot->shoot_date->format('d F Y') }}
                                        </span>
                                        <span class="text-xs font-mono text-slate-500">
                                            {{ substr($shoot->start_time, 0, 5) }} ({{ $shoot->duration_minutes }} мин.)
                                        </span>
                                        <span class="px-2 py-0.5 rounded text-[0.65rem] font-bold uppercase tracking-wider
                                            {{ $shoot->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : ($shoot->status === 'cancelled' ? 'bg-rose-100 text-rose-800' : 'bg-blue-100 text-blue-800') }}">
                                            {{ $shoot->status_label }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-slate-600 mt-1 flex flex-wrap items-center gap-3">
                                        @if($shoot->location)
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                                <span>{{ $shoot->location }}</span>
                                            </span>
                                        @endif
                                        @if($shoot->price)
                                            <span>Стоимость: <strong>{{ number_format($shoot->price, 0, '', ' ') }} ₽</strong></span>
                                        @endif
                                        @if($shoot->prepayment)
                                            <span class="text-emerald-700">Предоплата: {{ number_format($shoot->prepayment, 0, '', ' ') }} ₽</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <a href="{{ route('shoots.share', ['token' => $shoot->share_token]) }}" target="_blank"
                                       class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition-colors inline-flex items-center gap-1">
                                        <span>Карточка</span>
                                        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>

</div>
@endsection
