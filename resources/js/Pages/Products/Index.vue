<script setup>
import { ref, computed } from 'vue';
import { Head, usePage, router, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
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
            search: '',
            location_id: '',
            sort: 'name',
            direction: 'asc',
        }),
    },
});

const page = usePage();

const filterState = ref({
    search: props.filters.search ?? '',
    location_id: props.filters.location_id ?? '',
    sort: props.filters.sort ?? 'name',
    direction: props.filters.direction ?? 'asc',
});

const hasSuccessMessage = computed(() => {
    return Boolean(page.props.flash && page.props.flash.success);
});

const successMessage = computed(() => {
    return page.props.flash?.success ?? '';
});

// Filtros
const applyFilters = () => {
    router.get(
        route('products.index'),
        {
            search: filterState.value.search || undefined,
            location_id: filterState.value.location_id || undefined,
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
        search: '',
        location_id: '',
        sort: 'name',
        direction: 'asc',
    };
    applyFilters();
};

// Borrar producto
const deleteProduct = (product) => {
    if (
        !confirm(
            `¿Seguro que quieres eliminar el producto "${product.name}"?`,
        )
    ) {
        return;
    }

    router.delete(route('products.destroy', product.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Productos" />

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                <!-- Cabecera -->
                <div
                    class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div>
                        <h1 class="text-2xl font-semibold text-slate-50">
                            Productos
                        </h1>
                        <p class="mt-1 text-sm text-slate-400">
                            Listado de productos que usas en tu cocina:
                            nombre, unidad, cantidad base y ubicación habitual.
                        </p>
                    </div>

                    <!-- Botón a pantalla de creación -->
                    <Link
                        :href="route('products.create')"
                        class="inline-flex items-center rounded-xl border border-indigo-500/70 bg-indigo-500/15 px-4 py-2 text-sm font-medium text-indigo-100 shadow-sm shadow-indigo-500/30 hover:bg-indigo-500/25"
                    >
                        Nuevo producto
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
                        <div class="w-full md:w-1/3">
                            <InputLabel
                                for="search"
                                value="Buscar por nombre"
                                class="text-slate-200"
                            />
                            <TextInput
                                id="search"
                                v-model="filterState.search"
                                type="text"
                                class="mt-1 block w-full"
                                placeholder="Buscar producto..."
                                @keyup.enter="applyFilters"
                            />
                        </div>

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

                        <div class="w-full md:w-1/3 flex gap-3">
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
                                        value="name"
                                        class="bg-slate-900 text-slate-100"
                                    >
                                        Nombre
                                    </option>
                                    <option
                                        value="default_unit"
                                        class="bg-slate-900 text-slate-100"
                                    >
                                        Unidad
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

                        <div class="flex gap-2 justify-end">
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

                <!-- Tabla -->
                <div class="km-card overflow-hidden">
                    <div
                        v-if="!products.length"
                        class="p-6 text-center text-slate-400 text-sm"
                    >
                        No hay productos que coincidan con los filtros
                        actuales.
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="km-table">
                            <thead>
                                <tr>
                                    <th>
                                        Nombre
                                    </th>
                                    <th>
                                        Cantidad base
                                    </th>
                                    <th>
                                        Ubicación habitual
                                    </th>
                                    <th>
                                        Notas
                                    </th>
                                    <th class="text-right">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="product in products"
                                    :key="product.id"
                                >
                                    <td
                                        class="whitespace-nowrap text-sm font-medium text-slate-50"
                                    >
                                        {{ product.name }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-slate-200"
                                    >
                                        <span v-if="product.default_quantity">
                                            {{ product.default_quantity }}
                                            {{ product.default_unit }}
                                        </span>
                                        <span v-else class="text-slate-500">
                                            —
                                        </span>
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-slate-300"
                                    >
                                        {{
                                            product.location?.name
                                                ?? 'Sin ubicación'
                                        }}
                                    </td>
                                    <td
                                        class="text-sm text-slate-300"
                                    >
                                        {{ product.notes || '—' }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-right"
                                    >
                                        <div class="inline-flex gap-2">
                                            <Link
                                                :href="
                                                    route(
                                                        'products.edit',
                                                        product.id,
                                                    )
                                                "
                                                class="text-xs px-3 py-1 rounded-lg border border-slate-600/80 text-slate-100 hover:bg-slate-800/80"
                                            >
                                                Editar
                                            </Link>
                                            <button
                                                type="button"
                                                class="text-xs px-3 py-1 rounded-lg border border-rose-500/70 text-rose-300 hover:bg-rose-500/15"
                                                @click="deleteProduct(product)"
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
            </div>
        </div>
    </AuthenticatedLayout>
</template>
