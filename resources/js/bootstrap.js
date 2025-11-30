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

// NO añadimos manualmente X-CSRF-TOKEN, Laravel + axios
// ya se entienden vía cookie XSRF-TOKEN -> cabecera X-XSRF-TOKEN.
