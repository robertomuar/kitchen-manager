<script setup>
import { computed, ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  tables: { type: Array, default: () => [] },
  activeTable: { type: String, default: null },
  columns: { type: Array, default: () => [] },
  rows: { type: Object, default: null },
})

const q = ref('')

const filteredTables = computed(() => {
  const s = q.value.trim().toLowerCase()
  if (!s) return props.tables
  return props.tables.filter(t => String(t).toLowerCase().includes(s))
})

const title = computed(() => props.activeTable ? `DB · ${props.activeTable}` : 'DB Browser')

function openTable(t) {
  router.get('/admin/db', { table: t }, { preserveScroll: true })
}

function clear() {
  router.get('/admin/db', {}, { preserveScroll: false, preserveState: false })
}
</script>

<template>
  <AuthenticatedLayout>
    <Head :title="title" />

    <div class="py-8">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
        <!-- Cabecera -->
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h1 class="text-2xl font-semibold text-slate-50">DB Browser</h1>
            <p class="mt-1 text-sm text-slate-400">
              Navega tablas y registros (solo admin).
            </p>
          </div>

          <div class="flex gap-2">
            <Link
              href="/admin"
              class="rounded-xl border border-slate-800 bg-transparent px-4 py-2 text-sm font-semibold text-slate-300 hover:border-slate-600 hover:text-slate-100"
            >
              ← Admin
            </Link>
            <button
              type="button"
              @click="clear"
              class="rounded-xl border border-slate-700 bg-slate-900/70 px-4 py-2 text-sm font-semibold text-slate-100 hover:border-slate-500 hover:bg-slate-900/90"
            >
              Limpiar
            </button>
          </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-4">
          <!-- Sidebar tablas -->
          <div class="km-card overflow-hidden lg:col-span-1">
            <div class="px-6 py-4 border-b border-slate-800/80 flex items-center justify-between">
              <h2 class="text-sm font-semibold text-slate-100">Tablas</h2>
              <span class="text-xs text-slate-400">Total: {{ tables.length }}</span>
            </div>

            <div class="p-4">
              <input
                v-model="q"
                type="text"
                placeholder="Buscar tabla..."
                class="w-full rounded-xl border border-slate-800 bg-slate-950/70 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 shadow-sm shadow-slate-900/40 focus:border-indigo-400/60 focus:ring-2 focus:ring-indigo-400/30"
              />

              <div class="mt-3 max-h-[60vh] space-y-2 overflow-auto pr-1">
                <button
                  v-for="t in filteredTables"
                  :key="t"
                  type="button"
                  @click="openTable(t)"
                  class="w-full rounded-xl border px-3 py-2 text-left text-sm shadow-sm shadow-slate-900/30"
                  :class="activeTable === t
                    ? 'border-indigo-400/50 bg-indigo-500/10 text-slate-50'
                    : 'border-slate-800 bg-slate-950/40 text-slate-300 hover:border-slate-600 hover:bg-slate-900/40 hover:text-slate-100'"
                >
                  {{ t }}
                </button>

                <div v-if="filteredTables.length === 0" class="text-xs text-slate-500">
                  No hay coincidencias.
                </div>
              </div>
            </div>
          </div>

          <!-- Tabla -->
          <div class="km-card overflow-hidden lg:col-span-3">
            <div class="px-6 py-4 border-b border-slate-800/80 flex items-center justify-between">
              <h2 class="text-sm font-semibold text-slate-100">
                {{ activeTable ? `Tabla: ${activeTable}` : 'Selecciona una tabla' }}
              </h2>

              <span v-if="rows" class="text-xs text-slate-400">
                Página {{ rows.current_page }} / {{ rows.last_page }} · Total: {{ rows.total }}
              </span>
            </div>

            <div v-if="!activeTable" class="p-6 text-sm text-slate-400">
              Elige una tabla a la izquierda.
            </div>

            <div v-else-if="!rows" class="p-6 text-sm text-slate-400">
              Cargando...
            </div>

            <div v-else class="overflow-x-auto">
              <table class="km-table">
                <thead>
                  <tr>
                    <th v-for="c in columns" :key="c">{{ c }}</th>
                    <th>Acciones</th>
                  </tr>
                </thead>

                <tbody>
                  <tr v-for="(r, idx) in rows.data" :key="idx">
                    <td v-for="c in columns" :key="c" class="whitespace-nowrap text-sm text-slate-200">
                      {{ r?.[c] ?? '' }}
                    </td>

                    <td class="whitespace-nowrap">
                      <Link
                        v-if="columns.includes('id')"
                        :href="`/admin/db/row?table=${encodeURIComponent(activeTable)}&id=${encodeURIComponent(r.id)}`"
                        class="inline-flex items-center rounded-xl border border-slate-700 bg-slate-900/70 px-3 py-1.5 text-xs font-semibold text-slate-100 hover:border-slate-500 hover:bg-slate-900/90"
                      >
                        Ver
                      </Link>
                      <span v-else class="text-slate-500">—</span>
                    </td>
                  </tr>
                </tbody>
              </table>

              <!-- Paginación -->
              <div class="flex items-center justify-between px-6 py-4 border-t border-slate-800/80">
                <span class="text-xs text-slate-500">Mostrando 50 por página</span>

                <div class="flex gap-2">
                  <Link
                    v-if="rows.prev_page_url"
                    :href="rows.prev_page_url"
                    class="rounded-xl border border-slate-700 bg-slate-900/70 px-4 py-2 text-sm font-semibold text-slate-100 hover:border-slate-500 hover:bg-slate-900/90"
                    preserve-scroll
                  >
                    ← Anterior
                  </Link>

                  <Link
                    v-if="rows.next_page_url"
                    :href="rows.next_page_url"
                    class="rounded-xl border border-slate-700 bg-slate-900/70 px-4 py-2 text-sm font-semibold text-slate-100 hover:border-slate-500 hover:bg-slate-900/90"
                    preserve-scroll
                  >
                    Siguiente →
                  </Link>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
