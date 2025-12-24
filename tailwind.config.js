import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                // Kassako Brand Colors
                'kassako': {
                    'forest': '#1A3D2E',        // Primary - Deep forest green
                    'gold': '#C4A962',          // Accent - Warm gold
                    'cream': '#FDFBF7',         // Background - Warm cream
                    'text': '#1C1C1C',          // Text - Near black
                    'muted': '#6B6B6B',         // Muted - Gray
                    'success': '#2D7A4F',       // Success - Green
                    'warning': '#D97706',       // Warning - Amber
                    'danger': '#DC2626',        // Danger - Red
                },
                // Extended palette for UI elements
                'forest': {
                    50: '#f0f7f4',
                    100: '#dceee5',
                    200: '#bbddcd',
                    300: '#8ec4ab',
                    400: '#5fa586',
                    500: '#3d8a6a',
                    600: '#2d7054',
                    700: '#1A3D2E',              // Primary
                    800: '#1a3d2e',
                    900: '#15322a',
                    950: '#0b1c17',
                },
                'gold': {
                    50: '#fdfaef',
                    100: '#f9f2d8',
                    200: '#f2e3af',
                    300: '#e9cf7d',
                    400: '#C4A962',              // Accent
                    500: '#d4a23b',
                    600: '#be8a2a',
                    700: '#9e6b24',
                    800: '#815523',
                    900: '#6b4720',
                    950: '#3d250f',
                },
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
            },
            fontSize: {
                // Custom type scale for landing page
                'hero': ['4.5rem', { lineHeight: '1.1', letterSpacing: '-0.02em', fontWeight: '700' }],
                'hero-mobile': ['2.5rem', { lineHeight: '1.15', letterSpacing: '-0.02em', fontWeight: '700' }],
                'display': ['3.5rem', { lineHeight: '1.15', letterSpacing: '-0.02em', fontWeight: '600' }],
                'display-mobile': ['2rem', { lineHeight: '1.2', letterSpacing: '-0.01em', fontWeight: '600' }],
                'section': ['2.25rem', { lineHeight: '1.25', letterSpacing: '-0.01em', fontWeight: '600' }],
                'section-mobile': ['1.75rem', { lineHeight: '1.3', letterSpacing: '-0.01em', fontWeight: '600' }],
            },
            spacing: {
                // Safe area insets for mobile
                'safe-top': 'env(safe-area-inset-top)',
                'safe-bottom': 'env(safe-area-inset-bottom)',
                'safe-left': 'env(safe-area-inset-left)',
                'safe-right': 'env(safe-area-inset-right)',
                // Section spacing
                '18': '4.5rem',
                '22': '5.5rem',
                '30': '7.5rem',
            },
            borderRadius: {
                '4xl': '2rem',
                '5xl': '2.5rem',
            },
            boxShadow: {
                'card': '0 1px 3px 0 rgb(0 0 0 / 0.05), 0 1px 2px -1px rgb(0 0 0 / 0.05)',
                'card-hover': '0 10px 15px -3px rgb(0 0 0 / 0.08), 0 4px 6px -4px rgb(0 0 0 / 0.05)',
                'elevated': '0 20px 25px -5px rgb(0 0 0 / 0.08), 0 8px 10px -6px rgb(0 0 0 / 0.05)',
                'glow-gold': '0 0 40px -8px rgba(196, 169, 98, 0.4)',
                'glow-forest': '0 0 40px -8px rgba(26, 61, 46, 0.3)',
            },
            animation: {
                'count-up': 'countUp 2s ease-out forwards',
                'fade-in-up': 'fadeInUp 0.6s ease-out forwards',
                'fade-in': 'fadeIn 0.4s ease-out forwards',
                'slide-in-left': 'slideInLeft 0.5s ease-out forwards',
                'slide-in-right': 'slideInRight 0.5s ease-out forwards',
                'pulse-soft': 'pulseSoft 3s ease-in-out infinite',
                'float': 'float 6s ease-in-out infinite',
            },
            keyframes: {
                countUp: {
                    '0%': { opacity: '0', transform: 'translateY(20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                fadeInUp: {
                    '0%': { opacity: '0', transform: 'translateY(30px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideInLeft: {
                    '0%': { opacity: '0', transform: 'translateX(-30px)' },
                    '100%': { opacity: '1', transform: 'translateX(0)' },
                },
                slideInRight: {
                    '0%': { opacity: '0', transform: 'translateX(30px)' },
                    '100%': { opacity: '1', transform: 'translateX(0)' },
                },
                pulseSoft: {
                    '0%, 100%': { opacity: '1' },
                    '50%': { opacity: '0.85' },
                },
                float: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-10px)' },
                },
            },
            transitionTimingFunction: {
                'bounce-sm': 'cubic-bezier(0.4, 0, 0.2, 1.15)',
            },
        },
    },

    plugins: [forms, typography],
};
