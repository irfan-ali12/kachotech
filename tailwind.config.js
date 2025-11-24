/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './**/*.php',
    './template-parts/**/*.php',
    './assets/**/*.{js,jsx}',
  ],
  theme: {
    extend: {
      colors: {
        'kt': {
          'primary': '#ff2446',
          'dark': '#050816',
        }
      },
      fontFamily: {
        sans: ['-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', 'sans-serif'],
      },
    },
  },
  plugins: [],
}
