// resources/js/app.js

import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';

const appName = import.meta.env.VITE_APP_NAME || 'KitchenManager';

/*
|--------------------------------------------------------------------------
| Mapa de rutas (solo paths relativos)
|--------------------------------------------------------------------------
*/
const namedRoutes = {
    // Auth
    login: '/login',
    register: '/register',
    'password.request': '/forgot-password',
    'password.email': '/forgot-password',
    'password.reset': '/reset-password',
    'password.store': '/reset-password',
    'verification.notice': '/verify-email',
    'verification.send': '/email/verification-notification',
    'verification.verify': '/verify-email',
    logout: '/logout',

    // Dashboard
    dashboard: '/dashboard',

    // Productos
    'products.index': '/products',
    'products.create': '/products/create',
    'products.edit': ({ product }) => `/products/${product}/edit`,
    'products.store': '/products',
    'products.update': ({ product }) => `/products/${product}`,
    'products.destroy': ({ product }) => `/products/${product}`,

    // Stock
    'stock.index': '/stock',
    'stock.create': '/stock/create',
    'stock.edit': ({ stockItem }) => `/stock/${stockItem}/edit`,
    'stock.store': '/stock',
    'stock.update': ({ stockItem }) => `/stock/${stockItem}`,
    'stock.destroy': ({ stockItem }) => `/stock/${stockItem}`,
    'stock.replenishment.export': '/stock/replenishment/export',

    // Ubicaciones
    'locations.index': '/locations',
    'locations.create': '/locations/create',
    'locations.edit': ({ location }) => `/locations/${location}/edit`,
    'locations.store': '/locations',
    'locations.update': ({ location }) => `/locations/${location}`,
    'locations.destroy': ({ location }) => `/locations/${location}`,

    // Perfil
    'profile.edit': '/profile',
    'profile.update': '/profile',
    'profile.destroy': '/profile',

    // Compartir cocina
    'kitchen.share.store': '/kitchen/share',
    'kitchen.share.destroy': ({ share }) => `/kitchen/share/${share}`,

    // Código de barras
    'barcode.lookup': '/barcode/lookup',
};

/*
|--------------------------------------------------------------------------
| Normalizar parámetros cuando se pasa solo un ID
|--------------------------------------------------------------------------
|
| Ej:
|   route('products.edit', 5)
|   route('stock.edit', stockItem.id)
|
*/
function normalizeParams(name, rawParams) {
    if (rawParams == null) {
        return {};
    }

    // Si ya es objeto, lo devolvemos tal cual
    if (typeof rawParams === 'object') {
        return rawParams;
    }

    // Si es un ID simple (string/number) decidimos la key según la ruta
    switch (name) {
        case 'products.edit':
        case 'products.update':
        case 'products.destroy':
            return { product: rawParams };

        case 'stock.edit':
        case 'stock.update':
        case 'stock.destroy':
            return { stockItem: rawParams };

        case 'locations.edit':
        case 'locations.update':
        case 'locations.destroy':
            return { location: rawParams };

        case 'kitchen.share.destroy':
            return { share: rawParams };

        default:
            // fallback genérico
            return { id: rawParams };
    }
}

/*
|--------------------------------------------------------------------------
| Construir la URL relativa
|--------------------------------------------------------------------------
*/
function buildUrl(name, params = {}) {
    const def = namedRoutes[name];

    if (!def) {
        console.warn(`Ruta '${name}' no definida en namedRoutes.`);
        return '#';
    }

    let path;

    if (typeof def === 'string') {
        path = def;
    } else if (typeof def === 'function') {
        path = def(params);
    } else {
        console.warn(`Definición de ruta '${name}' no válida.`);
        return '#';
    }

    // IMPORTANTE: siempre devolvemos path relativo
    // Nada de http:// ni https:// para evitar Mixed Content
    return path;
}

/*
|--------------------------------------------------------------------------
| Helper global route()
|--------------------------------------------------------------------------
*/
function route(name, rawParams = {}) {
    const params = normalizeParams(name, rawParams);
    return buildUrl(name, params);
}

/*
|--------------------------------------------------------------------------
| Inertia + Vue
|--------------------------------------------------------------------------
*/
createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
        return pages[`./Pages/${name}.vue`];
    },
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) });

        vueApp.use(plugin);

        // route() disponible en todos los componentes y en window
        vueApp.config.globalProperties.route = route;
        window.route = route;

        vueApp.mount(el);
    },
    progress: {
        color: '#4f46e5',
    },
});
