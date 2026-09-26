@extends('layouts.app')

@section('title', ($article->meta_title ?: $article->title) . ' — Роман Юн')
@section('description', $article->meta_description ?: ($article->excerpt ?: Str::limit(strip_tags($article->content), 160)))

{{-- Open Graph Meta Tags for Social Sharing --}}
@section('og_title', $article->meta_title ?: $article->title)
@section('og_description', $article->meta_description ?: ($article->excerpt ?: Str::limit(strip_tags($article->content), 160)))
@section('og_type', 'article')
@section('og_image', $article->cover_url)
@section('og_url', route('articles.show', $article->slug))

@push('meta_links')
    <meta property="article:published_time" content="{{ $article->published_at?->toIso8601String() ?? $article->created_at->toIso8601String() }}">
    <meta property="article:author" content="Роман Юн">
@endpush

@section('content')
<div x-data="articleReader()" class="py-8 md:py-14">
    <article class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 md:space-y-10">
        
        {{-- Breadcrumbs / Back button --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('articles.index') }}" 
               class="inline-flex items-center gap-2 text-xs font-mono uppercase tracking-widest text-neutral-400 hover:text-crimson transition-colors group">
                <span class="group-hover:-translate-x-1 transition-transform">&larr;</span>
                <span>Ко всем статьям</span>
            </a>

            {{-- Reader mode trigger (Desktop & Mobile) --}}
            <button type="button" 
                    @click="openReader()" 
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-crimson/10 hover:bg-crimson text-crimson hover:text-white border border-crimson/30 hover:border-crimson text-[0.7rem] font-mono font-bold uppercase tracking-wider transition-all shadow-sm cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <span>Режим чтения</span>
            </button>
        </div>

        {{-- Header Area --}}
        <header class="space-y-5">
            {{-- Metadata Pill Row --}}
            <div class="flex flex-wrap items-center gap-2.5 sm:gap-3 text-xs font-mono text-neutral-400">
                <span class="text-crimson font-bold uppercase tracking-wider">
                    {{ $article->formatted_date }}
                </span>
                <span>&bull;</span>
                <span class="inline-flex items-center gap-1.5 bg-white/5 border border-white/10 px-2.5 py-1 rounded">
                    <svg class="w-3.5 h-3.5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ $article->estimated_reading_time }} мин чтения</span>
                </span>
                <span>&bull;</span>
                <span class="inline-flex items-center gap-1.5 bg-white/5 border border-white/10 px-2.5 py-1 rounded">
                    <svg class="w-3.5 h-3.5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <span>{{ $article->views_count }} {{ trans_choice('просмотр|просмотра|просмотров', $article->views_count, [], 'ru') }}</span>
                </span>
                <span>&bull;</span>
                <a href="#comments" class="inline-flex items-center gap-1.5 bg-white/5 border border-white/10 hover:border-neutral-500 hover:text-white px-2.5 py-1 rounded transition-colors">
                    <svg class="w-3.5 h-3.5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                    </svg>
                    <span>{{ $article->comments->count() }} {{ trans_choice('комментарий|комментария|комментариев', $article->comments->count(), [], 'ru') }}</span>
                </a>
            </div>

            {{-- Title H1: Balanced, not oversized --}}
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight font-display text-white leading-snug">
                {{ $article->title }}
            </h1>

            {{-- Excerpt / Lead paragraph --}}
            @if($article->excerpt)
                <p class="text-sm sm:text-base text-neutral-300 font-sans leading-relaxed border-l-2 border-crimson pl-4 py-1 italic">
                    {{ $article->excerpt }}
                </p>
            @endif
        </header>


        {{-- Article Main Content --}}
        <div class="article-rich-content prose prose-invert prose-neutral max-w-none 
                    prose-headings:font-display prose-headings:tracking-tight prose-headings:text-white
                    prose-h2:text-xl sm:prose-h2:text-2xl prose-h2:mt-8 prose-h2:mb-4 prose-h2:border-b prose-h2:border-cine-border prose-h2:pb-3
                    prose-h3:text-lg sm:prose-h3:text-xl prose-h3:mt-6 prose-h3:mb-2.5 prose-h3:text-neutral-200
                    prose-p:text-neutral-300 prose-p:text-base sm:prose-p:text-lg prose-p:leading-relaxed prose-p:mb-5
                    prose-li:text-neutral-300 prose-li:text-base sm:prose-li:text-lg
                    prose-strong:text-white prose-strong:font-bold
                    prose-blockquote:border-l-2 prose-blockquote:border-crimson prose-blockquote:bg-white/[0.02] prose-blockquote:py-3 prose-blockquote:px-5 prose-blockquote:rounded-r-lg prose-blockquote:text-neutral-300 prose-blockquote:italic
                    prose-img:rounded-xl prose-img:border prose-img:border-cine-border prose-img:shadow-md prose-img:my-6 prose-img:max-h-[600px] prose-img:w-full prose-img:object-cover">
            {!! $article->content !!}
        </div>

        {{-- Additional Photos Gallery (if any) --}}
        @if($article->images && $article->images->count() > 0)
            <div class="space-y-4 pt-6 border-t border-cine-border">
                <h3 class="text-xs font-mono uppercase tracking-widest text-neutral-400 font-bold">
                    Фотографии к статье
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($article->images as $photo)
                        <div class="group relative aspect-[4/3] rounded-lg overflow-hidden bg-neutral-900 border border-cine-border">
                            <picture>
                                <source type="image/avif" 
                                        srcset="{{ $photo->getSrcsetAttribute('avif') }}" 
                                        sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 350px">
                                <source type="image/webp" 
                                        srcset="{{ $photo->getSrcsetAttribute('webp') }}" 
                                        sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 350px">
                                <img src="{{ $photo->medium_url }}" 
                                     srcset="{{ $photo->getSrcsetAttribute() }}"
                                     sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 350px"
                                     alt="{{ $photo->caption ?: $article->title }}" 
                                     loading="lazy" 
                                     decoding="async"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            </picture>
                            @if($photo->caption)
                                <div class="absolute bottom-0 inset-x-0 bg-black/80 backdrop-blur-sm p-2 text-[0.7rem] font-mono text-neutral-300 truncate">
                                    {{ $photo->caption }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Social Sharing Bar --}}
        <div class="p-6 rounded-2xl bg-cine-surface border border-cine-border shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-mono uppercase tracking-widest text-crimson font-bold block">
                        ПОДЕЛИТЬСЯ СТАТЬЕЙ
                    </span>
                    <p class="text-xs text-neutral-400 font-mono">
                        Отправьте материал друзьям или сохраните себе в мессенджер
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2.5">
                    {{-- Telegram --}}
                    @php
                        $shareUrl = urlencode(url()->current());
                        $shareTitle = urlencode($article->title . ' — Роман Юн');
                    @endphp
                    <a href="https://t.me/share/url?url={{ $shareUrl }}&text={{ $shareTitle }}" 
                       target="_blank" 
                       rel="noopener noreferrer" 
                       class="px-3.5 py-2 rounded-lg bg-[#2AABEE]/15 hover:bg-[#2AABEE]/25 text-[#2AABEE] border border-[#2AABEE]/30 text-xs font-mono font-bold uppercase tracking-wider inline-flex items-center gap-2 transition-colors"
                       title="Поделиться в Telegram">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                            <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221l-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.14.18-.357.295-.6.295-.002 0-.003 0-.005 0l.213-3.054 5.56-5.022c.24-.213-.054-.334-.373-.121l-6.869 4.326-2.96-.924c-.643-.204-.657-.643.136-.953l11.57-4.458c.538-.196 1.006.128.832.937z"/>
                        </svg>
                        <span>Telegram</span>
                    </a>

                    {{-- VKontakte --}}
                    <a href="https://vk.com/share.php?url={{ $shareUrl }}&title={{ $shareTitle }}" 
                       target="_blank" 
                       rel="noopener noreferrer" 
                       class="px-3.5 py-2 rounded-lg bg-[#0077FF]/15 hover:bg-[#0077FF]/25 text-[#0077FF] border border-[#0077FF]/30 text-xs font-mono font-bold uppercase tracking-wider inline-flex items-center gap-2 transition-colors"
                       title="Поделиться во ВКонтакте">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                            <path d="M15.07 2H8.93C4.55 2 2 4.55 2 8.93v6.14C2 19.45 4.55 22 8.93 22h6.14c4.38 0 6.93-2.55 6.93-6.93V8.93C22 4.55 19.45 2 15.07 2zm3.38 13.91h-1.45c-.55 0-.72-.44-1.7-1.43-.86-.84-1.25-.95-1.46-.95-.3 0-.39.09-.39.51v1.37c0 .35-.11.56-1.03.56-1.52 0-3.21-.92-4.4-2.64-1.79-2.52-2.28-4.42-2.28-4.81 0-.21.08-.4.49-.4h1.45c.37 0 .5.17.64.56.71 2.06 1.9 3.86 2.39 3.86.19 0 .27-.09.27-.56V10.4c-.06-1-.59-1.09-.59-1.44 0-.17.14-.34.37-.34h2.28c.31 0 .42.16.42.53v2.85c0 .31.14.42.23.42.19 0 .34-.11.68-.45 1.06-1.19 1.82-3.03 1.82-3.03.1-.22.28-.4.64-.4h1.45c.44 0 .53.22.43.53-.18.83-1.92 3.29-2.01 3.43-.2.31-.28.45 0 .83.2.28.88.86 1.33 1.39.83.96 1.47 1.77 1.64 2.33.17.55-.1.82-.66.82z"/>
                        </svg>
                        <span>ВКонтакте</span>
                    </a>

                    {{-- Copy Link Button --}}
                    <button type="button" 
                            @click="copyCurrentUrl()"
                            class="px-3.5 py-2 rounded-lg border border-white/15 bg-white/5 hover:bg-white/10 text-white text-xs font-mono font-bold uppercase tracking-wider inline-flex items-center gap-2 transition-colors cursor-pointer">
                        <svg x-show="!copied" class="w-4 h-4 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <svg x-show="copied" x-cloak class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span x-text="copied ? 'Ссылка скопирована!' : 'Скопировать ссылку'"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Author Signature Card --}}
        <div class="p-6 rounded-2xl bg-cine-surface border border-cine-border flex items-center justify-between gap-6 flex-col sm:flex-row">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-crimson flex items-center justify-center text-white font-extrabold text-sm tracking-wider font-display shrink-0">
                    RY
                </div>
                <div>
                    <div class="text-white font-bold font-display text-base">Роман Юн</div>
                    <div class="text-xs text-neutral-400 font-mono">Фотограф в Иркутске &bull; Автор статей</div>
                </div>
            </div>
            <div>
                <a href="{{ \App\Models\Setting::hasTelegram() ? \App\Models\Setting::telegramUrl('Здравствуйте, Роман! Прочитал статью «' . $article->title . '», хочу обсудить съёмку.') : route('contacts.index') }}" 
                   @if(\App\Models\Setting::hasTelegram()) target="_blank" rel="noopener" @endif
                   class="btn-crimson px-5 py-2.5 text-xs font-mono font-bold tracking-wider uppercase inline-flex items-center gap-2">
                    <span>Обсудить съёмку</span>
                    <span>&nearr;</span>
                </a>
            </div>
        </div>

        {{-- Comments Section --}}
        <section id="comments" class="pt-8 border-t border-cine-border space-y-8 scroll-mt-24">
            
            <div class="flex items-baseline justify-between">
                <div class="space-y-1">
                    <span class="text-xs font-mono uppercase tracking-widest text-crimson font-bold block">
                        ОБСУЖДЕНИЕ
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold uppercase tracking-tight font-display text-white">
                        Комментарии ({{ $article->comments->count() }})
                    </h2>
                </div>
                <span class="text-xs font-mono text-neutral-500">
                    Открытое обсуждение
                </span>
            </div>

            {{-- Flash success message --}}
            @if(session('comment_success'))
                <div class="p-4 rounded-xl bg-emerald-950/60 border border-emerald-500/40 text-emerald-200 text-sm flex items-center gap-3">
                    <div class="w-6 h-6 rounded-full bg-emerald-500/20 flex items-center justify-center shrink-0 text-emerald-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span>{{ session('comment_success') }}</span>
                </div>
            @endif

            {{-- Validation Errors --}}
            @if($errors->any())
                <div class="p-4 rounded-xl bg-rose-950/60 border border-rose-500/40 text-rose-200 text-sm space-y-1">
                    <div class="font-bold">Пожалуйста, проверьте данные:</div>
                    <ul class="list-disc list-inside text-xs space-y-0.5 text-rose-300">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- New Comment Form --}}
            <div class="bg-cine-surface border border-cine-border rounded-2xl p-6 sm:p-8 shadow-sm">
                <h3 class="text-base font-bold font-display uppercase tracking-wider text-white mb-4">
                    Оставить комментарий
                </h3>
                <form action="{{ route('articles.comments.store', $article) }}" method="POST" class="space-y-4">
                    @csrf
                    
                    {{-- Honeypot field (hidden from real users, bots will fill it) --}}
                    <div style="display:none !important;" aria-hidden="true">
                        <label for="website_url">Do not fill this</label>
                        <input type="text" name="website_url" id="website_url" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="author_name" class="block text-xs font-mono uppercase tracking-wider text-neutral-400 mb-1.5 font-bold">
                                Ваше имя <span class="text-crimson">*</span>
                            </label>
                            <input type="text" 
                                   id="author_name" 
                                   name="author_name" 
                                   value="{{ old('author_name') }}" 
                                   required 
                                   placeholder="Например, Анна" 
                                   class="w-full bg-[#111115] border border-cine-border focus:border-crimson rounded-lg px-4 py-2.5 text-white text-sm placeholder:text-neutral-600 focus:outline-none transition-colors">
                        </div>

                        <div>
                            <label for="author_email" class="block text-xs font-mono uppercase tracking-wider text-neutral-400 mb-1.5">
                                Email <span class="text-[0.65rem] text-neutral-500 font-normal lowercase">(не публикуется на сайте)</span>
                            </label>
                            <input type="email" 
                                   id="author_email" 
                                   name="author_email" 
                                   value="{{ old('author_email') }}" 
                                   placeholder="anna@example.com" 
                                   class="w-full bg-[#111115] border border-cine-border focus:border-crimson rounded-lg px-4 py-2.5 text-white text-sm placeholder:text-neutral-600 focus:outline-none transition-colors">
                        </div>
                    </div>

                    <div>
                        <label for="comment_content" class="block text-xs font-mono uppercase tracking-wider text-neutral-400 mb-1.5 font-bold">
                            Текст комментария <span class="text-crimson">*</span>
                        </label>
                        <textarea id="comment_content" 
                                  name="content" 
                                  rows="4" 
                                  required 
                                  placeholder="Напишите ваш отзыв, вопрос или впечатление от статьи..." 
                                  class="w-full bg-[#111115] border border-cine-border focus:border-crimson rounded-lg p-4 text-white text-sm placeholder:text-neutral-600 focus:outline-none transition-colors resize-y leading-relaxed">{{ old('content') }}</textarea>
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <span class="text-[0.7rem] font-mono text-neutral-500">
                            Комментарии модерируются
                        </span>
                        <button type="submit" class="btn-crimson px-6 py-3 text-xs font-mono font-bold uppercase tracking-wider inline-flex items-center gap-2">
                            <span>Отправить комментарий</span>
                            <span>&rarr;</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Comments List --}}
            <div class="space-y-4">
                @forelse($article->comments as $comment)
                    <div class="p-6 rounded-2xl bg-cine-surface border border-cine-border space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-white/10 border border-white/15 flex items-center justify-center text-xs font-mono font-bold text-white shrink-0">
                                    {{ $comment->initials }}
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-white font-sans">
                                        {{ $comment->author_name }}
                                    </div>
                                    <div class="text-[0.7rem] font-mono text-neutral-500">
                                        {{ $comment->formatted_date }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-sm text-neutral-300 leading-relaxed font-sans pl-12 whitespace-pre-line">
                            {{ $comment->content }}
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center bg-cine-surface/50 border border-dashed border-cine-border rounded-2xl text-xs font-mono text-neutral-500">
                        Пока нет ни одного комментария. Станьте первым, кто выскажет своё мнение!
                    </div>
                @endforelse
            </div>

        </section>

        {{-- Related Articles Section --}}
        @if(isset($relatedArticles) && $relatedArticles->count() > 0)
            <section class="pt-12 border-t border-cine-border space-y-6">
                <div class="flex items-baseline justify-between">
                    <h3 class="text-xl sm:text-2xl font-extrabold uppercase tracking-tight font-display text-white">
                        Читайте также
                    </h3>
                    <a href="{{ route('articles.index') }}" class="text-xs font-mono text-crimson font-bold uppercase hover:underline">
                        Все статьи &rarr;
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    @foreach($relatedArticles as $rel)
                        <a href="{{ route('articles.show', $rel->slug) }}" class="group bg-cine-surface border border-cine-border rounded-xl overflow-hidden shadow-sm hover:border-neutral-500 transition-all flex flex-col justify-between">
                            <div class="aspect-[16/10] overflow-hidden bg-neutral-900">
                                <img src="{{ $rel->cover_url }}" alt="{{ $rel->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            </div>
                            <div class="p-4 space-y-2 flex-1 flex flex-col justify-between">
                                <div>
                                    <span class="text-[0.68rem] font-mono text-crimson font-bold uppercase block mb-1">
                                        {{ $rel->formatted_date }}
                                    </span>
                                    <h4 class="text-sm font-bold uppercase tracking-tight text-white group-hover:text-crimson font-display line-clamp-2 transition-colors">
                                        {{ $rel->title }}
                                    </h4>
                                </div>
                                <div class="text-[0.68rem] font-mono text-neutral-400 pt-2 border-t border-cine-border flex items-center justify-between">
                                    <span>👁 {{ $rel->views_count }}</span>
                                    <span>💬 {{ $rel->comments_count }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

    </article>

    {{-- ================================================================ --}}
    {{-- FULL-SCREEN IMMERSIVE READER MODE (РЕЖИМ ДЛЯ ЧТЕНИЯ)              --}}
    {{-- ================================================================ --}}
    <div x-show="readerOpen" 
         x-cloak 
         x-transition:enter="transition ease-out duration-250"
         x-transition:enter-start="opacity-0 scale-[0.99]"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-[0.99]"
         @keydown.escape.window="closeReader()"
         ref="readerContainer"
         @scroll="updateProgress($event)"
         :class="{
            'bg-[#0e0e12] text-slate-100': theme === 'dark',
            'bg-[#f7f2e7] text-[#2c231e]': theme === 'sepia',
            'bg-[#ffffff] text-[#1c1c1f]': theme === 'light'
         }"
         class="fixed inset-0 z-50 overflow-y-auto selection:bg-crimson selection:text-white transition-colors duration-300">

        {{-- Scroll Progress Bar at the Very Top --}}
        <div class="fixed top-0 left-0 h-1 bg-crimson z-50 transition-all duration-100 ease-out" 
             :style="'width: ' + readProgress + '%'"></div>

        {{-- Sticky Reader Toolbar --}}
        <div :class="{
                'bg-[#0e0e12]/95 border-white/10 text-slate-300': theme === 'dark',
                'bg-[#f7f2e7]/95 border-[#e2d8c3] text-[#55473d]': theme === 'sepia',
                'bg-white/95 border-slate-200 text-slate-600': theme === 'light'
             }" 
             class="sticky top-0 z-40 backdrop-blur-md border-b px-4 sm:px-6 py-2.5 transition-colors">
            <div class="max-w-4xl mx-auto flex items-center justify-between gap-3">
                
                {{-- Left: Article title & progress label --}}
                <div class="min-w-0 flex items-center gap-3">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded text-[0.68rem] font-mono font-bold tracking-wider uppercase border"
                          :class="{
                            'bg-crimson/15 text-crimson border-crimson/30': theme === 'dark',
                            'bg-crimson/10 text-crimson border-crimson/20': theme !== 'dark'
                          }">
                        Режим чтения
                    </span>
                    <span class="text-xs font-mono font-semibold hidden md:inline truncate" x-text="readProgress + '% прочитано'"></span>
                </div>

                {{-- Center/Right: Reader Controls --}}
                <div class="flex items-center gap-2 sm:gap-3">
                    
                    {{-- Font Size Controls (A- / A+) --}}
                    <div class="flex items-center border rounded-lg overflow-hidden p-0.5"
                         :class="{
                            'border-white/15 bg-white/5': theme === 'dark',
                            'border-[#decfae] bg-[#eee6d3]': theme === 'sepia',
                            'border-slate-200 bg-slate-100': theme === 'light'
                         }">
                        <button type="button" 
                                @click="decreaseFont()" 
                                title="Уменьшить шрифт"
                                class="px-2.5 py-1 text-xs font-bold rounded hover:bg-black/10 transition-colors cursor-pointer select-none">
                            A-
                        </button>
                        <span class="text-[0.65rem] font-mono px-1.5 opacity-60 uppercase" x-text="fontSize"></span>
                        <button type="button" 
                                @click="increaseFont()" 
                                title="Увеличить шрифт"
                                class="px-2.5 py-1 text-xs font-bold rounded hover:bg-black/10 transition-colors cursor-pointer select-none">
                            A+
                        </button>
                    </div>

                    {{-- Font Family Toggle (Sans / Serif) --}}
                    <div class="hidden sm:flex items-center border rounded-lg overflow-hidden p-0.5 text-xs font-semibold"
                         :class="{
                            'border-white/15 bg-white/5': theme === 'dark',
                            'border-[#decfae] bg-[#eee6d3]': theme === 'sepia',
                            'border-slate-200 bg-slate-100': theme === 'light'
                         }">
                        <button type="button" 
                                @click="fontFamily = 'sans'" 
                                :class="fontFamily === 'sans' ? 'bg-crimson text-white shadow-sm' : 'opacity-70 hover:opacity-100'"
                                class="px-2.5 py-1 rounded transition-all cursor-pointer font-sans">
                            Sans
                        </button>
                        <button type="button" 
                                @click="fontFamily = 'serif'" 
                                :class="fontFamily === 'serif' ? 'bg-crimson text-white shadow-sm' : 'opacity-70 hover:opacity-100'"
                                class="px-2.5 py-1 rounded transition-all cursor-pointer font-serif">
                            Serif
                        </button>
                    </div>

                    {{-- Theme Switcher (Dark / Sepia / Light) --}}
                    <div class="flex items-center border rounded-lg overflow-hidden p-0.5 text-xs"
                         :class="{
                            'border-white/15 bg-white/5': theme === 'dark',
                            'border-[#decfae] bg-[#eee6d3]': theme === 'sepia',
                            'border-slate-200 bg-slate-100': theme === 'light'
                         }">
                        <button type="button" 
                                @click="theme = 'dark'" 
                                title="Тёмная тема"
                                :class="theme === 'dark' ? 'bg-white/20 text-white' : 'opacity-60 hover:opacity-100'"
                                class="w-7 h-7 rounded flex items-center justify-center transition-all cursor-pointer">
                            🌙
                        </button>
                        <button type="button" 
                                @click="theme = 'sepia'" 
                                title="Книжная сепия"
                                :class="theme === 'sepia' ? 'bg-[#dfcead] text-[#2c231e] font-bold shadow-sm' : 'opacity-60 hover:opacity-100'"
                                class="w-7 h-7 rounded flex items-center justify-center transition-all cursor-pointer">
                            📜
                        </button>
                        <button type="button" 
                                @click="theme = 'light'" 
                                title="Светлая бумага"
                                :class="theme === 'light' ? 'bg-white text-slate-900 font-bold shadow-sm' : 'opacity-60 hover:opacity-100'"
                                class="w-7 h-7 rounded flex items-center justify-center transition-all cursor-pointer">
                            ☀️
                        </button>
                    </div>

                    {{-- Close Button --}}
                    <button type="button" 
                            @click="closeReader()" 
                            class="px-3 py-1.5 rounded-lg border border-crimson/40 bg-crimson text-white text-xs font-mono font-bold uppercase tracking-wider hover:opacity-90 transition-all flex items-center gap-1.5 cursor-pointer ml-1">
                        <span>✕</span>
                        <span class="hidden sm:inline">Выйти</span>
                    </button>
                </div>

            </div>
        </div>

        {{-- Immersive Article Body in Reader View --}}
        <main class="max-w-2xl mx-auto px-5 sm:px-8 py-10 sm:py-16 space-y-8">
            
            {{-- Header info --}}
            <header class="space-y-4 border-b pb-6"
                    :class="{
                        'border-white/10': theme === 'dark',
                        'border-[#e4d8c0]': theme === 'sepia',
                        'border-slate-200': theme === 'light'
                    }">
                <div class="flex items-center gap-2 text-xs font-mono opacity-60">
                    <span>{{ $article->formatted_date }}</span>
                    <span>&bull;</span>
                    <span>{{ $article->estimated_reading_time }} мин чтения</span>
                    <span>&bull;</span>
                    <span>Роман Юн</span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight leading-snug"
                    :class="{
                        'font-serif': fontFamily === 'serif',
                        'font-display': fontFamily === 'sans'
                    }">
                    {{ $article->title }}
                </h1>

                @if($article->excerpt)
                    <p class="text-sm sm:text-base leading-relaxed italic opacity-80 border-l-2 border-crimson pl-3.5 py-1">
                        {{ $article->excerpt }}
                    </p>
                @endif
            </header>


            {{-- Text Body with Dynamic Font Size & Family --}}
            <div class="transition-all duration-150 reader-article-content"
                 :class="{
                    'text-base leading-relaxed': fontSize === 'sm',
                    'text-lg sm:text-xl leading-relaxed': fontSize === 'base',
                    'text-xl sm:text-2xl leading-loose': fontSize === 'lg',
                    'font-serif': fontFamily === 'serif',
                    'font-sans': fontFamily === 'sans',
                    'text-slate-200': theme === 'dark',
                    'text-[#2f2722]': theme === 'sepia',
                    'text-[#1d1d20]': theme === 'light'
                 }">
                {!! $article->content !!}
            </div>

            {{-- Gallery photos if present --}}
            @if($article->images && $article->images->count() > 0)
                <div class="space-y-4 pt-6 border-t"
                     :class="{
                        'border-white/10': theme === 'dark',
                        'border-[#e4d8c0]': theme === 'sepia',
                        'border-slate-200': theme === 'light'
                     }">
                    <div class="text-xs font-mono uppercase tracking-widest font-bold opacity-60">
                        Фотографии к материалу
                    </div>
                    <div class="space-y-4">
                        @foreach($article->images as $photo)
                            <div class="rounded-lg overflow-hidden shadow-sm">
                                <img src="{{ $photo->image_url }}" alt="{{ $photo->caption ?: $article->title }}" class="w-full h-auto object-cover">
                                @if($photo->caption)
                                    <div class="text-xs font-mono opacity-70 p-2 text-center">{{ $photo->caption }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Reader Footer Controls --}}
            <footer class="pt-8 border-t space-y-6"
                    :class="{
                        'border-white/10': theme === 'dark',
                        'border-[#e4d8c0]': theme === 'sepia',
                        'border-slate-200': theme === 'light'
                    }">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="text-xs font-mono opacity-60">
                        Вы дочитали статью до конца &bull; Спасибо за внимание!
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="button" 
                                @click="goToComments()"
                                class="px-4 py-2 rounded-lg text-xs font-mono font-bold uppercase tracking-wider border transition-colors cursor-pointer"
                                :class="{
                                    'border-white/20 bg-white/5 hover:bg-white/10 text-white': theme === 'dark',
                                    'border-[#decfae] bg-[#eee6d3] text-[#2c231e] hover:bg-[#e4d8c0]': theme === 'sepia',
                                    'border-slate-300 bg-slate-100 text-slate-800 hover:bg-slate-200': theme === 'light'
                                }">
                            К комментариям ({{ $article->comments->count() }}) &darr;
                        </button>

                        <button type="button" 
                                @click="closeReader()" 
                                class="px-4 py-2 rounded-lg bg-crimson text-white text-xs font-mono font-bold uppercase tracking-wider hover:opacity-90 transition-opacity cursor-pointer">
                            Завершить чтение ✕
                        </button>
                    </div>
                </div>
            </footer>

        </main>
    </div>

