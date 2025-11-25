// resources/js/bootstrap.js

import axios from 'axios';

window.axios = axios;

// Todas las peticiones serán AJAX
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// CSRF token desde la cabecera <meta>
const token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error(
        'CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token',
    );
}
