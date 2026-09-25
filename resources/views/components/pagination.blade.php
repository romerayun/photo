@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Пагинация" class="flex items-center justify-center space-x-2 text-xs font-mono font-bold select-none pt-8">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="px-4 py-2.5 rounded-lg border border-white/5 bg-white/[0.02] text-neutral-600 cursor-not-allowed opacity-50 flex items-center gap-1.5">
                <span>&larr;</span>
                <span class="hidden sm:inline">Назад</span>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="px-4 py-2.5 rounded-lg border border-cine-border bg-cine-surface text-neutral-300 hover:text-white hover:border-crimson hover:bg-crimson/10 transition-all flex items-center gap-1.5">
                <span>&larr;</span>
                <span class="hidden sm:inline">Назад</span>
            </a>
        @endif

        {{-- Pagination Elements --}}
        <div class="flex items-center space-x-1.5">
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="px-3 py-2.5 text-neutral-600 font-mono">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="w-10 h-10 rounded-lg bg-crimson text-white flex items-center justify-center shadow-crimson-btn font-extrabold">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="w-10 h-10 rounded-lg border border-cine-border bg-cine-surface text-neutral-400 hover:text-white hover:border-neutral-500 hover:bg-white/5 transition-all flex items-center justify-center">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="px-4 py-2.5 rounded-lg border border-cine-border bg-cine-surface text-neutral-300 hover:text-white hover:border-crimson hover:bg-crimson/10 transition-all flex items-center gap-1.5">
                <span class="hidden sm:inline">Вперёд</span>
                <span>&rarr;</span>
            </a>
        @else
            <span class="px-4 py-2.5 rounded-lg border border-white/5 bg-white/[0.02] text-neutral-600 cursor-not-allowed opacity-50 flex items-center gap-1.5">
                <span class="hidden sm:inline">Вперёд</span>
                <span>&rarr;</span>
            </span>
        @endif
    </nav>
@endif
