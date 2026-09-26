@foreach($seriesList as $series)
    @php
        $orderNumber = ($seriesList->currentPage() - 1) * $seriesList->perPage() + $loop->iteration;
    @endphp
    <article class="bg-white border border-arch-border shadow-card-depth overflow-hidden group transition-all duration-300 hover:border-black/30 flex flex-col justify-between">
        <a href="{{ route('series.show', ['slug' => $series->slug]) }}" class="block">
            
            {{-- Cover Image in 3:4 aspect ratio --}}
            <div class="overflow-hidden aspect-[3/4] relative bg-neutral-900">
                <picture>
                    <source type="image/avif" 
                            srcset="{{ $series->getCoverSrcsetAttribute('avif') }}" 
                            sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 400px">
                    <source type="image/webp" 
                            srcset="{{ $series->getCoverSrcsetAttribute('webp') }}" 
                            sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 400px">
                    <img src="{{ $series->medium_cover_url }}" 
                         srcset="{{ $series->getCoverSrcsetAttribute() }}"
                         sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 400px"
                         alt="{{ $series->localizedTitle($locale) }}" 
                         loading="lazy"
                         decoding="async"
                         class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105">
                </picture>
                
                <div class="absolute top-3.5 left-3.5 bg-black/80 text-white text-[0.65rem] uppercase tracking-wider font-mono font-bold px-2.5 py-1 backdrop-blur-sm">
                    {{ sprintf('%02d', $orderNumber) }} &bull; {{ $series->category ? $series->category->localizedName($locale) : 'СЕРИЯ' }}
                </div>

                @if($series->is_demo)
                <div class="absolute top-3.5 right-3.5 bg-crimson text-white text-[0.6rem] uppercase tracking-widest px-2 py-0.5 font-bold">
                    ДЕМО
                </div>
                @endif
            </div>

            {{-- Card Body --}}
            <div class="p-5 sm:p-6 space-y-2.5">
                <div class="flex items-center justify-between text-[0.7rem] text-neutral-500 font-mono">
                    <span class="truncate pr-2">{{ $series->localizedLocation($locale) }}</span>
                    <span class="shrink-0">{{ $series->photos->count() }} кадров</span>
                </div>

                <h2 class="text-xl sm:text-2xl font-extrabold uppercase tracking-tight font-display text-arch-text group-hover:text-crimson transition-colors leading-tight line-clamp-2">
                    {{ $series->localizedTitle($locale) }}
                </h2>

                @if($series->localizedDescription($locale))
                <p class="text-xs text-neutral-600 line-clamp-2 pt-0.5 font-mono leading-relaxed">
                    {{ $series->localizedDescription($locale) }}
                </p>
                @endif

                <div class="pt-2">
                    <span class="btn-crimson px-4 py-2 text-[0.68rem] font-bold">
                        {{ __('site.view_series') }} &nearr;
                    </span>
                </div>
            </div>

        </a>
    </article>
@endforeach
