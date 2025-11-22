<script setup>
import { computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({}),
    },
    lowStockItems: {
        type: Array,
        default: () => [],
    },
    soonExpiringItems: {
        type: Array,
        default: () => [],
    },
});

// Contadores
const productsCount = computed(() => props.stats?.products_count ?? 0);
const locationsCount = computed(() => props.stats?.locations_count ?? 0);
const stockItemsCount = computed(() => props.stats?.stock_items_count ?? 0);
const lowStockCount = computed(() => props.stats?.low_stock_count ?? 0);
const soonExpiringCount = computed(() => props.stats?.soon_expiring_count ?? 0);
const urgentExpiringCount = computed(() => props.stats?.urgent_expiring_count ?? 0);

// Flags
const hasLowStock = computed(() => props.lowStockItems.length > 0);
const hasSoonExpiries = computed(() => props.soonExpiringItems.length > 0);

// ---- Lógica de caducidad (igual que en Stock/Index) ----
const getExpiryStatus = (item) => {
    if (!item.expires_at) {
        return null;
    }

    const today = new Date();
    const expiryDate = new Date(item.expires_at);

    today.setHours(0, 0, 0, 0);
    expiryDate.setHours(0, 0, 0, 0);

    const diffTime = expiryDate.getTime() - today.getTime();
    const diffDays = Math.round(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays < 0) {
        return { type: 'expired', days: diffDays };
    } else if (diffDays === 0) {
        return { type: 'today', days: 0 };
    } else if (diffDays <= 2) {
        return { type: 'urgent', days: diffDays };
    } else if (diffDays <= 7) {
        return { type: 'soon', days: diffDays };
    }

    return { type: 'normal', days: diffDays };
};

const getExpiryLabel = (status) => {
    if (!status) return '';

    if (status.type === 'expired') {
        return 'Caducado';
    }

    if (status.type === 'today') {
        return 'Caduca hoy';
    }

    if (status.type === 'urgent') {
        return `Caduca en ${status.days} día${status.days === 1 ? '' : 's'}`;
    }

    if (status.type === 'soon') {
        return `Caduca en ${status.days} días`;
    }

    return '';
};

