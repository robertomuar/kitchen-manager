<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppMark from '@/Components/AppMark.vue';

const appName = computed(() => usePage().props.app?.name ?? 'KitchenManager');
</script>

<template>
  <div class="min-h-screen bg-white text-slate-900">
    <header class="border-b border-slate-200">
      <div class="mx-auto flex w-full max-w-6xl items-center justify-between px-6 py-5">
        <Link href="/" class="inline-flex items-center gap-3">
          <div
            class="flex h-10 w-10 items-center justify-center rounded-xl"
            style="background: rgba(209,139,0,0.12); border: 1px solid rgba(209,139,0,0.2);"
            aria-hidden="true"
          >
            <AppMark class="h-5 w-5" style="color: var(--km-accent)" />
          </div>
          <div>
            <div class="text-sm font-semibold tracking-tight">{{ appName }}</div>
            <div class="text-xs text-slate-500">Control de stock y caducidades</div>
          </div>
        </Link>
        <nav class="hidden items-center gap-6 text-sm text-slate-600 md:flex">
          <Link href="/features" class="hover:text-slate-900">Funcionalidades</Link>
          <Link href="/pricing" class="hover:text-slate-900">Precios</Link>
          <Link href="/faq" class="hover:text-slate-900">FAQ</Link>
          <Link href="/blog" class="hover:text-slate-900">Blog</Link>
          <Link href="/contact" class="hover:text-slate-900">Contacto</Link>
        </nav>
        <div class="flex items-center gap-3 text-sm">
          <Link href="/login" class="text-slate-600 hover:text-slate-900">Entrar</Link>
          <Link
            href="/register"
            class="rounded-full bg-amber-500 px-4 py-2 font-semibold text-white shadow-sm hover:bg-amber-600"
          >
            Crear cuenta
          </Link>
        </div>
      </div>
    </header>

    <main class="mx-auto w-full max-w-6xl px-6 py-12">
      <slot />
    </main>

    <footer class="border-t border-slate-200">
      <div class="mx-auto flex w-full max-w-6xl flex-col gap-4 px-6 py-8 text-sm text-slate-500 md:flex-row md:items-center md:justify-between">
        <div>© {{ new Date().getFullYear() }} {{ appName }}. Hecho para cocinas organizadas.</div>
        <div class="flex flex-wrap gap-4">
          <Link href="/privacy-policy" class="hover:text-slate-700">Política de privacidad</Link>
          <Link href="/terms" class="hover:text-slate-700">Términos</Link>
          <Link href="/contact" class="hover:text-slate-700">Contacto</Link>
        </div>
      </div>
    </footer>
  </div>
</template>
