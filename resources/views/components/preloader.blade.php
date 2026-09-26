{{-- Kinetic Typographic Preloader with Focus Arrow & Exact Reference Blur/Alignment --}}
<div id="site-preloader"
     style="position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; width: 100vw !important; height: 100vh !important; z-index: 2147483647 !important; background-color: #000000 !important; display: flex !important; align-items: center !important; justify-content: center !important; overflow: hidden !important; user-select: none !important; -webkit-user-select: none !important; opacity: 1; transition: opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1);"
     aria-hidden="false"
     role="progressbar"
     aria-label="Загрузка сайта">

    {{-- Clean deep black stage exactly like in reference video --}}
    <div style="position: relative; display: flex; align-items: center; justify-content: center; width: 100%; max-width: 700px; padding: 0 16px;">

        {{-- Center Row Stage: Arrow + Kinetic Drum --}}
        <div style="position: relative; display: flex; align-items: center; justify-content: center; width: 100%;">
            
            {{-- Thick Clean Reference Arrow: Locked strictly to middle active line --}}
            <div style="margin-right: 20px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg class="preloader-arrow-svg"
                     viewBox="0 0 24 24" 
                     fill="none" 
                     stroke="#FFFFFF" 
                     stroke-width="3.5" 
                     stroke-linecap="round" 
                     stroke-linejoin="round">
                    <line x1="3" y1="12" x2="20" y2="12"></line>
                    <polyline points="13 5 21 12 13 19"></polyline>
                </svg>
            </div>

            {{-- Slot Viewport: Fixed window showing 5 lines --}}
            <div class="slot-viewport">
                <div id="slot-track" class="slot-track">
                    @php
                        $words = [
                            'фокус',
                            'стиль',
                            'атмосфера',
                            'энергия',
                            'эмоции',
                            'кадр',
                            'история',
                            'искусство',
                            'свет',
                            'качество',
                            'эстетика'
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
/* Exact recreation of reference typography, viewport, and rolling blur layers */
.preloader-arrow-svg {
    width: 38px;
    height: 38px;
}

@media (min-width: 640px) {
    .preloader-arrow-svg {
        width: 52px;
        height: 52px;
    }
}

@media (min-width: 768px) {
    .preloader-arrow-svg {
        width: 62px;
        height: 62px;
    }
}

.slot-viewport {
    position: relative;
    height: 250px;
    width: 250px;
    overflow: hidden;
    mask-image: linear-gradient(to bottom, transparent 0%, black 25%, black 75%, transparent 100%);
    -webkit-mask-image: linear-gradient(to bottom, transparent 0%, black 25%, black 75%, transparent 100%);
}

@media (min-width: 640px) {
    .slot-viewport {
        height: 310px;
        width: 360px;
    }
}

@media (min-width: 768px) {
    .slot-viewport {
        height: 350px;
        width: 440px;
    }
}

.slot-track {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    display: flex;
    flex-direction: column;
    will-change: transform;
    transition: transform 0.22s cubic-bezier(0.16, 1, 0.3, 1);
}

.slot-item {
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    padding-left: 6px;
    box-sizing: border-box;
    will-change: transform, opacity, filter;
    transition: transform 0.22s cubic-bezier(0.16, 1, 0.3, 1),
                opacity 0.22s cubic-bezier(0.16, 1, 0.3, 1),
                filter 0.22s cubic-bezier(0.16, 1, 0.3, 1);
}

@media (min-width: 640px) {
    .slot-item {
        height: 62px;
        padding-left: 8px;
    }
}

@media (min-width: 768px) {
    .slot-item {
        height: 70px;
        padding-left: 10px;
    }
}

.slot-text {
    font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    font-size: 2.2rem;
    font-weight: 800;
    line-height: 1;
    letter-spacing: -0.04em;
    color: #ffffff;
    white-space: nowrap;
}

@media (min-width: 640px) {
    .slot-text {
        font-size: 3.1rem;
    }
}

@media (min-width: 768px) {
    .slot-text {
        font-size: 3.8rem;
    }
}

/* Reference blur & scale stages relative to active center item */
.slot-item.item-active {
    opacity: 1 !important;
    filter: blur(0px) !important;
    transform: scale(1) translateZ(0) !important;
}

.slot-item.item-near {
    opacity: 0.35 !important;
    filter: blur(3.5px) !important;
    transform: scale(0.96) translateZ(0) !important;
}

.slot-item.item-far {
    opacity: 0.12 !important;
    filter: blur(7px) !important;
    transform: scale(0.91) translateZ(0) !important;
}

.slot-item.item-hidden {
    opacity: 0 !important;
    filter: blur(12px) !important;
    transform: scale(0.85) translateZ(0) !important;
}

#site-preloader.preloader-fade-out {
    opacity: 0 !important;
    pointer-events: none !important;
}
</style>

<script>
(function() {
    var preloader = document.getElementById('site-preloader');
    if (!preloader) return;

    // Safety: ensure it is always top-level in body
    if (preloader.parentNode !== document.body) {
        document.body.prepend(preloader);
    }

    var track = document.getElementById('slot-track');
    var items = Array.from(document.querySelectorAll('.slot-item'));
    var viewport = document.querySelector('.slot-viewport');
    if (!items.length || !track || !viewport) return;

    var totalItems = items.length;
    var currentIndex = 0;
    var isDone = false;

    function getItemHeight() {
        var rect = items[0].getBoundingClientRect();
        return rect.height || (window.innerWidth < 640 ? 50 : (window.innerWidth < 768 ? 62 : 70));
    }

    function getViewportHeight() {
        var rect = viewport.getBoundingClientRect();
        return rect.height || (window.innerWidth < 640 ? 250 : (window.innerWidth < 768 ? 310 : 350));
    }

    function setIndex(index) {
        var itemH = getItemHeight();
        var viewH = getViewportHeight();
        // Exact center alignment: active item is centered vertically in viewport exactly opposite the arrow
        var offset = (viewH / 2) - (index * itemH) - (itemH / 2);
        track.style.transform = 'translate3d(0, ' + Math.round(offset) + 'px, 0)';

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

    // Lock page scrolling while preloader runs
    document.documentElement.style.overflow = 'hidden';
    document.body.style.overflow = 'hidden';

    // Set initial active state
    setIndex(0);

    // Fast snappy slot roll every 200ms
    var rollTimer = setInterval(function() {
        currentIndex = (currentIndex + 1) % totalItems;
        setIndex(currentIndex);
    }, 200);

    function closePreloader() {
        if (isDone) return;
        isDone = true;

        setTimeout(function() {
            clearInterval(rollTimer);
            // Snap to climax word ('эстетика' - last word)
            setIndex(totalItems - 1);

            setTimeout(function() {
                preloader.classList.add('preloader-fade-out');
                document.documentElement.style.overflow = '';
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
        setTimeout(closePreloader, 2600);
    }

    window.addEventListener('resize', function() {
        setIndex(currentIndex);
    });
})();
</script>