</div>

@push('scripts')
<script>
function articleReader() {
    return {
        readerOpen: false,
        theme: localStorage.getItem('photo_reader_theme') || 'dark',
        fontSize: localStorage.getItem('photo_reader_font_size') || 'base',
        fontFamily: localStorage.getItem('photo_reader_font_family') || 'sans',
        readProgress: 0,
        copied: false,

        init() {
            this.$watch('theme', val => localStorage.setItem('photo_reader_theme', val));
            this.$watch('fontSize', val => localStorage.setItem('photo_reader_font_size', val));
            this.$watch('fontFamily', val => localStorage.setItem('photo_reader_font_family', val));
        },

        openReader() {
            this.readerOpen = true;
            document.body.style.overflow = 'hidden';
            this.$nextTick(() => {
                this.updateProgress();
            });
        },

        closeReader() {
            this.readerOpen = false;
            document.body.style.overflow = '';
        },

        goToComments() {
            this.closeReader();
            setTimeout(() => {
                const el = document.getElementById('comments');
                if (el) el.scrollIntoView({ behavior: 'smooth' });
            }, 100);
        },

        updateProgress(e) {
            const container = e ? e.target : this.$refs.readerContainer;
            if (!container) return;
            const scrollTop = container.scrollTop;
            const scrollHeight = container.scrollHeight - container.clientHeight;
            this.readProgress = scrollHeight > 0 ? Math.min(100, Math.max(0, Math.round((scrollTop / scrollHeight) * 100))) : 0;
        },

        decreaseFont() {
            if (this.fontSize === 'lg') this.fontSize = 'base';
            else if (this.fontSize === 'base') this.fontSize = 'sm';
        },

        increaseFont() {
            if (this.fontSize === 'sm') this.fontSize = 'base';
            else if (this.fontSize === 'base') this.fontSize = 'lg';
        },

        copyCurrentUrl() {
            navigator.clipboard.writeText(window.location.href);
            this.copied = true;
            setTimeout(() => { this.copied = false; }, 2500);
        }
    };
}
</script>
@endpush

