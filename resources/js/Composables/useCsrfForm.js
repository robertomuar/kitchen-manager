// resources/js/Composables/useCsrfForm.js
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';

/**
 * Refresca el token CSRF llamando a Sanctum.
 * Si usas Sanctum, existe /sanctum/csrf-cookie.
 * Si no lo usas, puedes dejar la llamada como está (no rompe nada).
 */
async function refreshCsrf() {
    try {
        await axios.get('/sanctum/csrf-cookie');
    } catch (error) {
        console.error('No se pudo refrescar el token CSRF:', error);
    }
}

/**
 * Envoltorio de useForm que refresca CSRF antes de POST / PUT / PATCH / DELETE.
 */
export function useCsrfForm(initialData) {
    const form = useForm(initialData);

    const originalPost = form.post.bind(form);
    const originalPut = form.put.bind(form);
    const originalPatch = form.patch.bind(form);
    const originalDelete = form.delete.bind(form);

    form.post = async (url, options = {}) => {
        await refreshCsrf();
        return originalPost(url, options);
    };

    form.put = async (url, options = {}) => {
        await refreshCsrf();
        return originalPut(url, options);
    };

    form.patch = async (url, options = {}) => {
        await refreshCsrf();
        return originalPatch(url, options);
    };

    form.delete = async (url, options = {}) => {
        await refreshCsrf();
        return originalDelete(url, options);
    };

    return form;
}
