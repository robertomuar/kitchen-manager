<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import KitchenSharing from './Partials/KitchenSharing.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
        default: false,
    },
    status: {
        type: String,
        default: '',
    },
    kitchenSharing: {
        type: Object,
        default: () => ({
            sharedUsers: [],
        }),
    },
});
</script>

<template>
    <Head title="Perfil" />

    <AuthenticatedLayout>
        <!-- Clase para aplicar estilos a inputs dentro de los parciales -->
        <div class="py-8 km-profile">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8 space-y-6">
                <!-- Información de perfil -->
                <section class="km-card p-6 sm:p-8 space-y-6">
                    <header>
                        <h2 class="text-xl font-semibold text-slate-50">
                            Información de perfil
                        </h2>
                        <p class="mt-1 text-sm text-slate-400">
                            Actualiza tu nombre y correo electrónico. Estos
                            datos se usarán en toda la aplicación.
                        </p>
                    </header>

                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </section>

                <!-- Cambiar contraseña -->
                <section class="km-card p-6 sm:p-8 space-y-6">
                    <header>
                        <h2 class="text-xl font-semibold text-slate-50">
                            Cambiar contraseña
                        </h2>
                        <p class="mt-1 text-sm text-slate-400">
                            Asegúrate de usar una contraseña larga y con
                            caracteres aleatorios.
                        </p>
                    </header>

                    <UpdatePasswordForm class="max-w-xl" />
                </section>

                <!-- Compartir cocina -->
                <KitchenSharing :shared-users="kitchenSharing.sharedUsers" />

                <!-- Eliminar cuenta -->
                <section
                    class="km-card p-6 sm:p-8 space-y-6 border border-rose-800/80 bg-gradient-to-br from-rose-950/70 via-rose-900/40 to-slate-950/80"
                >
                    <header>
                        <h2 class="text-xl font-semibold text-rose-50">
                            Eliminar cuenta
                        </h2>
                        <p class="mt-1 text-sm text-rose-100/80">
                            Una vez que elimines tu cuenta, no podrás recuperar
                            ningún dato. Descarga cualquier información que
                            quieras conservar antes de continuar.
                        </p>
                    </header>

                    <DeleteUserForm class="max-w-xl" />
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* ✅ SOLO letra (sin tocar el fondo) */
.km-profile :deep(input),
.km-profile :deep(textarea),
.km-profile :deep(select) {
    color: rgb(0, 0, 0) !important;      /* slate-50 */
    caret-color: rgb(248 250 252) !important; /* cursor */
}

/* ✅ Placeholder visible (también es “letra”) */
.km-profile :deep(input::placeholder),
.km-profile :deep(textarea::placeholder) {
    color: rgb(148 163 184) !important; /* slate-400 */
    opacity: 1 !important;
}

/* ✅ Autofill (Chrome/Edge): a veces pone el texto oscuro */
.km-profile :deep(input:-webkit-autofill),
.km-profile :deep(textarea:-webkit-autofill),
.km-profile :deep(select:-webkit-autofill) {
    -webkit-text-fill-color: rgb(248 250 252) !important;
    caret-color: rgb(248 250 252) !important;
}
</style>
