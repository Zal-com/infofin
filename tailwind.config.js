import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography'
import preset from './vendor/filament/support/tailwind.config.preset'

const plugin = require('tailwindcss/plugin')

/** @type {import('tailwindcss').Config} */
export default {
    presets: [preset],
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
        './vendor/awcodes/filament-badgeable-column/resources/**/*.blade.php',
        './vendor/awcodes/filament-tiptap-editor/resources/**/*.blade.php',

    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [forms, typography, plugin(function ({addBase, theme}) {
        addBase({
            'h1': {fontSize: theme('fontSize.4xl'), fontWeight: 800},
            'h2': {fontSize: theme('fontSize.2xl')},
            'h3': {fontSize: theme('fontSize.xl')},
        })
    })
    ],
};
