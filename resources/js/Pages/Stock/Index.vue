<script setup>
import { computed, ref } from 'vue';
import { Head, usePage, router, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    stockItems: {
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

// Lista de reposición (sobre los ítems ya filtrados)
const lowStockItems = computed(() =>
    props.stockItems.filter((item) => item.is_below_minimum === true),
);

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

// --- EXPORTAR LISTA DE REPOSICIÓN (CSV) ---
const exportReplenishment = () => {
    const params = {
        product_id: filterState.value.product_id || undefined,
        location_id: filterState.value.location_id || undefined,
    };

    const url = route('stock.replenishment.export', params);
    window.location.href = url;
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
                        <h1 class="text-2xl font-semibold text-slate-50">
                            Stock de mi cocina
                        </h1>
                        <p class="mt-1 text-sm text-slate-400">
                            Vista general del stock: cantidades, ubicaciones y
                            caducidades de tus productos.
                        </p>
                    </div>

                    <!-- Botón a pantalla de creación -->
                    <Link
                        :href="route('stock.create')"
                        class="inline-flex items-center rounded-xl border border-indigo-500/70 bg-indigo-500/15 px-4 py-2 text-sm font-medium text-indigo-100 shadow-sm shadow-indigo-500/30 hover:bg-indigo-500/25"
                    >
                        Nuevo registro de stock
                    </Link>
                </div>

                <!-- Mensaje de éxito -->
                <div
                    v-if="hasSuccessMessage"
                    class="rounded-2xl border border-emerald-500/60 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200 shadow-sm shadow-emerald-500/30"
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
                                class="text-slate-200"
                            />
                            <select
                                id="filter_product"
                                v-model="filterState.product_id"
                                class="mt-1 block w-full rounded-xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm text-slate-100 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                            >
                                <option
                                    value=""
                                    class="bg-slate-900 text-slate-100"
                                >
                                    Todos los productos
                                </option>
                                <option
                                    v-for="product in products"
                                    :key="product.id"
                                    :value="product.id"
                                    class="bg-slate-900 text-slate-100"
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
                                class="text-slate-200"
                            />
                            <select
                                id="filter_location"
                                v-model="filterState.location_id"
                                class="mt-1 block w-full rounded-xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm text-slate-100 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                            >
                                <option
                                    value=""
                                    class="bg-slate-900 text-slate-100"
                                >
                                    Todas las ubicaciones
                                </option>
                                <option
                                    v-for="location in locations"
                                    :key="location.id"
                                    :value="location.id"
                                    class="bg-slate-900 text-slate-100"
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
                                class="text-slate-200"
                            />
                            <select
                                id="filter_status"
                                v-model="filterState.status"
                                class="mt-1 block w-full rounded-xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm text-slate-100 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                            >
                                <option
                                    value=""
                                    class="bg-slate-900 text-slate-100"
                                >
                                    Todos
                                </option>
                                <option
                                    value="low"
                                    class="bg-slate-900 text-slate-100"
                                >
                                    Solo bajo mínimo
                                </option>
                                <option
                                    value="normal"
                                    class="bg-slate-900 text-slate-100"
                                >
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
                                    class="text-slate-200"
                                />
                                <select
                                    id="sort"
                                    v-model="filterState.sort"
                                    class="mt-1 block w-full rounded-xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm text-slate-100 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                >
                                    <option
                                        value="expires_at"
                                        class="bg-slate-900 text-slate-100"
                                    >
                                        Fecha de caducidad
                                    </option>
                                    <option
                                        value="quantity"
                                        class="bg-slate-900 text-slate-100"
                                    >
                                        Cantidad
                                    </option>
                                </select>
                            </div>

                            <div class="flex-1">
                                <InputLabel
                                    for="direction"
                                    value="Dirección"
                                    class="text-slate-200"
                                />
                                <select
                                    id="direction"
                                    v-model="filterState.direction"
                                    class="mt-1 block w-full rounded-xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm text-slate-100 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                >
                                    <option
                                        value="asc"
                                        class="bg-slate-900 text-slate-100"
                                    >
                                        Ascendente
                                    </option>
                                    <option
                                        value="desc"
                                        class="bg-slate-900 text-slate-100"
                                    >
                                        Descendente
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="flex gap-2 justify-end w-full md:w-1/2">
                            <button
                                type="button"
                                class="text-sm text-slate-400 hover:text-slate-100"
                                @click="clearFilters"
                            >
                                Limpiar
                            </button>
                            <PrimaryButton
                                type="button"
                                @click="applyFilters"
                            >
                                Aplicar filtros
                            </PrimaryButton>
                        </div>
                    </div>
                </div>

                <!-- Tabla de stock -->
                <div class="km-card overflow-hidden">
                    <div
                        v-if="!stockItems.length"
                        class="p-6 text-center text-slate-400 text-sm"
                    >
                        Todavía no tienes stock registrado (o los filtros no
                        devuelven resultados).
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
                                <tr v-for="item in stockItems" :key="item.id">
                                    <td
                                        class="whitespace-nowrap text-sm font-medium text-slate-50"
                                    >
                                        {{ item.product?.name ?? '—' }}
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
                                            item.location?.name ??
                                            'Sin ubicación'
                                        }}
                                    </td>
                                    <!-- SOLO fecha -->
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

                                    <!-- Estado -->
                                    <td class="whitespace-nowrap text-sm">
                                        <div class="flex flex-wrap gap-1">
                                            <span
                                                v-if="item.is_below_minimum"
                                                class="km-badge-red"
                                            >
                                                Bajo mínimo
                                            </span>

                                            <span
                                                v-if="item.is_open"
                                                class="km-badge-amber"
                                            >
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
                                        </div>
                                    </td>

                                    <!-- Acciones -->
                                    <td
                                        class="whitespace-nowrap text-sm text-right"
                                    >
                                        <div class="inline-flex gap-2">
                                            <!-- EDITAR: ahora usamos router.visit -->
                                            <button
                                                type="button"
                                                class="text-xs px-3 py-1 rounded-lg border border-slate-600/80 text-slate-100 hover:bg-slate-800/80"
                                                @click="goToEdit(item)"
                                            >
                                                Editar
                                            </button>

                                            <button
                                                type="button"
                                                class="text-xs px-3 py-1 rounded-lg border border-rose-500/70 text-rose-300 hover:bg-rose-500/15"
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

                <!-- Lista de reposición -->
                <div class="km-card overflow-hidden">
                    <div
                        class="px-6 py-4 border-b border-slate-800/80 flex items-center justify-between"
                    >
                        <h2 class="text-sm font-semibold text-slate-100">
                            Lista de reposición (bajo mínimo)
                        </h2>

                        <div class="flex items-center gap-3">
                            <span class="text-xs text-slate-400">
                                Total: {{ lowStockItems.length }}
                            </span>
                            <button
                                type="button"
                                @click="exportReplenishment"
                                class="inline-flex items-center rounded-xl border border-emerald-500/70 bg-emerald-500/10 px-3 py-1.5 text-xs font-medium text-emerald-100 shadow-sm shadow-emerald-500/30 hover:bg-emerald-500/20"
                            >
                                Exportar lista (CSV)
                            </button>
                        </div>
                    </div>

                    <div
                        v-if="!lowStockItems.length"
                        class="p-6 text-sm text-slate-400"
                    >
                        De momento no hay ningún producto por debajo del mínimo.
                        ✅
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
                                            item.location?.name ??
                                            'Sin ubicación'
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
                                        class="whitespace-nowrap text-sm text-slate-200"
                                    >
                                        {{
                                            Math.max(
                                                0,
                                                (item.min_quantity ?? 0) -
                                                    (item.quantity ?? 0),
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
