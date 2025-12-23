<script setup>
import { inject } from 'vue';
import { Link } from '@inertiajs/vue3';
import { cookieConsentKey } from '@/utils/consent';

const cookieConsent = inject(cookieConsentKey, null);

const openSettings = () => {
  if (typeof window !== 'undefined' && typeof window.openCookieSettings === 'function') {
    window.openCookieSettings();
    return;
  }

  cookieConsent?.openSettings?.();
};
</script>

<template>
  <footer class="border-t border-slate-200 bg-white">
    <div class="mx-auto flex w-full max-w-6xl flex-col gap-3 px-6 py-4 text-sm text-slate-700 md:flex-row md:items-center md:justify-between">
      <div>© {{ new Date().getFullYear() }} KitchenManager. Todos los derechos reservados.</div>
      <div class="flex flex-wrap items-center gap-4">
        <Link href="/legal/aviso-legal" class="hover:text-slate-700">Aviso legal</Link>
        <Link href="/legal/terminos" class="hover:text-slate-700">Términos</Link>
        <Link href="/legal/privacidad" class="hover:text-slate-700">Privacidad</Link>
        <Link href="/legal/cookies" class="hover:text-slate-700">Cookies</Link>
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
