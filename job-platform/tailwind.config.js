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
            fontFamily: {
                sans: ['Montserrat', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    primary: '#2A81CB',
                    navy: '#142B47',
                    section: '#EAF2FA',
                    accent: '#E1EEFA',
                    canvas: '#F7FAFC',
                },
            },
        },
    },

    plugins: [forms],
};
