import { defineConfig, splitVendorChunkPlugin } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        splitVendorChunkPlugin(),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (!id.includes('node_modules')) {
                        return undefined;
                    }

                    if (id.includes('vue')) {
                        return 'vue';
                    }

                    if (id.includes('@inertiajs')) {
                        return 'inertia';
                    }

                    if (id.includes('axios')) {
                        return 'axios';
                    }

                    return 'vendor';
                },
            },
        },
    },
});
