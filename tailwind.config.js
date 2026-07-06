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
                sans: ['Nunito Sans', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#eef8f8',
                    100: '#d5eeee',
                    200: '#aedddd',
                    300: '#7ac5c5',
                    400: '#4da8a8',
                    500: '#088486',
                    600: '#076e70',
                    700: '#065a5c',
                    800: '#054849',
                    900: '#043a3b',
                    950: '#022526',
                },
                wsj: {
                    purple: '#7413dc',
                    orange: '#ff912a',
                    green: '#205b41',
                    teal: '#088486',
                    navy: '#003982',
                    blue: '#006ddf',
                    red: '#ed3f23',
                    yellow: '#ffe627',
                },
            },
        },
    },

    plugins: [forms],
};
