<script setup>
const emit = defineEmits(['accept-all', 'reject', 'configure']);

const openSettings = () => {
  if (typeof window !== 'undefined' && typeof window.openCookieSettings === 'function') {
    window.openCookieSettings();
    return;
  }

  emit('configure');
};
</script>

<template>
  <div class="fixed bottom-4 left-1/2 z-[1100] w-[calc(100%-2rem)] max-w-3xl -translate-x-1/2 rounded-2xl border border-slate-200 bg-white p-4 shadow-lg">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <p class="text-sm font-semibold text-slate-900">Cookies en KitchenManager</p>
        <p class="mt-1 text-sm text-slate-600">
          Usamos cookies necesarias para el funcionamiento básico. Puedes aceptar analíticas
          opcionales o configurar tus preferencias.
        </p>
      </div>
      <div class="flex flex-wrap gap-2">
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
          @click="openSettings"
        >
          Configurar
        </button>
        <button
          type="button"
          class="rounded-full bg-amber-500 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-600"
          @click="emit('accept-all')"
        >
          Aceptar todas
        </button>
      </div>
    </div>
  </div>
</template>
