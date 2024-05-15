/** @type {import('tailwindcss').Config} */
const plugin = require('tailwindcss/plugin')

export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {},
    },
    plugins: [
        plugin(function ({addBase, theme}) {
            addBase({
                'h1': {fontSize: theme('fontSize.4xl'), fontWeight: 800},
                'h2': {fontSize: theme('fontSize.2xl')},
                'h3': {fontSize: theme('fontSize.xl')},
            })
        })
    ],
}

