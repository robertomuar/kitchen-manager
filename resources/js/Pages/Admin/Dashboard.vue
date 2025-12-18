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
            <h1 class="text-2xl font-semibold text-slate-50">Admin</h1>
            <p class="mt-1 text-sm text-slate-400">
              Panel de administración: métricas y acceso al visor de base de datos.
            </p>
          </div>

          <div class="flex gap-2">
            <Link
              href="/admin/db"
              class="rounded-xl border border-slate-700 bg-slate-900/70 px-4 py-2 text-sm font-semibold text-slate-100 shadow-sm shadow-slate-900/40 hover:border-slate-500 hover:bg-slate-900/90"
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
            class="rounded-2xl border border-slate-800 bg-slate-950/70 p-4 shadow-sm shadow-slate-900/40"
          >
            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
              {{ String(k).replaceAll('_', ' ') }}
            </p>
            <p class="mt-3 text-3xl font-semibold text-slate-50">
              {{ v }}
            </p>
            <p class="mt-1 text-xs text-slate-400">
              Total de registros.
            </p>
          </div>

          <div
            v-if="items.length === 0"
            class="rounded-2xl border border-slate-800 bg-slate-950/70 p-4 text-sm text-slate-400"
          >
            No hay métricas configuradas.
          </div>
        </div>

        <!-- Accesos -->
        <div class="km-card overflow-hidden">
          <div class="px-6 py-4 border-b border-slate-800/80 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-slate-100">Accesos</h2>
            <span class="text-xs text-slate-400">Admin tools</span>
          </div>

          <div class="p-6 flex flex-wrap gap-3">
            <Link
              href="/admin/db"
              class="rounded-xl border border-slate-700 bg-slate-900/70 px-4 py-2 text-sm font-semibold text-slate-100 hover:border-slate-500 hover:bg-slate-900/90"
            >
              Abrir DB Browser →
            </Link>

            <Link
              href="/dashboard"
              class="rounded-xl border border-slate-800 bg-transparent px-4 py-2 text-sm font-semibold text-slate-300 hover:border-slate-600 hover:text-slate-100"
            >
              Volver al Panel
            </Link>
          </div>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>
