<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import AuthCard from '@/Components/AuthCard.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { useCsrfForm } from '@/Composables/useCsrfForm';

const props = defineProps({
  status: { type: String, default: null },
});

const form = useCsrfForm({});
const page = usePage();

const userEmail = computed(() => page.props.auth?.user?.email ?? '');

const resendCooldown = ref(0);
let cooldownTimer = null;

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');

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

const resendDisabled = computed(() => form.processing || resendCooldown.value > 0);

const submit = () => {
  form.post(route('verification.send'), {
    onSuccess: () => startCooldown(),
  });
};

onMounted(() => {
  if (verificationLinkSent.value) startCooldown();
});

onUnmounted(() => {
  if (cooldownTimer) clearInterval(cooldownTimer);
});
</script>

<template>
  <GuestLayout :busy="form.processing">
    <Head title="Verifica tu correo" />

    <AuthCard
      title="Verifica tu correo"
      subtitle="Confirma tu dirección para activar la cuenta"
    >
      <p class="text-sm" style="color: var(--km-muted)">
        Necesitamos confirmar la dirección
        <span class="font-semibold" style="color: var(--km-text)">{{ userEmail }}</span>
        antes de darte acceso completo. Te hemos enviado un enlace de verificación:
        abre el correo y pulsa el botón para validar tu cuenta.
      </p>

      <div
        v-if="verificationLinkSent"
        class="mt-4 rounded-2xl border px-4 py-3 text-sm font-semibold"
        style="border-color: rgba(34,197,94,.25); background: rgba(34,197,94,.10); color: rgba(11,16,36,.85)"
      >
        ¡Listo! Te hemos reenviado un correo con el enlace de verificación.
        Revisa también la carpeta de spam o promociones.
      </div>

      <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <!-- ✅ Botón reenviar (sin <form>) -->
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
          <button
            type="button"
            class="km-btn sm:w-auto sm:px-6"
            :disabled="resendDisabled"
            @click="submit"
          >
            <span v-if="resendCooldown === 0">Reenviar enlace seguro</span>
            <span v-else>Espera {{ resendCooldown }}s</span>
          </button>

          <p v-if="resendCooldown > 0" class="text-xs" style="color: var(--km-muted)">
            Para evitar abusos, puedes reenviar el correo cuando termine la cuenta regresiva.
          </p>
        </div>

        <!-- ✅ Logout con Inertia (NO form anidado) -->
        <Link
          :href="route('logout')"
          method="post"
          as="button"
          class="text-sm font-semibold underline sm:text-right"
          style="color: var(--km-text)"
        >
          Cambiar de cuenta
        </Link>
      </div>

      <p class="mt-6 text-xs" style="color: var(--km-muted)">
        Este paso protege tu cocina compartida y evita accesos no deseados. Si el enlace tarda, espera
        unos segundos o solicita uno nuevo para mantener la sesión protegida.
      </p>
    </AuthCard>
  </GuestLayout>
</template>
