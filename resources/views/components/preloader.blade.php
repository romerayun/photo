{{-- Kinetic Typographic Preloader with Focus Arrow & 3D Depth --}}
<div id="site-preloader"
     class="fixed inset-0 z-[99999] flex items-center justify-center bg-[#070709] select-none pointer-events-auto transition-all duration-700 ease-out"
     aria-hidden="false"
     role="progressbar"
     aria-label="Загрузка сайта">

    {{-- Subtle Ambient Vignette & Cinema Grain Background --}}
    <div class="absolute inset-0 pointer-events-none bg-[radial-gradient(circle_at_center,rgba(229,25,32,0.06)_0%,rgba(7,7,9,0.92)_65%,#050507_100%)]"></div>

    {{-- Subtly animated corner marks for high-end cinematic/viewfinder aesthetic --}}
    <div class="absolute top-6 left-6 text-[10px] font-mono tracking-widest text-neutral-500 uppercase flex items-center gap-2">
        <span class="w-1.5 h-1.5 bg-crimson rounded-full animate-ping"></span>
        <span>REC &bull; 4K 60FPS</span>
    </div>
    <div class="absolute bottom-6 right-6 text-[10px] font-mono tracking-widest text-neutral-500 uppercase hidden sm:block">
        <span>ROMAN YUN &bull; PORTFOLIO</span>
    </div>

    {{-- Main Kinetic Reel Stage --}}
    <div class="relative w-full max-w-xl mx-auto px-4 flex flex-col items-center justify-center">

        {{-- Focus Line / Dynamic Arrow and Slot Mask Container --}}
        <div class="relative w-full flex items-center justify-center py-6">

            {{-- Left Neon / Crimson Accent Arrow --}}
            <div class="kinetic-arrow-wrapper mr-3 sm:mr-5 flex items-center justify-center shrink-0">
                <svg class="w-8 h-8 sm:w-11 sm:h-11 md:w-13 md:h-13 text-white kinetic-arrow-icon"
                     viewBox="0 0 24 24" 
                     fill="none" 
                     stroke="currentColor" 
                     stroke-width="2.6" 
                     stroke-linecap="round" 
                     stroke-linejoin="round">
                    <line x1="4" y1="12" x2="19" y2="12"></line>
                    <polyline points="13 6 19 12 13 18"></polyline>
                </svg>
            </div>

            {{-- 3D Perspective Words Slot Viewport --}}
            <div class="kinetic-viewport relative h-[4.2rem] sm:h-[5.5rem] md:h-[6.2rem] w-[260px] sm:w-[360px] md:w-[420px] overflow-hidden">
                {{-- Top & Bottom Soft Feather Masks --}}
                <div class="kinetic-feather-mask-top"></div>
                <div class="kinetic-feather-mask-bottom"></div>

                {{-- Scrolling Cylindrical Track --}}
                <div id="kinetic-track" class="kinetic-cylinder-track">
                    {{-- List of words matching Roman Yun's high-end photography & production craft --}}
                    @php
                        $words = [
                            'ФОКУС',
                            'ЭСТЕТИКА',
                            'СТИЛЬ',
                            'АТМОСФЕРА',
                            'ЭНЕРГИЯ',
                            'ЭМОЦИИ',
                            'КАДР',
                            'ИСТОРИЯ',
                            'ИСКУССТВО',
                            'КАЧЕСТВО',
                            'СВЕТ',
                            'ФОКУС'
                        ];
                    @endphp

                    @foreach($words as $index => $word)
                        <div class="kinetic-word-row" data-index="{{ $index }}">
                            <span class="kinetic-word-text">{{ $word }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Minimalist Progress Bar + Counter --}}
        <div class="mt-8 sm:mt-10 w-44 sm:w-56 flex flex-col items-center gap-2.5">
            <div class="w-full h-[2px] bg-white/10 rounded-full overflow-hidden relative">
                <div id="kinetic-progress-bar"
                     class="h-full bg-gradient-to-r from-crimson to-white rounded-full transition-[width] duration-150 ease-out"
                     style="width: 0%;"></div>
            </div>
            <div class="w-full flex items-center justify-between text-[11px] font-mono tracking-widest text-neutral-400">
                <span>ЗАГРУЗКА</span>
                <span id="kinetic-progress-number" class="text-white font-bold">0%</span>
            </div>
        </div>
    </div>
</div>

<style>
/* Kinetic Preloader 3D & Blur Stack */
#site-preloader {
    perspective: 1200px;
}

