<script setup>
import { computed, ref, watch } from 'vue';

const props = defineProps({
  open: {
    type: Boolean,
    default: false,
  },
  consent: {
    type: Object,
    default: null,
  },
});

const emit = defineEmits(['close', 'save', 'accept-all', 'reject']);

const analytics = ref(false);

const canShow = computed(() => props.open);
const currentConsent = computed(() => props.consent?.value ?? props.consent);

watch(
  () => props.open,
  (value) => {
    if (value) {
      analytics.value = currentConsent.value?.analytics ?? false;
    }
  }
);

watch(canShow, (value) => {
  if (typeof document === 'undefined') return;
  document.body.style.overflow = value ? 'hidden' : '';
});

const savePreferences = () => {
  emit('save', analytics.value);
};
</script>

<template>
  <teleport to="body">
    <div
      v-if="canShow"
      class="fixed inset-0 z-[1150] flex items-center justify-center bg-slate-900/60 px-4"
      role="dialog"
      aria-modal="true"
      aria-labelledby="cookies-title"
    >
      <div class="w-full max-w-xl rounded-2xl bg-white p-6 shadow-xl">
        <div class="flex items-start justify-between">
          <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-amber-600">Preferencias</p>
            <h2 id="cookies-title" class="mt-2 text-2xl font-semibold text-slate-900">
              Configura tus cookies
            </h2>
          </div>
          <button
            type="button"
            class="rounded-full p-2 text-slate-500 hover:bg-slate-100"
            aria-label="Cerrar"
            @click="emit('close')"
          >
            ✕
          </button>
        </div>

        <div class="mt-6 space-y-4 text-sm text-slate-600">
          <div class="rounded-xl border border-slate-200 p-4">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-semibold text-slate-900">Cookies necesarias</p>
                <p class="text-xs text-slate-500">Imprescindibles para el funcionamiento básico.</p>
              </div>
              <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                Siempre activas
              </span>
            </div>
          </div>

          <div class="rounded-xl border border-slate-200 p-4">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-semibold text-slate-900">Cookies analíticas</p>
                <p class="text-xs text-slate-500">
                  Nos ayudan a entender el uso de la app y mejorarla.
                </p>
              </div>
              <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
                <input
                  v-model="analytics"
                  type="checkbox"
                  class="h-4 w-4 rounded border-slate-300 text-amber-500 focus:ring-amber-500"
                >
                Activar
              </label>
            </div>
          </div>
        </div>

        <div class="mt-6 flex flex-col gap-2 sm:flex-row sm:justify-end">
          <button
            type="button"
            class="rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
            @click="emit('reject')"
          >
            Rechazar no esenciales
          </button>
          <button
            type="button"
            class="rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
            @click="emit('accept-all')"
          >
            Aceptar todas
          </button>
          <button
            type="button"
            class="rounded-full bg-amber-500 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-600"
            @click="savePreferences"
          >
            Guardar preferencias
          </button>
        </div>
      </div>
    </div>
  </teleport>
</template>
