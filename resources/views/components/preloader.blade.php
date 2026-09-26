{{-- Elegant Editorial Minimalist Preloader for Roman Yun Photography --}}
<div id="site-preloader"
     style="position: fixed !important; top: 0 !important; left: 0 !important; width: 100vw !important; height: 100vh !important; z-index: 2147483647 !important; background-color: #09090B !important; display: flex !important; flex-direction: column !important; align-items: center !important; justify-content: center !important; overflow: hidden !important; user-select: none !important; -webkit-user-select: none !important; transition: opacity 0.65s cubic-bezier(0.16, 1, 0.3, 1), transform 0.65s cubic-bezier(0.16, 1, 0.3, 1);"
     aria-hidden="false"
     role="progressbar"
     aria-label="Загрузка сайта">

    {{-- Subtle Ambient Radial Red Glow --}}
    <div style="position: absolute; width: 420px; height: 420px; background: radial-gradient(circle, rgba(229, 25, 32, 0.12) 0%, transparent 70%); pointer-events: none; border-radius: 50%; filter: blur(40px);"></div>

    <div style="position: relative; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 0 24px;">

        {{-- Camera Aperture / Lens Focus Ring with Monogram --}}
        <div style="position: relative; width: 88px; height: 88px; margin-bottom: 28px; display: flex; align-items: center; justify-content: center;">
            {{-- Outer rotating aperture ring --}}
            <svg class="preloader-spin-ring" style="position: absolute; inset: 0; width: 100%; height: 100%;" viewBox="0 0 88 88" fill="none">
                <circle cx="44" cy="44" r="41" stroke="rgba(255, 255, 255, 0.08)" stroke-width="2" />
                <circle cx="44" cy="44" r="41" stroke="#E51920" stroke-width="2" stroke-linecap="round" stroke-dasharray="70 200" />
            </svg>

            {{-- Inner counter-rotating thin tick ring --}}
            <svg class="preloader-counter-ring" style="position: absolute; inset: 6px; width: calc(100% - 12px); height: calc(100% - 12px);" viewBox="0 0 76 76" fill="none">
                <circle cx="38" cy="38" r="35" stroke="rgba(255, 255, 255, 0.15)" stroke-width="1.2" stroke-dasharray="6 14" />
            </svg>

            {{-- Center RY Monogram --}}
            <div style="width: 44px; height: 44px; background-color: #E51920; display: flex; align-items: center; justify-content: center; color: #FFFFFF; font-weight: 900; font-size: 15px; letter-spacing: 0.08em; font-family: 'Plus Jakarta Sans', sans-serif; box-shadow: 0 0 25px rgba(229, 25, 32, 0.5);">
                RY
            </div>
        </div>

        {{-- Author & Title --}}
        <h2 style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 19px; font-weight: 800; letter-spacing: 0.16em; text-transform: uppercase; color: #FFFFFF; margin: 0 0 6px 0;">
            РОМАН ЮН
        </h2>
        <p style="font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 11px; letter-spacing: 0.22em; text-transform: uppercase; color: #8E8E93; margin: 0 0 32px 0;">
            ФОТОГРАФИЯ &bull; ИРКУТСК
        </p>

        {{-- Smooth Minimal Progress Bar & Percentage --}}
        <div style="width: 220px; display: flex; flex-direction: column; align-items: center; gap: 10px;">
            <div style="width: 100%; height: 2px; background: rgba(255, 255, 255, 0.1); border-radius: 9999px; overflow: hidden; position: relative;">
                <div id="preloader-bar" style="height: 100%; width: 0%; background: linear-gradient(90deg, #E51920, #FFFFFF); border-radius: 9999px; transition: width 0.15s ease-out;"></div>
            </div>
            <div style="width: 100%; display: flex; justify-content: space-between; align-items: center; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 11px; color: #71717A; letter-spacing: 0.12em;">
                <span style="display: inline-flex; align-items: center; gap: 6px;">
                    <span style="width: 5px; height: 5px; border-radius: 50%; background-color: #E51920; display: inline-block; animation: preloaderPulseDot 1s infinite alternate;"></span>
                    ЗАГРУЗКА
                </span>
                <span id="preloader-percent" style="color: #FFFFFF; font-weight: 700;">0%</span>
            </div>
        </div>

    </div>
</div>

<style>
@keyframes preloaderSpin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

@keyframes preloaderCounterSpin {
    from {
        transform: rotate(360deg);
    }
    to {
        transform: rotate(0deg);
    }
}

@keyframes preloaderPulseDot {
    0% {
        opacity: 0.3;
        transform: scale(0.8);
    }
    100% {
        opacity: 1;
        transform: scale(1.2);
    }
}

.preloader-spin-ring {
    animation: preloaderSpin 2.2s linear infinite;
    transform-origin: center;
}

.preloader-counter-ring {
    animation: preloaderCounterSpin 8s linear infinite;
    transform-origin: center;
}

#site-preloader.preloader-fade-out {
    opacity: 0 !important;
    transform: scale(1.03) !important;
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

    // Lock scroll during loading
    document.documentElement.style.overflow = 'hidden';
    document.body.style.overflow = 'hidden';

    var bar = document.getElementById('preloader-bar');
    var percent = document.getElementById('preloader-percent');
    var progress = 0;
    var isLoaded = false;

    // Simulate natural progress feel
    var timer = setInterval(function() {
        if (progress < 90) {
            progress += Math.floor(Math.random() * 9) + 4;
            if (progress > 90) progress = 90;
            updateUI(progress);
        }
    }, 80);

    function updateUI(val) {
        if (bar) bar.style.width = val + '%';
        if (percent) percent.textContent = Math.round(val) + '%';
    }

    function finish() {
        if (isLoaded) return;
        isLoaded = true;

        clearInterval(timer);
        updateUI(100);

        setTimeout(function() {
            preloader.classList.add('preloader-fade-out');
            document.documentElement.style.overflow = '';
            document.body.style.overflow = '';

            setTimeout(function() {
                if (preloader.parentNode) {
                    preloader.parentNode.removeChild(preloader);
                }
            }, 700);
        }, 300);
    }

    if (document.readyState === 'complete') {
        setTimeout(finish, 400);
    } else {
        window.addEventListener('load', function() {
            setTimeout(finish, 350);
        });
        // Safety timeout so user is never blocked
        setTimeout(finish, 2200);
    }
})();
</script>
