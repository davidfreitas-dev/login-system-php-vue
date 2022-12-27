/** @type {import('tailwindcss').Config} */

const colors = require('tailwindcss/colors');

delete colors['lightBlue'];
delete colors['warmGray'];
delete colors['trueGray'];
delete colors['coolGray'];
delete colors['blueGray'];

module.exports = {
  content: [
    './index.html',
    './src/**/*.{vue,js}',
  ],
  theme: {
    extend: {},
    colors: {
      ...colors,
      dark: '#34355B',
      black: '#252644',
      white: '#FFFFFF',
      light: '#F5F7FA',
      brand: '#00D495',
      alert: '#FFBE00',
      danger: '#FF316D',
      success: '#00E4A1',
      'dark-gray': '#9396AA',
    }
  },
  plugins: [],
}