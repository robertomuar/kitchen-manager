<script setup>
import { computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  counts: { type: Object, default: () => ({}) },
})

const items = computed(() => Object.entries(props.counts ?? {}))
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Admin" />

    <div class="py-8">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
        <!-- Cabecera -->
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h1 class="text-2xl font-semibold text-[color:var(--km-text)]">Admin</h1>
            <p class="mt-1 text-sm text-[color:var(--km-muted)]">
              Panel de administración: métricas y acceso al visor de base de datos.
            </p>
          </div>

          <div class="flex gap-2">
            <Link
              href="/admin/db"
              class="km-btn w-auto px-4 py-2 text-sm"
            >
              DB Browser →
            </Link>
          </div>
        </div>

        <!-- Tarjetas -->
        <div class="grid gap-4 md:grid-cols-4">
          <div
            v-for="([k, v], i) in items"
            :key="k"
            class="km-card p-4"
          >
            <p class="text-xs font-medium uppercase tracking-wide text-[color:var(--km-muted)]">
              {{ String(k).replaceAll('_', ' ') }}
            </p>
            <p class="mt-3 text-3xl font-semibold text-[color:var(--km-text)]">
              {{ v }}
            </p>
            <p class="mt-1 text-xs text-[color:var(--km-muted)]">
              Total de registros.
            </p>
          </div>

          <div
            v-if="items.length === 0"
            class="km-card p-4 text-sm text-[color:var(--km-muted)]"
          >
            No hay métricas configuradas.
          </div>
        </div>

        <!-- Accesos -->
        <div class="km-card overflow-hidden">
          <div class="px-6 py-4 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-[color:var(--km-text)]">Accesos</h2>
            <span class="text-xs text-[color:var(--km-muted)]">Admin tools</span>
          </div>
          <div class="km-divider" />

          <div class="p-6 flex flex-wrap gap-3">
            <Link
              href="/admin/db"
              class="km-btn w-auto px-4 py-2 text-sm"
            >
              Abrir DB Browser →
            </Link>

            <Link
              href="/dashboard"
              class="km-link text-sm font-semibold"
            >
              Volver al Panel
            </Link>
          </div>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>
