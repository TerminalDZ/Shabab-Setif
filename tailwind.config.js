/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./views/**/*.php",
        "./src/**/*.php",
        "./public/*.php"
    ],
    theme: {
        extend: {
            colors: {
                primary: '#2d6a4f', // Algerian Green
                secondary: '#d62828', // Algerian Red
                accent: '#ffffff',
                dark: '#1a1a1a',
                light: '#f8f9fa'
            },
            fontFamily: {
                tajawal: ['Tajawal', 'sans-serif'],
            }
        },
    },
    plugins: [],
}
