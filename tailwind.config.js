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
                'bg': { 100: '#0F1C2E', 200: '#1f2b3e', 300: '#374357' },
                'primary': { 100: '#1F3A5F', 200: '#4d648d', 300: '#acc2ef' },
                'accent': { 100: '#3D5A80', 200: '#cee8ff' },
                'text': { 100: '#FFFFFF', 200: '#e0e0e0' },
            },
        },
    },

    plugins: [forms],
};
