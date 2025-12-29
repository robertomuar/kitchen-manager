<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import { useCsrfForm } from '@/Composables/useCsrfForm';
import axios from 'axios';

const props = defineProps({
    stockItem: {
        type: Object,
        default: null,
    },
    item: {
        type: Object,
        default: null,
    },
    products: {
        type: Array,
        default: () => [],
    },
    locations: {
        type: Array,
        default: () => [],
    },
    movements: {
        type: Array,
        default: () => [],
    },
    mode: {
        type: String,
        default: 'create', // 'create' | 'edit'
    },
});

const UNIT_OPTIONS = [
    'unidad',
    'ud',
    'L',
    'ml',
    'kg',
    'g',
    'pack',
    'caja',
    'bolsa',
    'bote',
];

const baseItem = props.stockItem ?? props.item ?? null;

const productOptions = ref(props.products ?? []);
const productMeta = ref(null);
const productSearch = ref('');
const productLoading = ref(false);

const locationOptions = ref(props.locations ?? []);
const locationMeta = ref(null);
const locationSearch = ref('');
const locationLoading = ref(false);

let productSearchTimeout;
let locationSearchTimeout;

const isEdit = computed(
    () => props.mode === 'edit' || !!baseItem?.id,
);

const form = useCsrfForm({
    product_id: baseItem?.product_id ?? '',
    quantity: baseItem?.quantity ?? '',
    unit: baseItem?.unit ?? '',
    location_id: baseItem?.location_id ?? '',
    min_quantity: baseItem?.min_quantity ?? '',
    expires_at: baseItem?.expires_at
        ? String(baseItem.expires_at).substring(0, 10)
        : '',
    is_open: baseItem?.is_open ?? false,
    notes: baseItem?.notes ?? '',
});

const title = computed(() =>
    isEdit.value ? 'Editar registro de stock' : 'Nuevo registro de stock',
);

const submitLabel = computed(() =>
    isEdit.value ? 'Guardar cambios' : 'Guardar stock',
);

const fetchProductOptions = async (page = 1) => {
    productLoading.value = true;

    try {
        const response = await axios.get(route('products.options'), {
            params: {
                search: productSearch.value || undefined,
                page,
            },
        });

        productOptions.value = response.data.data ?? [];
        productMeta.value = response.data.meta ?? null;
    } catch (error) {
        console.error('No se pudieron cargar los productos', error);
    } finally {
        productLoading.value = false;
    }
};

const fetchLocationOptions = async (page = 1) => {
    locationLoading.value = true;

    try {
        const response = await axios.get(route('locations.options'), {
            params: {
                search: locationSearch.value || undefined,
                page,
            },
        });

        locationOptions.value = response.data.data ?? [];
        locationMeta.value = response.data.meta ?? null;
    } catch (error) {
        console.error('No se pudieron cargar las ubicaciones', error);
    } finally {
        locationLoading.value = false;
    }
};

const changeProductPage = (page) => {
    if (!productMeta.value) return;

    if (page < 1 || page > (productMeta.value.last_page ?? 1)) {
        return;
    }

    fetchProductOptions(page);
};

const changeLocationPage = (page) => {
    if (!locationMeta.value) return;

    if (page < 1 || page > (locationMeta.value.last_page ?? 1)) {
        return;
    }

    fetchLocationOptions(page);
};

watch(productSearch, () => {
    clearTimeout(productSearchTimeout);
    productSearchTimeout = setTimeout(() => fetchProductOptions(1), 250);
});

watch(locationSearch, () => {
    clearTimeout(locationSearchTimeout);
    locationSearchTimeout = setTimeout(() => fetchLocationOptions(1), 250);
});

onMounted(() => {
    fetchProductOptions();
    fetchLocationOptions();
});

