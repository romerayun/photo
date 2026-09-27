import './bootstrap';
import Alpine from 'alpinejs';

// Register Alpine store or components if needed
Alpine.data('lightbox', (photos = []) => ({
    isOpen: false,
    photos: photos,
    currentIndex: 0,
    touchStartX: 0,
    touchStartY: 0,

    open(index) {
        this.currentIndex = index;
        this.isOpen = true;
        document.body.style.overflow = 'hidden';
    },

    close() {
        this.isOpen = false;
        document.body.style.overflow = '';
    },

    next() {
        if (this.photos.length > 0) {
            this.currentIndex = (this.currentIndex + 1) % this.photos.length;
        }
    },

    prev() {
        if (this.photos.length > 0) {
            this.currentIndex = (this.currentIndex - 1 + this.photos.length) % this.photos.length;
        }
    },

    handleTouchStart(e) {
        if (!e.changedTouches || e.changedTouches.length === 0) return;
        this.touchStartX = e.changedTouches[0].clientX;
        this.touchStartY = e.changedTouches[0].clientY;
    },

    handleTouchEnd(e) {
        if (!e.changedTouches || e.changedTouches.length === 0) return;
        const diffX = e.changedTouches[0].clientX - this.touchStartX;
        const diffY = e.changedTouches[0].clientY - this.touchStartY;
        // Check horizontal swipe if swipe distance is significant and predominantly horizontal
        if (Math.abs(diffX) > 40 && Math.abs(diffX) > Math.abs(diffY)) {
            if (diffX < 0) {
                this.next();
            } else {
                this.prev();
            }
        }
    },

    currentPhoto() {
        return this.photos[this.currentIndex] || {};
    }
}));

window.Alpine = Alpine;
Alpine.start();

/**
 * Lazy Image Skeleton Loader
 * Automatically detects images with .lazy-img-fade or within .lazy-skeleton-wrapper
 * and removes skeleton shimmer once the image has completely downloaded.
 */
function initLazyImageLoader(root = document) {
    const images = root.querySelectorAll('img.lazy-img-fade, .lazy-skeleton-wrapper img');
    
    images.forEach((img) => {
        const wrapper = img.closest('.lazy-skeleton-wrapper');

        const markLoaded = () => {
            img.classList.add('is-loaded');
            if (wrapper) {
                wrapper.classList.add('is-loaded');
            }
        };

        if (img.complete && img.naturalWidth > 0) {
            markLoaded();
        } else {
            img.addEventListener('load', markLoaded, { once: true });
            img.addEventListener('error', markLoaded, { once: true });
        }
    });
}

// Initialize on page load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => initLazyImageLoader());
} else {
    initLazyImageLoader();
}

// Observe dynamic insertions (e.g. Load More pagination)
if (typeof MutationObserver !== 'undefined') {
    const observer = new MutationObserver((mutations) => {
        let shouldInit = false;
        for (const mutation of mutations) {
            if (mutation.addedNodes.length > 0) {
                for (const node of mutation.addedNodes) {
                    if (node.nodeType === 1 && (node.matches?.('.lazy-skeleton-wrapper, img.lazy-img-fade') || node.querySelector?.('.lazy-skeleton-wrapper, img.lazy-img-fade'))) {
                        shouldInit = true;
                        break;
                    }
                }
            }
            if (shouldInit) break;
        }
        if (shouldInit) {
            initLazyImageLoader();
        }
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
}

window.initLazyImageLoader = initLazyImageLoader;

