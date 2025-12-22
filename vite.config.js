import { defineConfig, splitVendorChunkPlugin } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        splitVendorChunkPlugin(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    build: {
        sourcemap: false,
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (!id.includes('node_modules')) {
                        return;
                    }

                    if (id.includes('html5-qrcode')) {
                        return 'html5-qrcode';
                    }

                    if (
                        id.includes('vue') ||
                        id.includes('@inertiajs') ||
                        id.includes('ziggy')
                    ) {
                        return 'framework';
                    }

                    if (id.includes('axios')) {
                        return 'http';
                    }

                    return 'vendor';
                },
            },
        },
    },
});
