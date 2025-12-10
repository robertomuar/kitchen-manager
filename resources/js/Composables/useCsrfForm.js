// resources/js/Composables/useCsrfForm.js

import { useForm } from '@inertiajs/vue3';
import { getCsrfToken } from '@/bootstrap';

/**
 * Pequeño wrapper sobre useForm que añade automáticamente el _token
 * a los datos que se envían en cualquier petición del formulario.
 *
 * Puedes usarlo igual que useForm:
 *   const form = useCsrfForm({ ... });
 *   form.post(route('algo'));
 */
export function useCsrfForm(initialData) {
    const form = useForm(initialData);

    form.transform((data) => {
        const token = getCsrfToken();
        if (!token) {
            return data;
        }

        // Si ya hay _token en los datos, no lo tocamos
        if (data instanceof FormData) {
            if (!data.has('_token')) {
                data.set('_token', token);
            }
            return data;
        }

        if (data && typeof data === 'object' && !data._token) {
            return {
                _token: token,
                ...data,
            };
        }

        return data;
    });

    return form;
}
