// resources/js/bootstrap.js

import 'vite/modulepreload-polyfill';

import axios from 'axios';

const readMetaCsrfToken = () => {
    const token = document.head.querySelector('meta[name="csrf-token"]')?.content?.trim();
    return token || null;
};

const readCookieCsrfToken = () => {
    const match = document.cookie.match(/(?:^|; )XSRF-TOKEN=([^;]+)/);
    if (match?.[1]) {
        try {
            return decodeURIComponent(match[1]);
        } catch (error) {
            console.warn('No se pudo decodificar la cookie XSRF-TOKEN', error);
            return match[1];
        }
    }

    return null;
};

const ensureMetaCsrfToken = (token) => {
    if (!token) return null;

    let metaTag = document.head.querySelector('meta[name="csrf-token"]');
    if (!metaTag) {
        metaTag = document.createElement('meta');
        metaTag.setAttribute('name', 'csrf-token');
        document.head.appendChild(metaTag);
    }

    if (metaTag.content !== token) {
        metaTag.setAttribute('content', token);
    }

    return token;
};

const resolveCsrfToken = () => {
    // 1) Token esperado en la etiqueta <meta name="csrf-token" ...>
    const metaToken = readMetaCsrfToken();
    if (metaToken) {
        return metaToken;
    }

    // 2) Fallback defensivo: intenta recuperar el token almacenado en la cookie XSRF-TOKEN
    // (Laravel la envía automáticamente en respuestas web).
    const cookieToken = readCookieCsrfToken();
    if (cookieToken) {
        return ensureMetaCsrfToken(cookieToken);
    }

    console.error(
        'Token CSRF no encontrado. Verifica que <meta name="csrf-token" ...> exista en el layout.'
    );

    return null;
};

const syncCsrfTokenFromCookie = () => {
    const cookieToken = readCookieCsrfToken();

    if (cookieToken) {
        return ensureMetaCsrfToken(cookieToken);
    }

    return null;
};

let csrfRefreshPromise = null;

const refreshCsrfToken = async () => {
    if (csrfRefreshPromise) return csrfRefreshPromise;

    csrfRefreshPromise = (async () => {
        try {
            await axios.get('/sanctum/csrf-cookie', {
                withCredentials: true,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
        } catch (error) {
            console.warn('No se pudo refrescar el token CSRF', error);
            return null;
        } finally {
            csrfRefreshPromise = null;
        }

        return syncCsrfTokenFromCookie() ?? resolveCsrfToken();
    })();

    return csrfRefreshPromise;
};

export const getCsrfToken = () => resolveCsrfToken();
export const csrfToken = getCsrfToken();
export const syncCsrf = () => syncCsrfTokenFromCookie();
export const refreshCsrf = () => refreshCsrfToken();

window.axios = axios;

// Cabecera estándar para peticiones AJAX
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Usar cookies de la misma origin (sesión + XSRF-TOKEN)
window.axios.defaults.withCredentials = true;

// Fuerza a usar siempre el mismo origen que la página actual para evitar
// peticiones mixtas http/https (Mixed Content) en producción. En local se
// mantendrá http porque window.location.origin reflejará ese protocolo.
window.axios.defaults.baseURL = window.location.origin;

// Aseguramos el token CSRF en todas las peticiones axios (auth, formularios, etc.).
window.axios.interceptors.request.use((config) => {
    const token = getCsrfToken();

    if (!config.headers) {
        config.headers = {};
    }

    if (token) {
        config.headers['X-CSRF-TOKEN'] = token;
        config.headers['X-XSRF-TOKEN'] = token;

        // Refuerza el token en el payload para formularios que usan FormData/JSON.
        if (config.data instanceof FormData) {
            if (!config.data.has('_token')) {
                config.data.set('_token', token);
            }
        } else if (config.data && typeof config.data === 'object' && !config.data._token) {
            config.data = { _token: token, ...config.data };
        }
    }

    return config;
});

window.axios.interceptors.response.use(
    (response) => response,
    async (error) => {
        const status = error?.response?.status;
        const originalRequest = error?.config;

        if (status === 419 && originalRequest && !originalRequest._retry) {
            originalRequest._retry = true;

            const token = await refreshCsrfToken();

            if (token) {
                if (!originalRequest.headers) {
                    originalRequest.headers = {};
                }

                originalRequest.headers['X-CSRF-TOKEN'] = token;
                originalRequest.headers['X-XSRF-TOKEN'] = token;

                if (originalRequest.data instanceof FormData) {
                    if (!originalRequest.data.has('_token')) {
                        originalRequest.data.set('_token', token);
                    }
                } else if (
                    originalRequest.data !== null &&
                    typeof originalRequest.data === 'object' &&
                    !originalRequest.data?._token
                ) {
                    originalRequest.data = { _token: token, ...originalRequest.data };
                }

                return window.axios(originalRequest);
            }
        }

        return Promise.reject(error);
    },
);
