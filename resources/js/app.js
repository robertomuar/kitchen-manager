// resources/js/app.js

import './bootstrap';
import { getCsrfToken, syncCsrf } from './bootstrap';
import '../css/app.css';

import { createApp } from 'vue';
import { createInertiaApp, Link, router } from '@inertiajs/vue3';
import Root from './Root.vue';
// ✅ 1) Limpia _token/_method de la URL actual (por si venías con ?_token=...)
try {
  const u = new URL(window.location.href);
  if (u.searchParams.has('_token') || u.searchParams.has('_method')) {
    u.searchParams.delete('_token');
    u.searchParams.delete('_method');
    const qs = u.searchParams.toString();
    window.history.replaceState({}, document.title, u.pathname + (qs ? `?${qs}` : '') + u.hash);
  }
} catch (e) {}

// ✅ 2) Limpia defaults de Ziggy para que route() NO vuelva a añadir _token nunca
try {
  const ziggy = window.Ziggy; // Ziggy suele estar aquí si tienes @routes o similar
  if (ziggy && ziggy.defaults) {
    delete ziggy.defaults._token;
    delete ziggy.defaults._method;
  }
} catch (e) {}


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
    'password.reset': (token) =>
        `/reset-password/${encodeURIComponent(normalizeId(token))}`,
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

    // --- Legal ---
    terms: () => '/terms',
    'cookies-policy': () => '/cookies-policy',
    privacy: () => '/privacy-policy',

    // --- Productos ---
    'products.index': () => '/products',
    'products.create': () => '/products/create',
    'products.store': () => '/products',
    'products.edit': (product) =>
        `/products/${encodeURIComponent(normalizeId(product))}/edit`,
    'products.update': (product) =>
        `/products/${encodeURIComponent(normalizeId(product))}`,
    'products.destroy': (product) =>
        `/products/${encodeURIComponent(normalizeId(product))}`,
    'products.options': () => '/products/options',

    // --- Stock ---
    'stock.index': () => '/stock',
    'stock.create': () => '/stock/create',
    'stock.store': () => '/stock',
    'stock.edit': (item) =>
        `/stock/${encodeURIComponent(normalizeId(item))}/edit`,
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
        `/locations/${encodeURIComponent(normalizeId(location))}/edit`,
    'locations.update': (location) =>
        `/locations/${encodeURIComponent(normalizeId(location))}`,
    'locations.destroy': (location) =>
        `/locations/${encodeURIComponent(normalizeId(location))}`,
    'locations.options': () => '/locations/options',

    // --- Compartir cocina ---
    'kitchen.share.store': () => '/kitchen/share',
    'kitchen.share.destroy': (share) =>
        `/kitchen/share/${encodeURIComponent(normalizeId(share))}`,

    // --- Lookup código de barras ---
    'barcode.lookup': () => '/barcode/lookup',
};

const extractAuthUserId = (page) => page?.props?.auth?.user?.id ?? null;
let authStateInitialized = false;
let lastAuthUserId = null;

const refreshPageIfAuthChanged = (page) => {
    if (!page) return;

    const currentUserId = extractAuthUserId(page);

    if (!authStateInitialized) {
        authStateInitialized = true;
        lastAuthUserId = currentUserId;
        syncCsrf();
        return;
    }

    const hasChanged = lastAuthUserId !== currentUserId;

    lastAuthUserId = currentUserId;
    syncCsrf();

    if (hasChanged) {
        window.location.reload();
    }
};

const fullReloadPaths = new Set([
    '/stock/export/missing.csv',
    '/stock/export/missing.pdf',
]);

/**
 * Solo forzar reload completo para rutas de exportación.
 */
const forceFullReloadForExports = (event) => {
    const visit = event?.detail?.visit ?? event;
    if (!visit) return;

    if ((visit.method ?? 'get').toLowerCase() !== 'get') return;

    const targetUrl = toRelativeUrl(visit.url ?? '');
    if (!targetUrl) return;

    const targetPath = targetUrl.split('?')[0];

    if (!fullReloadPaths.has(targetPath)) return;

    event?.preventDefault?.();
    window.location.assign(targetUrl);
};

// Aseguramos que todas las peticiones de Inertia incluyan el token CSRF.
// Además de la cabecera, añadimos `_token` al payload cuando Inertia envía
// formularios JSON/FormData para evitar errores 419.
const applyCsrfToVisit = (visit) => {
  const csrfToken = getCsrfToken();
  const method = (visit.method ?? 'get').toLowerCase();
  const headers = { ...(visit.headers || {}) };

  // ✅ Header CSRF (para POST/PUT/PATCH/DELETE)
  if (csrfToken && !headers['X-CSRF-TOKEN']) {
    headers['X-CSRF-TOKEN'] = csrfToken;
  }

  // ✅ Nunca añadir _token a GET (si no, acaba en la URL)
  if (csrfToken && method !== 'get' && visit.data !== undefined) {
    if (visit.data instanceof FormData) {
      if (!visit.data.has('_token')) visit.data.set('_token', csrfToken);
    } else if (typeof visit.data === 'object' && visit.data !== null) {
      if (!('_token' in visit.data)) visit.data._token = csrfToken;
    }
  }

  return { ...visit, headers };
};

if (getCsrfToken()) {
    router.on('before', (event) => {
        const visit = event?.detail?.visit ?? event;
        if (!visit) return;

        applyCsrfToVisit(visit);
    });
} else {
    console.error(
        'Token CSRF no encontrado. Verifica que <meta name="csrf-token" ...> exista en el layout.'
    );
}

router.on('before', forceFullReloadForExports);

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

        const normalized = path.replace(/\/+$/, '') || '/';

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

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue');
        return pages[`./Pages/${name}.vue`]();
    },
    setup({ el, App, props, plugin }) {
        const initialPage =
            props?.initialPage ??
            (() => {
                try {
                    return JSON.parse(el?.dataset?.page ?? '{}');
                } catch (error) {
                    console.warn('No se pudo leer la página inicial', error);
                    return null;
                }
            })();

        refreshPageIfAuthChanged(initialPage);

        const vueApp = createApp(Root, { App, props });

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

router.on('success', (event) => {
    const page = event?.detail?.page ?? event;
    refreshPageIfAuthChanged(page);
});
