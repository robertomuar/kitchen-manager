import { useForm } from '@inertiajs/vue3';
import { getCsrfToken } from '@/bootstrap';

/**
 * Envuelve useForm para añadir y refrescar el token CSRF en cada envío.
 */
export function useCsrfForm(initialValues = {}) {
    const form = useForm({
        _token: getCsrfToken(),
        ...initialValues,
    });

    const refreshCsrfToken = () => {
        const token = getCsrfToken();
        if (token) {
            form._token = token;
        }
    };

    const wrapSubmission = (methodName) => {
        const original = form[methodName];
        if (typeof original !== 'function') return;

        form[methodName] = (...args) => {
            refreshCsrfToken();
            return original.apply(form, args);
        };
    };

    ['submit', 'post', 'put', 'patch', 'delete'].forEach(wrapSubmission);

    form.refreshCsrfToken = refreshCsrfToken;

    return form;
}
