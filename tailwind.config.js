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
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#f5f0ff',
                    100: '#ede5ff',
                    200: '#dccfff',
                    300: '#c3a8ff',
                    400: '#a472ff',
                    500: '#8b3dff',
                    600: '#7413dc',
                    700: '#6510b8',
                    800: '#530e96',
                    900: '#450c7a',
                    950: '#2a0552',
                },
            },
        },
    },

    plugins: [forms],
};
