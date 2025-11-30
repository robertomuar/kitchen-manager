// resources/js/app.js

import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp, Link } from '@inertiajs/vue3';

const originalRoute = typeof route !== 'undefined' ? route : null;

const toRelativeUrl = (value) => {
    if (!value) return '';
    if (typeof value !== 'string') return '';
    if (value.startsWith('/')) return value;

    try {
        const parsed = new URL(value, window.location.origin);
        return `${parsed.pathname}${parsed.search}${parsed.hash}`;
    } catch (error) {
        console.warn('[route] No se pudo normalizar la URL', value, error);
        return value;
    }
};

const appName = import.meta.env.VITE_APP_NAME || 'KitchenManager';

/**
 * Mapa simple de rutas -> paths.
 * NO usa dominios absolutos, solo rutas relativas para evitar Mixed Content.
 */
const normalizeId = (params) => {
    if (params === undefined || params === null) return '';
    if (typeof params === 'string' || typeof params === 'number') {
        return String(params);
    }
    if (typeof params === 'object') {
        if (params.id !== undefined && params.id !== null) return String(params.id);
        if (params.value !== undefined && params.value !== null) return String(params.value);
    }
    return '';
};

const routeMap = {
    // --- Auth ---
    login: () => '/login',
    register: () => '/register',
    'password.request': () => '/forgot-password',
    'password.email': () => '/forgot-password',
    'password.reset': (token) => `/reset-password/${encodeURIComponent(normalizeId(token))}`,
    'password.store': () => '/reset-password',
    'verification.notice': () => '/verify-email',
    'verification.send': () => '/email/verification-notification',
    'password.confirm': () => '/confirm-password',
    'password.update': () => '/password',
    logout: () => '/logout',

    // --- Perfil ---
    'profile.edit': () => '/profile',
    'profile.update': () => '/profile',
    'profile.destroy': () => '/profile',

    // --- Dashboard ---
    dashboard: () => '/dashboard',

    // --- Productos ---
    'products.index': () => '/products',
    'products.create': () => '/products/create',
    'products.store': () => '/products',
    'products.edit': (product) =>
        `/products/${encodeURIComponent(normalizeId(product))}`,
    'products.update': (product) =>
        `/products/${encodeURIComponent(normalizeId(product))}`,
    'products.destroy': (product) =>
        `/products/${encodeURIComponent(normalizeId(product))}`,

    // --- Stock ---
    'stock.index': () => '/stock',
    'stock.create': () => '/stock/create',
    'stock.store': () => '/stock',
    'stock.edit': (item) =>
        `/stock/${encodeURIComponent(normalizeId(item))}`,
    'stock.update': (item) =>
        `/stock/${encodeURIComponent(normalizeId(item))}`,
    'stock.destroy': (item) =>
        `/stock/${encodeURIComponent(normalizeId(item))}`,
    'stock.replenishment.export': () => '/stock/replenishment/export',

    // --- Ubicaciones ---
    'locations.index': () => '/locations',
    'locations.create': () => '/locations/create',
    'locations.store': () => '/locations',
    'locations.edit': (location) =>
        `/locations/${encodeURIComponent(normalizeId(location))}`,
    'locations.update': (location) =>
        `/locations/${encodeURIComponent(normalizeId(location))}`,
    'locations.destroy': (location) =>
        `/locations/${encodeURIComponent(normalizeId(location))}`,

    // --- Compartir cocina ---
    'kitchen.share.store': () => '/kitchen/share',
    'kitchen.share.destroy': (share) =>
        `/kitchen/share/${encodeURIComponent(normalizeId(share))}`,

    // --- Lookup código de barras ---
    'barcode.lookup': () => '/barcode/lookup',
};

/**
 * Helper global route() compatible con:
 *   - route('login')
 *   - route('products.edit', id)
 *   - route().current('dashboard')
 *   - route().has('locations.index')
 */
const routeFn = (nameOrPath = null, params = null, absolute = false) => {
    // Si se llama sin argumentos: route().current(...)
    if (!nameOrPath) {
        return routeFn;
    }

    // Si ya nos pasan una URL absoluta o relativa, la respetamos
    if (
        typeof nameOrPath === 'string' &&
        (nameOrPath.startsWith('http://') ||
            nameOrPath.startsWith('https://') ||
            nameOrPath.startsWith('/'))
    ) {
        return absolute ? nameOrPath : toRelativeUrl(nameOrPath);
    }

    if (originalRoute) {
        const url = originalRoute(nameOrPath, params, true);
        return absolute ? url : toRelativeUrl(url);
    }

    const builder = routeMap[nameOrPath];

    if (typeof builder === 'function') {
        return builder(params);
    }

    if (typeof builder === 'string') {
        return builder;
    }

    // Fallback: asumimos que el nombre es un path.
    const path = `/${nameOrPath}`;
    return absolute
        ? new URL(path, window.location.origin).toString()
        : path;
};

// route().current('dashboard') o route().current(['products.index', 'products.create'])
routeFn.current = (names) => {
    if (!names) return false;

    const list = Array.isArray(names) ? names : [names];
    const currentPath =
        toRelativeUrl(window.location.pathname).replace(/\/+$/, '') || '/';

    return list.some((name) => {
        const path = routeFn(name);
        if (!path) return false;

        const normalized =
            path.replace(/\/+$/, '') || '/';

        return (
            currentPath === normalized ||
            (normalized !== '/' &&
                currentPath.startsWith(normalized + '/'))
        );
    });
};

// route().has('locations.index')
routeFn.has = (name) => {
    if (originalRoute?.has) return originalRoute.has(name);

    return Object.prototype.hasOwnProperty.call(routeMap, name);
};

// lo colgamos de Vue y del window, como hacía Ziggy
createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue', {
            eager: true,
        });
        return pages[`./Pages/${name}.vue`];
    },
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) });

        vueApp.use(plugin);
        vueApp.component('Link', Link);

        vueApp.config.globalProperties.route = routeFn;
        window.route = routeFn;

        vueApp.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
