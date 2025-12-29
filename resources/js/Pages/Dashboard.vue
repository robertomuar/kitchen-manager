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

// ---- Navegación tarjetas (solo interactividad) ----
const goToProducts = () => {
    router.visit(route('products.index'));
};

const goToLocations = () => {
    router.visit(route('locations.index'));
};

const goToStock = () => {
    router.visit(route('stock.index'));
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
                        <h1 class="text-2xl font-semibold text-[color:var(--km-text)]">
                            Panel
                        </h1>
                        <p class="mt-1 text-sm text-[color:var(--km-muted)]">
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
                        class="km-card p-4 cursor-pointer"
                        role="button"
                        tabindex="0"
                        @click="goToProducts"
                        @keydown.enter.prevent="goToProducts"
                        @keydown.space.prevent="goToProducts"
                    >
                        <p
                            class="text-xs font-medium uppercase tracking-wide text-[color:var(--km-muted)]"
                        >
                            Productos
                        </p>
                        <p class="mt-3 text-3xl font-semibold text-[color:var(--km-text)]">
                            {{ productsCount }}
                        </p>
                        <p class="mt-1 text-xs text-[color:var(--km-muted)]">
                            Elementos configurados en tu catálogo.
                        </p>
                    </div>

                    <!-- Ubicaciones -->
                    <div
                        class="km-card p-4 cursor-pointer"
                        role="button"
                        tabindex="0"
                        @click="goToLocations"
                        @keydown.enter.prevent="goToLocations"
                        @keydown.space.prevent="goToLocations"
                    >
                        <p
                            class="text-xs font-medium uppercase tracking-wide text-[color:var(--km-muted)]"
                        >
                            Ubicaciones
                        </p>
                        <p class="mt-3 text-3xl font-semibold text-[color:var(--km-text)]">
                            {{ locationsCount }}
                        </p>
                        <p class="mt-1 text-xs text-[color:var(--km-muted)]">
                            Neveras, congeladores, armarios, estanterías...
                        </p>
                    </div>

                    <!-- Registros de stock -->
                    <div
                        class="km-card p-4 cursor-pointer"
                        role="button"
                        tabindex="0"
                        @click="goToStock"
                        @keydown.enter.prevent="goToStock"
                        @keydown.space.prevent="goToStock"
                    >
                        <p
                            class="text-xs font-medium uppercase tracking-wide text-[color:var(--km-muted)]"
                        >
                            Registros de stock
                        </p>
                        <p class="mt-3 text-3xl font-semibold text-[color:var(--km-text)]">
                            {{ stockItemsCount }}
                        </p>
                        <p class="mt-1 text-xs text-[color:var(--km-muted)]">
                            Entradas de stock registradas.
                        </p>
                    </div>

                    <!-- Bajo mínimo + caducidades -->
                    <div
                        class="km-card border-rose-200/70 p-4 shadow-md shadow-rose-200/30 cursor-pointer"
                        role="button"
                        tabindex="0"
                        @click="goToLowStock"
                        @keydown.enter.prevent="goToLowStock"
                        @keydown.space.prevent="goToLowStock"
                    >
                        <p
                            class="text-xs font-medium uppercase tracking-wide text-rose-600"
                        >
                            Bajo mínimo
                        </p>
                        <p class="mt-3 text-3xl font-semibold text-rose-700">
                            {{ lowStockCount }}
                        </p>
                        <p class="mt-1 text-xs text-rose-600/80">
                            Productos que necesitan reponerse.
                        </p>

                        <div class="mt-3 flex flex-wrap gap-2 text-[11px]">
                            <span
                                class="inline-flex items-center rounded-full bg-amber-500/10 px-2 py-0.5 font-medium text-amber-700 border border-amber-500/40"
                            >
                                Caducan ≤ 7 días:
                                <span class="ml-1 font-semibold">
                                    {{ soonExpiringCount }}
                                </span>
                            </span>
                            <span
                                class="inline-flex items-center rounded-full bg-rose-500/10 px-2 py-0.5 font-medium text-rose-700 border border-rose-500/40"
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
                                @click.stop="goToLowStock"
                                class="km-link text-xs"
                            >
                                Ver lista de reposición →
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Productos bajo mínimo recientes -->
                <div class="km-card overflow-hidden">
                    <div class="px-6 py-4 flex items-center justify-between">
                        <h2 class="text-sm font-semibold text-[color:var(--km-text)]">
                            Productos bajo mínimo recientes
                        </h2>
                        <span class="text-xs text-[color:var(--km-muted)]">
                            Total: {{ lowStockCount }}
                        </span>
                    </div>
                    <div class="km-divider" />

                    <div
                        v-if="!hasLowStock"
                        class="p-6 text-sm text-[color:var(--km-muted)]"
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
                                        class="whitespace-nowrap text-sm font-medium text-[color:var(--km-text)]"
                                    >
                                        {{ item.product?.name ?? '—' }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-[color:var(--km-muted)]"
                                    >
                                        {{
                                            item.location?.name
                                                ?? 'Sin ubicación'
                                        }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-[color:var(--km-text)]"
                                    >
                                        <div class="font-medium text-[color:var(--km-text)]">
                                            {{ item.available_units }} uds disponibles
                                        </div>
                                        <div class="text-xs text-[color:var(--km-muted)]">
                                            {{ item.quantity }} {{ item.unit || 'uds' }}
                                            <span v-if="(item.open_units ?? 0) > 0">
                                                ({{ item.open_units }} abiertas)
                                            </span>
                                        </div>
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-[color:var(--km-text)]"
                                    >
                                        {{ item.min_quantity }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-[color:var(--km-muted)]"
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
                    <div class="px-6 py-4 flex items-center justify-between">
                        <h2 class="text-sm font-semibold text-[color:var(--km-text)]">
                            Próximas caducidades (≤ 7 días)
                        </h2>
                        <span class="text-xs text-[color:var(--km-muted)]">
                            Total: {{ soonExpiringCount }}
                        </span>
                    </div>
                    <div class="km-divider" />

                    <div
                        v-if="!hasSoonExpiries"
                        class="p-6 text-sm text-[color:var(--km-muted)]"
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
                                        class="whitespace-nowrap text-sm font-medium text-[color:var(--km-text)]"
                                    >
                                        {{ item.product?.name ?? '—' }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-[color:var(--km-muted)]"
                                    >
                                        {{
                                            item.location?.name
                                                ?? 'Sin ubicación'
                                        }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-[color:var(--km-text)]"
                                    >
                                        {{ item.quantity }} {{ item.unit }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-[color:var(--km-muted)]"
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
                                                    ? 'bg-rose-500/10 text-rose-600 border-rose-500/40'
                                                    : 'bg-amber-500/10 text-amber-700 border-amber-500/40',
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
