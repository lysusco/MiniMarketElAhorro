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
                'bg': { 100: '#fffefb', 200: '#f5f4f1', 300: '#cccbc8' },
                'primary': { 100: '#d4eaf7', 200: '#b6ccd8', 300: '#3b3c3d' },
                'accent': { 100: '#71c4ef', 200: '#00668c' },
                'text': { 100: '#1d1c1c', 200: '#313d44' },
            },
        },
    },

    plugins: [forms],
};
