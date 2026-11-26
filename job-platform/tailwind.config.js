import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                primary: {
                    DEFAULT: '#2A81CB',
                    50: '#F0F7FD',
                    100: '#E1EEFA',
                    200: '#C2DDF6',
                    300: '#94C3EF',
                    400: '#5FA3E5',
                    500: '#2A81CB',
                    600: '#1F6BB0',
                    700: '#19558D',
                    800: '#164875',
                    900: '#143C61',
                },
                navy: {
                    DEFAULT: '#142B47',
                    50: '#F4F7FA',
                    100: '#E7ECF3',
                    200: '#C7D4E3',
                    300: '#9EB4CF',
                    400: '#698EBA',
                    500: '#436DA1',
                    600: '#325482',
                    700: '#264166',
                    800: '#1D3350',
                    900: '#142B47',
                },
                pastel: {
                    DEFAULT: '#EAF2FA',
                    light: '#F4F8FC',
                    dark: '#D8E7F5',
                },
                accent: {
                    DEFAULT: '#E1EEFA',
                    light: '#E6F0FA',
                },
                surface: {
                    DEFAULT: '#F7FAFC',
                    card: '#FFFFFF',
                }
            },
            fontFamily: {
                sans: ['Montserrat', ...defaultTheme.fontFamily.sans],
                montserrat: ['Montserrat', 'sans-serif'],
            },
        },
    },

    plugins: [forms],
};
