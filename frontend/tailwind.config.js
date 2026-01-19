/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
        serif: ['Crimson Pro', 'Georgia', 'serif'],
      },
      transitionDuration: {
        'slow': '600ms',
        'slower': '1000ms',
      },
      transitionTimingFunction: {
        'cozy': 'cubic-bezier(0.4, 0, 0.2, 1)',
      },
    },
  },
  plugins: [],
}

