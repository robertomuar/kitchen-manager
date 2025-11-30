<script setup>
import { useForm } from '@inertiajs/vue3';

import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <!-- SOLO el formulario, sin tarjetas ni cabeceras -->
    <form @submit.prevent="updatePassword" class="space-y-6 max-w-xl">
        <!-- Contraseña actual -->
        <div>
            <InputLabel
                for="current_password"
                value="Contraseña actual"
                class="text-slate-200"
            />

            <TextInput
                id="current_password"
                v-model="form.current_password"
                type="password"
                autocomplete="current-password"
                class="mt-1 block w-full rounded-xl border border-slate-700 bg-slate-950/70 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
            />

            <InputError
                :message="form.errors.current_password"
                class="mt-2"
            />
        </div>

        <!-- Nueva contraseña -->
        <div>
            <InputLabel
                for="password"
                value="Nueva contraseña"
                class="text-slate-200"
            />

            <TextInput
                id="password"
                v-model="form.password"
                type="password"
                autocomplete="new-password"
                class="mt-1 block w-full rounded-xl border border-slate-700 bg-slate-950/70 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
            />

            <InputError :message="form.errors.password" class="mt-2" />
        </div>

        <!-- Confirmar contraseña -->
        <div>
            <InputLabel
                for="password_confirmation"
                value="Confirmar contraseña"
                class="text-slate-200"
            />

            <TextInput
                id="password_confirmation"
                v-model="form.password_confirmation"
                type="password"
                autocomplete="new-password"
                class="mt-1 block w-full rounded-xl border border-slate-700 bg-slate-950/70 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
            />

            <InputError
                :message="form.errors.password_confirmation"
                class="mt-2"
            />
        </div>

        <!-- Botón -->
        <div class="flex items-center gap-3">
            <PrimaryButton :disabled="form.processing" type="submit">
                Guardar contraseña
            </PrimaryButton>

            <p
                v-if="form.recentlySuccessful"
                class="text-xs text-emerald-300"
            >
                Guardado.
            </p>
        </div>
    </form>
</template>
