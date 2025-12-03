// resources/js/bootstrap.js

import 'vite/modulepreload-polyfill';

import axios from 'axios';

const resolveCookie = (name) => {
    const match = document.cookie.match(
        // eslint-disable-next-line no-useless-escape
        new RegExp(`(?:^|; )${name.replace(/([.*+?^${}()|[\]\\])/g, '\\$1')}=([^;]+)`) // escape nombre
    );
    return match?.[1] ? decodeURIComponent(match[1]) : null;
};

const resolveCsrfToken = () => {
    const metaToken = document.head.querySelector('meta[name="csrf-token"]')?.content;
    if (metaToken) {
        return metaToken;
    }

    // Fallback defensivo: intenta recuperar el token almacenado en la cookie XSRF-TOKEN
    // (Laravel la envía automáticamente en respuestas web).
    const cookieToken = resolveCookie('XSRF-TOKEN');
    if (cookieToken) {
        return cookieToken;
    }

    console.error(
        'Token CSRF no encontrado. Verifica que <meta name="csrf-token" ...> exista en el layout.'
    );

    return null;
};

export const csrfToken = resolveCsrfToken();
export const xsrfCookieToken = resolveCookie('XSRF-TOKEN');

window.axios = axios;

// Cabecera estándar para peticiones AJAX
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Usar cookies de la misma origin (sesión + XSRF-TOKEN)
window.axios.defaults.withCredentials = true;

// Indicar a axios las cabeceras/cookies de XSRF para que las envíe automáticamente
window.axios.defaults.xsrfCookieName = 'XSRF-TOKEN';
window.axios.defaults.xsrfHeaderName = 'X-XSRF-TOKEN';

// Fuerza a usar siempre el mismo origen que la página actual para evitar
// peticiones mixtas http/https (Mixed Content) en producción. En local se
// mantendrá http porque window.location.origin reflejará ese protocolo.
window.axios.defaults.baseURL = window.location.origin;

// Aseguramos el token CSRF en todas las peticiones axios (auth, formularios, etc.).
if (csrfToken) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
}

// Si existe la cookie XSRF-TOKEN añadimos la cabecera esperada por Laravel Sanctum
if (xsrfCookieToken) {
    window.axios.defaults.headers.common['X-XSRF-TOKEN'] = xsrfCookieToken;
}
