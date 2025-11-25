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

        /**
         * Mapa mínimo de rutas de Laravel que usamos en el frontend.
         * Todo son rutas RELATIVAS para evitar Mixed Content.
         */
        const routeMap = {
            // --- Auth ---
            login: '/login',
            register: '/register',
            'password.request': '/forgot-password',
            'password.reset': '/reset-password/:token',
            'verification.notice': '/verify-email',
            'verification.send': '/email/verification-notification',
            'password.confirm': '/confirm-password',
            'password.update': '/password',
            'profile.edit': '/profile',
            'profile.update': '/profile',
            'profile.destroy': '/profile',
            logout: '/logout',

            // --- Dashboard ---
            dashboard: '/dashboard',

            // --- Productos ---
            'products.index': '/products',
            'products.create': '/products/create',
            'products.edit': '/products/:product/edit',
            'products.store': '/products',
            'products.update': '/products/:product',
            'products.destroy': '/products/:product',

            // --- Stock ---
            'stock.index': '/stock',
            'stock.create': '/stock/create',
            'stock.edit': '/stock/:stockItem/edit',
            'stock.store': '/stock',
            'stock.update': '/stock/:stockItem',
            'stock.destroy': '/stock/:stockItem',
            'stock.replenishment.export': '/stock/replenishment/export',

            // --- Ubicaciones ---
            'locations.index': '/locations',
            'locations.create': '/locations/create',
            'locations.edit': '/locations/:location/edit',
            'locations.store': '/locations',
            'locations.update': '/locations/:location',
            'locations.destroy': '/locations/:location',

            // --- Compartir cocina ---
            'kitchen.share.store': '/kitchen/share',
            'kitchen.share.destroy': '/kitchen/share/:share',

            // --- Lookup código de barras ---
            'barcode.lookup': '/barcode/lookup',
        };

        /**
         * Construye la URL a partir del nombre de ruta y parámetros.
         * Soporta:
         *   route('products.edit', 5)
         *   route('products.edit', { product: 5 })
         */
        const buildPath = (name, params = null) => {
            const template = routeMap[name];

            if (!template) {
                // Fallback: si no conocemos el nombre, devolvemos "/nombre"
                return `/${name}`;
            }

            // Si la ruta no tiene placeholders, la devolvemos tal cual
            if (!template.includes(':')) {
                return template;
            }

            let url = template;

            // Permitir route('ruta', id) o route('ruta', { clave: id })
            if (params !== null && params !== undefined) {
                if (typeof params !== 'object' || Array.isArray(params)) {
                    // Usamos el primer placeholder que aparezca
                    const match = url.match(/:([A-Za-z_]+)/);
                    if (match) {
                        url = url.replace(
                            `:${match[1]}`,
                            encodeURIComponent(
                                Array.isArray(params) ? params[0] : params,
                            ),
                        );
                    }
                } else {
                    Object.entries(params).forEach(([key, value]) => {
                        url = url.replace(
                            `:${key}`,
                            encodeURIComponent(value),
                        );
                    });
                }
            }

            // Si quedara algún placeholder sin rellenar, lo limpiamos
            return url.replace(/:([A-Za-z_]+)/g, '');
        };

        /**
         * Helper route “tipo Ziggy” muy simplificado.
         *
         * Uso:
         *   route('login')
         *   route('products.edit', 5)
         *   route().current('dashboard')
         *   route().has('locations.index')
         */
        function route(nameOrPath, params = null) {
            // Compatibilidad con route().current(...) y route().has(...)
            if (nameOrPath === undefined || nameOrPath === null) {
                return {
                    current: (name) => route.current(name),
                    has: (name) => route.has(name),
                };
            }

            if (typeof nameOrPath !== 'string') {
                return '/';
            }

            // Si ya es URL absoluta o path, lo devolvemos tal cual
            if (
                nameOrPath.startsWith('http://') ||
                nameOrPath.startsWith('https://') ||
                nameOrPath.startsWith('/')
            ) {
                return nameOrPath;
            }

            // Nombre de ruta de Laravel
            return buildPath(nameOrPath, params);
        }

        route.has = (name) =>
            Object.prototype.hasOwnProperty.call(routeMap, name);

        route.current = (name) => {
            const target = buildPath(name);

            if (typeof window === 'undefined') {
                return false;
            }

            const currentPath =
                window.location.pathname.replace(/\/+$/, '') || '/';
            const targetPath = target.replace(/\/+$/, '') || '/';

            return currentPath === targetPath;
        };

        // Disponible en los componentes Vue y como window.route (como Ziggy)
        vueApp.config.globalProperties.route = route;
        window.route = route;

        vueApp.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
