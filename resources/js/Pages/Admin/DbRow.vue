<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  table: { type: String, required: true },
  row: { type: Object, required: true },
})

const backToTable = () => `/admin/db?table=${encodeURIComponent(props.table)}`
</script>

<template>
  <AuthenticatedLayout>
    <Head :title="`DB · ${table} · Registro`" />

    <div class="py-8">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
        <!-- Cabecera -->
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h1 class="text-2xl font-semibold text-slate-50">Registro</h1>
            <p class="mt-1 text-sm text-slate-400">
              Tabla: <span class="font-semibold text-slate-100">{{ table }}</span>
            </p>
          </div>

          <div class="flex gap-2">
            <Link
              :href="backToTable()"
              class="rounded-xl border border-slate-700 bg-slate-900/70 px-4 py-2 text-sm font-semibold text-slate-100 hover:border-slate-500 hover:bg-slate-900/90"
            >
              ← Volver
            </Link>

            <Link
              href="/admin"
              class="rounded-xl border border-slate-800 bg-transparent px-4 py-2 text-sm font-semibold text-slate-300 hover:border-slate-600 hover:text-slate-100"
            >
              Admin
            </Link>
          </div>
        </div>

        <div class="km-card overflow-hidden">
          <div class="px-6 py-4 border-b border-slate-800/80 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-slate-100">Detalle</h2>
            <span class="text-xs text-slate-400">ID: {{ row.id ?? '—' }}</span>
          </div>

          <div class="overflow-x-auto">
            <table class="km-table">
              <thead>
                <tr>
                  <th>Campo</th>
                  <th>Valor</th>
                </tr>
              </thead>

              <tbody>
                <tr v-for="(v, k) in row" :key="k">
                  <td class="whitespace-nowrap text-sm font-medium text-slate-50">
                    {{ k }}
                  </td>

                  <td class="text-sm text-slate-200">
                    <pre
                      v-if="typeof v === 'string' && v.length > 140"
                      class="whitespace-pre-wrap break-words rounded-xl border border-slate-800 bg-slate-950/70 p-3 text-xs text-slate-100"
                    >{{ v }}</pre>
                    <span v-else>{{ v }}</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>
