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
                sans: ['"Plus Jakarta Sans"', 'Inter', '-apple-system', 'BlinkMacSystemFont', ...defaultTheme.fontFamily.sans],
                serif: ['"Cormorant Garamond"', 'Playfair Display', 'Georgia', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                apple: {
                    black: '#000000',
                    bg: '#070708',
                    surface: '#0E0E11',
                    card: '#151518',
                    cardHover: '#1D1D22',
                    border: 'rgba(255, 255, 255, 0.08)',
                    borderHover: 'rgba(255, 255, 255, 0.20)',
                    line: 'rgba(255, 255, 255, 0.12)',
                    text: '#F5F5F7',
                    textMuted: '#86868B',
                    textDark: '#515154',
                    blue: '#2997FF',
                    blueHover: '#147CE5',
                    gold: '#D4AF37',
                    titanium: '#8E8E93',
                },
            },
            letterSpacing: {
                tightest: '-0.04em',
                tighter: '-0.025em',
                wide: '0.04em',
                widest: '0.18em',
            },
            boxShadow: {
                'apple-glow': '0 0 50px -10px rgba(255, 255, 255, 0.12)',
                'apple-blue-glow': '0 0 50px -10px rgba(41, 151, 255, 0.3)',
                'apple-card': '0 8px 32px 0 rgba(0, 0, 0, 0.45)',
            },
            backgroundImage: {
                'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
                'apple-hero-radial': 'radial-gradient(circle at 50% 0%, rgba(41, 151, 255, 0.12), transparent 60%)',
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
