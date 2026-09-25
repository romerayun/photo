@extends('layouts.admin')

@section('title', 'Календарь съёмок')

@section('content')
<div x-data="shootCalendar({
    initialShoots: {{ Js::from($shootsInMonth->map(fn($s) => [
        'id' => $s->id,
        'client_name' => $s->client_name,
        'social_link' => $s->social_link,
        'social_url' => $s->social_url,
        'phone' => $s->phone,
        'phone_clean' => $s->phone_clean,
        'description' => $s->description,
        'shoot_date' => $s->shoot_date->format('Y-m-d'),
        'start_time' => substr($s->start_time, 0, 5),
        'end_time' => $s->end_time,
        'duration_minutes' => $s->duration_minutes,
        'duration_label' => $s->duration_label,
        'status' => $s->status,
        'status_label' => $s->status_label,
        'location' => $s->location,
        'price' => $s->price,
        'notes' => $s->notes,
        'gallery_link' => $s->gallery_link,
        'clean_gallery_url' => $s->clean_gallery_url,
        'share_url' => $s->share_url,
        'share_token' => $s->share_token,
        'receipt_url' => $s->receipt_url,
        'receipt_original_name' => $s->receipt_original_name,
        'receipt_uploaded_at' => $s->receipt_uploaded_at ? $s->receipt_uploaded_at->timezone('Asia/Irkutsk')->format('d.m.Y H:i') : null,
        'booking_confirmed_at' => $s->booking_confirmed_at ? $s->booking_confirmed_at->timezone('Asia/Irkutsk')->format('d.m.Y H:i') : null,
        'files' => $s->files->map(fn($f) => [
            'id' => $f->id,
            'original_name' => $f->original_name,
            'url' => $f->url,
            'is_image' => $f->is_image,
            'formatted_size' => $f->formatted_size,
            'extension' => $f->extension,
            'created_at' => $f->created_at->format('d.m.Y H:i'),
        ])->values()->all(),
    ])) }},
    year: {{ $year }},
    month: {{ $month }},
    csrfToken: '{{ csrf_token() }}',
    storeUrl: '{{ route('admin.shoots.store') }}',
    indexUrl: '{{ route('admin.shoots.index') }}'
})" class="space-y-6">

    {{-- Top Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-serif font-bold text-slate-900">Календарь съёмок</h1>
                <span class="px-2.5 py-0.5 rounded-full bg-slate-900 text-white text-xs font-semibold" x-text="shoots.length + ' съёмок'"></span>
            </div>
            <p class="text-xs text-slate-500 mt-1">График фотосессий, бронирования, контакты клиентов, мудборды и файлы съёмок.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            {{-- Switch View: Calendar / List --}}
            <div class="inline-flex rounded-lg border border-slate-200 bg-white p-1 shadow-sm">
                <button type="button" 
                        @click="activeView = 'calendar'" 
                        :class="activeView === 'calendar' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                        class="px-3 py-1.5 rounded-md text-xs font-semibold transition-all flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Календарь</span>
                </button>
                <button type="button" 
                        @click="activeView = 'list'" 
                        :class="activeView === 'list' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                        class="px-3 py-1.5 rounded-md text-xs font-semibold transition-all flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    <span>Список</span>
                </button>
            </div>

            {{-- New Shoot Button --}}
            <button type="button" 
                    @click="openCreateModal()" 
                    class="px-4 py-2 bg-crimson hover:bg-crimson/90 text-white text-xs uppercase tracking-wider font-bold rounded-lg shadow-sm hover:shadow transition-all inline-flex items-center gap-1.5 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span>Запланировать съёмку</span>
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-4 bg-white border border-slate-200 rounded-xl shadow-sm">
            <span class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Ближайшие</span>
            <span class="text-2xl font-serif font-bold text-slate-900 block mt-1">{{ $stats['upcoming'] }}</span>
            <span class="text-[0.7rem] text-slate-400">Предстоящие съёмки</span>
        </div>
        <div class="p-4 bg-white border border-slate-200 rounded-xl shadow-sm">
            <span class="text-xs uppercase tracking-wider text-slate-500 font-semibold">В этом месяце</span>
            <span class="text-2xl font-serif font-bold text-slate-900 block mt-1" x-text="shootsInCurrentMonthCount()"></span>
            <span class="text-[0.7rem] text-slate-400" x-text="monthName() + ' ' + currentYear"></span>
        </div>
        <div class="p-4 bg-white border border-slate-200 rounded-xl shadow-sm">
            <span class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Проведено</span>
            <span class="text-2xl font-serif font-bold text-emerald-600 block mt-1">{{ $stats['completed'] }}</span>
            <span class="text-[0.7rem] text-slate-400">Успешных съёмок</span>
        </div>
        <div class="p-4 bg-white border border-slate-200 rounded-xl shadow-sm">
            <span class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Всего в базе</span>
            <span class="text-2xl font-serif font-bold text-slate-900 block mt-1">{{ $stats['total'] }}</span>
            <span class="text-[0.7rem] text-slate-400">История съёмок</span>
        </div>
    </div>

    {{-- CALENDAR VIEW --}}
    <div x-show="activeView === 'calendar'" class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden transition-all">
        
        {{-- Calendar Bar (Month Selector & Quick controls) --}}
        <div class="p-4 sm:p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50/50">
            <div class="flex items-center gap-3">
                <div class="flex items-center bg-white border border-slate-200 rounded-lg shadow-sm">
                    <button type="button" @click="prevMonth()" class="p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-50 rounded-l-lg transition-colors cursor-pointer" title="Предыдущий месяц">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button type="button" @click="goToday()" class="px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 border-x border-slate-200 transition-colors cursor-pointer">
                        Сегодня
                    </button>
                    <button type="button" @click="nextMonth()" class="p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-50 rounded-r-lg transition-colors cursor-pointer" title="Следующий месяц">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>

                <h2 class="text-lg sm:text-xl font-serif font-bold text-slate-900 capitalize" x-text="monthName() + ' ' + currentYear"></h2>
            </div>

            {{-- Legend --}}
            <div class="flex items-center gap-4 text-xs text-slate-600">
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                    <span>Запланирована</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <span>Проведена</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-400"></span>
                    <span>Отменена</span>
                </div>
            </div>
        </div>

        {{-- Weekdays Header --}}
        <div class="grid grid-cols-7 border-b border-slate-200 text-center bg-slate-100/70 text-[0.7rem] uppercase tracking-wider font-bold text-slate-600">
            <div class="py-2.5 border-r border-slate-200">Пн</div>
            <div class="py-2.5 border-r border-slate-200">Вт</div>
            <div class="py-2.5 border-r border-slate-200">Ср</div>
            <div class="py-2.5 border-r border-slate-200">Чт</div>
            <div class="py-2.5 border-r border-slate-200">Пт</div>
            <div class="py-2.5 border-r border-slate-200 text-rose-600">Сб</div>
            <div class="py-2.5 text-rose-600">Вс</div>
        </div>

        {{-- Calendar Grid Days --}}
        <div class="grid grid-cols-7 auto-rows-fr bg-slate-200 gap-px">
            <template x-for="(day, index) in calendarDays" :key="index">
                <div class="bg-white min-h-[110px] sm:min-h-[130px] p-1.5 sm:p-2 flex flex-col justify-between group transition-colors hover:bg-slate-50/90 relative"
                     :class="{'!bg-slate-50/60 opacity-60': !day.isCurrentMonth, '!bg-amber-50/40': day.isToday}">
                    
                    {{-- Day Header --}}
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs font-semibold inline-flex items-center justify-center w-6 h-6 rounded-full"
                              :class="day.isToday ? 'bg-crimson text-white font-bold' : (day.isWeekend ? 'text-rose-600' : 'text-slate-700')"
                              x-text="day.dayNumber">
                        </span>

                        {{-- Quick Add Button on Hover --}}
                        <button type="button" 
                                @click="openCreateModal(day.dateString)"
                                class="opacity-0 group-hover:opacity-100 p-1 text-slate-400 hover:text-slate-900 hover:bg-slate-200 rounded transition-all cursor-pointer"
                                title="Добавить съёмку на этот день">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        </button>
                    </div>

                    {{-- Day Shoots List --}}
                    <div class="flex-grow space-y-1 overflow-y-auto max-h-[90px] pr-0.5 custom-scrollbar">
                        <template x-for="shoot in getShootsForDay(day.dateString)" :key="shoot.id">
                            <div @click="openViewModal(shoot)"
                                 class="p-1.5 rounded-md text-[0.7rem] cursor-pointer transition-all border shadow-xs hover:shadow hover:scale-[1.01]"
                                 :class="getShootStyle(shoot.status)">
                                <div class="flex items-center justify-between gap-1 font-semibold leading-tight truncate">
                                    <span class="truncate" x-text="shoot.client_name"></span>
                                    <span class="text-[0.65rem] shrink-0 font-mono opacity-80" x-text="shoot.start_time"></span>
                                </div>
                                <div class="flex items-center justify-between gap-1 text-[0.62rem] opacity-75 mt-0.5">
                                    <span class="truncate" x-text="shoot.location || shoot.duration_label"></span>
                                    <div class="flex items-center gap-1.5 shrink-0 font-mono">
                                        <template x-if="shoot.booking_confirmed_at">
                                            <span class="inline-flex items-center text-emerald-700 font-bold" title="Бронь подтверждена">
                                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            </span>
                                        </template>
                                        <template x-if="!shoot.booking_confirmed_at && shoot.receipt_url">
                                            <span class="inline-flex items-center text-amber-600 font-bold" title="Чек загружен (ожидает подтверждения)">
                                                <svg class="w-2.5 h-2.5 animate-pulse" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="6"/></svg>
                                            </span>
                                        </template>
                                        <template x-if="shoot.files && shoot.files.length > 0">
                                            <span class="flex items-center text-slate-600 font-bold" title="Прикреплены файлы">
                                                <svg class="w-2.5 h-2.5 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                                <span x-text="shoot.files.length"></span>
                                            </span>
                                        </template>
                                        <span x-text="shoot.duration_label"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                </div>
            </template>
        </div>

    </div>

    {{-- LIST VIEW --}}
    <div x-show="activeView === 'list'" style="display: none;" class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        
        {{-- List Filter Bar --}}
        <div class="p-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
            <div class="flex items-center gap-2">
                <input type="text" 
                       x-model="searchQuery" 
                       placeholder="Поиск по имени, телефону или описанию..." 
                       class="px-3.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs placeholder-slate-400 focus:outline-none focus:border-slate-900 w-full sm:w-72">
            </div>

            <div class="flex items-center gap-1.5 overflow-x-auto text-xs">
                <button type="button" @click="filterStatus = 'all'" :class="filterStatus === 'all' ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 hover:bg-slate-100'" class="px-3 py-1.5 rounded-lg border border-slate-200 font-semibold transition-colors cursor-pointer">Все</button>
                <button type="button" @click="filterStatus = 'planned'" :class="filterStatus === 'planned' ? 'bg-blue-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100'" class="px-3 py-1.5 rounded-lg border border-slate-200 font-semibold transition-colors cursor-pointer">Запланированные</button>
                <button type="button" @click="filterStatus = 'completed'" :class="filterStatus === 'completed' ? 'bg-emerald-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100'" class="px-3 py-1.5 rounded-lg border border-slate-200 font-semibold transition-colors cursor-pointer">Проведённые</button>
                <button type="button" @click="filterStatus = 'cancelled'" :class="filterStatus === 'cancelled' ? 'bg-rose-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100'" class="px-3 py-1.5 rounded-lg border border-slate-200 font-semibold transition-colors cursor-pointer">Отменённые</button>
            </div>
        </div>

        {{-- Shoots Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50 text-[0.7rem] uppercase tracking-wider text-slate-500 border-b border-slate-200 font-semibold">
                    <tr>
                        <th class="px-6 py-3.5">Дата и время</th>
                        <th class="px-6 py-3.5">Клиент</th>
                        <th class="px-6 py-3.5">Контакты</th>
                        <th class="px-6 py-3.5">Локация и описание</th>
                        <th class="px-6 py-3.5">Файлы</th>
                        <th class="px-6 py-3.5">Статус</th>
                        <th class="px-6 py-3.5 text-right">Действия</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <template x-for="shoot in filteredShoots" :key="shoot.id">
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                <span class="font-semibold text-slate-900 block" x-text="formatDateRussian(shoot.shoot_date)"></span>
                                <div class="text-xs text-slate-500 font-mono mt-0.5 flex items-center gap-1.5">
                                    <span x-text="shoot.start_time + ' – ' + (shoot.end_time || '')"></span>
                                    <span class="text-slate-300">&bull;</span>
                                    <span x-text="shoot.duration_label"></span>
                                </div>
                            </td>
                            <td class="px-6 py-3.5">
                                <span class="font-semibold text-slate-900 block" x-text="shoot.client_name"></span>
                                <span x-show="shoot.price" class="text-xs text-emerald-600 font-mono font-medium block" x-text="shoot.price + ' ₽'"></span>
                            </td>
                            <td class="px-6 py-3.5 text-xs">
                                <div class="space-y-1">
                                    <div x-show="shoot.phone" class="flex items-center gap-1.5 text-slate-700 font-mono">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        <a :href="'tel:' + shoot.phone_clean" class="hover:text-crimson hover:underline" x-text="shoot.phone"></a>
                                    </div>
                                    <div x-show="shoot.social_link" class="flex items-center gap-1.5 text-blue-600">
                                        <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                        <a :href="shoot.social_url" target="_blank" class="hover:underline truncate max-w-[150px]" x-text="shoot.social_link"></a>
                                    </div>
                                    <div x-show="!shoot.phone && !shoot.social_link" class="text-slate-400 italic">Контакты не указаны</div>
                                </div>
                            </td>
                            <td class="px-6 py-3.5 text-xs text-slate-600 max-w-xs">
                                <div x-show="shoot.location" class="font-semibold text-slate-800 mb-0.5 flex items-center gap-1">
                                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span class="truncate" x-text="shoot.location"></span>
                                </div>
                                <p class="line-clamp-2 text-slate-500" x-text="shoot.description || '—'"></p>
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap text-xs">
                                <template x-if="shoot.files && shoot.files.length > 0">
                                    <button type="button" @click="openViewModal(shoot)" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium transition-colors cursor-pointer">
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                        <span x-text="shoot.files.length + ' ' + (shoot.files.length === 1 ? 'файл' : (shoot.files.length < 5 ? 'файла' : 'файлов'))"></span>
                                    </button>
                                </template>
                                <template x-if="!shoot.files || shoot.files.length === 0">
                                    <span class="text-slate-400 italic text-[0.75rem]">Нет файлов</span>
                                </template>
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-[0.68rem] uppercase font-bold tracking-wider"
                                      :class="{
                                          'bg-blue-100 text-blue-800': shoot.status === 'planned',
                                          'bg-emerald-100 text-emerald-800': shoot.status === 'completed',
                                          'bg-rose-100 text-rose-800': shoot.status === 'cancelled'
                                      }"
                                      x-text="shoot.status_label">
                                </span>
                            </td>
                            <td class="px-6 py-3.5 text-right whitespace-nowrap text-xs space-x-1.5">
                                <button type="button" @click="copyShareLink(shoot)" class="px-2.5 py-1 bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 font-semibold rounded-md transition-colors cursor-pointer inline-flex items-center gap-1" title="Скопировать ссылку для клиента">
                                    <svg class="w-3.5 h-3.5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                                    <span>Ссылка</span>
                                </button>
                                <button type="button" @click="openViewModal(shoot)" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-md transition-colors cursor-pointer">
                                    Просмотр
                                </button>
                                <button type="button" @click="openEditModal(shoot)" class="px-2.5 py-1 bg-neutral-900 hover:bg-neutral-800 text-white font-semibold rounded-md transition-colors cursor-pointer">
                                    Ред.
                                </button>
                                <button type="button" @click="deleteShoot(shoot)" class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 font-semibold rounded-md transition-colors cursor-pointer">
                                    Удалить
                                </button>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredShoots.length === 0">
                        <td colspan="7" class="px-6 py-10 text-center text-slate-400">
                            Съёмок не найдено. Нажмите «Запланировать съёмку», чтобы добавить первую запись.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

    {{-- MODAL: Create / Edit Shoot --}}
    <div x-show="isFormModalOpen" 
         style="display: none;"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
        
        <div @click.away="closeFormModal()"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             class="bg-white rounded-2xl shadow-2xl max-w-3xl lg:max-w-4xl w-full overflow-hidden border border-slate-200 my-6 sm:my-8">
            
            {{-- Modal Header --}}
            <div class="px-6 md:px-8 py-5 bg-slate-900 text-white flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-serif font-bold" x-text="isEditMode ? 'Редактировать съёмку' : 'Новая запись о съёмке'"></h3>
                    <p class="text-xs text-slate-400 mt-1">Внесите данные клиента, дату, параметры и прикрепите файлы</p>
                </div>
                <button type="button" @click="closeFormModal()" class="text-slate-400 hover:text-white p-1.5 rounded-lg transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Form Body --}}
            <form @submit.prevent="submitShootForm()" class="p-6 md:p-8 space-y-5 max-h-[85vh] overflow-y-auto custom-scrollbar">
                
                {{-- Client Information: Name, Phone, Social Link in 3 cols --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Имя клиента <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               x-model="formData.client_name" 
                               required 
                               placeholder="Например: Анастасия Белова" 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-900 focus:bg-white focus:outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 transition-colors">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Телефон <span class="text-slate-400 font-normal lowercase">(необязательно)</span>
                        </label>
                        <input type="tel" 
                               x-model="formData.phone" 
                               placeholder="+7 999 123-45-67" 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-900 focus:bg-white focus:outline-none focus:border-slate-900 transition-colors">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Ссылка на соцсеть <span class="text-slate-400 font-normal lowercase">(необязательно)</span>
                        </label>
                        <input type="text" 
                               x-model="formData.social_link" 
                               placeholder="@username или t.me/..." 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-900 focus:bg-white focus:outline-none focus:border-slate-900 transition-colors">
                    </div>
                </div>

                {{-- Date, Start Time, Duration --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Дата съёмки <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" 
                               x-model="formData.shoot_date" 
                               required 
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-900 focus:bg-white focus:outline-none focus:border-slate-900 transition-colors">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Время начала <span class="text-rose-500">*</span>
                        </label>
                        <input type="time" 
                               x-model="formData.start_time" 
                               required 
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-900 focus:bg-white focus:outline-none focus:border-slate-900 transition-colors">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Длительность <span class="text-rose-500">*</span>
                        </label>
                        <select x-model="formData.duration_minutes" 
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-900 focus:bg-white focus:outline-none focus:border-slate-900 transition-colors">
                            <option value="30">30 минут</option>
                            <option value="45">45 минут</option>
                            <option value="60">1 час (60 мин)</option>
                            <option value="90">1.5 часа (90 мин)</option>
                            <option value="120">2 часа (120 мин)</option>
                            <option value="150">2.5 часа (150 мин)</option>
                            <option value="180">3 часа (180 мин)</option>
                            <option value="240">4 часа (240 мин)</option>
                        </select>
                    </div>
                </div>

                {{-- Status, Location, Price, Prepayment --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Статус съёмки
                        </label>
                        <select x-model="formData.status" 
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-900 focus:bg-white focus:outline-none focus:border-slate-900 transition-colors">
                            <option value="planned">Запланирована</option>
                            <option value="completed">Проведена</option>
                            <option value="cancelled">Отменена</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Локация / Студия
                        </label>
                        <input type="text" 
                               x-model="formData.location" 
                               placeholder="Студия Вспышка, зал 2" 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-900 focus:bg-white focus:outline-none focus:border-slate-900 transition-colors">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Стоимость (₽)
                        </label>
                        <input type="number" 
                               x-model="formData.price" 
                               placeholder="3500" 
                               min="0"
                               step="500" 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-900 focus:bg-white focus:outline-none focus:border-slate-900 transition-colors">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Предоплата (₽)
                        </label>
                        <input type="number" 
                               x-model="formData.prepayment" 
                               placeholder="1000 (по умолч.)" 
                               min="0"
                               step="100" 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-900 focus:bg-white focus:outline-none focus:border-slate-900 transition-colors">
                    </div>
                </div>

                {{-- Gallery / Ready Photos Link --}}
                <div class="p-3.5 bg-amber-50/60 rounded-xl border border-amber-200">
                    <label class="block text-xs font-bold uppercase tracking-wider text-amber-950 mb-1 flex items-center justify-between">
                        <span>Ссылка на готовые фотографии</span>
                        <span class="text-amber-800/70 font-normal lowercase">Яндекс Диск, Облако, Vigbo и т.д.</span>
                    </label>
                    <input type="url" 
                           x-model="formData.gallery_link" 
                           placeholder="https://disk.yandex.ru/d/... (или любая ссылка на облако)" 
                           class="w-full px-3.5 py-2 bg-white border border-amber-300 rounded-lg text-sm text-slate-900 focus:bg-white focus:outline-none focus:border-slate-900 transition-colors">
                    <p class="text-[0.68rem] text-amber-800/80 mt-1">
                        Когда статус съёмки переключится на «Проведена», клиент увидит на своей странице кнопку для скачивания фотографий.
                    </p>
                </div>

                {{-- Description / Shoot Details --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Описание съёмки, образ, концепт
                    </label>
                    <textarea x-model="formData.description" 
                              rows="3" 
                              placeholder="Пожелания клиента, образы, одежда, реквизит, референсы или тайминг..." 
                              class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-900 focus:bg-white focus:outline-none focus:border-slate-900 transition-colors"></textarea>
                </div>

                {{-- FILE ATTACHMENTS SECTION IN FORM --}}
                <div class="pt-2 border-t border-slate-200">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5 flex items-center justify-between">
                        <span>Файлы и материалы съёмки</span>
                        <span class="text-slate-400 font-normal lowercase">мудборд, референсы, бриф, договоры</span>
                    </label>

                    {{-- Already attached files in Edit mode --}}
                    <template x-if="isEditMode && currentShootFiles.length > 0">
                        <div class="mb-4 space-y-2">
                            <span class="text-[0.7rem] font-bold text-slate-600 uppercase tracking-wider block">Уже прикреплённые файлы (<span x-text="currentShootFiles.length"></span>):</span>
                            
                            {{-- Grid with thumbnails for images and cards for documents --}}
                            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-2.5 max-h-60 overflow-y-auto pr-1">
                                <template x-for="file in currentShootFiles" :key="file.id">
                                    <div class="relative group rounded-xl border border-slate-200 bg-white overflow-hidden shadow-2xs hover:shadow-md transition-all">
                                        
                                        {{-- Image Thumbnail with Lightbox trigger --}}
                                        <template x-if="file.is_image">
                                            <div class="relative aspect-square bg-slate-100 cursor-pointer overflow-hidden"
                                                 @click="openLightbox(file.url, file.original_name)"
                                                 title="Нажмите, чтобы открыть миниатюру на весь экран">
                                                <img :src="file.url" :alt="file.original_name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none">
                                                    <span class="p-1.5 rounded-full bg-white/25 text-white backdrop-blur-xs">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/></svg>
                                                    </span>
                                                </div>
                                            </div>
                                        </template>

                                        {{-- Non-Image File Tile --}}
                                        <template x-if="!file.is_image">
                                            <a :href="file.url" target="_blank" class="aspect-square bg-slate-50 flex flex-col items-center justify-center p-2 text-center border-b border-slate-100 group-hover:bg-slate-100 transition-colors">
                                                <div class="w-9 h-9 rounded-lg bg-slate-200 text-slate-700 flex items-center justify-center font-mono font-bold text-xs uppercase mb-1" x-text="file.extension"></div>
                                                <span class="text-[0.62rem] text-slate-500 font-mono" x-text="file.formatted_size"></span>
                                            </a>
                                        </template>

                                        {{-- File Name and Size --}}
                                        <div class="p-1.5 bg-white border-t border-slate-100">
                                            <p class="text-[0.68rem] font-medium text-slate-800 truncate" :title="file.original_name" x-text="file.original_name"></p>
                                            <p class="text-[0.62rem] text-slate-400 font-mono" x-text="file.formatted_size"></p>
                                        </div>

                                        {{-- Delete Button --}}
                                        <button type="button" 
                                                @click.stop="deleteFile(file.id)" 
                                                class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-black/60 hover:bg-rose-600 text-white flex items-center justify-center shadow transition-colors cursor-pointer z-10" 
                                                title="Удалить файл">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- Upload dropzone / input --}}
                    <div class="border-2 border-dashed border-slate-300 hover:border-slate-500 rounded-xl p-4 text-center bg-slate-50/50 hover:bg-slate-50 transition-all cursor-pointer relative">
                        <input type="file" 
                               multiple 
                               @change="handleFilesSelected($event)" 
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                               accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar">
                        <div class="flex flex-col items-center justify-center space-y-1">
                            <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            <span class="text-xs font-semibold text-slate-700">Нажмите для выбора файлов или перетащите их сюда</span>
                            <span class="text-[0.68rem] text-slate-400">Фотографии референсов (JPG, PNG), PDF, документы до 50 МБ</span>
                        </div>
                    </div>

                    {{-- Selected new files to upload (THUMBNAILS DISPLAY) --}}
                    <template x-if="newSelectedFiles.length > 0">
                        <div class="mt-3.5 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[0.7rem] font-bold text-slate-700 uppercase tracking-wider">Файлы к загрузке (<span x-text="newSelectedFiles.length"></span>):</span>
                                <button type="button" @click="clearNewSelectedFiles()" class="text-[0.68rem] text-rose-500 hover:text-rose-700 font-medium cursor-pointer">Очистить все</button>
                            </div>

                            {{-- Thumbnails grid for new staged files --}}
                            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-2.5 max-h-64 overflow-y-auto pr-1">
                                <template x-for="(file, idx) in newSelectedFiles" :key="idx">
                                    <div class="relative group rounded-xl border border-emerald-200 bg-white overflow-hidden shadow-2xs hover:shadow-md transition-all">
                                        
                                        {{-- Image Thumbnail with click to open in Lightbox --}}
                                        <template x-if="file.is_image">
                                            <div class="relative aspect-square bg-emerald-50 cursor-pointer overflow-hidden"
                                                 @click="openLightbox(file.url, file.name)"
                                                 title="Нажмите, чтобы открыть миниатюру на весь экран">
                                                <img :src="file.url" :alt="file.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                                                
                                                {{-- Hover Overlay with zoom icon --}}
                                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none">
                                                    <span class="p-1.5 rounded-full bg-white/25 text-white backdrop-blur-xs">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/></svg>
                                                    </span>
                                                </div>
                                            </div>
                                        </template>

                                        {{-- Non-Image Document Tile --}}
                                        <template x-if="!file.is_image">
                                            <div class="aspect-square bg-slate-50 flex flex-col items-center justify-center p-2 text-center border-b border-slate-100">
                                                <div class="w-9 h-9 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center font-mono font-bold text-xs uppercase mb-1">
                                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                                </div>
                                                <span class="text-[0.62rem] text-slate-500 font-mono" x-text="formatBytes(file.size)"></span>
                                            </div>
                                        </template>

                                        {{-- Name & Size bar --}}
                                        <div class="p-1.5 bg-white border-t border-slate-100">
                                            <p class="text-[0.68rem] font-medium text-slate-800 truncate" :title="file.name" x-text="file.name"></p>
                                            <p class="text-[0.62rem] text-slate-400 font-mono" x-text="formatBytes(file.size)"></p>
                                        </div>

                                        {{-- Remove from staging button --}}
                                        <button type="button" 
                                                @click.stop="removeNewSelectedFile(idx)" 
                                                class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-black/60 hover:bg-rose-600 text-white flex items-center justify-center shadow transition-colors cursor-pointer z-10"
                                                title="Убрать из списка">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Modal Actions --}}
                <div class="pt-4 border-t border-slate-200 flex items-center justify-between gap-3">
                    <button type="button" 
                            @click="closeFormModal()" 
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs uppercase font-bold rounded-lg transition-colors cursor-pointer">
                        Отмена
                    </button>

                    <div class="flex items-center gap-2">
                        <button type="submit" 
                                :disabled="isSubmitting"
                                class="px-5 py-2.5 bg-neutral-900 hover:bg-neutral-800 disabled:opacity-50 text-white text-xs uppercase tracking-wider font-bold rounded-lg shadow-sm hover:shadow transition-all inline-flex items-center gap-2 cursor-pointer">
                            <span x-show="!isSubmitting" x-text="isEditMode ? 'Сохранить изменения' : 'Внести съёмку в календарь'"></span>
                            <span x-show="isSubmitting">Сохранение...</span>
                        </button>
                    </div>
                </div>

            </form>

        </div>
    </div>

    {{-- MODAL: View Shoot Details --}}
    <div x-show="isViewModalOpen" 
         style="display: none;"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
        
        <div @click.away="closeViewModal()"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white rounded-2xl shadow-2xl max-w-3xl lg:max-w-4xl w-full overflow-hidden border border-slate-200 my-6 sm:my-8">
            
            <template x-if="selectedShoot">
                <div>
                    {{-- Header with Status Color --}}
                    <div class="p-6 md:p-8 text-white"
                         :class="{
                             'bg-slate-900': selectedShoot.status === 'planned',
                             'bg-emerald-900': selectedShoot.status === 'completed',
                             'bg-rose-950': selectedShoot.status === 'cancelled'
                         }">
                        <div class="flex items-start justify-between">
                            <div>
                                <span class="px-2.5 py-0.5 rounded-full text-[0.65rem] uppercase font-bold tracking-wider inline-block mb-2"
                                      :class="{
                                          'bg-blue-500/20 text-blue-200 border border-blue-400/30': selectedShoot.status === 'planned',
                                          'bg-emerald-500/20 text-emerald-200 border border-emerald-400/30': selectedShoot.status === 'completed',
                                          'bg-rose-500/20 text-rose-200 border border-rose-400/30': selectedShoot.status === 'cancelled'
                                      }"
                                      x-text="selectedShoot.status_label">
                                </span>
                                <h3 class="text-2xl md:text-3xl font-serif font-bold text-white leading-tight" x-text="selectedShoot.client_name"></h3>
                            </div>
                            <button type="button" @click="closeViewModal()" class="text-white/60 hover:text-white p-1.5 rounded-lg transition-colors cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        {{-- Date & Time Pill --}}
                        <div class="mt-4 flex flex-wrap items-center gap-2 text-xs text-white/90">
                            <div class="px-3 py-1.5 rounded-lg bg-white/10 backdrop-blur-xs flex items-center gap-1.5 border border-white/10">
                                <svg class="w-4 h-4 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span class="font-medium" x-text="formatDateRussian(selectedShoot.shoot_date)"></span>
                            </div>
                            <div class="px-3 py-1.5 rounded-lg bg-white/10 backdrop-blur-xs flex items-center gap-1.5 border border-white/10 font-mono">
                                <svg class="w-4 h-4 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span x-text="selectedShoot.start_time + ' – ' + (selectedShoot.end_time || '') + ' (' + selectedShoot.duration_label + ')'"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="p-6 md:p-8 space-y-5 text-sm text-slate-700 max-h-[75vh] overflow-y-auto custom-scrollbar">
                        
                        {{-- Contacts --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                            <div>
                                <span class="text-[0.68rem] uppercase font-bold tracking-wider text-slate-400 block mb-1">Телефон</span>
                                <template x-if="selectedShoot.phone">
                                    <a :href="'tel:' + selectedShoot.phone_clean" class="text-sm font-semibold text-slate-900 hover:text-crimson flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        <span x-text="selectedShoot.phone"></span>
                                    </a>
                                </template>
                                <template x-if="!selectedShoot.phone">
                                    <span class="text-xs text-slate-400 italic">Не указан</span>
                                </template>
                            </div>

                            <div>
                                <span class="text-[0.68rem] uppercase font-bold tracking-wider text-slate-400 block mb-1">Соцсеть / Мессенджер</span>
                                <template x-if="selectedShoot.social_link">
                                    <a :href="selectedShoot.social_url" target="_blank" class="text-sm font-semibold text-blue-600 hover:underline flex items-center gap-1.5 truncate">
                                        <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        <span class="truncate" x-text="selectedShoot.social_link"></span>
                                    </a>
                                </template>
                                <template x-if="!selectedShoot.social_link">
                                    <span class="text-xs text-slate-400 italic">Не указана</span>
                                </template>
                            </div>
                        </div>

                        {{-- PUBLIC CLIENT SHARE LINK BOX --}}
                        <div class="p-4 rounded-xl bg-gradient-to-r from-amber-500/10 via-amber-500/5 to-transparent border border-amber-300/40 space-y-2.5">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                                    </div>
                                    <div>
                                        <span class="text-xs font-bold text-slate-900 block">Публичная карточка для клиента</span>
                                        <span class="text-[0.68rem] text-slate-500 leading-tight block">Ссылка на страницу со временем, локацией, мудбордом и кнопкой календаря</span>
                                    </div>
                                </div>
                                
                                <a :href="selectedShoot.share_url" target="_blank" class="px-2.5 py-1 text-xs text-amber-900 font-semibold bg-white border border-amber-200 rounded-md hover:bg-amber-50 transition-colors inline-flex items-center gap-1 shrink-0">
                                    <span>Открыть</span>
                                    <svg class="w-3 h-3 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                            </div>

                            <div class="flex items-center gap-2">
                                <input type="text" readonly :value="selectedShoot.share_url" class="px-3 py-1.5 bg-white border border-slate-300 rounded-lg text-xs font-mono text-slate-700 flex-grow select-all">
                                
                                <button type="button" @click="copyText(selectedShoot.share_url, 'link-' + selectedShoot.id, 'Ссылка на съёмку скопирована в буфер!')" class="px-3.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-bold transition-all shrink-0 cursor-pointer inline-flex items-center gap-1.5 shadow-sm">
                                    <span x-text="copiedKey === 'link-' + selectedShoot.id ? '✓ Скопировано!' : 'Копировать ссылку'"></span>
                                </button>
                            </div>

                            <div class="pt-0.5">
                                <button type="button" @click="copyClientMessage(selectedShoot)" class="text-[0.72rem] text-slate-600 hover:text-slate-900 font-medium inline-flex items-center gap-1 underline underline-offset-2 cursor-pointer">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                    <span x-text="copiedKey === 'msg-' + selectedShoot.id ? '✓ Текст для клиента скопирован в буфер!' : 'Скопировать готовый текст с ссылкой для Telegram / WhatsApp'"></span>
                                </button>
                            </div>
                        </div>

                        {{-- READY PHOTOS GALLERY LINK IN VIEW MODAL --}}
                        <div class="p-3.5 rounded-xl border transition-all"
                             :class="selectedShoot.gallery_link ? 'bg-emerald-50/70 border-emerald-200' : 'bg-slate-50 border-slate-200'">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                                         :class="selectedShoot.gallery_link ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-500'">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <span class="text-xs font-bold text-slate-900 block">Готовые фотографии (ссылка)</span>
                                        <template x-if="selectedShoot.gallery_link && !isEditingGalleryLink">
                                            <a :href="selectedShoot.clean_gallery_url" target="_blank" class="text-xs text-emerald-700 hover:underline truncate block font-mono" x-text="selectedShoot.gallery_link"></a>
                                        </template>
                                        <template x-if="!selectedShoot.gallery_link && !isEditingGalleryLink">
                                            <span class="text-[0.72rem] text-slate-500 italic block">Ссылка на облако с готовыми фото пока не добавлена</span>
                                        </template>
                                    </div>
                                </div>

                                <div class="shrink-0 flex items-center gap-1.5" x-show="!isEditingGalleryLink">
                                    <template x-if="selectedShoot.gallery_link">
                                        <div class="flex items-center gap-1.5">
                                            <button type="button" @click="startEditingGalleryLink()" class="px-2 py-1 text-xs font-medium text-slate-600 hover:text-slate-900 bg-white border border-slate-200 rounded-md hover:bg-slate-50 transition-colors cursor-pointer" title="Изменить ссылку">
                                                Изменить
                                            </button>
                                            <button type="button" @click="copyText(selectedShoot.clean_gallery_url, 'gallery-' + selectedShoot.id, 'Ссылка на диск с фото скопирована!')" class="px-2.5 py-1 text-xs font-semibold bg-white border border-emerald-300 text-emerald-800 rounded-md hover:bg-emerald-50 transition-colors cursor-pointer">
                                                <span x-text="copiedKey === 'gallery-' + selectedShoot.id ? '✓ Скопировано' : 'Копировать'"></span>
                                            </button>
                                            <a :href="selectedShoot.clean_gallery_url" target="_blank" class="px-2.5 py-1 text-xs font-bold bg-emerald-700 text-white rounded-md hover:bg-emerald-800 transition-colors inline-flex items-center gap-1">
                                                <span>Открыть</span>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            </a>
                                        </div>
                                    </template>
                                    <template x-if="!selectedShoot.gallery_link">
                                        <button type="button" 
                                                @click="startEditingGalleryLink()" 
                                                class="px-3 py-1.5 text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors cursor-pointer shadow-xs inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                            <span>Добавить ссылку</span>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            {{-- Inline Quick Link Input Form --}}
                            <div x-show="isEditingGalleryLink" x-cloak class="mt-3 pt-3 border-t border-slate-200">
                                <label class="text-[0.68rem] uppercase font-bold text-slate-500 block mb-1.5">
                                    Ссылка на облачную галерею (Яндекс Диск, Google Drive, Облако Mail.ru и др.):
                                </label>
                                <div class="flex items-center gap-2">
                                    <input type="url" 
                                           id="quick_gallery_input"
                                           x-model="quickGalleryLink" 
                                           @keydown.enter.prevent="saveQuickGalleryLink()"
                                           @keydown.escape.prevent="cancelEditingGalleryLink()"
                                           placeholder="https://disk.yandex.ru/d/... (или любая ссылка на облако)" 
                                           class="flex-grow px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs placeholder-slate-400 focus:outline-none focus:border-emerald-600 font-mono">
                                    <button type="button" 
                                            @click="saveQuickGalleryLink()" 
                                            :disabled="isSavingGalleryLink"
                                            class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg transition-colors cursor-pointer shrink-0 disabled:opacity-50 inline-flex items-center gap-1.5 shadow-sm">
                                        <span x-show="!isSavingGalleryLink">Сохранить</span>
                                        <span x-show="isSavingGalleryLink">Сохранение...</span>
                                    </button>
                                    <button type="button" 
                                            @click="cancelEditingGalleryLink()" 
                                            class="px-3 py-2 bg-white hover:bg-slate-100 text-slate-600 border border-slate-200 font-semibold text-xs rounded-lg transition-colors cursor-pointer shrink-0">
                                        Отмена
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- CLIENT RECEIPT INDICATOR --}}
                        <div class="p-3.5 rounded-xl border transition-all"
                             :class="selectedShoot.booking_confirmed_at ? 'bg-emerald-50 border-emerald-300' : (selectedShoot.receipt_url ? 'bg-amber-50/80 border-amber-300' : 'bg-slate-50 border-slate-200')">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                                         :class="selectedShoot.booking_confirmed_at ? 'bg-emerald-600 text-white' : (selectedShoot.receipt_url ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-400')">
                                        <template x-if="selectedShoot.booking_confirmed_at">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        </template>
                                        <template x-if="!selectedShoot.booking_confirmed_at">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                                        </template>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold text-slate-900 block">Предоплата и бронь</span>
                                            <template x-if="selectedShoot.booking_confirmed_at">
                                                <span class="px-2 py-0.5 rounded-full text-[0.62rem] font-bold font-mono uppercase bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                    Подтверждена
                                                </span>
                                            </template>
                                        </div>
                                        <template x-if="selectedShoot.booking_confirmed_at">
                                            <div class="flex items-center gap-1.5 mt-0.5">
                                                <span class="text-[0.72rem] text-emerald-700 font-medium">✓ Бронь подтверждена</span>
                                                <span class="text-[0.65rem] text-slate-500 font-mono" x-text="selectedShoot.booking_confirmed_at"></span>
                                            </div>
                                        </template>
                                        <template x-if="!selectedShoot.booking_confirmed_at && selectedShoot.receipt_url">
                                            <div class="flex items-center gap-1.5 mt-0.5">
                                                <span class="text-[0.72rem] text-amber-700 font-semibold">Чек загружен:</span>
                                                <span class="text-[0.65rem] text-slate-500 font-mono" x-text="selectedShoot.receipt_uploaded_at"></span>
                                            </div>
                                        </template>
                                        <template x-if="!selectedShoot.receipt_url">
                                            <span class="text-[0.72rem] text-slate-400 italic block mt-0.5">Клиент ещё не загрузил чек</span>
                                        </template>
                                    </div>
                                </div>

                                <div class="shrink-0 flex items-center gap-2" x-show="selectedShoot.receipt_url">
                                    <a :href="selectedShoot.receipt_url" target="_blank" class="px-2.5 py-1 text-xs font-semibold bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-lg transition-colors inline-flex items-center gap-1 shadow-2xs" title="Открыть чек в новой вкладке">
                                        <span>Чек</span>
                                        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>

                                    <template x-if="!selectedShoot.booking_confirmed_at">
                                        <button type="button"
                                                @click="confirmBooking()"
                                                :disabled="isConfirmingBooking"
                                                class="px-3 py-1.5 text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-all shadow-sm cursor-pointer inline-flex items-center gap-1.5 disabled:opacity-50">
                                            <template x-if="!isConfirmingBooking">
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                    <span>Подтвердить бронь</span>
                                                </span>
                                            </template>
                                            <template x-if="isConfirmingBooking">
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                                                    <span>Подтверждаем...</span>
                                                </span>
                                            </template>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        {{-- Location & Price --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" x-show="selectedShoot.location || selectedShoot.price">
                            <div x-show="selectedShoot.location">
                                <span class="text-[0.68rem] uppercase font-bold tracking-wider text-slate-400 block mb-0.5">Локация</span>
                                <span class="font-semibold text-slate-900" x-text="selectedShoot.location"></span>
                            </div>
                            <div x-show="selectedShoot.price">
                                <span class="text-[0.68rem] uppercase font-bold tracking-wider text-slate-400 block mb-0.5">Стоимость</span>
                                <span class="font-serif font-bold text-slate-900 text-lg" x-text="selectedShoot.price + ' ₽'"></span>
                            </div>
                        </div>

                        {{-- Description --}}
                        <div>
                            <span class="text-[0.68rem] uppercase font-bold tracking-wider text-slate-400 block mb-1">Описание и концепт</span>
                            <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-700 whitespace-pre-line leading-relaxed"
                                 x-text="selectedShoot.description || 'Описание не заполнено.'">
                            </div>
                        </div>

                        {{-- ATTACHED FILES VIEW & UPLOAD SECTION --}}
                        <div class="pt-3 border-t border-slate-200">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <span class="text-[0.68rem] uppercase font-bold tracking-wider text-slate-700">Файлы и материалы</span>
                                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold" x-text="(selectedShoot.files ? selectedShoot.files.length : 0) + ' шт.'"></span>
                                </div>

                                {{-- Quick Add File Button --}}
                                <label class="text-xs text-crimson hover:text-crimson/80 font-bold flex items-center gap-1 cursor-pointer">
                                    <input type="file" multiple @change="uploadDirectFiles($event)" class="hidden" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                    <span>Добавить файл</span>
                                </label>
                            </div>

                            {{-- Files Grid --}}
                            <template x-if="selectedShoot.files && selectedShoot.files.length > 0">
                                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3">
                                    <template x-for="file in selectedShoot.files" :key="file.id">
                                        <div class="relative group rounded-xl border border-slate-200 bg-white overflow-hidden shadow-2xs hover:shadow-md transition-all flex flex-col justify-between">
                                            
                                            {{-- If Image: click opens lightbox --}}
                                            <template x-if="file.is_image">
                                                <div class="relative aspect-square bg-slate-100 cursor-pointer overflow-hidden"
                                                     @click="openLightbox(file.url, file.original_name)"
                                                     title="Нажмите, чтобы открыть миниатюру на весь экран">
                                                    <img :src="file.url" :alt="file.original_name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                                                    
                                                    {{-- Hover zoom overlay --}}
                                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none">
                                                        <span class="p-1.5 rounded-full bg-white/25 text-white backdrop-blur-xs">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/></svg>
                                                        </span>
                                                    </div>
                                                </div>
                                            </template>

                                            {{-- If Non-Image: document badge --}}
                                            <template x-if="!file.is_image">
                                                <a :href="file.url" target="_blank" class="aspect-square bg-slate-50 flex flex-col items-center justify-center p-2 text-center border-b border-slate-100 group-hover:bg-slate-100 transition-colors">
                                                    <div class="w-10 h-10 rounded-lg bg-slate-200 text-slate-700 flex items-center justify-center font-mono font-bold text-xs uppercase mb-1" x-text="file.extension"></div>
                                                    <span class="text-[0.62rem] text-slate-500 font-mono" x-text="file.formatted_size"></span>
                                                </a>
                                            </template>

                                            {{-- Caption & Action Bar --}}
                                            <div class="p-2 bg-white border-t border-slate-100 flex items-center justify-between gap-1">
                                                <div class="min-w-0 pr-1">
                                                    <p class="text-[0.68rem] font-medium text-slate-800 truncate" :title="file.original_name" x-text="file.original_name"></p>
                                                    <p class="text-[0.62rem] text-slate-400 font-mono" x-text="file.formatted_size"></p>
                                                </div>
                                                <div class="flex items-center gap-1 shrink-0">
                                                    <a :href="file.url" download class="p-1 text-slate-400 hover:text-blue-600 rounded hover:bg-blue-50 transition-colors" title="Скачать файл">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                    </a>
                                                    <button type="button" @click="deleteFile(file.id)" class="p-1 text-slate-400 hover:text-rose-600 rounded hover:bg-rose-50 transition-colors cursor-pointer" title="Удалить файл">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <template x-if="!selectedShoot.files || selectedShoot.files.length === 0">
                                <div class="p-4 rounded-xl border border-dashed border-slate-200 text-center text-xs text-slate-400 bg-slate-50">
                                    К этой съёмке пока не прикреплены файлы. Нажмите «Добавить файл», чтобы загрузить мудборд или референсы.
                                </div>
                            </template>
                        </div>

                        {{-- Quick Status Toggle Bar --}}
                        <div class="pt-2">
                            <span class="text-[0.68rem] uppercase font-bold tracking-wider text-slate-400 block mb-1.5">Быстро изменить статус</span>
                            <div class="grid grid-cols-3 gap-2">
                                <button type="button" 
                                        @click="quickChangeStatus(selectedShoot, 'planned')" 
                                        :class="selectedShoot.status === 'planned' ? 'bg-blue-600 text-white font-bold' : 'bg-slate-100 hover:bg-slate-200 text-slate-700'"
                                        class="py-1.5 px-2 rounded-lg text-xs transition-colors cursor-pointer text-center">
                                    Запланирована
                                </button>
                                <button type="button" 
                                        @click="quickChangeStatus(selectedShoot, 'completed')" 
                                        :class="selectedShoot.status === 'completed' ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-100 hover:bg-slate-200 text-slate-700'"
                                        class="py-1.5 px-2 rounded-lg text-xs transition-colors cursor-pointer text-center">
                                    Проведена
                                </button>
                                <button type="button" 
                                        @click="quickChangeStatus(selectedShoot, 'cancelled')" 
                                        :class="selectedShoot.status === 'cancelled' ? 'bg-rose-600 text-white font-bold' : 'bg-slate-100 hover:bg-slate-200 text-slate-700'"
                                        class="py-1.5 px-2 rounded-lg text-xs transition-colors cursor-pointer text-center">
                                    Отменена
                                </button>
                            </div>
                        </div>

                    </div>

                    {{-- Footer Actions --}}
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                        <button type="button" 
                                @click="deleteShoot(selectedShoot)" 
                                class="text-rose-600 hover:text-rose-800 text-xs font-semibold cursor-pointer">
                            Удалить съёмку
                        </button>

                        <div class="flex items-center gap-2">
                            <button type="button" 
                                    @click="copyShareLink(selectedShoot)" 
                                    class="px-3.5 py-2 bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-300 rounded-lg text-xs font-bold transition-colors inline-flex items-center gap-1.5 cursor-pointer">
                                <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                                <span x-text="copiedKey === 'link-' + selectedShoot.id ? '✓ Ссылка скопирована!' : 'Поделиться'"></span>
                            </button>

                            <button type="button" 
                                    @click="openEditModal(selectedShoot)" 
                                    class="px-4 py-2 bg-neutral-900 hover:bg-neutral-800 text-white text-xs uppercase font-bold rounded-lg transition-colors cursor-pointer">
                                Редактировать
                            </button>
                        </div>
                    </div>
                </div>
            </template>

        </div>
    </div>

    {{-- Toast Notification --}}
    <div x-show="showToast" 
         style="display: none;"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-3"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-3"
         class="fixed bottom-6 right-6 z-50 bg-slate-900 text-white px-4 py-3 rounded-xl shadow-2xl flex items-center gap-3 border border-white/10 text-xs">
        <div class="w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
        </div>
        <span class="font-medium" x-text="toastMessage"></span>
    </div>

    {{-- Fullscreen Image Lightbox Modal --}}
    <div x-show="lightboxOpen" 
         style="display: none;"
         x-cloak 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="closeLightbox()"
         class="fixed inset-0 z-[120] flex items-center justify-center bg-black/95 backdrop-blur-md p-4 sm:p-8"
         role="dialog"
         aria-modal="true"
         aria-label="Просмотр изображения">
        
        {{-- Close button --}}
        <button type="button" 
                @click="closeLightbox()" 
                class="absolute top-4 right-4 sm:top-6 sm:right-6 text-white/80 hover:text-white p-2.5 rounded-full bg-white/10 hover:bg-white/20 transition-all z-50 cursor-pointer shadow-lg"
                title="Закрыть (Esc)">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        {{-- Download button in Lightbox --}}
        <a :href="lightboxUrl" 
           :download="lightboxTitle || 'image'" 
           target="_blank"
           class="absolute top-4 right-16 sm:top-6 sm:right-20 text-white/80 hover:text-white px-3.5 py-2 rounded-full bg-white/10 hover:bg-white/20 transition-all z-50 cursor-pointer text-xs font-semibold inline-flex items-center gap-1.5 shadow-lg"
           title="Скачать файл">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            <span class="hidden sm:inline">Скачать</span>
        </a>

        {{-- Fullscreen Image Preview --}}
        <div class="relative max-w-full max-h-[90vh] flex flex-col items-center justify-center select-none" 
             @click.away="closeLightbox()">
            <img :src="lightboxUrl" 
                 :alt="lightboxTitle" 
                 class="max-w-full max-h-[82vh] object-contain rounded-xl shadow-2xl ring-1 ring-white/10">
            
            <template x-if="lightboxTitle">
                <div class="mt-3.5 text-center text-xs sm:text-sm text-white/90 font-mono px-4 max-w-xl truncate bg-white/10 py-1.5 rounded-full border border-white/10 backdrop-blur-xs" x-text="lightboxTitle"></div>
            </template>
        </div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('shootCalendar', (config) => ({
        shoots: config.initialShoots || [],
        currentYear: config.year,
        currentMonth: config.month, // 1-12
        activeView: 'calendar', // 'calendar' or 'list'
        searchQuery: '',
        filterStatus: 'all',
        
        // Modals
        isFormModalOpen: false,
        isViewModalOpen: false,
        isEditMode: false,
        isSubmitting: false,
        selectedShoot: null,

        // Share & Clipboard state
        copiedKey: null,
        toastMessage: '',
        showToast: false,

        // Form state
        formData: {
            id: null,
            client_name: '',
            social_link: '',
            phone: '',
            description: '',
            shoot_date: '',
            start_time: '12:00',
            duration_minutes: 60,
            status: 'planned',
            location: '',
            price: '',
            gallery_link: ''
        },

        // Files handling in form
        newSelectedFiles: [],
        currentShootFiles: [],

        // Lightbox modal state
        lightboxOpen: false,
        lightboxUrl: '',
        lightboxTitle: '',

        // Quick Gallery Link inline state in View modal
        isEditingGalleryLink: false,
        quickGalleryLink: '',
        isSavingGalleryLink: false,
        isConfirmingBooking: false,

        init() {
            // Initial setup
        },

        // Helper: Russian month name
        monthName() {
            const months = [
                'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
                'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'
            ];
            return months[this.currentMonth - 1];
        },

        // Calculate shoots for current month
        shootsInCurrentMonthCount() {
            const prefix = `${this.currentYear}-${String(this.currentMonth).padStart(2, '0')}`;
            return this.shoots.filter(s => s.shoot_date && s.shoot_date.startsWith(prefix)).length;
        },

        // Calendar Grid generation
        get calendarDays() {
            const year = this.currentYear;
            const month = this.currentMonth - 1; // 0-indexed for JS Date

            const firstDayOfMonth = new Date(year, month, 1);
            const lastDayOfMonth = new Date(year, month + 1, 0);

            // Russian week starts on Monday (1). Sunday is 0 -> 7
            let startDayOfWeek = firstDayOfMonth.getDay();
            if (startDayOfWeek === 0) startDayOfWeek = 7;

            const days = [];
            const todayStr = new Date().toISOString().split('T')[0];

            // Leading days from previous month
            const prevMonthLastDay = new Date(year, month, 0).getDate();
            for (let i = startDayOfWeek - 1; i > 0; i--) {
                const dayNum = prevMonthLastDay - i + 1;
                const prevDate = new Date(year, month - 1, dayNum);
                const dateString = `${prevDate.getFullYear()}-${String(prevDate.getMonth() + 1).padStart(2, '0')}-${String(dayNum).padStart(2, '0')}`;
                const dow = prevDate.getDay();
                days.push({
                    dayNumber: dayNum,
                    dateString: dateString,
                    isCurrentMonth: false,
                    isToday: dateString === todayStr,
                    isWeekend: dow === 0 || dow === 6
                });
            }

            // Current month days
            for (let d = 1; d <= lastDayOfMonth.getDate(); d++) {
                const dateString = `${year}-${String(month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                const currDate = new Date(year, month, d);
                const dow = currDate.getDay();
                days.push({
                    dayNumber: d,
                    dateString: dateString,
                    isCurrentMonth: true,
                    isToday: dateString === todayStr,
                    isWeekend: dow === 0 || dow === 6
                });
            }

            // Trailing days to fill 35 or 42 grid cells (complete weeks)
            const remaining = (7 - (days.length % 7)) % 7;
            for (let d = 1; d <= remaining; d++) {
                const nextDate = new Date(year, month + 1, d);
                const dateString = `${nextDate.getFullYear()}-${String(nextDate.getMonth() + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                const dow = nextDate.getDay();
                days.push({
                    dayNumber: d,
                    dateString: dateString,
                    isCurrentMonth: false,
                    isToday: dateString === todayStr,
                    isWeekend: dow === 0 || dow === 6
                });
            }

            return days;
        },

        // Shoots on a specific day
        getShootsForDay(dateString) {
            return this.shoots
                .filter(s => s.shoot_date === dateString)
                .sort((a, b) => a.start_time.localeCompare(b.start_time));
        },

        // Color badge for shoot cards
        getShootStyle(status) {
            if (status === 'completed') {
                return 'bg-emerald-50 text-emerald-900 border-emerald-200 hover:bg-emerald-100/80';
            }
            if (status === 'cancelled') {
                return 'bg-rose-50 text-rose-800 border-rose-200 line-through opacity-70 hover:bg-rose-100/80';
            }
            return 'bg-blue-50 text-blue-900 border-blue-200 hover:bg-blue-100/80';
        },

        // Filtered shoots for list view
        get filteredShoots() {
            let list = [...this.shoots];

            if (this.filterStatus !== 'all') {
                list = list.filter(s => s.status === this.filterStatus);
            }

            if (this.searchQuery.trim() !== '') {
                const q = this.searchQuery.toLowerCase();
                list = list.filter(s => 
                    (s.client_name && s.client_name.toLowerCase().includes(q)) ||
                    (s.phone && s.phone.includes(q)) ||
                    (s.social_link && s.social_link.toLowerCase().includes(q)) ||
                    (s.description && s.description.toLowerCase().includes(q)) ||
                    (s.location && s.location.toLowerCase().includes(q))
                );
            }

            return list.sort((a, b) => {
                if (a.shoot_date === b.shoot_date) {
                    return a.start_time.localeCompare(b.start_time);
                }
                return b.shoot_date.localeCompare(a.shoot_date);
            });
        },

        // Month Navigation
        prevMonth() {
            if (this.currentMonth === 1) {
                this.currentMonth = 12;
                this.currentYear--;
            } else {
                this.currentMonth--;
            }
            this.fetchShoots();
        },

        nextMonth() {
            if (this.currentMonth === 12) {
                this.currentMonth = 1;
                this.currentYear++;
            } else {
                this.currentMonth++;
            }
            this.fetchShoots();
        },

        goToday() {
            const now = new Date();
            this.currentYear = now.getFullYear();
            this.currentMonth = now.getMonth() + 1;
            this.fetchShoots();
        },

        // AJAX fetch shoots for the selected month
        async fetchShoots() {
            try {
                const url = `${config.indexUrl}?year=${this.currentYear}&month=${this.currentMonth}`;
                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (response.ok) {
                    const data = await response.json();
                    if (data.shoots) {
                        this.shoots = data.shoots;
                    }
                }
            } catch (e) {
                console.error('Error fetching shoots:', e);
            }
        },

        // Files selection in modal with instant image preview thumbnails
        handleFilesSelected(event) {
            const rawFiles = Array.from(event.target.files);
            if (!rawFiles.length) return;

            const mapped = rawFiles.map(file => {
                const isImage = file.type ? file.type.startsWith('image/') : /\.(jpe?g|png|webp|gif|avif|bmp|svg)$/i.test(file.name);
                return {
                    file: file,
                    name: file.name,
                    size: file.size,
                    is_image: isImage,
                    url: isImage ? URL.createObjectURL(file) : null
                };
            });

            this.newSelectedFiles = [...this.newSelectedFiles, ...mapped];
            event.target.value = '';
        },

        removeNewSelectedFile(index) {
            const item = this.newSelectedFiles[index];
            if (item && item.url) {
                URL.revokeObjectURL(item.url);
            }
            this.newSelectedFiles.splice(index, 1);
        },

        clearNewSelectedFiles() {
            this.newSelectedFiles.forEach(item => {
                if (item && item.url) {
                    URL.revokeObjectURL(item.url);
                }
            });
            this.newSelectedFiles = [];
        },

        // Fullscreen image lightbox viewer
        openLightbox(url, title = '') {
            if (!url) return;
            this.lightboxUrl = url;
            this.lightboxTitle = title;
            this.lightboxOpen = true;
            document.body.style.overflow = 'hidden';
        },

        closeLightbox() {
            this.lightboxOpen = false;
            this.lightboxUrl = '';
            this.lightboxTitle = '';
            document.body.style.overflow = '';
        },

        // Open create modal
        openCreateModal(prefillDate = null) {
            this.isEditMode = false;
            this.clearNewSelectedFiles();
            this.currentShootFiles = [];
            const defaultDate = prefillDate || new Date().toISOString().split('T')[0];
            this.formData = {
                id: null,
                client_name: '',
                social_link: '',
                phone: '',
                description: '',
                shoot_date: defaultDate,
                start_time: '12:00',
                duration_minutes: 60,
                status: 'planned',
                location: '',
                price: '',
                prepayment: '',
                gallery_link: ''
            };
            this.isFormModalOpen = true;
        },

        // Open edit modal
        openEditModal(shoot = null) {
            const targetShoot = shoot || this.selectedShoot;
            if (!targetShoot) return;

            this.isViewModalOpen = false;
            this.isEditMode = true;
            this.clearNewSelectedFiles();
            this.currentShootFiles = targetShoot.files ? [...targetShoot.files] : [];
            this.formData = {
                id: targetShoot.id,
                client_name: targetShoot.client_name,
                social_link: targetShoot.social_link || '',
                phone: targetShoot.phone || '',
                description: targetShoot.description || '',
                shoot_date: targetShoot.shoot_date,
                start_time: targetShoot.start_time,
                duration_minutes: targetShoot.duration_minutes || 60,
                status: targetShoot.status || 'planned',
                location: targetShoot.location || '',
                price: targetShoot.price || '',
                prepayment: targetShoot.prepayment || '',
                gallery_link: targetShoot.gallery_link || ''
            };
            this.isFormModalOpen = true;
        },

        closeFormModal() {
            this.isFormModalOpen = false;
            this.clearNewSelectedFiles();
            this.currentShootFiles = [];
        },

        // Open view details modal
        openViewModal(shoot) {
            // Find freshest shoot data
            const found = this.shoots.find(s => s.id === shoot.id) || shoot;
            this.selectedShoot = found;
            this.isEditingGalleryLink = false;
            this.quickGalleryLink = '';
            this.isViewModalOpen = true;
        },

        closeViewModal() {
            this.isViewModalOpen = false;
            this.isEditingGalleryLink = false;
            this.quickGalleryLink = '';
            this.selectedShoot = null;
        },

        // Quick Gallery Link inline handlers
        startEditingGalleryLink() {
            this.quickGalleryLink = this.selectedShoot ? (this.selectedShoot.gallery_link || '') : '';
            this.isEditingGalleryLink = true;
            this.$nextTick(() => {
                const el = document.getElementById('quick_gallery_input');
                if (el) el.focus();
            });
        },

        cancelEditingGalleryLink() {
            this.isEditingGalleryLink = false;
            this.quickGalleryLink = '';
        },

        async saveQuickGalleryLink() {
            if (!this.selectedShoot) return;
            this.isSavingGalleryLink = true;

            try {
                const response = await fetch(`/admin/shoots/${this.selectedShoot.id}/gallery-link`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': config.csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        gallery_link: (this.quickGalleryLink || '').trim()
                    })
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    this.selectedShoot.gallery_link = result.gallery_link;
                    this.selectedShoot.clean_gallery_url = result.clean_gallery_url;
                    if (result.status) {
                        this.selectedShoot.status = result.status;
                        this.selectedShoot.status_label = result.status_label;
                    }

                    // Update in main shoots array
                    const found = this.shoots.find(s => s.id === this.selectedShoot.id);
                    if (found) {
                        found.gallery_link = result.gallery_link;
                        found.clean_gallery_url = result.clean_gallery_url;
                        if (result.status) {
                            found.status = result.status;
                            found.status_label = result.status_label;
                        }
                    }

                    this.isEditingGalleryLink = false;
                    this.toastMessage = 'Ссылка на готовые фото сохранена!';
                    this.showToast = true;
                    setTimeout(() => this.showToast = false, 3000);
                } else {
                    alert(result.message || 'Ошибка сохранения ссылки.');
                }
            } catch (e) {
                console.error(e);
                alert('Произошла ошибка при сохранении ссылки.');
            } finally {
                this.isSavingGalleryLink = false;
            }
        },

        // Admin confirms client prepayment & booking
        async confirmBooking() {
            if (!this.selectedShoot || this.isConfirmingBooking) return;
            this.isConfirmingBooking = true;

            try {
                const response = await fetch(`/admin/shoots/${this.selectedShoot.id}/confirm-booking`, {
                    method: 'PATCH',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': config.csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    this.selectedShoot.booking_confirmed_at = result.booking_confirmed_at;

                    // Update in main shoots array
                    const found = this.shoots.find(s => s.id === this.selectedShoot.id);
                    if (found) {
                        found.booking_confirmed_at = result.booking_confirmed_at;
                    }

                    this.toastMessage = 'Бронь успешно подтверждена!';
                    this.showToast = true;
                    setTimeout(() => this.showToast = false, 3000);
                } else {
                    alert(result.message || 'Ошибка при подтверждении бронирования.');
                }
            } catch (e) {
                console.error(e);
                alert('Произошла сетевая ошибка при подтверждении брони.');
            } finally {
                this.isConfirmingBooking = false;
            }
        },

        // Submit form (create or edit) with FormData to support file uploads
        async submitShootForm() {
            this.isSubmitting = true;

            const url = this.isEditMode 
                ? `/admin/shoots/${this.formData.id}` 
                : config.storeUrl;

            const formPayload = new FormData();
            formPayload.append('client_name', this.formData.client_name);
            formPayload.append('social_link', this.formData.social_link || '');
            formPayload.append('phone', this.formData.phone || '');
            formPayload.append('description', this.formData.description || '');
            formPayload.append('shoot_date', this.formData.shoot_date);
            formPayload.append('start_time', this.formData.start_time);
            formPayload.append('duration_minutes', this.formData.duration_minutes);
            formPayload.append('status', this.formData.status);
            formPayload.append('location', this.formData.location || '');
            formPayload.append('gallery_link', this.formData.gallery_link || '');
            if (this.formData.price) {
                formPayload.append('price', this.formData.price);
            }
            if (this.formData.prepayment) {
                formPayload.append('prepayment', this.formData.prepayment);
            }

            if (this.isEditMode) {
                formPayload.append('_method', 'PUT');
            }

            // Append attached files
            this.newSelectedFiles.forEach((item) => {
                formPayload.append('files[]', item.file);
            });

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': config.csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formPayload
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    this.clearNewSelectedFiles();
                    this.closeFormModal();
                    await this.fetchShoots();
                    if (this.selectedShoot && result.shoot) {
                        this.selectedShoot = result.shoot;
                    }
                } else {
                    alert(result.message || 'Ошибка сохранения. Проверьте правильность заполнения полей.');
                }
            } catch (err) {
                alert('Произошла ошибка при отправке запроса.');
                console.error(err);
            } finally {
                this.isSubmitting = false;
            }
        },

        // Upload additional files directly from View modal
        async uploadDirectFiles(event) {
            const files = Array.from(event.target.files);
            if (!files.length || !this.selectedShoot) return;

            const formData = new FormData();
            files.forEach(f => formData.append('files[]', f));

            try {
                const response = await fetch(`/admin/shoots/${this.selectedShoot.id}/files`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': config.csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    if (!this.selectedShoot.files) this.selectedShoot.files = [];
                    this.selectedShoot.files.push(...result.files);

                    // Update in main shoots array
                    const item = this.shoots.find(s => s.id === this.selectedShoot.id);
                    if (item) {
                        item.files = this.selectedShoot.files;
                    }
                } else {
                    alert(result.message || 'Не удалось загрузить файлы.');
                }
            } catch (err) {
                alert('Ошибка загрузки файлов.');
                console.error(err);
            } finally {
                event.target.value = '';
            }
        },

        // Delete file
        async deleteFile(fileId) {
            if (!confirm('Вы действительно хотите удалить этот файл?')) return;

            try {
                const response = await fetch(`/admin/shoots/files/${fileId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': config.csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    // Remove from selectedShoot
                    if (this.selectedShoot && this.selectedShoot.files) {
                        this.selectedShoot.files = this.selectedShoot.files.filter(f => f.id !== fileId);
                    }
                    // Remove from edit modal list
                    this.currentShootFiles = this.currentShootFiles.filter(f => f.id !== fileId);

                    // Remove from main shoots collection
                    this.shoots.forEach(s => {
                        if (s.files) {
                            s.files = s.files.filter(f => f.id !== fileId);
                        }
                    });
                } else {
                    alert(result.message || 'Ошибка удаления файла.');
                }
            } catch (err) {
                console.error(err);
                alert('Не удалось удалить файл.');
            }
        },

        // Quick status switch from View modal
        async quickChangeStatus(shoot, newStatus) {
            try {
                const response = await fetch(`/admin/shoots/${shoot.id}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': config.csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ status: newStatus })
                });

                if (response.ok) {
                    const result = await response.json();
                    shoot.status = result.status;
                    shoot.status_label = result.status_label;
                    if (this.selectedShoot && this.selectedShoot.id === shoot.id) {
                        this.selectedShoot.status = result.status;
                        this.selectedShoot.status_label = result.status_label;
                    }
                }
            } catch (e) {
                console.error(e);
            }
        },

        // Delete shoot
        async deleteShoot(shoot) {
            if (!confirm(`Вы действительно хотите удалить съёмку для "${shoot.client_name}"?`)) {
                return;
            }

            try {
                const response = await fetch(`/admin/shoots/${shoot.id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': config.csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) {
                    this.shoots = this.shoots.filter(s => s.id !== shoot.id);
                    if (this.selectedShoot && this.selectedShoot.id === shoot.id) {
                        this.closeViewModal();
                    }
                }
            } catch (e) {
                console.error(e);
                alert('Не удалось удалить съёмку.');
            }
        },

        // Format bytes helper
        formatBytes(bytes) {
            if (bytes === 0) return '0 Б';
            const k = 1024;
            const sizes = ['Б', 'КБ', 'МБ', 'ГБ'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
        },

        // Copy to clipboard helper
        copyText(text, key, message = 'Скопировано в буфер обмена!') {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text);
            } else {
                const textArea = document.createElement("textarea");
                textArea.value = text;
                textArea.style.position = "fixed";
                textArea.style.opacity = "0";
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                try { document.execCommand('copy'); } catch (err) {}
                document.body.removeChild(textArea);
            }
            this.copiedKey = key;
            this.toastMessage = message;
            this.showToast = true;
            setTimeout(() => {
                if (this.copiedKey === key) this.copiedKey = null;
            }, 2500);
            setTimeout(() => {
                this.showToast = false;
            }, 3000);
        },

        copyShareLink(shoot) {
            this.copyText(shoot.share_url, 'link-' + shoot.id, 'Ссылка на съёмку для ' + shoot.client_name + ' скопирована!');
        },

        copyClientMessage(shoot) {
            const msg = `Здравствуйте, ${shoot.client_name}! Вот ссылка на персональную карточку нашей предстоящей фотосессии (тайминг, адрес локации и референсы):\n${shoot.share_url}`;
            this.copyText(msg, 'msg-' + shoot.id, 'Готовое сообщение для клиента скопировано!');
        },

        // Date formatter for Russian display
        formatDateRussian(dateStr) {
            if (!dateStr) return '';
            try {
                const [year, month, day] = dateStr.split('-');
                const months = [
                    'января', 'февраля', 'марта', 'апреля', 'мая', 'июня',
                    'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'
                ];
                return `${parseInt(day, 10)} ${months[parseInt(month, 10) - 1]} ${year}`;
            } catch (e) {
                return dateStr;
            }
        }
    }));
});
</script>
@endpush
@endsection
