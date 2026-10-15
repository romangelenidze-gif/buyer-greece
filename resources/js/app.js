import './bootstrap';

import Alpine from 'alpinejs';

import Swiper from 'swiper';

import { Navigation, Pagination } from 'swiper/modules';

import 'swiper/css';

import 'swiper/css/navigation';

window.Swiper = Swiper;

window.SwiperModules = [Navigation, Pagination];

window.Alpine = Alpine;

Alpine.start();