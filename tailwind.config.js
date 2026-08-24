import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            colors: {
                canvas: '#f6f8f7',
                ink: '#122027',
                muted: '#5f6f75',
                line: '#dfe7e5',
                primary: {
                    50: '#eefbf8',
                    100: '#d5f5ee',
                    500: '#168d82',
                    600: '#0f746c',
                    700: '#105e59',
                    950: '#083a37',
                },
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                surface: '0 1px 2px rgba(18, 32, 39, 0.04), 0 8px 24px rgba(18, 32, 39, 0.04)',
            },
        },
    },

    plugins: [forms],
};
