<script setup>
import { computed, ref } from 'vue';
import { Head, usePage, router, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    stockItems: {
        type: Object,
        default: () => ({}),
    },
    lowStockItems: {
        type: Array,
        default: () => [],
    },
    products: {
        type: Array,
        default: () => [],
    },
    locations: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({
            product_id: '',
            location_id: '',
            status: '',
            sort: 'expires_at',
            direction: 'asc',
        }),
    },
});

const page = usePage();

const filterState = ref({
    product_id: props.filters.product_id ?? '',
    location_id: props.filters.location_id ?? '',
    status: props.filters.status ?? '',
    sort: props.filters.sort ?? 'expires_at',
    direction: props.filters.direction ?? 'asc',
});

const hasSuccessMessage = computed(() => {
    return Boolean(page.props.flash && page.props.flash.success);
});

const successMessage = computed(() => {
    return page.props.flash?.success ?? '';
});

const paginatedItems = computed(() => props.stockItems?.data ?? []);
const paginationLinks = computed(() => props.stockItems?.links ?? []);
const paginationMeta = computed(() => props.stockItems?.meta ?? null);

// Lista de reposición (sobre los ítems ya filtrados)
const replenishmentItems = computed(() => props.lowStockItems ?? []);

// --- FILTROS ---
const applyFilters = () => {
    router.get(
        route('stock.index'),
        {
            product_id: filterState.value.product_id || undefined,
            location_id: filterState.value.location_id || undefined,
            status: filterState.value.status || undefined,
            sort: filterState.value.sort,
            direction: filterState.value.direction,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
};

const clearFilters = () => {
    filterState.value = {
        product_id: '',
        location_id: '',
        status: '',
        sort: 'expires_at',
        direction: 'asc',
    };
    applyFilters();
};

// --- BORRAR REGISTRO ---
const deleteItem = (item) => {
    if (
        !confirm(
            `¿Seguro que quieres eliminar el stock de "${
                item.product?.name ?? 'producto'
            }"?`,
        )
    ) {
        return;
    }

    router.delete(route('stock.destroy', item.id), {
        preserveScroll: true,
    });
};

// --- IR A EDITAR (FORZAMOS /edit) ---
const goToEdit = (item) => {
    router.visit(route('stock.edit', item.id));
};

// --- LÓGICA DE CADUCIDAD ---
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

    return null;
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

// --- EXPORTAR LISTA DE REPOSICIÓN (CSV / PDF) ---
// ✅ NO usamos Ziggy aquí (evita /stock.export.missing y problemas de rutas no incluidas)
const exportReplenishmentCsv = () => {
    const url = new URL('/stock/export/missing.csv', window.location.origin);

    if (filterState.value.product_id) {
        url.searchParams.set('product_id', String(filterState.value.product_id));
    }

    if (filterState.value.location_id) {
        url.searchParams.set('location_id', String(filterState.value.location_id));
    }

    window.location.href = url.toString();
};

const exportReplenishmentPdf = () => {
    const url = new URL('/stock/export/missing.pdf', window.location.origin);

    if (filterState.value.product_id) {
        url.searchParams.set('product_id', String(filterState.value.product_id));
    }

    if (filterState.value.location_id) {
        url.searchParams.set('location_id', String(filterState.value.location_id));
    }

    window.open(url.toString(), '_blank', 'noopener');
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Stock" />

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                <!-- Cabecera -->
                <div
                    class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div>
                        <h1 class="text-2xl font-semibold text-[color:var(--km-text)]">
                            Stock de mi cocina
                        </h1>
                        <p class="mt-1 text-sm text-[color:var(--km-muted)]">
                            Vista general del stock: cantidades, ubicaciones y
                            caducidades de tus productos.
                        </p>
                    </div>

                    <!-- Botón a pantalla de creación -->
                    <Link
                        :href="route('stock.create')"
                        class="km-btn w-auto px-4 py-2 text-sm"
                    >
                        Nuevo registro de stock
                    </Link>
                </div>

                <!-- Mensaje de éxito -->
                <div
                    v-if="hasSuccessMessage"
                    class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700"
                >
                    {{ successMessage }}
                </div>

                <!-- Filtros -->
                <div class="km-card p-4 sm:p-5">
                    <div
                        class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between"
                    >
                        <!-- Producto -->
                        <div class="w-full md:w-1/3">
                            <InputLabel
                                for="filter_product"
                                value="Filtrar por producto"
                            />
                            <select
                                id="filter_product"
                                v-model="filterState.product_id"
                                class="km-input mt-1"
                            >
                                <option value="">
                                    Todos los productos
                                </option>
                                <option
                                    v-for="product in products"
                                    :key="product.id"
                                    :value="product.id"
                                >
                                    {{ product.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Ubicación -->
                        <div class="w-full md:w-1/3">
                            <InputLabel
                                for="filter_location"
                                value="Filtrar por ubicación"
                            />
                            <select
                                id="filter_location"
                                v-model="filterState.location_id"
                                class="km-input mt-1"
                            >
                                <option value="">
                                    Todas las ubicaciones
                                </option>
                                <option
                                    v-for="location in locations"
                                    :key="location.id"
                                    :value="location.id"
                                >
                                    {{ location.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Estado -->
                        <div class="w-full md:w-1/3">
                            <InputLabel
                                for="filter_status"
                                value="Estado"
                            />
                            <select
                                id="filter_status"
                                v-model="filterState.status"
                                class="km-input mt-1"
                            >
                                <option value="">
                                    Todos
                                </option>
                                <option value="low">
                                    Solo bajo mínimo
                                </option>
                                <option value="normal">
                                    Solo normales
                                </option>
                            </select>
                        </div>
                    </div>

                    <div
                        class="mt-4 flex flex-col gap-4 md:flex-row md:items-end md:justify-between"
                    >
                        <!-- Orden -->
                        <div class="flex gap-3 w-full md:w-1/2">
                            <div class="flex-1">
                                <InputLabel
                                    for="sort"
                                    value="Ordenar por"
                                />
                                <select
                                    id="sort"
                                    v-model="filterState.sort"
                                    class="km-input mt-1"
                                >
                                    <option value="expires_at">
                                        Fecha de caducidad
                                    </option>
                                    <option value="quantity">
                                        Cantidad
                                    </option>
                                </select>
                            </div>

                            <div class="flex-1">
                                <InputLabel
                                    for="direction"
                                    value="Dirección"
                                />
                                <select
                                    id="direction"
                                    v-model="filterState.direction"
                                    class="km-input mt-1"
                                >
                                    <option value="asc">
                                        Ascendente
                                    </option>
                                    <option value="desc">
                                        Descendente
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="flex gap-2 justify-end w-full md:w-1/2">
                            <button
                                type="button"
                                class="km-link text-sm"
                                @click="clearFilters"
                            >
                                Limpiar
                            </button>
                            <PrimaryButton type="button" @click="applyFilters">
                                Aplicar filtros
                            </PrimaryButton>
                        </div>
                    </div>
                </div>

                <!-- Tabla de stock -->
                <div class="km-card overflow-hidden">
                    <div
                        v-if="!paginatedItems.length"
                        class="p-6 text-center text-[color:var(--km-muted)] text-sm"
                    >
                        Todavía no tienes stock registrado (o los filtros no devuelven resultados).
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="km-table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Ubicación</th>
                                    <th>Caducidad</th>
                                    <th>Estado</th>
                                    <th class="text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in paginatedItems" :key="item.id">
                                    <td class="whitespace-nowrap text-sm font-medium text-[color:var(--km-text)]">
                                        {{ item.product?.name ?? '—' }}
                                    </td>

                                    <td class="whitespace-nowrap text-sm text-[color:var(--km-text)]">
                                        {{ item.quantity }} {{ item.unit }}
                                    </td>

                                    <td class="whitespace-nowrap text-sm text-[color:var(--km-muted)]">
                                        {{ item.location?.name ?? 'Sin ubicación' }}
                                    </td>

                                    <!-- SOLO fecha -->
                                    <td class="whitespace-nowrap text-sm text-[color:var(--km-muted)]">
                                        {{
                                            item.expires_at
                                                ? item.expires_at.substring(0, 10)
                                                : '—'
                                        }}
                                    </td>

                                    <!-- Estado -->
                                    <td class="whitespace-nowrap text-sm">
                                        <div class="flex flex-wrap gap-1">
                                            <span
                                                v-if="item.is_below_minimum"
                                                class="km-badge-red"
                                            >
                                                Bajo mínimo
                                            </span>

                                            <span v-if="item.is_open" class="km-badge-amber">
                                                Abierto
                                            </span>

                                            <span
                                                v-if="
                                                    getExpiryStatus(item) &&
                                                    getExpiryLabel(getExpiryStatus(item))
                                                "
                                                :class="[
                                                    'inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium border',
                                                    (getExpiryStatus(item)?.type === 'expired' ||
                                                        getExpiryStatus(item)?.type === 'urgent' ||
                                                        getExpiryStatus(item)?.type === 'today')
                                                        ? 'bg-rose-500/10 text-rose-600 border-rose-500/40'
                                                        : 'bg-amber-500/10 text-amber-700 border-amber-500/40',
                                                ]"
                                            >
                                                {{ getExpiryLabel(getExpiryStatus(item)) }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Acciones -->
                                    <td class="whitespace-nowrap text-sm text-right">
                                        <div class="inline-flex gap-2">
                                            <button
                                                type="button"
                                                class="km-link text-xs"
                                                @click="goToEdit(item)"
                                            >
                                                Editar
                                            </button>

                                            <button
                                                type="button"
                                                class="text-xs px-3 py-1 rounded-lg border border-rose-500/50 text-rose-600 hover:bg-rose-500/10"
                                                @click="deleteItem(item)"
                                            >
                                                Borrar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div
                    v-if="paginationLinks.length > 3"
                    class="flex flex-col items-center justify-between gap-3 px-2 text-sm text-[color:var(--km-muted)] sm:flex-row"
                >
                    <p v-if="paginationMeta">
                        Mostrando {{ paginationMeta.from ?? 0 }}–{{ paginationMeta.to ?? 0 }}
                        de {{ paginationMeta.total ?? 0 }} registros
                    </p>

                    <nav class="flex flex-wrap items-center gap-2" aria-label="Paginación">
                        <template v-for="link in paginationLinks" :key="link.label">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                class="rounded-lg border border-[color:var(--km-border)] px-3 py-1 text-xs font-medium transition hover:bg-[color:var(--km-bg-2)]"
                                :class="{
                                    'bg-[color:var(--km-bg-2)] text-[color:var(--km-text)]': link.active,
                                }"
                                v-html="link.label"
                            />
                            <span
                                v-else
                                class="rounded-lg border border-transparent px-3 py-1 text-xs text-[color:var(--km-muted)]"
                                v-html="link.label"
                            />
                        </template>
                    </nav>
                </div>

                <!-- Lista de reposición -->
                <div class="km-card overflow-hidden">
                    <div class="px-6 py-4 flex items-center justify-between">
                        <h2 class="text-sm font-semibold text-[color:var(--km-text)]">
                            Lista de reposición (bajo mínimo)
                        </h2>

                        <div class="flex items-center gap-3">
                            <span class="text-xs text-[color:var(--km-muted)]">
                                Total: {{ replenishmentItems.length }}
                            </span>

                            <button
                                type="button"
                                @click="exportReplenishmentPdf"
                                class="km-btn w-auto px-3 py-1.5 text-xs"
                            >
                                Exportar PDF
                            </button>

                            <button
                                type="button"
                                @click="exportReplenishmentCsv"
                                class="inline-flex items-center rounded-xl border border-[color:var(--km-border)] bg-[color:var(--km-bg-2)] px-3 py-1.5 text-xs font-medium text-[color:var(--km-text)] shadow-sm hover:bg-white/80"
                            >
                                Exportar CSV
                            </button>
                        </div>
                    </div>
                    <div class="km-divider" />

                    <div
                        v-if="!replenishmentItems.length"
                        class="p-6 text-sm text-[color:var(--km-muted)]"
                    >
                        De momento no hay ningún producto por debajo del mínimo. ✅
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="km-table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Ubicación</th>
                                    <th>Cantidad actual</th>
                                    <th>Mínimo</th>
                                    <th>Falta aprox.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in replenishmentItems" :key="item.id">
                                    <td class="whitespace-nowrap text-sm font-medium text-[color:var(--km-text)]">
                                        {{ item.product?.name ?? '—' }}
                                    </td>

                                    <td class="whitespace-nowrap text-sm text-[color:var(--km-muted)]">
                                        {{ item.location?.name ?? 'Sin ubicación' }}
                                    </td>

                                    <td class="whitespace-nowrap text-sm text-[color:var(--km-text)]">
                                        {{ item.quantity }} {{ item.unit }}
                                    </td>

                                    <td class="whitespace-nowrap text-sm text-[color:var(--km-text)]">
                                        {{ item.min_quantity }}
                                    </td>

                                    <td class="whitespace-nowrap text-sm text-[color:var(--km-text)]">
                                        {{
                                            Math.max(
                                                0,
                                                (item.min_quantity ?? 0) - (item.quantity ?? 0),
                                            ).toFixed(2)
                                        }}
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
