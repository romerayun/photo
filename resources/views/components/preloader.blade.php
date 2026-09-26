{{-- Light, Airy & Minimalist Preloader --}}
<div id="site-preloader"
     style="position: fixed !important; top: 0 !important; left: 0 !important; width: 100vw !important; height: 100vh !important; z-index: 2147483647 !important; background-color: #FAFAFA !important; display: flex !important; flex-direction: column !important; align-items: center !important; justify-content: center !important; overflow: hidden !important; user-select: none !important; -webkit-user-select: none !important; transition: opacity 0.7s cubic-bezier(0.16, 1, 0.3, 1), transform 0.7s cubic-bezier(0.16, 1, 0.3, 1);"
     aria-hidden="false"
     role="progressbar"
     aria-label="Загрузка сайта">

    {{-- Subtle Soft Atmospheric Glow (Pure Airy Light) --}}
    <div style="position: absolute; width: 500px; height: 500px; background: radial-gradient(circle, rgba(255, 255, 255, 0.9) 0%, rgba(240, 240, 243, 0.6) 50%, transparent 75%); pointer-events: none; border-radius: 50%; filter: blur(60px);"></div>

    <div style="position: relative; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 0 24px;">

        {{-- Minimalist Floating Focus Ring / Shutter --}}
        <div style="position: relative; width: 68px; height: 68px; margin-bottom: 24px; display: flex; align-items: center; justify-content: center;">
            {{-- Delicate Outer Halo --}}
            <svg class="airy-spin-ring" style="position: absolute; inset: 0; width: 100%; height: 100%;" viewBox="0 0 68 68" fill="none">
                <circle cx="34" cy="34" r="31" stroke="rgba(0, 0, 0, 0.05)" stroke-width="1.5" />
                <circle cx="34" cy="34" r="31" stroke="#18181B" stroke-width="1.5" stroke-linecap="round" stroke-dasharray="45 150" />
            </svg>

            {{-- Clean Soft Center Initial or Shutter Dot --}}
            <div style="width: 8px; height: 8px; background-color: #E51920; border-radius: 50%; animation: airyPulse 1.8s cubic-bezier(0.4, 0, 0.2, 1) infinite alternate;"></div>
        </div>

        {{-- Clean Author Name --}}
        <span style="font-family: 'Plus Jakarta Sans', -apple-system, sans-serif; font-size: 15px; font-weight: 700; letter-spacing: 0.24em; text-transform: uppercase; color: #111114; margin-bottom: 8px; display: block;">
            РОМАН ЮН
        </span>

        {{-- Subtle Tagline --}}
        <span style="font-family: 'Plus Jakarta Sans', -apple-system, sans-serif; font-size: 10.5px; font-weight: 500; letter-spacing: 0.18em; text-transform: uppercase; color: #8E8E93; margin-bottom: 28px; display: block;">
            ФОТОГРАФ
        </span>

        {{-- Ultra-thin Airy Loading Line --}}
        <div style="width: 140px; height: 1.5px; background: rgba(0, 0, 0, 0.06); border-radius: 9999px; overflow: hidden; position: relative;">
            <div id="airy-progress-bar" style="height: 100%; width: 0%; background: #18181B; border-radius: 9999px; transition: width 0.15s ease-out;"></div>
        </div>

    </div>
</div>

<style>
@keyframes airySpin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

@keyframes airyPulse {
    0% {
        transform: scale(0.85);
        opacity: 0.7;
    }
    100% {
        transform: scale(1.35);
        opacity: 1;
        box-shadow: 0 0 14px rgba(229, 25, 32, 0.6);
    }
}

.airy-spin-ring {
    animation: airySpin 2.4s cubic-bezier(0.4, 0, 0.2, 1) infinite;
    transform-origin: center;
}

#site-preloader.preloader-fade-out {
    opacity: 0 !important;
    transform: scale(1.02) !important;
    pointer-events: none !important;
}
</style>

<script>
(function() {
    var preloader = document.getElementById('site-preloader');
    if (!preloader) return;

    if (preloader.parentNode !== document.body) {
        document.body.prepend(preloader);
    }

    // Temporarily pause scroll
    document.documentElement.style.overflow = 'hidden';
    document.body.style.overflow = 'hidden';

    var bar = document.getElementById('airy-progress-bar');
    var progress = 0;
    var isLoaded = false;

    var timer = setInterval(function() {
        if (progress < 90) {
            progress += Math.floor(Math.random() * 12) + 6;
            if (progress > 90) progress = 90;
            if (bar) bar.style.width = progress + '%';
        }
    }, 70);

    function finish() {
        if (isLoaded) return;
        isLoaded = true;

        clearInterval(timer);
        if (bar) bar.style.width = '100%';

        setTimeout(function() {
            preloader.classList.add('preloader-fade-out');
            document.documentElement.style.overflow = '';
            document.body.style.overflow = '';

            setTimeout(function() {
                if (preloader.parentNode) {
                    preloader.parentNode.removeChild(preloader);
                }
            }, 750);
        }, 220);
    }

    if (document.readyState === 'complete') {
        setTimeout(finish, 350);
    } else {
        window.addEventListener('load', function() {
            setTimeout(finish, 300);
        });
        setTimeout(finish, 1800);
    }
})();
</script>
