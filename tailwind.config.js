import typography from '@tailwindcss/typography'
import forms from '@tailwindcss/forms'
import aspectRatio from '@tailwindcss/aspect-ratio'
import containerQueries from '@tailwindcss/container-queries'
import flowbite from 'flowbite/plugin'

export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ["ui-sans-serif", "sans-serif", "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"],
      },
      colors: {
        blue: {
          50: 'rgb(var(--brand-blue-50) / <alpha-value>)',
          100: 'rgb(var(--brand-blue-100) / <alpha-value>)',
          200: 'rgb(var(--brand-blue-200) / <alpha-value>)',
          300: 'rgb(var(--brand-blue-300) / <alpha-value>)',
          400: 'rgb(var(--brand-blue-400) / <alpha-value>)',
          500: 'rgb(var(--brand-blue-500) / <alpha-value>)',
          600: 'rgb(var(--brand-blue-600) / <alpha-value>)',
          700: 'rgb(var(--brand-blue-700) / <alpha-value>)',
          800: 'rgb(var(--brand-blue-800) / <alpha-value>)',
          900: 'rgb(var(--brand-blue-900) / <alpha-value>)',
          950: 'rgb(var(--brand-blue-950) / <alpha-value>)',
        },
        'sidebar-color': '#ffff',
        'svg-icon-color': 'rgb(100 116 139)'
      }
    },
  },
  plugins: [
    typography,
    forms,
    aspectRatio,
    containerQueries,
    flowbite({
      datatables: true,
      charts: true,
    }),
  ],
}