// ---- Ir a lista de reposición ----
// Ahora vamos a /stock SIN status=low y forzamos recarga del estado.
const goToLowStock = () => {
    router.get(
        route('stock.index'),
        {},
        {
            preserveState: false,   // fuerza a que el servidor mande datos nuevos
            preserveScroll: false,
            replace: false,
        },
    );
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Panel" />

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                <!-- Cabecera -->
                <div
                    class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div>
                        <h1 class="text-2xl font-semibold text-slate-50">
                            Panel
                        </h1>
                        <p class="mt-1 text-sm text-slate-400">
                            Resumen rápido de tu despensa: productos,
                            ubicaciones, stock bajo mínimo y próximas
                            caducidades.
                        </p>
                    </div>
                </div>

                <!-- Tarjetas de resumen -->
                <div class="grid gap-4 md:grid-cols-4">
                    <!-- Productos -->
                    <div
                        class="rounded-2xl border border-slate-800 bg-slate-950/70 p-4 shadow-sm shadow-slate-900/40"
                    >
                        <p
                            class="text-xs font-medium uppercase tracking-wide text-slate-400"
                        >
                            Productos
                        </p>
                        <p class="mt-3 text-3xl font-semibold text-slate-50">
                            {{ productsCount }}
                        </p>
                        <p class="mt-1 text-xs text-slate-400">
                            Elementos configurados en tu catálogo.
                        </p>
                    </div>

                    <!-- Ubicaciones -->
                    <div
                        class="rounded-2xl border border-slate-800 bg-slate-950/70 p-4 shadow-sm shadow-slate-900/40"
                    >
                        <p
                            class="text-xs font-medium uppercase tracking-wide text-slate-400"
                        >
                            Ubicaciones
                        </p>
                        <p class="mt-3 text-3xl font-semibold text-slate-50">
                            {{ locationsCount }}
                        </p>
                        <p class="mt-1 text-xs text-slate-400">
                            Neveras, congeladores, armarios, estanterías...
                        </p>
                    </div>

                    <!-- Registros de stock -->
                    <div
                        class="rounded-2xl border border-slate-800 bg-slate-950/70 p-4 shadow-sm shadow-slate-900/40"
                    >
                        <p
                            class="text-xs font-medium uppercase tracking-wide text-slate-400"
                        >
                            Registros de stock
                        </p>
                        <p class="mt-3 text-3xl font-semibold text-slate-50">
                            {{ stockItemsCount }}
                        </p>
                        <p class="mt-1 text-xs text-slate-400">
                            Entradas de stock registradas.
                        </p>
                    </div>

                    <!-- Bajo mínimo + caducidades -->
                    <div
                        class="rounded-2xl border border-rose-800/80 bg-gradient-to-br from-rose-900/70 via-rose-900/40 to-slate-950/80 p-4 shadow-md shadow-rose-900/40"
                    >
                        <p
                            class="text-xs font-medium uppercase tracking-wide text-rose-100"
                        >
                            Bajo mínimo
                        </p>
                        <p class="mt-3 text-3xl font-semibold text-rose-50">
                            {{ lowStockCount }}
                        </p>
                        <p class="mt-1 text-xs text-rose-100/80">
                            Productos que necesitan reponerse.
                        </p>

                        <div class="mt-3 flex flex-wrap gap-2 text-[11px]">
                            <span
                                class="inline-flex items-center rounded-full bg-amber-500/15 px-2 py-0.5 font-medium text-amber-100 border border-amber-500/50"
                            >
                                Caducan ≤ 7 días:
                                <span class="ml-1 font-semibold">
                                    {{ soonExpiringCount }}
                                </span>
                            </span>
                            <span
                                class="inline-flex items-center rounded-full bg-rose-500/20 px-2 py-0.5 font-medium text-rose-50 border border-rose-500/70"
                            >
                                De ellos, ≤ 2 días:
                                <span class="ml-1 font-semibold">
                                    {{ urgentExpiringCount }}
                                </span>
                            </span>
                        </div>

                        <div class="mt-4">
                            <button
                                type="button"
                                @click="goToLowStock"
                                class="text-xs font-medium text-rose-100 underline-offset-2 hover:underline"
                            >
                                Ver lista de reposición →
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Productos bajo mínimo recientes -->
                <div class="km-card overflow-hidden">
                    <div
                        class="px-6 py-4 border-b border-slate-800/80 flex items-center justify-between"
                    >
                        <h2 class="text-sm font-semibold text-slate-100">
                            Productos bajo mínimo recientes
                        </h2>
                        <span class="text-xs text-slate-400">
                            Total: {{ lowStockCount }}
                        </span>
                    </div>

                    <div
                        v-if="!hasLowStock"
                        class="p-6 text-sm text-slate-400"
                    >
                        De momento no hay productos por debajo del mínimo. ✅
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="km-table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Ubicación</th>
                                    <th>Cantidad</th>
                                    <th>Mínimo</th>
                                    <th>Actualizado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="item in lowStockItems"
                                    :key="item.id"
                                >
                                    <td
                                        class="whitespace-nowrap text-sm font-medium text-slate-50"
                                    >
                                        {{ item.product?.name ?? '—' }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-slate-300"
                                    >
                                        {{
                                            item.location?.name
                                                ?? 'Sin ubicación'
                                        }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-slate-200"
                                    >
                                        {{ item.quantity }} {{ item.unit }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-slate-200"
                                    >
                                        {{ item.min_quantity }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-slate-300"
                                    >
                                        {{
                                            item.updated_at
                                                ? item.updated_at.substring(
                                                      0,
                                                      10,
                                                  )
                                                : '—'
                                        }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Próximas caducidades -->
                <div class="km-card overflow-hidden">
                    <div
                        class="px-6 py-4 border-b border-slate-800/80 flex items-center justify-between"
                    >
                        <h2 class="text-sm font-semibold text-slate-100">
                            Próximas caducidades (≤ 7 días)
                        </h2>
                        <span class="text-xs text-slate-400">
                            Total: {{ soonExpiringCount }}
                        </span>
                    </div>

                    <div
                        v-if="!hasSoonExpiries"
                        class="p-6 text-sm text-slate-400"
                    >
                        No hay productos que caduquen en los próximos 7 días. 🙂
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="km-table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Ubicación</th>
                                    <th>Cantidad</th>
                                    <th>Caduca</th>
                                    <th>Aviso</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="item in soonExpiringItems"
                                    :key="item.id"
                                >
                                    <td
                                        class="whitespace-nowrap text-sm font-medium text-slate-50"
                                    >
                                        {{ item.product?.name ?? '—' }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-slate-300"
                                    >
                                        {{
                                            item.location?.name
                                                ?? 'Sin ubicación'
                                        }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-slate-200"
                                    >
                                        {{ item.quantity }} {{ item.unit }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-slate-300"
                                    >
                                        {{
                                            item.expires_at
                                                ? item.expires_at.substring(
                                                      0,
                                                      10,
                                                  )
                                                : '—'
                                        }}
                                    </td>
                                    <td class="whitespace-nowrap text-sm">
                                        <span
                                            v-if="getExpiryStatus(item)"
                                            :class="[
                                                'inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium border',
                                                (getExpiryStatus(item)?.type ===
                                                    'expired' ||
                                                    getExpiryStatus(item)
                                                        ?.type === 'urgent' ||
                                                    getExpiryStatus(item)
                                                        ?.type === 'today')
                                                    ? 'bg-rose-500/10 text-rose-200 border-rose-500/60'
                                                    : 'bg-amber-500/10 text-amber-100 border-amber-500/60',
                                            ]"
                                        >
                                            {{
                                                getExpiryLabel(
                                                    getExpiryStatus(item),
                                                )
                                            }}
                                        </span>
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
