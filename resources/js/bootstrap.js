// resources/js/bootstrap.js

import axios from 'axios';

window.axios = axios;

// Peticiones AJAX estándar
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// CSRF
const token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error(
        'CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token',
    );
}

// IMPORTANTE: no ponemos axios.defaults.baseURL.
// Así usará https://kitchenmanager.duckdns.org/login automáticamente
// y no mezclará http/https.
