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
            <h1 class="text-2xl font-semibold text-[color:var(--km-text)]">Registro</h1>
            <p class="mt-1 text-sm text-[color:var(--km-muted)]">
              Tabla: <span class="font-semibold text-[color:var(--km-text)]">{{ table }}</span>
            </p>
          </div>

          <div class="flex gap-2">
            <Link
              :href="backToTable()"
              class="km-btn w-auto px-4 py-2 text-sm"
            >
              ← Volver
            </Link>

            <Link
              href="/admin"
              class="km-link text-sm font-semibold"
            >
              Admin
            </Link>
          </div>
        </div>

        <div class="km-card overflow-hidden">
          <div class="px-6 py-4 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-[color:var(--km-text)]">Detalle</h2>
            <span class="text-xs text-[color:var(--km-muted)]">ID: {{ row.id ?? '—' }}</span>
          </div>
          <div class="km-divider" />

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
                  <td class="whitespace-nowrap text-sm font-medium text-[color:var(--km-text)]">
                    {{ k }}
                  </td>

                  <td class="text-sm text-[color:var(--km-text)]">
                    <pre
                      v-if="typeof v === 'string' && v.length > 140"
                      class="whitespace-pre-wrap break-words rounded-xl border border-[color:var(--km-border)] bg-[color:var(--km-bg-2)] p-3 text-xs text-[color:var(--km-text)]"
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