.kinetic-viewport {
    perspective: 800px;
    mask-image: linear-gradient(to bottom, transparent 0%, black 20%, black 80%, transparent 100%);
    -webkit-mask-image: linear-gradient(to bottom, transparent 0%, black 20%, black 80%, transparent 100%);
}

.kinetic-feather-mask-top,
.kinetic-feather-mask-bottom {
    position: absolute;
    left: 0;
    right: 0;
    height: 35%;
    z-index: 10;
    pointer-events: none;
}

.kinetic-feather-mask-top {
    top: 0;
    background: linear-gradient(to bottom, #070709 15%, rgba(7, 7, 9, 0) 100%);
}

.kinetic-feather-mask-bottom {
    bottom: 0;
    background: linear-gradient(to top, #070709 15%, rgba(7, 7, 9, 0) 100%);
}

.kinetic-arrow-wrapper {
    position: relative;
}

.kinetic-arrow-icon {
    filter: drop-shadow(0 0 16px rgba(255, 255, 255, 0.45));
    animation: kineticArrowPulse 1.2s cubic-bezier(0.4, 0, 0.2, 1) infinite alternate;
}

@keyframes kineticArrowPulse {
    0% {
        transform: translateX(-4px) scale(0.96);
        color: #ffffff;
    }
    100% {
        transform: translateX(4px) scale(1.04);
        color: #FFFFFF;
        filter: drop-shadow(0 0 20px rgba(229, 25, 32, 0.8));
    }
}

.kinetic-cylinder-track {
    display: flex;
    flex-direction: column;
    width: 100%;
    transform: translateY(0);
    will-change: transform;
    transition: transform 0.42s cubic-bezier(0.2, 0.85, 0.32, 1.2);
}

.kinetic-word-row {
    height: 4.2rem;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    padding-left: 0.5rem;
    box-sizing: border-box;
    will-change: transform, opacity, filter;
    transition: transform 0.42s cubic-bezier(0.2, 0.85, 0.32, 1.2),
                opacity 0.42s cubic-bezier(0.2, 0.85, 0.32, 1.2),
                filter 0.42s cubic-bezier(0.2, 0.85, 0.32, 1.2);
}

@media (min-width: 640px) {
    .kinetic-word-row {
        height: 5.5rem;
    }
}

@media (min-width: 768px) {
    .kinetic-word-row {
        height: 6.2rem;
    }
}

.kinetic-word-text {
    font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, sans-serif;
    font-size: 2.2rem;
    font-weight: 800;
    line-height: 1;
    letter-spacing: -0.03em;
    text-transform: lowercase;
    display: inline-block;
}

@media (min-width: 640px) {
    .kinetic-word-text {
        font-size: 3.1rem;
    }
}

@media (min-width: 768px) {
    .kinetic-word-text {
        font-size: 3.75rem;
    }
}

/* States based on relative distance from active index */
.kinetic-word-row.is-active {
    opacity: 1 !important;
    filter: blur(0px) !important;
    transform: scale(1) translateZ(0) !important;
    color: #FFFFFF !important;
}

.kinetic-word-row.is-near {
    opacity: 0.38 !important;
    filter: blur(3.5px) !important;
    transform: scale(0.92) translateZ(-40px) !important;
    color: #D4D4D8 !important;
}

.kinetic-word-row.is-far {
    opacity: 0.12 !important;
    filter: blur(7px) !important;
    transform: scale(0.82) translateZ(-100px) !important;
    color: #71717A !important;
}

.kinetic-word-row.is-hidden {
    opacity: 0 !important;
    filter: blur(12px) !important;
    transform: scale(0.7) translateZ(-160px) !important;
}

/* Exit transition */
#site-preloader.preloader-hidden {
    opacity: 0;
    transform: scale(1.04);
    pointer-events: none !important;
}
</style>

<script>
(function() {
    // Only run preloader once per session (or refresh) so it doesn't annoy regular navigation,
    // but gives a breathtaking first impression.
    var preloader = document.getElementById('site-preloader');
    if (!preloader) return;

    var track = document.getElementById('kinetic-track');
    var rows = Array.from(document.querySelectorAll('.kinetic-word-row'));
    var progressBar = document.getElementById('kinetic-progress-bar');
    var progressNumber = document.getElementById('kinetic-progress-number');

    var totalWords = rows.length;
    var currentIndex = 0;
    var progress = 0;
    var isDone = false;

    // Get current row height dynamically
    function getRowHeight() {
        if (!rows.length) return 70;
        return rows[0].getBoundingClientRect().height || (window.innerWidth < 640 ? 67.2 : (window.innerWidth < 768 ? 88 : 99.2));
    }

    function updateWords(index) {
        var rowHeight = getRowHeight();
        if (track) {
            track.style.transform = 'translateY(' + (-index * rowHeight) + 'px)';
        }

        rows.forEach(function(row, idx) {
            var diff = Math.abs(idx - index);
            row.classList.remove('is-active', 'is-near', 'is-far', 'is-hidden');

            if (diff === 0) {
                row.classList.add('is-active');
            } else if (diff === 1) {
                row.classList.add('is-near');
            } else if (diff === 2) {
                row.classList.add('is-far');
            } else {
                row.classList.add('is-hidden');
            }
        });
    }

    // Initial state
    updateWords(0);

    // Lock body scroll while preloader is active
    document.body.style.overflow = 'hidden';

    // Cycle through words at brisk, stylish rhythm
    var wordInterval = setInterval(function() {
        if (currentIndex < totalWords - 1) {
            currentIndex++;
            updateWords(currentIndex);
        } else {
            // Loop or hold at final key word
            currentIndex = 0;
            updateWords(currentIndex);
        }
    }, 280);

    // Progress bar animation
    var progressTimer = setInterval(function() {
        if (progress < 90) {
            progress += Math.floor(Math.random() * 8) + 4;
            if (progress > 90) progress = 90;
            applyProgress(progress);
        }
    }, 100);

    function applyProgress(val) {
        if (progressBar) progressBar.style.width = val + '%';
        if (progressNumber) progressNumber.textContent = Math.round(val) + '%';
    }

    function finishPreloader() {
        if (isDone) return;
        isDone = true;

        clearInterval(progressTimer);
        applyProgress(100);

        setTimeout(function() {
            clearInterval(wordInterval);
            // Select punchy climax word (e.g. 'эстетика' or 'фокус')
            updateWords(Math.min(currentIndex, totalWords - 1));

            setTimeout(function() {
                preloader.classList.add('preloader-hidden');
                document.body.style.overflow = '';

                setTimeout(function() {
                    if (preloader.parentNode) {
                        preloader.parentNode.removeChild(preloader);
                    }
                }, 750);
            }, 300);
        }, 350);
    }

    // Trigger completion on window load or safe fallback timeout
    if (document.readyState === 'complete') {
        setTimeout(finishPreloader, 1200);
    } else {
        window.addEventListener('load', function() {
            setTimeout(finishPreloader, 900);
        });
        // Safety timeout so preloader never blocks page indefinitely
        setTimeout(finishPreloader, 3200);
    }
})();
</script>
