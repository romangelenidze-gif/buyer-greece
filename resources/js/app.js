import './bootstrap';
import Alpine from 'alpinejs';
import Swiper from 'swiper';
import { Navigation, Pagination } from 'swiper/modules';

import 'swiper/css';
import 'swiper/css/navigation';

window.Swiper = Swiper;
window.SwiperModules = [Navigation, Pagination];

// Вычисление ширины скроллбара для предотвращения сдвига контента (Layout Shift)
const updateScrollbarWidth = () => {
    const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
    document.documentElement.style.setProperty('--scrollbar-width', `${scrollbarWidth}px`);
};

window.addEventListener('resize', updateScrollbarWidth);
document.addEventListener('DOMContentLoaded', updateScrollbarWidth);

window.Alpine = Alpine;
Alpine.start();