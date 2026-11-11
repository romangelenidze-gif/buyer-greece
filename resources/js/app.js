import './bootstrap';
import Alpine from 'alpinejs';
import Swiper from 'swiper';
import { Navigation, Pagination } from 'swiper/modules';

import 'swiper/css';
import 'swiper/css/navigation';

import PhotoSwipe from 'photoswipe';
import 'photoswipe/style.css';

window.Swiper = Swiper;
window.SwiperModules = [Navigation, Pagination];

// Вычисление ширины скроллбара для предотвращения сдвига контента (Layout Shift)
const updateScrollbarWidth = () => {
    const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
    document.documentElement.style.setProperty('--scrollbar-width', `${scrollbarWidth}px`);
};

window.addEventListener('resize', updateScrollbarWidth);
document.addEventListener('DOMContentLoaded', updateScrollbarWidth);

// Просмотр фото на весь экран (PhotoSwipe): двойной тап, щипок, перетаскивание, закрытие свайпом.
// Любой элемент с data-pswp-src открывает просмотр по клику.
document.addEventListener('click', (e) => {
    const trigger = e.target.closest('[data-pswp-src]');
    if (!trigger) return;

    const src = trigger.dataset.pswpSrc;
    const alt = trigger.dataset.pswpAlt || '';
    const probe = new Image();

    probe.onload = () => {
        const pswp = new PhotoSwipe({
            dataSource: [{
                src,
                alt,
                width: probe.naturalWidth,
                height: probe.naturalHeight,
            }],
            bgOpacity: 0.97,
            secondaryZoomLevel: 2,
            maxZoomLevel: 4,
            wheelToZoom: true,
            counter: false,
            arrowPrev: false,
            arrowNext: false,
            padding: { top: 20, bottom: 20, left: 0, right: 0 },
        });
        pswp.init();
    };
    probe.src = src;
});

window.Alpine = Alpine;
Alpine.start();