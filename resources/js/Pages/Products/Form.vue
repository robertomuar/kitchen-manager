<script setup>
import { computed, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import BarcodeScanner from '@/Components/BarcodeScanner.vue';
import { useCsrfForm } from '@/Composables/useCsrfForm';

const props = defineProps({
    product: {
        type: Object,
        default: null,
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

// Unidades disponibles para el select
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

const isEdit = computed(
    () => props.mode === 'edit' || !!props.product?.id,
);

const form = useCsrfForm({
    barcode: props.product?.barcode ?? '',
    name: props.product?.name ?? '',
    default_quantity: props.product?.default_quantity ?? '',
    default_unit: props.product?.default_unit ?? '',
    default_pack_size: props.product?.default_pack_size ?? '',
    location_id: props.product?.location_id ?? '',
    notes: props.product?.notes ?? '',
});

const title = computed(() =>
    isEdit.value ? 'Editar producto' : 'Nuevo producto',
);

const submitLabel = computed(() =>
    isEdit.value ? 'Guardar cambios' : 'Crear producto',
);

// Estado para lookup y escáner
const isScannerOpen = ref(false);
const isLookingUp = ref(false);
const lookupError = ref('');
const scannerError = ref('');

const sanitizeBarcode = (code) =>
    typeof code === 'string' || typeof code === 'number'
        ? String(code).trim()
        : '';

// Enviar formulario (crear / editar)
const submit = () => {
    if (isEdit.value) {
        form.put(route('products.update', props.product.id));
    } else {
        form.post(route('products.store'));
    }
};

// Buscar info del código de barras llamando a Laravel
const lookupBarcode = async () => {
    if (!form.barcode) {
        alert('Introduce primero un código de barras.');
        return;
    }

    try {
        isLookingUp.value = true;
        lookupError.value = '';

        // 🚫 Antes: const url = route('barcode.lookup');
        // ✅ Ahora: URL directa, evitando Ziggy
        const response = await axios.post('/barcode/lookup', {
            barcode: form.barcode,
        });

        if (!response.data || !response.data.success || !response.data.data) {
            lookupError.value =
                response.data?.message ??
                'No se han encontrado datos para este código.';
            return;
        }

        const data = response.data.data;

        // Rellenamos solo si el usuario no ha escrito nada
        if (data.name && !form.name) {
            form.name = data.name;
        }

        if (
            data.default_quantity !== null &&
            data.default_quantity !== undefined &&
            form.default_quantity === ''
        ) {
            form.default_quantity = data.default_quantity;
        }

        if (data.default_unit && !form.default_unit) {
            form.default_unit = data.default_unit;
        }

        if (!data.name && !data.default_quantity && !data.default_unit) {
            lookupError.value =
                'No hay datos útiles para este código, tendrás que rellenarlo a mano.';
        }
    } catch (error) {
        console.error('Error consultando código de barras:', error);
        lookupError.value =
            'No se pudo consultar la información. Inténtalo de nuevo.';
    } finally {
        isLookingUp.value = false;
    }
};

const openScanner = () => {
    scannerError.value = '';
    isScannerOpen.value = true;
};

// Cuando el escáner lee un código
const onBarcodeScanned = (code) => {
    const sanitized = sanitizeBarcode(code);

    if (!sanitized) {
        scannerError.value =
            'El escáner no pudo leer el código. Intenta acercarte un poco más y vuelve a probar.';
        return;
    }

    form.barcode = sanitized;
    isScannerOpen.value = false;
    // Lanzamos el lookup automáticamente
    lookupBarcode();
};

const onScannerError = (error) => {
    console.error('No se pudo usar la cámara:', error);
    scannerError.value =
        'No pudimos acceder a la cámara. Revisa los permisos del navegador o prueba con otro dispositivo.';
    isScannerOpen.value = false;
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
                            Define un producto que utilizas en tu cocina:
                            nombre, cantidad base, unidad y ubicación habitual.
                        </p>
                    </div>

                    <Link
                        :href="route('products.index')"
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
                        <!-- CÓDIGO DE BARRAS -->
                        <div class="space-y-1">
                            <label
                                for="barcode"
                                class="block text-sm font-medium text-slate-800"
                            >
                                Código de barras
                                <span class="text-xs text-slate-500">
                                    (opcional, pero muy recomendado)
                                </span>
                            </label>
                            <div class="flex flex-col gap-2 md:flex-row">
                                <input
                                    id="barcode"
                                    v-model="form.barcode"
                                    type="text"
                                    class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm
                                           text-slate-900 placeholder-slate-400 shadow-sm
                                           focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/40 focus:outline-none"
                                    placeholder="Ej: 8410100101010"
                                />

                                <div
                                    class="flex flex-row gap-2 md:flex-col md:w-56"
                                >
                                    <button
                                        type="button"
                                        :disabled="isLookingUp || form.processing"
                                        @click="lookupBarcode"
                                        class="inline-flex flex-1 items-center justify-center rounded-md border border-indigo-500/80 bg-indigo-500/90 px-3 py-2 text-xs font-semibold text-white shadow-sm hover:bg-indigo-600 disabled:opacity-60 disabled:cursor-wait"
                                    >
                                        {{
                                            isLookingUp
                                                ? 'Buscando...'
                                                : 'Buscar datos online'
                                        }}
                                    </button>

                                    <button
                                        type="button"
                                        @click="openScanner"
                                        class="inline-flex flex-1 items-center justify-center rounded-md border border-slate-300 bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-800 shadow-sm hover:bg-slate-200"
                                    >
                                        Escanear con cámara
                                    </button>
                                </div>
                            </div>

                            <p class="mt-1 text-xs text-slate-500">
                                Puedes escribir el código a mano o escanearlo
                                con la cámara del móvil/PC.
                            </p>

                            <InputError
                                class="mt-1 text-xs text-rose-600"
                                :message="form.errors.barcode"
                            />

                            <p
                                v-if="lookupError"
                                class="mt-2 text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-md px-3 py-2"
                            >
                                {{ lookupError }}
                            </p>
                        </div>

                        <!-- ESCÁNER DE CÓDIGOS (cámara) -->
                        <div
                            v-if="isScannerOpen"
                            class="rounded-2xl border border-slate-200 bg-slate-50 px-3 py-3"
                        >
                            <div
                                class="mb-2 flex items-center justify-between gap-2"
                            >
                                <p class="text-xs font-medium text-slate-700">
                                    Escanear código de barras
                                </p>
                                <button
                                    type="button"
                                    class="text-xs text-slate-500 hover:text-slate-800"
                                    @click="isScannerOpen = false"
                                >
                                    Cerrar
                                </button>
                            </div>

                            <BarcodeScanner
                                @scanned="onBarcodeScanned"
                                @error="onScannerError"
                                @closed="isScannerOpen = false"
                            />
                            <p
                                v-if="scannerError"
                                class="mt-2 rounded-md border border-rose-200 bg-rose-50 px-3 py-2 text-xs text-rose-700"
                            >
                                {{ scannerError }}
                            </p>
                        </div>

                        <!-- Nombre -->
                        <div class="space-y-1">
                            <label
                                for="name"
                                class="block text-sm font-medium text-slate-800"
                            >
                                Nombre *
                            </label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                required
                                class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm
                                       text-slate-900 placeholder-slate-400 shadow-sm
                                       focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/40 focus:outline-none"
                                placeholder="Leche entera, Pasta, Tomate frito..."
                            />
                            <InputError
                                class="mt-1 text-xs text-rose-600"
                                :message="form.errors.name"
                            />
                        </div>

                        <!-- Cantidad base + unidad -->
                        <div class="grid gap-6 md:grid-cols-2">
                            <div class="space-y-1">
                                <label
                                    for="default_quantity"
                                    class="block text-sm font-medium text-slate-800"
                                >
                                    Cantidad base
                                </label>
                                <input
                                    id="default_quantity"
                                    v-model="form.default_quantity"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm
                                           text-slate-900 placeholder-slate-400 shadow-sm
                                           focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/40 focus:outline-none"
                                    placeholder="1, 0.5, 3..."
                                />
                                <InputError
                                    class="mt-1 text-xs text-rose-600"
                                    :message="form.errors.default_quantity"
                                />
                            </div>

                            <div class="space-y-1">
                                <label
                                    for="default_unit"
                                    class="block text-sm font-medium text-slate-800"
                                >
                                    Unidad
                                </label>
                                <select
                                    id="default_unit"
                                    v-model="form.default_unit"
                                    class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm
                                           text-slate-900 shadow-sm
                                           focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/40 focus:outline-none"
                                >
                                    <option value="">
                                        Selecciona una unidad
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
                                    :message="form.errors.default_unit"
                                />
                            </div>
                        </div>

                        <!-- Tamaño de pack + ubicación -->
                        <div class="grid gap-6 md:grid-cols-2">
                            <div class="space-y-1">
                                <label
                                    for="default_pack_size"
                                    class="block text-sm font-medium text-slate-800"
                                >
                                    Tamaño de pack
                                    <span class="text-xs text-slate-500">
                                        (opcional)
                                    </span>
                                </label>
                                <input
                                    id="default_pack_size"
                                    v-model="form.default_pack_size"
                                    type="text"
                                    class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm
                                           text-slate-900 placeholder-slate-400 shadow-sm
                                           focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/40 focus:outline-none"
                                    placeholder="Ej: 6 (pack de 6)"
                                />
                                <InputError
                                    class="mt-1 text-xs text-rose-600"
                                    :message="form.errors.default_pack_size"
                                />
                            </div>

                            <div class="space-y-1">
                                <label
                                    for="location_id"
                                    class="block text-sm font-medium text-slate-800"
                                >
                                    Ubicación habitual
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
                                <p class="mt-1 text-xs text-slate-500">
                                    Estas ubicaciones se gestionan en la sección
                                    “Ubicaciones”.
                                </p>
                                <InputError
                                    class="mt-1 text-xs text-rose-600"
                                    :message="form.errors.location_id"
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
                                placeholder="Ej: marca preferida, usar primero los paquetes abiertos..."
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
