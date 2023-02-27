/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    fontFamily: {
      title: 'Unbounded, cursive',
      main: 'Heebo, sans-serif'
    },
    extend: {},
  },
  plugins: [
      require('@tailwindcss/line-clamp'),
      require("daisyui"),
      require("@tailwindcss/forms")
  ],
}
