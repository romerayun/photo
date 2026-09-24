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
                sans: ['"Plus Jakarta Sans"', 'Inter', ...defaultTheme.fontFamily.sans],
                display: ['"Unbounded"', '"Syne"', '"Plus Jakarta Sans"', 'Inter', ...defaultTheme.fontFamily.sans],
                serif: ['"Cormorant Garamond"', 'Playfair Display', 'Georgia', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                crimson: {
                    DEFAULT: '#E51920',
                    hover: '#C41218',
                    dark: '#9A0B10',
                    light: 'rgba(229, 25, 32, 0.10)',
                    glow: 'rgba(229, 25, 32, 0.35)',
                },
                cine: {
                    black: '#09090B',
                    surface: '#111114',
                    card: '#18181C',
                    border: 'rgba(255, 255, 255, 0.08)',
                    text: '#FFFFFF',
                    muted: '#8E8E93',
                },
                arch: {
                    bg: '#F8F8FA',
                    surface: '#FFFFFF',
                    border: '#E4E4E8',
                    line: '#EFEFF2',
                    text: '#0C0C0E',
                    muted: '#66666E',
                },
            },
            letterSpacing: {
                tightest: '-0.04em',
                tighter: '-0.025em',
                wide: '0.04em',
                widest: '0.18em',
                mega: '0.25em',
            },
            boxShadow: {
                'crimson-glow': '0 0 25px rgba(229, 25, 32, 0.35)',
                'crimson-btn': '0 4px 18px rgba(229, 25, 32, 0.35)',
                'card-depth': '0 12px 32px -4px rgba(0, 0, 0, 0.08)',
            },
            aspectRatio: {
                '4/5': '4 / 5',
                '3/4': '3 / 4',
                '16/10': '16 / 10',
                '21/9': '21 / 9',
            },
        },
    },
    plugins: [],
};
