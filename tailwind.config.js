import defaultTheme from 'tailwindcss/defaultTheme'
import forms from '@tailwindcss/forms'

const colors = require('tailwindcss/colors')

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
      fontFamily: {
        sans: ['Figtree', ...defaultTheme.fontFamily.sans],
      },
      colors: {
        coral: {
          50: '#fff4ed',
          100: '#ffe5d4',
          200: '#ffc7a8',
          300: '#ffa071',
          400: '#ff7f50',
          500: '#fe4711',
          600: '#ef2d07',
          700: '#c61c08',
          800: '#9d190f',
          900: '#7e1810',
          950: '#440806',
        },
      },
    },
  },

  plugins: [forms],
}
