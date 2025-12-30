import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        // 1. Path Bawaan Laravel Breeze (PENTING)
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        
        // 2. Path Resource Anda
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        
        // 3. Path Flowbite (AGAR NAVBAR & MODAL TETAP JALAN)
        './node_modules/flowbite/**/*.js' 
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [
        // 4. Plugin Flowbite
        require('flowbite/plugin'),
        
        // 5. Plugin Forms (INI KUNCI AGAR HALAMAN LOGIN BAGUS)
        forms,
    ],
};