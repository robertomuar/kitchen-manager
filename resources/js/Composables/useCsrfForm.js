import { useForm } from '@inertiajs/vue3';
import axios from 'axios';

/**
 * Refresca el token CSRF llamando a Sanctum.
 * Si ya tienes configurado Sanctum, esta ruta existe (/sanctum/csrf-cookie).
 * Si en tu proyecto no lo usas, puedes dejar esto como función vacía.
 */
async function refreshCsrf() {
    try {
        await axios.get('/sanctum/csrf-cookie');
    } catch (error) {
        console.error('No se pudo refrescar el token CSRF:', error);
    }
}

/**
 * Envoltorio de useForm que:
 *  - refresca el token CSRF antes de POST / PUT / PATCH / DELETE
 *  - respeta el comportamiento original de Inertia (this, opciones, etc.)
 */
export function useCsrfForm(initialData) {
    const form = useForm(initialData);

    // Guardamos referencias “ligadas” al form para no perder el this
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
