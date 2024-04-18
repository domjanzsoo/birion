import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'blue':  {
                    light: '#bae6fd',
                    DEFAULT: '#38bdf8',
                    dark: '#0369a1'
                },
                'red': {
                    DEFAULT: '#f43f5e',
                    dark: '#be123c'
                },
                'green': {
                    DEFAULT: '#4ade80',
                    light: '#86efac',
                    dark: '#047857'
                },
                'gray': {
                    DEFAULT: '#9ca3af',
                    light: '#d4d4d8',
                    dark: '#71717a'
                }
            }
        },
    },

    plugins: [forms, typography],
};
