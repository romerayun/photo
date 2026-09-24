import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],
    theme: {
        extend: {
            fontFamily: {
                serif: ['"Cormorant Garamond"', 'Playfair Display', 'Georgia', ...defaultTheme.fontFamily.serif],
                sans: ['"Plus Jakarta Sans"', 'Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                canvas: '#FAF8F5',
                surface: '#F4EFEB',
                subtle: '#ECE6DE',
                editorial: {
                    border: '#E3DDD3',
                    line: '#D5CFC4',
                },
                graphite: {
                    950: '#141312',
                    900: '#1E1D1B',
                    800: '#2D2B28',
                    700: '#4A4642',
                    600: '#6E6963',
                    500: '#918B82',
                    400: '#B5AFA6',
                    300: '#D6D1C8',
                    200: '#EAE5DC',
                    100: '#F5F1EB',
                    50: '#FAF8F5',
                },
                terracotta: {
                    DEFAULT: '#9E5B3D',
                    dark: '#83492F',
                    light: '#F4ECE6',
                    subtle: '#FAF4F0',
                },
            },
            letterSpacing: {
                widest: '.2em',
                editorial: '.08em',
            },
            aspectRatio: {
                '3/4': '3 / 4',
                '4/5': '4 / 5',
                '16/10': '16 / 10',
            },
        },
    },
    plugins: [],
};
