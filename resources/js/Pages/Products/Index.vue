<script setup>
import { ref, computed } from 'vue';
import { Head, usePage, router, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    products: {
        type: Object,
        default: () => ({}),
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

const productItems = computed(() => props.products?.data ?? []);
const paginationLinks = computed(() => props.products?.links ?? []);
const paginationMeta = computed(() => props.products?.meta ?? null);

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
                        <h1 class="text-2xl font-semibold text-[color:var(--km-text)]">
                            Productos
                        </h1>
                        <p class="mt-1 text-sm text-[color:var(--km-muted)]">
                            Listado de productos que usas en tu cocina:
                            nombre, unidad, cantidad base y ubicación habitual.
                        </p>
                    </div>

                    <!-- Botón a pantalla de creación -->
                    <Link
                        :href="route('products.create')"
                        class="km-btn w-auto px-4 py-2 text-sm"
                    >
                        Nuevo producto
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
                        <div class="w-full md:w-1/3">
                            <InputLabel
                                for="search"
                                value="Buscar por nombre"
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

                        <div class="w-full md:w-1/3 flex gap-3">
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
                                    <option value="name">
                                        Nombre
                                    </option>
                                    <option value="default_unit">
                                        Unidad
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

                        <div class="flex gap-2 justify-end">
                            <button
                                type="button"
                                class="km-link text-sm"
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
                        v-if="!productItems.length"
                        class="p-6 text-center text-[color:var(--km-muted)] text-sm"
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
                                    v-for="product in productItems"
                                    :key="product.id"
                                >
                                    <td
                                        class="whitespace-nowrap text-sm font-medium text-[color:var(--km-text)]"
                                    >
                                        {{ product.name }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-[color:var(--km-text)]"
                                    >
                                        <span v-if="product.default_quantity">
                                            {{ product.default_quantity }}
                                            {{ product.default_unit }}
                                        </span>
                                        <span v-else class="text-[color:var(--km-muted)]">
                                            —
                                        </span>
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-[color:var(--km-muted)]"
                                    >
                                        {{
                                            product.location?.name
                                                ?? 'Sin ubicación'
                                        }}
                                    </td>
                                    <td
                                        class="text-sm text-[color:var(--km-muted)]"
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
                                                class="km-link text-xs"
                                            >
                                                Editar
                                            </Link>
                                            <button
                                                type="button"
                                                class="text-xs px-3 py-1 rounded-lg border border-rose-500/50 text-rose-600 hover:bg-rose-500/10"
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

                <div
                    v-if="paginationLinks.length > 3"
                    class="flex flex-col items-center justify-between gap-3 px-2 text-sm text-[color:var(--km-muted)] sm:flex-row"
                >
                    <p v-if="paginationMeta">
                        Mostrando {{ paginationMeta.from ?? 0 }}–{{ paginationMeta.to ?? 0 }}
                        de {{ paginationMeta.total ?? 0 }} productos
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
            </div>
        </div>
    </AuthenticatedLayout>
</template>
