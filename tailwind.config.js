import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['-apple-system', 'BlinkMacSystemFont', 'SF Pro Display', 'SF Pro Text', 'Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                apple: {
                    bg: '#f2f2f2',
                    card: '#f5f5f7',
                    dark: '#1d1d1f',
                    gray: '#86868b',
                    blue: '#0071e3',
                    'blue-hover': '#0077ed',
                },
            },
            borderRadius: {
                'apple-sm': '0.75rem',  // 12px
                'apple': '1.25rem',     // 20px
                'apple-lg': '1.75rem',  // 28px
                'apple-xl': '2.5rem',   // 40px
            },
            boxShadow: {
                'apple-sm': '0 4px 12px rgba(0, 0, 0, 0.03)',
                'apple-card': '0 20px 40px rgba(0, 0, 0, 0.04)',
                'apple-hover': '0 30px 60px rgba(0, 0, 0, 0.08)',
            },
        },
    },
    plugins: [forms],
};