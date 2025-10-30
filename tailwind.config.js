import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

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
            fontFamily: {
                sans: ['Montserrat', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Material Design 3 Color System
                primary: {
                    50: '#e3f2fd',
                    100: '#bbdefb',
                    200: '#90caf9',
                    300: '#64b5f6',
                    400: '#42a5f5',
                    500: '#176BEF', // Main primary
                    600: '#1557D6',
                    700: '#0D47A1',
                    800: '#0a3d8a',
                    900: '#062e66',
                },
                secondary: {
                    50: '#f3e5f5',
                    100: '#e1bee7',
                    200: '#ce93d8',
                    300: '#ba68c8',
                    400: '#ab47bc',
                    500: '#9c27b0',
                    600: '#8e24aa',
                    700: '#7b1fa2',
                    800: '#6a1b9a',
                    900: '#4a148c',
                },
                surface: {
                    50: '#fafafa',
                    100: '#f5f5f5',
                    200: '#eeeeee',
                    300: '#e0e0e0',
                    400: '#bdbdbd',
                    500: '#9e9e9e',
                    600: '#757575',
                    700: '#616161',
                    800: '#424242',
                    900: '#212121',
                },
                error: {
                    50: '#ffebee',
                    100: '#ffcdd2',
                    200: '#ef9a9a',
                    300: '#e57373',
                    400: '#ef5350',
                    500: '#FF3E30',
                    600: '#e53935',
                    700: '#d32f2f',
                    800: '#c62828',
                    900: '#b71c1c',
                },
                success: {
                    50: '#e8f5e8',
                    100: '#c8e6c9',
                    200: '#a5d6a7',
                    300: '#81c784',
                    400: '#66bb6a',
                    500: '#179C52',
                    600: '#43a047',
                    700: '#388e3c',
                    800: '#2e7d32',
                    900: '#1b5e20',
                },
                warning: {
                    50: '#fff8e1',
                    100: '#ffecb3',
                    200: '#ffe082',
                    300: '#ffd54f',
                    400: '#ffca28',
                    500: '#F7B529',
                    600: '#ffb300',
                    700: '#ffa000',
                    800: '#ff8f00',
                    900: '#ff6f00',
                },
            },
            spacing: {
                '18': '4.5rem',
                '88': '22rem',
                '128': '32rem',
            },
            maxWidth: {
                '8xl': '88rem',
                '9xl': '96rem',
            },
            borderRadius: {
                'xl': '0.75rem',
                '2xl': '1rem',
                '3xl': '1.5rem',
            },
            boxShadow: {
                'material': '0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24)',
                'material-lg': '0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23)',
                'material-xl': '0 10px 20px rgba(0,0,0,0.19), 0 6px 6px rgba(0,0,0,0.23)',
            },
            animation: {
                'fade-in-up': 'fadeInUp 0.3s ease-out',
                'fade-in-down': 'fadeInDown 0.3s ease-out',
                'scale-in': 'scaleIn 0.2s ease-out',
                'ripple': 'ripple 0.6s linear',
                'skeleton': 'skeleton-loading 1.2s infinite',
            },
            keyframes: {
                fadeInUp: {
                    '0%': { opacity: '0', transform: 'translateY(20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                fadeInDown: {
                    '0%': { opacity: '0', transform: 'translateY(-20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                scaleIn: {
                    '0%': { opacity: '0', transform: 'scale(0.9)' },
                    '100%': { opacity: '1', transform: 'scale(1)' },
                },
                ripple: {
                    '0%': { transform: 'scale(0)', opacity: '0.6' },
                    '100%': { transform: 'scale(4)', opacity: '0' },
                },
                'skeleton-loading': {
                    '0%': { transform: 'translateX(-100%)' },
                    '100%': { transform: 'translateX(100%)' },
                },
            },
        },
    },

    plugins: [forms],
};
