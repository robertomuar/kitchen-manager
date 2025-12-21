<script setup>
import AppMark from '@/Components/AppMark.vue';

defineProps({
  title: { type: String, default: '' },
  subtitle: { type: String, default: '' },
});
</script>

<template>
  <div class="km-card">
    <div class="p-6 sm:p-8">
      <div class="flex items-start gap-3">
        <div
          class="flex h-11 w-11 items-center justify-center rounded-2xl"
          style="background: rgba(209,139,0,0.14); border: 1px solid rgba(209,139,0,0.20);"
          aria-hidden="true"
        >
          <!-- ✅ Icono corregido y centrado -->
          <AppMark class="h-6 w-6" style="color: var(--km-accent)" />
        </div>

        <div class="min-w-0">
          <h1 class="text-2xl font-semibold tracking-tight" style="color: var(--km-text)">
            {{ title }}
          </h1>
          <p v-if="subtitle" class="mt-1 text-sm" style="color: var(--km-muted)">
            {{ subtitle }}
          </p>
        </div>
      </div>

      <div class="mt-6">
        <slot />
      </div>

      <div v-if="$slots.footer" class="mt-6">
        <div class="km-divider" />
        <div class="text-sm" style="color: var(--km-muted)">
          <slot name="footer" />
        </div>
      </div>
    </div>
  </div>
</template>
