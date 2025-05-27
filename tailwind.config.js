// tailwind.config.js
/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    // This array tells Tailwind which files to scan for class names
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue", // If you decide to use Vue later
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}