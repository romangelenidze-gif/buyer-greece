import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        host: '0.0.0.0', // Доступ для всех сетевых интерфейсов
        cors: true,      // Разрешаем CORS-запросы
        hmr: {
            host: '192.168.1.53', // Принудительно привязываем HMR к вашему локальному IP
        },
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});