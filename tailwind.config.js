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
                sufra: {
                    bg: '#F6F1E9',
                    surface: '#FBF8F3',
                    text: '#2B2A27',
                    muted: '#7A756B',
                    border: '#E2DACB',
                    primary: '#4F5D45',
                    'primary-hover': '#424F3A',
                    accent: '#B5573A',
                    'accent-hover': '#9F4B32',
                },
            },
            fontFamily: {
                sans: ['"IBM Plex Sans Arabic"', 'sans-serif'],
                heading: ['"Noto Kufi Arabic"', 'sans-serif'],
            },
            fontSize: {
                // الأحجام المسموحة حصراً: 14 و 16 و 20 و 28 و 40
                '14': ['14px', { lineHeight: '20px' }],
                '16': ['16px', { lineHeight: '24px' }],
                '20': ['20px', { lineHeight: '28px' }],
                '28': ['28px', { lineHeight: '36px' }],
                '40': ['40px', { lineHeight: '48px' }],
                // توافق أسماء Tailwind القياسية مع الأحجام المحددة
                'xs': ['14px', { lineHeight: '20px' }],
                'sm': ['14px', { lineHeight: '20px' }],
                'base': ['16px', { lineHeight: '24px' }],
                'lg': ['20px', { lineHeight: '28px' }],
                'xl': ['28px', { lineHeight: '36px' }],
                '2xl': ['40px', { lineHeight: '48px' }],
            },
            borderRadius: {
                DEFAULT: '6px',
                'none': '0px',
                'sm': '4px',
                'md': '6px',
                'lg': '6px',
                'xl': '6px',
                'full': '9999px',
            },
            maxWidth: {
                'sufra': '1100px',
            },
            boxShadow: {
                'none': 'none',
                DEFAULT: 'none',
                'sm': 'none',
                'md': 'none',
                'lg': 'none',
                'xl': 'none',
            },
        },
    },

    plugins: [forms],
};
