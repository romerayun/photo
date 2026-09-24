import './bootstrap';
import Alpine from 'alpinejs';

// Register Alpine store or components if needed
Alpine.data('lightbox', (photos = []) => ({
    isOpen: false,
    photos: photos,
    currentIndex: 0,

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

    currentPhoto() {
        return this.photos[this.currentIndex] || {};
    }
}));

window.Alpine = Alpine;
Alpine.start();
