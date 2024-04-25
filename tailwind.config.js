/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./assets/images/*",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
      colors: {
        'logomdl': '#FEC816',
      }
    },
  },
  plugins: [],
}

