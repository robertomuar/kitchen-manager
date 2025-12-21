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

// Normaliza el código: string, sin espacios
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
    const sanitized = sanitizeBarcode(form.barcode);

    if (!sanitized) {
        alert('Introduce primero un código de barras.');
        return;
    }

    // Dejamos el código limpio en el formulario
    form.barcode = sanitized;

    try {
        isLookingUp.value = true;
        lookupError.value = '';

        const response = await axios.post('/barcode/lookup', {
            barcode: sanitized,
        });

        const payload = response?.data ?? {};

        if (!payload.success || !payload.data) {
            lookupError.value =
                payload?.message ??
                'No se han encontrado datos para este código.';
            return;
        }

        const data = payload.data;

        // Si el backend devuelve un barcode normalizado, lo usamos
        if (data.barcode) {
            form.barcode = String(data.barcode);
        }

        // Nombre del producto
        if (data.name && !form.name) {
            form.name = data.name;
        }

        // Cantidad base
        if (
            data.default_quantity !== null &&
            data.default_quantity !== undefined &&
            form.default_quantity === ''
        ) {
            form.default_quantity = data.default_quantity;
        }

        // Unidad
        if (data.default_unit && !form.default_unit) {
            form.default_unit = data.default_unit;
        }

        // Tamaño de pack
        if (data.default_pack_size && !form.default_pack_size) {
            form.default_pack_size = data.default_pack_size;
        }

        if (
            !data.name &&
            !data.default_quantity &&
            !data.default_unit &&
            !data.default_pack_size
        ) {
            lookupError.value =
                payload?.message ??
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
                        <h1 class="text-2xl font-semibold text-[color:var(--km-text)]">
                            {{ title }}
                        </h1>
                        <p class="mt-1 text-sm text-[color:var(--km-muted)]">
                            Gestiona la información base del producto: código de
                            barras, nombre, unidad de medida y ubicación habitual.
                        </p>
                    </div>

                    <Link
                        :href="route('products.index')"
                        class="km-link text-sm"
                    >
                        Volver al listado
                    </Link>
                </div>

                <!-- Tarjeta principal -->
                <div class="km-card p-6">
                    <form class="space-y-6" @submit.prevent="submit">
                        <!-- Código de barras y acciones -->
                        <div class="space-y-2">
                            <label
                                for="barcode"
                                class="block text-sm font-medium text-[color:var(--km-text)]"
                            >
                                Código de barras
                            </label>

                            <div class="flex flex-col gap-3 sm:flex-row">
                                <input
                                    id="barcode"
                                    v-model="form.barcode"
                                    type="text"
                                    inputmode="numeric"
                                    class="km-input"
                                    placeholder="Escanéalo o introdúcelo manualmente"
                                />

                                <div class="flex flex-col gap-2 sm:w-72">
                                    <button
                                        type="button"
                                        :disabled="isLookingUp || form.processing"
                                        class="km-btn w-full px-4 py-2 text-sm disabled:cursor-wait"
                                        @click="lookupBarcode"
                                    >
                                        <span v-if="isLookingUp" class="mr-2 h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                                        Consultar código
                                    </button>

                                    <button
                                        type="button"
                                        class="inline-flex items-center justify-center rounded-xl border border-[color:var(--km-border)] bg-[color:var(--km-bg-2)] px-4 py-2 text-sm font-semibold text-[color:var(--km-text)] shadow-sm hover:bg-white/80"
                                        @click="openScanner"
                                    >
                                        Escanear con cámara
                                    </button>
                                </div>
                            </div>

                            <p class="text-xs text-[color:var(--km-muted)]">
                                Si lo dejas vacío, podrás seguir creando el producto
                                pero no se completarán datos automáticamente.
                            </p>

                            <InputError
                                class="mt-1 text-xs text-rose-600"
                                :message="form.errors.barcode"
                            />

                            <p
                                v-if="lookupError"
                                class="text-xs text-amber-700 bg-amber-100/80 border border-amber-200 rounded-lg px-3 py-2"
                            >
                                {{ lookupError }}
                            </p>
                        </div>

                        <!-- Nombre -->
                        <div class="space-y-1">
                            <label
                                for="name"
                                class="block text-sm font-medium text-[color:var(--km-text)]"
                            >
                                Nombre *
                            </label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                required
                                class="km-input"
                                placeholder="Ej: Garbanzos cocidos, Leche entera…"
                            />
                            <InputError
                                class="mt-1 text-xs text-rose-600"
                                :message="form.errors.name"
                            />
                        </div>

                        <!-- Cantidad por defecto -->
                        <div class="grid gap-6 md:grid-cols-3">
                            <div class="space-y-1">
                                <label
                                    for="default_quantity"
                                    class="block text-sm font-medium text-[color:var(--km-text)]"
                                >
                                    Cantidad por defecto
                                </label>
                                <input
                                    id="default_quantity"
                                    v-model="form.default_quantity"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    class="km-input"
                                    placeholder="Ej: 1, 0.5, 200…"
                                />
                                <InputError
                                    class="mt-1 text-xs text-rose-600"
                                    :message="form.errors.default_quantity"
                                />
                            </div>

                            <div class="space-y-1">
                                <label
                                    for="default_unit"
                                    class="block text-sm font-medium text-[color:var(--km-text)]"
                                >
                                    Unidad de medida
                                </label>
                                <select
                                    id="default_unit"
                                    v-model="form.default_unit"
                                    class="km-input"
                                >
                                    <option value="">Sin unidad</option>
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

                            <div class="space-y-1">
                                <label
                                    for="default_pack_size"
                                    class="block text-sm font-medium text-[color:var(--km-text)]"
                                >
                                    Tamaño del pack
                                </label>
                                <input
                                    id="default_pack_size"
                                    v-model="form.default_pack_size"
                                    type="number"
                                    min="1"
                                    class="km-input"
                                    placeholder="Ej: 6 botellas, 12 unidades…"
                                />
                                <InputError
                                    class="mt-1 text-xs text-rose-600"
                                    :message="form.errors.default_pack_size"
                                />
                            </div>
                        </div>

                        <!-- Ubicación -->
                        <div class="space-y-1">
                            <label
                                for="location_id"
                                class="block text-sm font-medium text-[color:var(--km-text)]"
                            >
                                Ubicación habitual
                            </label>
                            <select
                                id="location_id"
                                v-model="form.location_id"
                                class="km-input"
                            >
                                <option value="">Sin ubicación</option>
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
                                placeholder="Apuntes adicionales: marca preferida, formato, etc."
                            ></textarea>
                            <InputError
                                class="mt-1 text-xs text-rose-600"
                                :message="form.errors.notes"
                            />
                        </div>

                        <!-- Botón -->
                        <div class="flex justify-end pt-4">
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
            </div>
        </div>

        <!-- Modal del escáner -->
        <div
            v-if="isScannerOpen"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur"
        >
            <div class="w-full max-w-xl p-5 km-card">
                <div class="mb-3 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-[color:var(--km-text)]">Escanear código</h3>
                        <p class="text-xs text-[color:var(--km-muted)]">
                            Activa la cámara para leer el código de barras del envase.
                        </p>
                    </div>
                    <button
                        type="button"
                        class="km-link text-sm"
                        @click="isScannerOpen = false"
                    >
                        Cerrar
                    </button>
                </div>

                <BarcodeScanner
                    @scanned="onBarcodeScanned"
                    @error="onScannerError"
                    @closed="() => (isScannerOpen = false)"
                />

                <p v-if="scannerError" class="mt-2 text-xs text-rose-600">
                    {{ scannerError }}
                </p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
