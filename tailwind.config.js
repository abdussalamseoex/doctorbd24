import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                // Primary Accent Mapping (Base: #2ECC71, Hover: #27AE60)
                sky: { 50: '#EAF9F1', 100: '#D5F4E4', 200: '#ABE8C8', 300: '#81DDAC', 400: '#57D18F', 500: '#2ECC71', 600: '#27AE60', 700: '#1D8248', 800: '#135730', 900: '#0A2B18', 950: '#05160C' },
                emerald: { 50: '#EAF9F1', 100: '#D5F4E4', 200: '#ABE8C8', 300: '#81DDAC', 400: '#57D18F', 500: '#2ECC71', 600: '#27AE60', 700: '#1D8248', 800: '#135730', 900: '#0A2B18', 950: '#05160C' },
                indigo: { 50: '#EAF9F1', 100: '#D5F4E4', 200: '#ABE8C8', 300: '#81DDAC', 400: '#57D18F', 500: '#2ECC71', 600: '#27AE60', 700: '#1D8248', 800: '#135730', 900: '#0A2B18', 950: '#05160C' },
                teal: { 50: '#EAF9F1', 100: '#D5F4E4', 200: '#ABE8C8', 300: '#81DDAC', 400: '#57D18F', 500: '#2ECC71', 600: '#27AE60', 700: '#1D8248', 800: '#135730', 900: '#0A2B18', 950: '#05160C' },
                cyan: { 50: '#EAF9F1', 100: '#D5F4E4', 200: '#ABE8C8', 300: '#81DDAC', 400: '#57D18F', 500: '#2ECC71', 600: '#27AE60', 700: '#1D8248', 800: '#135730', 900: '#0A2B18', 950: '#05160C' },
                blue: { 50: '#EAF9F1', 100: '#D5F4E4', 200: '#ABE8C8', 300: '#81DDAC', 400: '#57D18F', 500: '#2ECC71', 600: '#27AE60', 700: '#1D8248', 800: '#135730', 900: '#0A2B18', 950: '#05160C' },
                violet: { 50: '#EAF9F1', 100: '#D5F4E4', 200: '#ABE8C8', 300: '#81DDAC', 400: '#57D18F', 500: '#2ECC71', 600: '#27AE60', 700: '#1D8248', 800: '#135730', 900: '#0A2B18', 950: '#05160C' },
                fuchsia: { 50: '#EAF9F1', 100: '#D5F4E4', 200: '#ABE8C8', 300: '#81DDAC', 400: '#57D18F', 500: '#2ECC71', 600: '#27AE60', 700: '#1D8248', 800: '#135730', 900: '#0A2B18', 950: '#05160C' },
            },
            fontFamily: {
                sans: ['Inter', 'Noto Sans Bengali', ...defaultTheme.fontFamily.sans],
            },
            animation: {
                'fade-in': 'fadeIn 0.3s ease-in-out',
            },
            keyframes: {
                fadeIn: { '0%': { opacity: '0', transform: 'translateY(-10px)' }, '100%': { opacity: '1', transform: 'translateY(0)' } },
            },
        },
    },

    plugins: [forms, typography],
};

