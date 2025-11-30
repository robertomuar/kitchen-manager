// resources/js/bootstrap.js

import 'vite/modulepreload-polyfill';

import axios from 'axios';

window.axios = axios;

// Cabecera estándar para peticiones AJAX
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Usar cookies de la misma origin (sesión + XSRF-TOKEN)
window.axios.defaults.withCredentials = true;

// Fuerza a usar siempre el mismo origen que la página actual para evitar
// peticiones mixtas http/https (Mixed Content) en producción. En local se
// mantendrá http porque window.location.origin reflejará ese protocolo.
window.axios.defaults.baseURL = window.location.origin;

// Aseguramos el token CSRF en todas las peticiones.
const csrfToken = document.head.querySelector('meta[name="csrf-token"]');

if (csrfToken?.content) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.content;
} else {
    console.error(
        'Token CSRF no encontrado. Verifica que <meta name="csrf-token" ...> exista en el layout.'
    );
}
