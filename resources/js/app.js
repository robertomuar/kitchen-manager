// resources/js/app.js

import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp, Link } from '@inertiajs/vue3';

const appName = import.meta.env.VITE_APP_NAME || 'KitchenManager';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
        return pages[`./Pages/${name}.vue`];
    },
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) });

        vueApp.use(plugin);

        // Registrar Link globalmente
        vueApp.component('Link', Link);

        /**
         * Helper route(...) MUY SIMPLE para no depender de Ziggy
         * y que no reviente los componentes que ya lo usan.
         *
         * - Si le pasas '/login' => devuelve '/login'
         * - Si le pasas 'login'  => lo traduce a '/login'
         * - Si le pasas URL completa http/https => la deja tal cual
         */
        const simpleRoute = (nameOrPath) => {
            if (!nameOrPath) {
                return '/';
            }

            // Si ya es URL absoluta o path absoluto, la devolvemos tal cual
            if (
                nameOrPath.startsWith('http://') ||
                nameOrPath.startsWith('https://') ||
                nameOrPath.startsWith('/')
            ) {
                return nameOrPath;
            }

            // Mapa de rutas por nombre (lo típico de Breeze + lo tuyo)
            const map = {
                // Auth
                login: '/login',
                register: '/register',
                'password.request': '/forgot-password',
                'password.email': '/forgot-password',
                'password.reset': '/reset-password',
                'password.store': '/reset-password',
                'verification.notice': '/verify-email',
                'verification.send': '/email/verification-notification',
                'profile.edit': '/profile',
                'profile.update': '/profile',
                'profile.destroy': '/profile',

                // App principal
                dashboard: '/dashboard',
                'products.index': '/products',
                'products.create': '/products/create',
                'locations.index': '/locations',
                'locations.create': '/locations/create',
            };

            // Si existe en el mapa, la usamos; si no, devolvemos "/nombre"
            return map[nameOrPath] || `/${nameOrPath}`;
        };

        vueApp.config.globalProperties.route = simpleRoute;
        window.route = simpleRoute;

        vueApp.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
