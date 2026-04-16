/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./app/**/*.{js,ts,jsx,tsx,mdx}",
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['var(--font-rajdhani)'],
        mono: ['var(--font-mono)'],
      },
      colors: {
        'hell-red': '#FF0022',
        'void': '#0A0A0A',
      },
    },
  },
  plugins: [],
}
