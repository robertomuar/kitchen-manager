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
        vueApp.component('Link', Link);

        // Route helper simple para que Welcome, Layout, etc. no revienten
        const simpleRoute = (nameOrPath) => {
            if (!nameOrPath) {
                return '/';
            }

            if (
                nameOrPath.startsWith('http://') ||
                nameOrPath.startsWith('https://') ||
                nameOrPath.startsWith('/')
            ) {
                return nameOrPath;
            }

            const map = {
                login: '/login',
                register: '/register',
                'password.request': '/forgot-password',
                'password.reset': '/reset-password',
                'verification.notice': '/verify-email',
                'verification.send': '/email/verification-notification',
                'profile.edit': '/profile',
                'profile.update': '/profile',
                'profile.destroy': '/profile',

                dashboard: '/dashboard',
                'products.index': '/products',
                'products.create': '/products/create',
                'locations.index': '/locations',
                'locations.create': '/locations/create',
            };

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
