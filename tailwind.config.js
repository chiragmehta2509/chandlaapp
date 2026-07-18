/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'class',
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
    './app/**/*.php',
  ],
  theme: {
    extend: {
      colors: {
        'cb-cream': '#F9F8F3',
        'cb-navy': '#1A3646',
        'cb-gold': '#B8860B',
      },
    },
  },
  plugins: [],
};
