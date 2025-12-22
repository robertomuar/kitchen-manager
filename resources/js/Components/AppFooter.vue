<script setup>
import { inject } from 'vue';
import { Link } from '@inertiajs/vue3';
import { cookieConsentKey } from '@/utils/consent';

const cookieConsent = inject(cookieConsentKey, null);

const openSettings = () => {
  cookieConsent?.openSettings?.();
};
</script>

<template>
  <footer class="border-t border-slate-200 bg-white">
    <div class="mx-auto flex w-full max-w-6xl flex-col gap-3 px-6 py-4 text-sm text-slate-500 md:flex-row md:items-center md:justify-between">
      <div>© {{ new Date().getFullYear() }} KitchenManager. Todos los derechos reservados.</div>
      <div class="flex flex-wrap items-center gap-4">
        <Link href="/terms" class="hover:text-slate-700">Términos</Link>
        <Link href="/cookies-policy" class="hover:text-slate-700">Política de cookies</Link>
        <Link href="/privacy-policy" class="hover:text-slate-700">Privacidad</Link>
        <button
          type="button"
          class="font-semibold text-amber-600 hover:text-amber-700"
          @click="openSettings"
        >
          Configurar cookies
        </button>
      </div>
    </div>
  </footer>
</template>
