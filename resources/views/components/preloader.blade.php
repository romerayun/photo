{{-- Kinetic Typographic Preloader with Focus Arrow & Exact Reference Blur/Alignment --}}
<div id="site-preloader"
     class="fixed inset-0 z-[99999] flex items-center justify-center bg-black select-none pointer-events-auto transition-opacity duration-600 ease-out"
     aria-hidden="false"
     role="progressbar"
     aria-label="Загрузка сайта">

    {{-- Clean deep black background exactly like in reference video --}}
    <div class="relative w-full max-w-2xl px-4 flex flex-col items-center justify-center">

        {{-- Center Row Stage: Arrow + Kinetic Drum --}}
        <div class="relative flex items-center justify-center w-full">
            
            {{-- Thick Clean Reference Arrow: Locked perfectly to middle active baseline --}}
            <div class="mr-4 sm:mr-6 flex items-center justify-center shrink-0">
                <svg class="w-10 h-10 sm:w-14 sm:h-14 md:w-16 md:h-16 text-white" 
                     viewBox="0 0 24 24" 
                     fill="none" 
                     stroke="currentColor" 
                     stroke-width="3.2" 
                     stroke-linecap="round" 
                     stroke-linejoin="round">
                    <line x1="3" y1="12" x2="20" y2="12"></line>
                    <polyline points="13 5 21 12 13 19"></polyline>
                </svg>
            </div>

            {{-- Slot Viewport: Fixed window showing 5 lines (-2, -1, 0, +1, +2) --}}
            <div class="slot-viewport relative h-[240px] sm:h-[300px] md:h-[340px] w-[260px] sm:w-[380px] md:w-[460px] overflow-hidden">
                <div id="slot-track" class="slot-track">
                    @php
                        $words = [
                            'фокус',
                            'эстетика',
                            'стиль',
                            'атмосфера',
                            'энергия',
                            'эмоции',
                            'кадр',
                            'история',
                            'искусство',
                            'свет',
                            'результат',
                            'качество'
                        ];
                    @endphp

                    @foreach($words as $index => $word)
                        <div class="slot-item" data-index="{{ $index }}">
                            <span class="slot-text">{{ $word }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>

<style>
/* Exact recreation of reference typography and rolling blur reel */
#site-preloader {
    background-color: #000000;
}

.slot-viewport {
    position: relative;
    /* Soft edge fade like in video */
    mask-image: linear-gradient(to bottom, transparent 0%, black 22%, black 78%, transparent 100%);
    -webkit-mask-image: linear-gradient(to bottom, transparent 0%, black 22%, black 78%, transparent 100%);
}

.slot-track {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    display: flex;
    flex-direction: column;
    will-change: transform;
    transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1);
}

.slot-item {
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    padding-left: 0.25rem;
    box-sizing: border-box;
    will-change: transform, opacity, filter;
    transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1),
                opacity 0.28s cubic-bezier(0.16, 1, 0.3, 1),
                filter 0.28s cubic-bezier(0.16, 1, 0.3, 1),
                color 0.28s ease;
}

@media (min-width: 640px) {
    .slot-item {
        height: 60px;
    }
}

@media (min-width: 768px) {
    .slot-item {
        height: 68px;
    }
}

.slot-text {
    font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    font-size: 2.25rem;
    font-weight: 800;
    line-height: 1;
    letter-spacing: -0.04em;
    color: #ffffff;
    white-space: nowrap;
}

@media (min-width: 640px) {
    .slot-text {
        font-size: 3.25rem;
    }
}

@media (min-width: 768px) {
    .slot-text {
        font-size: 3.85rem;
    }
}

/* Reference blur & scale stages relative to active center item */
.slot-item.item-active {
    opacity: 1;
    filter: blur(0px);
    transform: scale(1);
}

.slot-item.item-near {
    opacity: 0.32;
    filter: blur(3.5px);
    transform: scale(0.96);
}

.slot-item.item-far {
    opacity: 0.12;
    filter: blur(7px);
    transform: scale(0.92);
}

.slot-item.item-hidden {
    opacity: 0;
    filter: blur(12px);
    transform: scale(0.88);
}

#site-preloader.preloader-fade-out {
    opacity: 0;
    pointer-events: none !important;
}
</style>

<script>
(function() {
    var preloader = document.getElementById('site-preloader');
    if (!preloader) return;

    var track = document.getElementById('slot-track');
    var items = Array.from(document.querySelectorAll('.slot-item'));
    var viewport = document.querySelector('.slot-viewport');
    if (!items.length || !track || !viewport) return;

    var totalItems = items.length;
    var currentIndex = 0;
    var isDone = false;

    function getItemHeight() {
        return items[0].getBoundingClientRect().height || (window.innerWidth < 640 ? 48 : (window.innerWidth < 768 ? 60 : 68));
    }

    function getViewportHeight() {
        return viewport.getBoundingClientRect().height || (window.innerWidth < 640 ? 240 : (window.innerWidth < 768 ? 300 : 340));
    }

    function setIndex(index) {
        var itemH = getItemHeight();
        var viewH = getViewportHeight();
        // Exact center alignment: active item is centered vertically in viewport exactly opposite the arrow
        var offset = (viewH / 2) - (index * itemH) - (itemH / 2);
        track.style.transform = 'translate3d(0, ' + offset + 'px, 0)';

        items.forEach(function(item, idx) {
            var diff = Math.abs(idx - index);
            item.classList.remove('item-active', 'item-near', 'item-far', 'item-hidden');
            if (diff === 0) {
                item.classList.add('item-active');
            } else if (diff === 1) {
                item.classList.add('item-near');
            } else if (diff === 2) {
                item.classList.add('item-far');
            } else {
                item.classList.add('item-hidden');
            }
        });
    }

    // Set initial position
    document.body.style.overflow = 'hidden';
    setIndex(0);

    // Roll through words fast and punchy like jitter video (every 220ms)
    var rollTimer = setInterval(function() {
        currentIndex = (currentIndex + 1) % totalItems;
        setIndex(currentIndex);
    }, 220);

    function closePreloader() {
        if (isDone) return;
        isDone = true;

        setTimeout(function() {
            clearInterval(rollTimer);
            // Snap to prominent word (e.g. 'эстетика' or 'фокус')
            setIndex(1); // 'эстетика'

            setTimeout(function() {
                preloader.classList.add('preloader-fade-out');
                document.body.style.overflow = '';
                setTimeout(function() {
                    if (preloader.parentNode) preloader.parentNode.removeChild(preloader);
                }, 650);
            }, 350);
        }, 1200);
    }

    if (document.readyState === 'complete') {
        closePreloader();
    } else {
        window.addEventListener('load', closePreloader);
        setTimeout(closePreloader, 2800);
    }

    window.addEventListener('resize', function() {
        setIndex(currentIndex);
    });
})();
</script>
