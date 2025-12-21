<script setup>
import { computed } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';

import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useCsrfForm } from '@/Composables/useCsrfForm';

const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
        default: false,
    },
    status: {
        type: String,
        default: '',
    },
});

const user = computed(() => usePage().props.auth.user);

const form = useCsrfForm({
    name: user.value?.name ?? '',
    email: user.value?.email ?? '',
});

const submit = () => {
    form.patch(route('profile.update'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <!-- SOLO el formulario, sin cabeceras ni tarjetas -->
    <form @submit.prevent="submit" class="space-y-6 max-w-xl">
        <!-- Nombre -->
        <div>
            <InputLabel
                for="name"
                value="Nombre"
            />

            <TextInput
                id="name"
                v-model="form.name"
                type="text"
                autocomplete="name"
                class="mt-1 block w-full"
            />

            <InputError :message="form.errors.name" class="mt-2" />
        </div>

        <!-- Email -->
        <div>
            <InputLabel
                for="email"
                value="Correo electrónico"
            />

            <TextInput
                id="email"
                v-model="form.email"
                type="email"
                autocomplete="username"
                class="mt-1 block w-full"
            />

            <InputError :message="form.errors.email" class="mt-2" />
        </div>

        <!-- Verificación de email -->
        <div v-if="props.mustVerifyEmail" class="space-y-2 text-sm">
            <p class="text-[color:var(--km-muted)]">
                Tu dirección de correo electrónico no está verificada.
            </p>

            <div class="flex flex-wrap items-center gap-2">
                <Link
                    :href="route('verification.send')"
                    method="post"
                    as="button"
                    class="km-link text-xs underline underline-offset-2"
                >
                    Reenviar correo de verificación
                </Link>

                <p
                    v-if="props.status === 'verification-link-sent'"
                    class="text-xs text-emerald-600"
                >
                    Se ha enviado un nuevo enlace de verificación a tu correo.
                </p>
            </div>
        </div>

        <!-- Botón -->
        <div class="flex items-center gap-3">
            <PrimaryButton :disabled="form.processing" type="submit">
                Guardar cambios
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
