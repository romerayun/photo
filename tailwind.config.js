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
                    bg: '#F5F5F7',            // Apple signature pearl background
                    surface: '#FFFFFF',       // Pure white cards
                    subtle: '#EBEBED',        // Subtle card / badge tint
                    card: '#FFFFFF',
                    border: 'rgba(0, 0, 0, 0.06)',
                    borderHover: 'rgba(0, 0, 0, 0.15)',
                    line: 'rgba(0, 0, 0, 0.08)',
                    text: '#1D1D1F',          // Deepest Apple text
                    textMuted: '#6E6E73',     // Secondary Apple text
                    textLight: '#86868B',     // Tertiary Apple text
                    accent: '#B07D53',        // Noble warm titanium / bronze
                    accentHover: '#96653E',
                    accentLight: '#FBF5EE',
                },
            },
            letterSpacing: {
                tightest: '-0.035em',
                tighter: '-0.02em',
                wide: '0.04em',
                widest: '0.16em',
            },
            boxShadow: {
                'apple-card': '0 4px 24px -2px rgba(0, 0, 0, 0.04), 0 2px 6px -1px rgba(0, 0, 0, 0.02)',
                'apple-card-hover': '0 20px 40px -10px rgba(0, 0, 0, 0.08), 0 4px 12px -2px rgba(0, 0, 0, 0.04)',
                'apple-pill': '0 2px 10px rgba(0, 0, 0, 0.06)',
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
