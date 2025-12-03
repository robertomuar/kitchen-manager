<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { useCsrfForm } from '@/Composables/useCsrfForm';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useCsrfForm({});

const page = usePage();

const userEmail = computed(() => page.props.auth?.user?.email ?? '');

const resendCooldown = ref(0);
let cooldownTimer = null;

const startCooldown = () => {
    resendCooldown.value = 60;

    if (cooldownTimer) clearInterval(cooldownTimer);

    cooldownTimer = setInterval(() => {
        if (resendCooldown.value <= 0) {
            clearInterval(cooldownTimer);
            cooldownTimer = null;
            return;
        }

        resendCooldown.value -= 1;
    }, 1000);
};

const submit = () => {
    form.post(route('verification.send'), {
        onSuccess: () => startCooldown(),
    });
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);

const resendDisabled = computed(
    () => form.processing || resendCooldown.value > 0,
);

onMounted(() => {
    if (verificationLinkSent.value) {
        startCooldown();
    }
});

onUnmounted(() => {
    if (cooldownTimer) clearInterval(cooldownTimer);
});
</script>

<template>
    <GuestLayout>
        <Head title="Verifica tu correo" />

        <div class="mb-4 text-sm text-gray-700">
            Necesitamos confirmar la dirección
            <span class="font-semibold">{{ userEmail }}</span> antes de darte
            acceso completo. Hemos enviado un enlace de verificación a tu buzón;
            solo tienes que abrirlo y pulsar el botón para validar tu cuenta.
        </div>

        <div
            class="mb-4 rounded-lg border border-green-200 bg-green-50 px-3 py-2 text-sm font-medium text-green-700"
            v-if="verificationLinkSent"
        >
            ¡Listo! Te hemos reenviado un correo con el enlace de verificación.
            Revisa también la carpeta de spam o promociones.
        </div>

        <form @submit.prevent="submit">
            <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <PrimaryButton
                        :class="{ 'opacity-50': resendDisabled }"
                        :disabled="resendDisabled"
                        type="submit"
                    >
                        <span v-if="resendCooldown === 0">
                            Reenviar enlace seguro
                        </span>
                        <span v-else>
                            Espera {{ resendCooldown }}s
                        </span>
                    </PrimaryButton>

                    <p
                        v-if="resendCooldown > 0"
                        class="text-xs text-gray-500"
                    >
                        Para evitar abusos, puedes reenviar el correo cuando termine
                        la cuenta regresiva.
                    </p>
                </div>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >Cambiar de cuenta</Link
                >
            </div>
        </form>

        <p class="mt-6 text-xs text-gray-500">
            Este paso protege tu cocina compartida y evita accesos no deseados.
            Si el enlace tarda, espera unos segundos o solicita uno nuevo para
            mantener la sesión protegida.
        </p>
    </GuestLayout>
</template>
