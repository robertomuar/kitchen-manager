<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useCsrfForm } from '@/Composables/useCsrfForm';

const form = useCsrfForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const username = computed(
    () => usePage().props.auth.user?.email ?? usePage().props.auth.user?.name ?? ''
);

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
        <input
            type="text"
            name="username"
            :value="username"
            autocomplete="username"
            class="sr-only"
            tabindex="-1"
            aria-hidden="true"
        />

        <!-- Contraseña actual -->
        <div>
            <InputLabel
                for="current_password"
                value="Contraseña actual"
            />

            <TextInput
                id="current_password"
                v-model="form.current_password"
                type="password"
                autocomplete="current-password"
                class="mt-1 block w-full"
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
            />

            <TextInput
                id="password"
                v-model="form.password"
                type="password"
                autocomplete="new-password"
                class="mt-1 block w-full"
            />

            <InputError :message="form.errors.password" class="mt-2" />
        </div>

        <!-- Confirmar contraseña -->
        <div>
            <InputLabel
                for="password_confirmation"
                value="Confirmar contraseña"
            />

            <TextInput
                id="password_confirmation"
                v-model="form.password_confirmation"
                type="password"
                autocomplete="new-password"
                class="mt-1 block w-full"
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
                class="text-xs text-emerald-600"
            >
                Guardado.
            </p>
        </div>
    </form>
</template>