// Autorellenar desde producto en modo create
watch(
    () => form.product_id,
    (newValue) => {
        if (!newValue) return;

        const selected = productOptions.value.find(
            (p) => String(p.id) === String(newValue),
        );
        if (!selected) return;

        if (!isEdit.value) {
            if (!form.unit && selected.default_unit) {
                form.unit = selected.default_unit;
            }
            if (
                !form.quantity &&
                selected.default_quantity != null
            ) {
                form.quantity = selected.default_quantity;
            }
            if (
                !form.min_quantity &&
                selected.default_quantity != null
            ) {
                form.min_quantity = selected.default_quantity;
            }
        }
    },
);

const submit = () => {
    if (isEdit.value && baseItem?.id) {
        form.put(route('stock.update', baseItem.id));
    } else {
        form.post(route('stock.store'));
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="title" />

        <div class="py-10">
            <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Cabecera -->
                <div
                    class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div>
                        <h1 class="text-2xl font-semibold text-[color:var(--km-text)]">
                            {{ title }}
                        </h1>
                        <p class="mt-1 text-sm text-[color:var(--km-muted)]">
                            Registra cuántas unidades tienes de cada producto,
                            dónde está y su fecha de caducidad.
                        </p>
                    </div>

                    <Link
                        :href="route('stock.index')"
                        class="km-link text-sm"
                    >
                        Volver al listado
                    </Link>
                </div>

                <!-- Tarjeta -->
                <div class="mx-auto max-w-4xl p-6 km-card">
                    <form class="space-y-6" @submit.prevent="submit">
                        <!-- Producto / Cantidad / Unidad -->
                        <div class="grid gap-6 md:grid-cols-3">
                            <!-- Producto -->
                            <div class="md:col-span-2 space-y-2">
                                <div class="flex items-center justify-between gap-2">
                                    <label
                                        for="product_id"
                                        class="block text-sm font-medium text-[color:var(--km-text)]"
                                    >
                                        Producto *
                                    </label>
                                    <input
                                        id="product_search"
                                        v-model="productSearch"
                                        type="search"
                                        class="km-input h-9 w-48 text-xs"
                                        placeholder="Buscar producto"
                                        aria-label="Buscar producto"
                                    />
                                </div>
                                <select
                                    id="product_id"
                                    v-model="form.product_id"
                                    required
                                    class="km-input"
                                    :disabled="productLoading"
                                >
                                    <option value="">
                                        Selecciona un producto
                                    </option>
                                    <option
                                        v-for="product in productOptions"
                                        :key="product.id"
                                        :value="product.id"
                                    >
                                        {{ product.name }}
                                    </option>
                                </select>
                                <div
                                    v-if="productMeta?.last_page > 1"
                                    class="flex items-center justify-between text-[11px] text-[color:var(--km-muted)]"
                                >
                                    <button
                                        type="button"
                                        class="km-link"
                                        :disabled="productLoading || (productMeta?.current_page ?? 1) <= 1"
                                        @click="
                                            changeProductPage(
                                                (productMeta?.current_page ?? 1) - 1,
                                            )
                                        "
                                    >
                                        Anterior
                                    </button>
                                    <span>
                                        Página {{ productMeta?.current_page ?? 1 }}
                                        de
                                        {{ productMeta?.last_page ?? 1 }}
                                    </span>
                                    <button
                                        type="button"
                                        class="km-link"
                                        :disabled="
                                            productLoading ||
                                                (productMeta?.current_page ?? 1) >=
                                                    (productMeta?.last_page ?? 1)
                                        "
                                        @click="
                                            changeProductPage(
                                                (productMeta?.current_page ?? 1) + 1,
                                            )
                                        "
                                    >
                                        Siguiente
                                    </button>
                                </div>
                                <InputError
                                    class="mt-1 text-xs text-rose-600"
                                    :message="form.errors.product_id"
                                />
                            </div>

                            <!-- Cantidad -->
                            <div class="space-y-1">
                                <label
                                    for="quantity"
                                    class="block text-sm font-medium text-[color:var(--km-text)]"
                                >
                                    Cantidad *
                                </label>
                                <input
                                    id="quantity"
                                    v-model="form.quantity"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    required
                                    class="km-input"
                                    placeholder="1"
                                />
                                <InputError
                                    class="mt-1 text-xs text-rose-600"
                                    :message="form.errors.quantity"
                                />
                            </div>
                        </div>

                        <!-- Unidad / Ubicación / Mínimo -->
                        <div class="grid gap-6 md:grid-cols-3">
                            <!-- Unidad -->
                            <div class="space-y-1">
                                <label
                                    for="unit"
                                    class="block text-sm font-medium text-[color:var(--km-text)]"
                                >
                                    Unidad
                                </label>
                                <select
                                    id="unit"
                                    v-model="form.unit"
                                    class="km-input"
                                >
                                    <option value="">
                                        Misma unidad que el producto
                                    </option>
                                    <option
                                        v-for="unit in UNIT_OPTIONS"
                                        :key="unit"
                                        :value="unit"
                                    >
                                        {{ unit }}
                                    </option>
                                </select>
                                <InputError
                                    class="mt-1 text-xs text-rose-600"
                                    :message="form.errors.unit"
                                />
                            </div>

                            <!-- Ubicación -->
                            <div class="space-y-2">
                                <div class="flex items-center justify-between gap-2">
                                    <label
                                        for="location_id"
                                        class="block text-sm font-medium text-[color:var(--km-text)]"
                                    >
                                        Ubicación
                                    </label>
                                    <input
                                        id="location_search"
                                        v-model="locationSearch"
                                        type="search"
                                        class="km-input h-9 w-40 text-xs"
                                        placeholder="Buscar ubicación"
                                        aria-label="Buscar ubicación"
                                    />
                                </div>
                                <select
                                    id="location_id"
                                    v-model="form.location_id"
                                    class="km-input"
                                    :disabled="locationLoading"
                                >
                                    <option value="">
                                        Sin ubicación asignada
                                    </option>
                                    <option
                                        v-for="location in locationOptions"
                                        :key="location.id"
                                        :value="location.id"
                                    >
                                        {{ location.name }}
                                    </option>
                                </select>
                                <div
                                    v-if="locationMeta?.last_page > 1"
                                    class="flex items-center justify-between text-[11px] text-[color:var(--km-muted)]"
                                >
                                    <button
                                        type="button"
                                        class="km-link"
                                        :disabled="locationLoading || (locationMeta?.current_page ?? 1) <= 1"
                                        @click="
                                            changeLocationPage(
                                                (locationMeta?.current_page ?? 1) - 1,
                                            )
                                        "
                                    >
                                        Anterior
                                    </button>
                                    <span>
                                        Página {{ locationMeta?.current_page ?? 1 }}
                                        de
                                        {{ locationMeta?.last_page ?? 1 }}
                                    </span>
                                    <button
                                        type="button"
                                        class="km-link"
                                        :disabled="
                                            locationLoading ||
                                                (locationMeta?.current_page ?? 1) >=
                                                    (locationMeta?.last_page ?? 1)
                                        "
                                        @click="
                                            changeLocationPage(
                                                (locationMeta?.current_page ?? 1) + 1,
                                            )
                                        "
                                    >
                                        Siguiente
                                    </button>
                                </div>
                                <InputError
                                    class="mt-1 text-xs text-rose-600"
                                    :message="form.errors.location_id"
                                />
                            </div>

                            <!-- Cantidad mínima -->
                            <div class="space-y-1">
                                <label
                                    for="min_quantity"
                                    class="block text-sm font-medium text-[color:var(--km-text)]"
                                >
                                    Cantidad mínima (para aviso)
                                </label>
                                <input
                                    id="min_quantity"
                                    v-model="form.min_quantity"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="km-input"
                                    placeholder="Ej: 2"
                                />
                                <p class="mt-1 text-xs text-[color:var(--km-muted)]">
                                    Si la cantidad baja de este número, el
                                    sistema marcará “Bajo mínimo”.
                                </p>
                                <InputError
                                    class="mt-1 text-xs text-rose-600"
                                    :message="form.errors.min_quantity"
                                />
                            </div>
                        </div>

                        <!-- Caducidad + Envase abierto -->
                        <div class="grid gap-6 md:grid-cols-3">
                            <div class="space-y-1 md:col-span-1">
                                <label
                                    for="expires_at"
                                    class="block text-sm font-medium text-[color:var(--km-text)]"
                                >
                                    Caducidad (opcional)
                                </label>
                                <input
                                    id="expires_at"
                                    v-model="form.expires_at"
                                    type="date"
                                    class="km-input"
                                />
                                <InputError
                                    class="mt-1 text-xs text-rose-600"
                                    :message="form.errors.expires_at"
                                />
                            </div>

                            <div class="space-y-2 md:col-span-2">
                                <label
                                    class="inline-flex items-center gap-2 mt-6"
                                >
                                    <input
                                        id="is_open"
                                        v-model="form.is_open"
                                        type="checkbox"
                                        class="h-4 w-4 rounded border-[color:var(--km-border)] text-[color:var(--km-accent)] focus:ring-[color:var(--km-ring)]"
                                    />
                                    <span
                                        class="text-sm font-medium text-[color:var(--km-text)]"
                                    >
                                        Envase abierto
                                    </span>
                                </label>
                                <p class="text-xs text-[color:var(--km-muted)]">
                                    Marca esta opción si el paquete ya está
                                    empezado, para saber qué productos usar
                                    primero.
                                </p>
                                <InputError
                                    class="mt-1 text-xs text-rose-600"
                                    :message="form.errors.is_open"
                                />
                            </div>
                        </div>

                        <!-- Notas -->
                        <div class="space-y-1">
                            <label
                                for="notes"
                                class="block text-sm font-medium text-[color:var(--km-text)]"
                            >
                                Notas (opcional)
                            </label>
                            <textarea
                                id="notes"
                                v-model="form.notes"
                                rows="3"
                                class="km-input"
                                placeholder="Ej: paquete empezado, usar primero..."
                            ></textarea>
                            <InputError
                                class="mt-1 text-xs text-rose-600"
                                :message="form.errors.notes"
                            />
                        </div>

                        <!-- Botón -->
                        <div class="pt-4 flex justify-end">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="km-btn w-auto px-5 py-2.5 text-sm disabled:cursor-wait"
                            >
                                {{ submitLabel }}
                            </button>
                        </div>
                    </form>
                </div>

                <div
                    v-if="isEdit"
                    class="mx-auto max-w-4xl p-6 km-card"
                >
                    <div class="flex items-center justify-between gap-2">
                        <h2 class="text-lg font-semibold text-[color:var(--km-text)]">
                            Historial de movimientos
                        </h2>
                        <span class="text-xs text-[color:var(--km-muted)]">
                            Últimos 10 registros
                        </span>
                    </div>

                    <div
                        v-if="!movements.length"
                        class="mt-3 text-sm text-[color:var(--km-muted)]"
                    >
                        Aún no hay movimientos registrados para este stock.
                    </div>

                    <ul v-else class="mt-4 divide-y divide-[color:var(--km-border)]">
                        <li
                            v-for="movement in movements"
                            :key="movement.id"
                            class="flex items-center justify-between py-2"
                        >
                            <div>
                                <p class="text-sm font-medium text-[color:var(--km-text)] capitalize">
                                    {{ movement.action }}
                                </p>
                                <p class="text-xs text-[color:var(--km-muted)]">
                                    Antes: {{ movement.quantity_before ?? '—' }} ·
                                    Después: {{ movement.quantity_after ?? '—' }}
                                </p>
                            </div>
                            <div class="text-[11px] text-[color:var(--km-muted)]">
                                {{ new Date(movement.created_at).toLocaleString('es-ES') }}
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
