import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/app.js',  // Bootstrap chargé ici
            ],
            refresh: true,
        }),
    ],
server: {
        port: 5174,        // ← port alternatif
        host: '0.0.0.0',   // ← nécessaire pour Docker
    },	
});
