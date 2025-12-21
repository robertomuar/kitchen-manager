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
        <div class="py-8">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8 space-y-6">
                <!-- Información de perfil -->
                <section class="km-card p-6 sm:p-8 space-y-6">
                    <header>
                        <h2 class="text-xl font-semibold text-[color:var(--km-text)]">
                            Información de perfil
                        </h2>
                        <p class="mt-1 text-sm text-[color:var(--km-muted)]">
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
                        <h2 class="text-xl font-semibold text-[color:var(--km-text)]">
                            Cambiar contraseña
                        </h2>
                        <p class="mt-1 text-sm text-[color:var(--km-muted)]">
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
                    class="km-card border-rose-200/70 p-6 sm:p-8 space-y-6 shadow-md shadow-rose-200/30"
                >
                    <header>
                        <h2 class="text-xl font-semibold text-rose-700">
                            Eliminar cuenta
                        </h2>
                        <p class="mt-1 text-sm text-rose-600/80">
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
