@extends('layouts.app')

@section('title', 'Статьи и заметки о фотографии — ' . __('site.author_name') . ' • Иркутск')
@section('description', 'Полезные статьи о фотосессиях в Иркутске, подготовке к портретной и парной съёмке, выборе локаций, работе со светом и естественности в кадре.')

@section('og_title', 'Статьи и заметки о фотографии — ' . __('site.author_name'))
@section('og_description', 'Полезные статьи о фотосессиях в Иркутске, подготовке к портретной и парной съёмке, выборе локаций и работе со светом.')
@section('og_type', 'blog')

@section('content')
<div class="py-12 md:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        
        {{-- Section Header --}}
        <div class="space-y-4 max-w-3xl">
            <div class="inline-flex items-center gap-2 text-xs font-mono uppercase tracking-widest text-crimson font-bold">
                <span class="w-2 h-2 rounded-full bg-crimson animate-pulse"></span>
                <span>ОПЫТ И ЗАМЕТКИ</span>
            </div>
            
            <h1 class="text-4xl sm:text-6xl font-extrabold uppercase tracking-tightest font-display text-white">
                СТАТЬИ
            </h1>
            
            <p class="text-sm sm:text-base text-neutral-400 font-mono leading-relaxed">
                Заметки о процессе съёмки, подготовке к фотосессии, работе со студийным и естественным светом, а также интересные локации Иркутска и Байкала.
            </p>
        </div>

        {{-- Articles Grid --}}
        @if($articles->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($articles as $article)
                    <article class="group flex flex-col bg-cine-surface border border-cine-border hover:border-neutral-500 transition-all duration-300 rounded-xl overflow-hidden shadow-sm hover:shadow-card-depth">
                        {{-- Cover Image Link --}}
                        <a href="{{ route('articles.show', $article->slug) }}" class="relative aspect-[16/10] overflow-hidden bg-neutral-900 block">
                            <img src="{{ $article->cover_url }}" 
                                 alt="{{ $article->title }}" 
                                 loading="lazy" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out">
                            <div class="absolute inset-0 bg-gradient-to-t from-cine-black/70 via-transparent to-transparent opacity-60 group-hover:opacity-40 transition-opacity"></div>
                            
                            {{-- Reading time pill --}}
                            <div class="absolute top-3 right-3 px-2.5 py-1 rounded bg-black/75 backdrop-blur-md border border-white/10 text-[0.65rem] font-mono text-neutral-300 uppercase tracking-wider">
                                {{ $article->estimated_reading_time }} мин чтения
                            </div>
                        </a>

                        {{-- Content Body --}}
                        <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                            <div class="space-y-3">
                                {{-- Date & Meta --}}
                                <div class="flex items-center gap-2 text-xs font-mono text-neutral-400">
                                    <span class="text-crimson font-semibold">{{ $article->formatted_date }}</span>
                                    <span>&bull;</span>
                                    <span>👁 {{ $article->views_count }}</span>
                                    @if($article->comments_count > 0)
                                        <span>&bull;</span>
                                        <span class="flex items-center gap-1 text-neutral-300">
                                            <svg class="w-3.5 h-3.5 text-neutral-400 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                            </svg>
                                            {{ $article->comments_count }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Title --}}
                                <h2 class="text-xl font-bold uppercase tracking-tight text-white group-hover:text-crimson transition-colors font-display line-clamp-2 leading-snug">
                                    <a href="{{ route('articles.show', $article->slug) }}">
                                        {{ $article->title }}
                                    </a>
                                </h2>

                                {{-- Excerpt --}}
                                @if($article->excerpt)
                                    <p class="text-xs sm:text-sm text-neutral-400 line-clamp-3 leading-relaxed">
                                        {{ $article->excerpt }}
                                    </p>
                                @endif
                            </div>

                            {{-- Card Footer --}}
                            <div class="pt-4 border-t border-cine-border flex items-center justify-between">
                                <a href="{{ route('articles.show', $article->slug) }}" class="inline-flex items-center gap-2 text-xs font-mono uppercase tracking-wider text-crimson font-bold group-hover:translate-x-1 transition-transform">
                                    <span>Читать статью</span>
                                    <span>&rarr;</span>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Custom Pagination --}}
            <div class="pt-4">
                {{ $articles->links('components.pagination') }}
            </div>
        @else
            <div class="bg-cine-surface border border-cine-border rounded-xl p-12 text-center space-y-4">
                <div class="w-12 h-12 rounded-full bg-white/5 border border-white/10 mx-auto flex items-center justify-center text-neutral-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold font-display uppercase tracking-tight text-white">Статьи готовятся к публикации</h3>
                <p class="text-xs sm:text-sm text-neutral-400 font-mono max-w-md mx-auto">
                    В скором времени здесь появятся подробные материалы о съемках, локациях и подборе образов.
                </p>
                <div class="pt-2">
                    <a href="{{ route('home') }}" class="btn-crimson px-5 py-2.5 text-xs font-mono font-bold tracking-wider inline-block">
                        На главную
                    </a>
                </div>
            </div>
        @endif

    </div>
</div>
@endsection