@push('styles')
<style>
    /* Strict site styling overrides against pasted inline styles */
    .article-rich-content,
    .article-rich-content * {
        box-sizing: border-box;
    }
    .article-rich-content {
        color: #D1D5DB; /* Tailwind neutral-300 */
        font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
    }
    .article-rich-content [style*="color: black"],
    .article-rich-content [style*="color: #000"],
    .article-rich-content [style*="color: rgb(0, 0, 0)"],
    .article-rich-content [style*="color: rgb(0,0,0)"],
    .article-rich-content [style*="color:#000"],
    .article-rich-content [style*="color:#000000"] {
        color: inherit !important;
    }
    .article-rich-content [style*="background"],
    .article-rich-content [style*="background-color"] {
        background-color: transparent !important;
    }
    .article-rich-content [style*="font-family"] {
        font-family: inherit !important;
    }
    .article-rich-content p,
    .article-rich-content span,
    .article-rich-content li {
        color: #D1D5DB;
        font-size: 1.0625rem;
        line-height: 1.8;
    }
    .article-rich-content h1,
    .article-rich-content h2,
    .article-rich-content h3,
    .article-rich-content h4 {
        font-family: 'Unbounded', 'Syne', sans-serif !important;
        color: #FFFFFF !important;
        letter-spacing: -0.02em;
    }
    .article-rich-content h2 {
        font-size: 1.5rem !important;
        line-height: 1.35;
        margin-top: 2rem !important;
        margin-bottom: 1rem !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        padding-bottom: 0.75rem;
    }
    @media (min-width: 640px) {
        .article-rich-content h2 {
            font-size: 1.75rem !important;
        }
    }
    .article-rich-content h3 {
        font-size: 1.25rem !important;
        line-height: 1.4;
        margin-top: 1.5rem !important;
        margin-bottom: 0.75rem !important;
        color: #F3F4F6 !important;
    }
    .article-rich-content ul {
        list-style-type: disc !important;
        padding-left: 1.5rem !important;
        margin: 1.25rem 0 !important;
    }
    .article-rich-content ol {
        list-style-type: decimal !important;
        padding-left: 1.5rem !important;
        margin: 1.25rem 0 !important;
    }
    .article-rich-content li {
        margin-bottom: 0.5rem;
    }
    .article-rich-content blockquote {
        border-left: 3px solid #E51920 !important;
        background: rgba(255, 255, 255, 0.03) !important;
        padding: 1rem 1.25rem !important;
        margin: 1.75rem 0 !important;
        border-radius: 0 0.75rem 0.75rem 0;
        color: #E5E7EB !important;
        font-style: italic;
    }
    .article-rich-content blockquote p {
        color: inherit !important;
        margin-bottom: 0 !important;
    }
    .article-rich-content strong,
    .article-rich-content b {
        color: #FFFFFF !important;
        font-weight: 700;
    }
    .article-rich-content a {
        color: #E51920 !important;
        text-decoration: underline;
        text-underline-offset: 4px;
        transition: opacity 0.2s;
    }
    .article-rich-content a:hover {
        opacity: 0.8;
    }
    .article-rich-content img {
        border-radius: 0.75rem !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5) !important;
        margin: 1.75rem 0 !important;
        max-width: 100%;
        height: auto;
    }

    /* Reader Mode Overrides */
    .reader-article-content [style*="color: black"],
    .reader-article-content [style*="color: #000"],
    .reader-article-content [style*="color: rgb(0, 0, 0)"],
    .reader-article-content [style*="color: rgb(0,0,0)"] {
        color: inherit !important;
    }
    .reader-article-content [style*="background"],
    .reader-article-content [style*="background-color"] {
        background-color: transparent !important;
    }
    .reader-article-content [style*="font-family"] {
        font-family: inherit !important;
    }
    .reader-article-content p {
        margin-bottom: 1.25em;
    }
    .reader-article-content h2 {
        font-size: 1.35em;
        font-weight: 700;
        margin-top: 1.75em;
        margin-bottom: 0.75em;
        line-height: 1.3;
    }
    .reader-article-content h3 {
        font-size: 1.15em;
        font-weight: 600;
        margin-top: 1.4em;
        margin-bottom: 0.6em;
        line-height: 1.35;
    }
    .reader-article-content ul {
        list-style-type: disc;
        padding-left: 1.5em;
        margin-bottom: 1.25em;
    }
    .reader-article-content ol {
        list-style-type: decimal;
        padding-left: 1.5em;
        margin-bottom: 1.25em;
    }
    .reader-article-content li {
        margin-bottom: 0.4em;
    }
    .reader-article-content blockquote {
        border-left: 3px solid #E51920;
        padding-left: 1rem;
        margin: 1.5rem 0;
        font-style: italic;
        opacity: 0.9;
    }
    .reader-article-content img {
        max-width: 100%;
        height: auto;
        border-radius: 0.75rem;
        margin: 1.5rem 0;
        display: block;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush
@endsection
