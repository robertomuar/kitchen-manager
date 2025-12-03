<script setup>
import { computed, watch } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import { useCsrfForm } from '@/Composables/useCsrfForm';

const props = defineProps({
    stockItem: {
        type: Object,
        default: null,
    },
    // por si en el backend lo llamaste "item" en vez de "stockItem"
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
    mode: {
        type: String,
        default: 'create', // 'create' | 'edit'
    },
});

// Unidades reutilizadas
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

// Objeto actual (por si viene como stockItem o como item)
const baseItem = props.stockItem ?? props.item ?? null;

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

// Cuando el usuario selecciona un producto, si estamos creando,
// rellenamos por defecto unidad / cantidades si están vacías.
watch(
    () => form.product_id,
    (newValue) => {
        if (!newValue) return;

        const selected = props.products.find(
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
                        <h1 class="text-2xl font-semibold text-slate-50">
                            {{ title }}
                        </h1>
                        <p class="mt-1 text-sm text-slate-400">
                            Registra cuántas unidades tienes de cada producto,
                            dónde está y su fecha de caducidad.
                        </p>
                    </div>

                    <Link
                        :href="route('stock.index')"
                        class="text-sm text-slate-300 hover:text-slate-100"
                    >
                        Volver al listado
                    </Link>
                </div>

                <!-- Tarjeta del formulario (BLANCA, TEXTO OSCURO) -->
                <div
                    class="mx-auto max-w-4xl rounded-2xl border border-slate-200 bg-white/95 p-6 shadow-xl shadow-slate-900/20 text-slate-900"
                >
                    <form
                        class="space-y-6"
                        @submit.prevent="submit"
                    >
                        <!-- Producto / Cantidad / Unidad -->
                        <div class="grid gap-6 md:grid-cols-3">
                            <!-- Producto -->
                            <div class="md:col-span-2 space-y-1">
                                <label
                                    for="product_id"
                                    class="block text-sm font-medium text-slate-800"
                                >
                                    Producto *
                                </label>
                                <select
                                    id="product_id"
                                    v-model="form.product_id"
                                    required
                                    class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm
                                           text-slate-900 shadow-sm
                                           focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/40 focus:outline-none"
                                >
                                    <option value="">
                                        Selecciona un producto
                                    </option>
                                    <option
                                        v-for="product in products"
                                        :key="product.id"
                                        :value="product.id"
                                    >
                                        {{ product.name }}
                                    </option>
                                </select>
                                <InputError
                                    class="mt-1 text-xs text-rose-600"
                                    :message="form.errors.product_id"
                                />
                            </div>

                            <!-- Cantidad -->
                            <div class="space-y-1">
                                <label
                                    for="quantity"
                                    class="block text-sm font-medium text-slate-800"
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
                                    class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm
                                           text-slate-900 placeholder-slate-400 shadow-sm
                                           focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/40 focus:outline-none"
                                    placeholder="1"
                                />
                                <InputError
                                    class="mt-1 text-xs text-rose-600"
                                    :message="form.errors.quantity"
                                />
                            </div>
                        </div>

                        <!-- Unidad / Ubicación / Mínimo / Caducidad -->
                        <div class="grid gap-6 md:grid-cols-3">
                            <!-- Unidad -->
                            <div class="space-y-1">
                                <label
                                    for="unit"
                                    class="block text-sm font-medium text-slate-800"
                                >
                                    Unidad
                                </label>
                                <select
                                    id="unit"
                                    v-model="form.unit"
                                    class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm
                                           text-slate-900 shadow-sm
                                           focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/40 focus:outline-none"
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
                            <div class="space-y-1">
                                <label
                                    for="location_id"
                                    class="block text-sm font-medium text-slate-800"
                                >
                                    Ubicación
                                </label>
                                <select
                                    id="location_id"
                                    v-model="form.location_id"
                                    class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm
                                           text-slate-900 shadow-sm
                                           focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/40 focus:outline-none"
                                >
                                    <option value="">
                                        Sin ubicación asignada
                                    </option>
                                    <option
                                        v-for="location in locations"
                                        :key="location.id"
                                        :value="location.id"
                                    >
                                        {{ location.name }}
                                    </option>
                                </select>
                                <InputError
                                    class="mt-1 text-xs text-rose-600"
                                    :message="form.errors.location_id"
                                />
                            </div>

                            <!-- Cantidad mínima -->
                            <div class="space-y-1">
                                <label
                                    for="min_quantity"
                                    class="block text-sm font-medium text-slate-800"
                                >
                                    Cantidad mínima (para aviso)
                                </label>
                                <input
                                    id="min_quantity"
                                    v-model="form.min_quantity"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm
                                           text-slate-900 placeholder-slate-400 shadow-sm
                                           focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/40 focus:outline-none"
                                    placeholder="Ej: 2"
                                />
                                <p class="mt-1 text-xs text-slate-500">
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
                                    class="block text-sm font-medium text-slate-800"
                                >
                                    Caducidad (opcional)
                                </label>
                                <input
                                    id="expires_at"
                                    v-model="form.expires_at"
                                    type="date"
                                    class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm
                                           text-slate-900 shadow-sm
                                           focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/40 focus:outline-none"
                                />
                                <InputError
                                    class="mt-1 text-xs text-rose-600"
                                    :message="form.errors.expires_at"
                                />
                            </div>

                            <div class="space-y-2 md:col-span-2">
                                <label class="inline-flex items-center gap-2 mt-6">
                                    <input
                                        id="is_open"
                                        v-model="form.is_open"
                                        type="checkbox"
                                        class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                    />
                                    <span
                                        class="text-sm font-medium text-slate-800"
                                    >
                                        Envase abierto
                                    </span>
                                </label>
                                <p class="text-xs text-slate-500">
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
                                class="block text-sm font-medium text-slate-800"
                            >
                                Notas (opcional)
                            </label>
                            <textarea
                                id="notes"
                                v-model="form.notes"
                                rows="3"
                                class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm
                                       text-slate-900 placeholder-slate-400 shadow-sm
                                       focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/40 focus:outline-none"
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
                                class="inline-flex items-center justify-center rounded-xl bg-indigo-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-indigo-500/40 hover:bg-indigo-600 disabled:opacity-60 disabled:cursor-wait"
                            >
                                {{ submitLabel }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
