<script setup>
import { Link } from '@inertiajs/vue3';
import ChefIllustration from '@/Components/ChefIllustration.vue';
import AppMark from '@/Components/AppMark.vue';

const props = defineProps({
  busy: { type: Boolean, default: false },
});
</script>

<template>
  <div class="km-page km-scope-light relative">
    <!-- halos -->
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
      <div class="km-halo-amber absolute -top-24 left-1/2 h-96 w-96 -translate-x-1/2 rounded-full blur-3xl" />
      <div class="km-halo-neutral absolute -bottom-24 right-1/3 h-96 w-96 rounded-full blur-3xl" />
    </div>

    <div class="relative z-10 flex min-h-screen flex-col">
      <!-- Header -->
      <header class="px-6 pt-6">
        <div class="mx-auto flex w-full max-w-6xl items-center justify-between">
          <Link href="/" class="inline-flex items-center gap-3">
            <div
              class="flex h-11 w-11 items-center justify-center rounded-2xl"
              style="background: rgba(209,139,0,0.14); border: 1px solid rgba(209,139,0,0.20);"
              aria-hidden="true"
            >
              <!-- ✅ Icono corregido y centrado -->
              <AppMark class="h-6 w-6" style="color: var(--km-accent)" />
            </div>

            <div class="leading-tight">
              <div class="text-base font-semibold tracking-tight">KitchenManager</div>
              <div class="text-xs" style="color: var(--km-muted)">Control de stock y caducidades</div>
            </div>
          </Link>
        </div>
      </header>

      <!-- Centro -->
      <main class="flex flex-1 items-center justify-center px-6 py-10">
        <div class="mx-auto w-full max-w-6xl">
          <div class="flex w-full items-center justify-center gap-10">
            <div class="hidden lg:flex items-center justify-center">
              <ChefIllustration :busy="props.busy" />
            </div>

            <div class="w-full max-w-md">
              <slot />
            </div>
          </div>
        </div>
      </main>

      <footer class="px-6 pb-6">
        <div class="mx-auto w-full max-w-6xl text-xs" style="color: rgba(11,16,36,0.45)">
          © {{ new Date().getFullYear() }} KitchenManager
        </div>
      </footer>
    </div>
  </div>
</template>
