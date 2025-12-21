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
            <h1 class="text-2xl font-semibold text-[color:var(--km-text)]">DB Browser</h1>
            <p class="mt-1 text-sm text-[color:var(--km-muted)]">
              Navega tablas y registros (solo admin).
            </p>
          </div>

          <div class="flex gap-2">
            <Link
              href="/admin"
              class="km-link text-sm font-semibold"
            >
              ← Admin
            </Link>
            <button
              type="button"
              @click="clear"
              class="km-btn w-auto px-4 py-2 text-sm"
            >
              Limpiar
            </button>
          </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-4">
          <!-- Sidebar tablas -->
          <div class="km-card overflow-hidden lg:col-span-1">
            <div class="px-6 py-4 flex items-center justify-between">
              <h2 class="text-sm font-semibold text-[color:var(--km-text)]">Tablas</h2>
              <span class="text-xs text-[color:var(--km-muted)]">Total: {{ tables.length }}</span>
            </div>
            <div class="km-divider" />

            <div class="p-4">
              <input
                v-model="q"
                type="text"
                placeholder="Buscar tabla..."
                class="km-input"
              />

              <div class="mt-3 max-h-[60vh] space-y-2 overflow-auto pr-1">
                <button
                  v-for="t in filteredTables"
                  :key="t"
                  type="button"
                  @click="openTable(t)"
                  class="w-full rounded-xl border px-3 py-2 text-left text-sm shadow-sm shadow-slate-900/30"
                  :class="activeTable === t
                    ? 'border-[color:var(--km-border)] bg-white/80 text-[color:var(--km-text)]'
                    : 'border-[color:var(--km-border)] bg-[color:var(--km-bg-2)] text-[color:var(--km-muted)] hover:bg-white/80 hover:text-[color:var(--km-text)]'"
                >
                  {{ t }}
                </button>

                <div v-if="filteredTables.length === 0" class="text-xs text-[color:var(--km-muted)]">
                  No hay coincidencias.
                </div>
              </div>
            </div>
          </div>

          <!-- Tabla -->
          <div class="km-card overflow-hidden lg:col-span-3">
            <div class="px-6 py-4 flex items-center justify-between">
              <h2 class="text-sm font-semibold text-[color:var(--km-text)]">
                {{ activeTable ? `Tabla: ${activeTable}` : 'Selecciona una tabla' }}
              </h2>

              <span v-if="rows" class="text-xs text-[color:var(--km-muted)]">
                Página {{ rows.current_page }} / {{ rows.last_page }} · Total: {{ rows.total }}
              </span>
            </div>
            <div class="km-divider" />

            <div v-if="!activeTable" class="p-6 text-sm text-[color:var(--km-muted)]">
              Elige una tabla a la izquierda.
            </div>

            <div v-else-if="!rows" class="p-6 text-sm text-[color:var(--km-muted)]">
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
                    <td v-for="c in columns" :key="c" class="whitespace-nowrap text-sm text-[color:var(--km-text)]">
                      {{ r?.[c] ?? '' }}
                    </td>

                    <td class="whitespace-nowrap">
                      <Link
                        v-if="columns.includes('id')"
                        :href="`/admin/db/row?table=${encodeURIComponent(activeTable)}&id=${encodeURIComponent(r.id)}`"
                        class="km-link text-xs"
                      >
                        Ver
                      </Link>
                      <span v-else class="text-[color:var(--km-muted)]">—</span>
                    </td>
                  </tr>
                </tbody>
              </table>

              <!-- Paginación -->
              <div class="flex items-center justify-between px-6 py-4">
                <span class="text-xs text-[color:var(--km-muted)]">Mostrando 50 por página</span>

                <div class="flex gap-2">
                  <Link
                    v-if="rows.prev_page_url"
                    :href="rows.prev_page_url"
                    class="km-btn w-auto px-4 py-2 text-sm"
                    preserve-scroll
                  >
                    ← Anterior
                  </Link>

                  <Link
                    v-if="rows.next_page_url"
                    :href="rows.next_page_url"
                    class="km-btn w-auto px-4 py-2 text-sm"
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
