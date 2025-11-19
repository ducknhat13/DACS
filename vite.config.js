import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            onwarn(warning, warn) {
                // Suppress CSS syntax warnings từ esbuild (không ảnh hưởng đến build)
                if (warning.message && warning.message.includes('css-syntax-error')) {
                    return;
                }
                warn(warning);
            },
        },
    },
});
