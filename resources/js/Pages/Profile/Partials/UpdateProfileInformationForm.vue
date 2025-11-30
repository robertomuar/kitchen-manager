<script setup>
import { computed } from 'vue';
import { useForm, usePage, Link } from '@inertiajs/vue3';

import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

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

const form = useForm({
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
                class="text-slate-200"
            />

            <TextInput
                id="name"
                v-model="form.name"
                type="text"
                autocomplete="name"
                class="mt-1 block w-full rounded-xl border border-slate-700 bg-slate-950/70 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
            />

            <InputError :message="form.errors.name" class="mt-2" />
        </div>

        <!-- Email -->
        <div>
            <InputLabel
                for="email"
                value="Correo electrónico"
                class="text-slate-200"
            />

            <TextInput
                id="email"
                v-model="form.email"
                type="email"
                autocomplete="username"
                class="mt-1 block w-full rounded-xl border border-slate-700 bg-slate-950/70 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
            />

            <InputError :message="form.errors.email" class="mt-2" />
        </div>

        <!-- Verificación de email -->
        <div v-if="props.mustVerifyEmail" class="space-y-2 text-sm">
            <p class="text-slate-400">
                Tu dirección de correo electrónico no está verificada.
            </p>

            <div class="flex flex-wrap items-center gap-2">
                <Link
                    :href="route('verification.send')"
                    method="post"
                    as="button"
                    class="text-xs font-medium text-indigo-200 underline underline-offset-2 hover:text-indigo-100"
                >
                    Reenviar correo de verificación
                </Link>

                <p
                    v-if="props.status === 'verification-link-sent'"
                    class="text-xs text-emerald-300"
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
                class="text-xs text-emerald-300"
            >
                Guardado.
            </p>
        </div>
    </form>
</template>
