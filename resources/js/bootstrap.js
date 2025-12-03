// resources/js/bootstrap.js

import 'vite/modulepreload-polyfill';

import axios from 'axios';

const resolveCsrfToken = () => {
    const metaToken = document.head.querySelector('meta[name="csrf-token"]')?.content;
    if (metaToken) {
        return metaToken;
    }

    // Fallback defensivo: intenta recuperar el token almacenado en la cookie XSRF-TOKEN
    // (Laravel la envía automáticamente en respuestas web).
    const match = document.cookie.match(/(?:^|; )XSRF-TOKEN=([^;]+)/);
    if (match?.[1]) {
        return decodeURIComponent(match[1]);
    }

    console.error(
        'Token CSRF no encontrado. Verifica que <meta name="csrf-token" ...> exista en el layout.'
    );

    return null;
};

export const csrfToken = resolveCsrfToken();

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
if (csrfToken) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
}
