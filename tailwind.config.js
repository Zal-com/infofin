import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

const plugin = require('tailwindcss/plugin')

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
        },
    },

    plugins: [forms, plugin(function ({addBase, theme}) {
        addBase({
            'h1': {fontSize: theme('fontSize.4xl'), fontWeight: 800},
            'h2': {fontSize: theme('fontSize.2xl')},
            'h3': {fontSize: theme('fontSize.xl')},
        })
    })
    ],
};
