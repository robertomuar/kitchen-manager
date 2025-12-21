<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Perfil" />

    <AuthenticatedLayout>
        <div class="py-10">
            <div class="mx-auto max-w-4xl space-y-8 sm:px-6 lg:px-8">
                <!-- Datos de perfil -->
                <section class="km-card p-6 sm:p-8 lg:p-10 space-y-6">
                    <header>
                        <h2 class="text-lg font-semibold text-[color:var(--km-text)]">
                            Información de perfil
                        </h2>
                        <p class="mt-1 text-sm text-[color:var(--km-muted)]">
                            Actualiza tu nombre y correo electrónico. Estos datos
                            se usarán en toda la aplicación.
                        </p>
                    </header>

                    <div class="mt-4">
                        <UpdateProfileInformationForm
                            :must-verify-email="mustVerifyEmail"
                            :status="status"
                            class="max-w-xl space-y-6"
                        />
                    </div>
                </section>

                <!-- Cambiar contraseña -->
                <section class="km-card p-6 sm:p-8 lg:p-10 space-y-6">
                    <header>
                        <h2 class="text-lg font-semibold text-[color:var(--km-text)]">
                            Cambiar contraseña
                        </h2>
                        <p class="mt-1 text-sm text-[color:var(--km-muted)]">
                            Asegúrate de usar una contraseña larga y con
                            caracteres aleatorios.
                        </p>
                    </header>

                    <div class="mt-4">
                        <UpdatePasswordForm class="max-w-xl space-y-6" />
                    </div>
                </section>

                <!-- Eliminar cuenta -->
                <section
                    class="km-card border-rose-200/70 p-6 sm:p-8 lg:p-10 space-y-6 shadow-md shadow-rose-200/30"
                >
                    <header>
                        <h2 class="text-lg font-semibold text-rose-700">
                            Eliminar cuenta
                        </h2>
                        <p class="mt-1 text-sm text-rose-600/80">
                            Una vez que elimines tu cuenta, no podrás recuperar
                            ningún dato. Descarga cualquier información que
                            quieras conservar antes de continuar.
                        </p>
                    </header>

                    <div class="mt-4">
                        <DeleteUserForm class="max-w-xl space-y-6" />
                    </div>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
