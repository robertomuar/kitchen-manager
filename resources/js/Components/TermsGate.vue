<script setup>
import { Link } from '@inertiajs/vue3';
import { onMounted, ref, watch } from 'vue';
import { acceptTerms, getTermsAcceptance, TERMS_VERSION } from '@/utils/consent';

const isOpen = ref(false);
const hasConfirmed = ref(false);

const refreshState = () => {
  isOpen.value = !getTermsAcceptance();
};

const handleAccept = () => {
  acceptTerms();
  isOpen.value = false;
};

onMounted(() => {
  refreshState();
});

watch(isOpen, (value) => {
  if (typeof document === 'undefined') return;
  document.body.style.overflow = value ? 'hidden' : '';
});
</script>

<template>
  <teleport to="body">
    <div
      v-if="isOpen"
      class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 px-4"
      role="dialog"
      aria-modal="true"
      aria-labelledby="terms-title"
    >
      <div class="w-full max-w-2xl rounded-2xl bg-white p-6 shadow-xl">
        <div class="flex flex-col gap-4">
          <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-amber-600">
              Términos y condiciones
            </p>
            <h2 id="terms-title" class="mt-2 text-2xl font-semibold text-slate-900">
              Necesitamos tu aceptación
            </h2>
            <p class="mt-3 text-sm text-slate-600">
              Para continuar usando KitchenManager debes aceptar los términos vigentes (versión
              {{ TERMS_VERSION }}). Aquí tienes un resumen rápido y puedes revisar el detalle en
              las páginas legales.
            </p>
          </div>

          <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
            <ul class="list-disc space-y-2 pl-4">
              <li>Usarás la plataforma de forma responsable y con datos reales.</li>
              <li>Nos comprometemos a proteger tu información y respetar tu privacidad.</li>
              <li>Podemos actualizar estos términos; te avisaremos cuando cambien.</li>
            </ul>
            <div class="mt-4 flex flex-wrap gap-3 text-sm">
              <Link href="/terms" class="font-semibold text-amber-600 hover:text-amber-700">
                Leer términos completos
              </Link>
              <Link href="/privacy-policy" class="font-semibold text-amber-600 hover:text-amber-700">
                Política de privacidad
              </Link>
            </div>
          </div>

          <label class="flex items-start gap-3 text-sm text-slate-700">
            <input
              v-model="hasConfirmed"
              type="checkbox"
              class="mt-1 h-4 w-4 rounded border-slate-300 text-amber-500 focus:ring-amber-500"
            >
            <span>He leído y acepto los términos (opcional).</span>
          </label>

          <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
            <button
              type="button"
              class="inline-flex items-center justify-center rounded-full bg-amber-500 px-6 py-2 text-sm font-semibold text-white shadow-sm hover:bg-amber-600"
              @click="handleAccept"
            >
              Aceptar y continuar
            </button>
          </div>
        </div>
      </div>
    </div>
  </teleport>
</template>
