// resources/js/bootstrap.js

import 'vite/modulepreload-polyfill';
import axios from 'axios';

// Hacemos Axios global para que Inertia lo use
window.axios = axios;

// Cabecera estándar para peticiones AJAX
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Usar siempre mismo origen que la página (evita Mixed Content http/https)
window.axios.defaults.baseURL = window.location.origin;

// En caso de que uses cookies de sesión, esto permite que viajen
window.axios.defaults.withCredentials = true;

/**
 * Lee el token CSRF del <meta name="csrf-token" ...> del layout Blade.
 */
const readMetaCsrfToken = () => {
    const element = document.head.querySelector('meta[name="csrf-token"]');
    return element?.getAttribute('content')?.trim() || null;
};

/**
 * Devuelve el token CSRF actual (si existe).
 */
export const getCsrfToken = () => readMetaCsrfToken();

/**
 * Sincroniza el token CSRF desde el meta al header global de Axios.
 * La usamos al arrancar la app y cuando cambie el usuario autenticado.
 */
export const syncCsrf = () => {
    const token = readMetaCsrfToken();

    if (!token) {
        console.error(
            'Token CSRF no encontrado. Asegúrate de que <meta name="csrf-token" ...> existe en el layout.'
        );
        return null;
    }

    // Cabecera que Laravel espera por defecto
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;

    return token;
};

// Inicializamos el header CSRF al arrancar
const initialToken = syncCsrf();
if (!initialToken) {
    console.warn('No se pudo inicializar el token CSRF en Axios.');
}
