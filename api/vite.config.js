import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/site.css',
                'resources/js/site.js',
                'resources/css/admin.css',
                'resources/js/admin.js',
            ],
            refresh: true,
            fonts: [
                bunny('Rethink Sans', { weights: [400, 500, 600, 700] }),
                bunny('Instrument Serif', { weights: [400], styles: ['normal', 'italic'] }),
                bunny('Figtree', { weights: [400, 500, 600, 700] }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
