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
                        <h2 class="text-lg font-semibold text-slate-50">
                            Información de perfil
                        </h2>
                        <p class="mt-1 text-sm text-slate-400">
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
                        <h2 class="text-lg font-semibold text-slate-50">
                            Cambiar contraseña
                        </h2>
                        <p class="mt-1 text-sm text-slate-400">
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
                    class="rounded-2xl border border-rose-900/80 bg-gradient-to-br from-rose-950/80 via-rose-900/70 to-slate-950/90 p-6 sm:p-8 lg:p-10 space-y-6 shadow-md shadow-rose-900/40"
                >
                    <header>
                        <h2 class="text-lg font-semibold text-rose-50">
                            Eliminar cuenta
                        </h2>
                        <p class="mt-1 text-sm text-rose-100/80">
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
