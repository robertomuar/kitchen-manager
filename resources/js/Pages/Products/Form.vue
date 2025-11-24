<script setup>
import { computed, ref, onBeforeUnmount } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import { Html5Qrcode } from 'html5-qrcode';

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

const form = useForm({
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

const submit = () => {
    if (isEdit.value) {
        form.put(route('products.update', props.product.id));
    } else {
        form.post(route('products.store'));
    }
};

// ---- Estado para la búsqueda por código ----
const lookupLoading = ref(false);
const lookupMessage = ref('');
const lookupError = ref('');

/**
 * Llama al backend para buscar datos reales por código de barras
 */
const lookupBarcode = async () => {
    lookupMessage.value = '';
    lookupError.value = '';

    if (!form.barcode) {
        lookupError.value = 'Introduce primero un código de barras.';
        return;
    }

    try {
        lookupLoading.value = true;

        const response = await window.axios.get(
            route('products.barcode.lookup'),
            {
                params: { barcode: form.barcode },
            },
        );

        const data = response.data;

        if (!data.found) {
            lookupError.value =
                data.message || 'No se encontró información para este código.';
            return;
        }

        const suggested = data.suggested ?? {};

        // Solo rellenamos campos vacíos para no pisar lo que ya hayas escrito
        if (!form.name && suggested.name) {
            form.name = suggested.name;
        }

        if (!form.default_quantity && suggested.default_quantity) {
            form.default_quantity = suggested.default_quantity;
        }

        if (!form.default_unit && suggested.default_unit) {
            form.default_unit = suggested.default_unit;
        }

        lookupMessage.value =
            'Datos sugeridos aplicados. Revisa antes de guardar.';
    } catch (error) {
        if (error.response?.data?.message) {
            lookupError.value = error.response.data.message;
        } else {
            lookupError.value =
                'No se pudo consultar la información. Inténtalo de nuevo.';
        }
    } finally {
        lookupLoading.value = false;
    }
};

// ---- ESCÁNER CON CÁMARA (html5-qrcode) ----
const scannerVisible = ref(false);
const scannerInstance = ref(null);
const scannerError = ref('');
const scanning = ref(false);

const SCANNER_ELEMENT_ID = 'barcode-scanner';

/**
 * Inicia el escáner con la cámara trasera (si existe)
 */
const startScanner = async () => {
    scannerError.value = '';

    // Evitar usar en entorno sin window (por si acaso)
    if (typeof window === 'undefined') {
        scannerError.value =
            'El escáner solo está disponible en el navegador.';
        return;
    }

    if (scannerInstance.value) {
        return;
    }

    try {
        const html5QrCode = new Html5Qrcode(SCANNER_ELEMENT_ID);
        scannerInstance.value = html5QrCode;

        scanning.value = true;

        await html5QrCode.start(
            { facingMode: 'environment' }, // cámara trasera en móvil
            {
                fps: 10,
                qrbox: {
                    width: 250,
                    height: 250,
                },
            },
            async (decodedText /* , decodedResult */) => {
                // Cuando escaneamos correctamente
                form.barcode = decodedText;

                // Buscamos datos online automáticamente
                await lookupBarcode();

                // Paramos la cámara
                await stopScanner();
                scannerVisible.value = false;
            },
            (errorMessage) => {
                // Errores de lectura continuos (podemos ignorarlos)
                // console.debug('Scan error', errorMessage);
            },
        );
    } catch (e) {
        console.error(e);
        scannerError.value =
            'No se pudo iniciar la cámara. Revisa permisos del navegador.';
        scanning.value = false;
        if (scannerInstance.value) {
            try {
                await scannerInstance.value.stop();
                await scannerInstance.value.clear();
            } catch (_) {}
            scannerInstance.value = null;
        }
    }
};

/**
 * Detiene el escáner si está activo
 */
const stopScanner = async () => {
    if (scannerInstance.value) {
        try {
            if (scanning.value) {
                await scannerInstance.value.stop();
            }
            await scannerInstance.value.clear();
        } catch (e) {
            console.error(e);
        }
        scannerInstance.value = null;
    }
    scanning.value = false;
};

/**
 * Mostrar / ocultar el panel de escaneo
 */
const toggleScanner = async () => {
    scannerError.value = '';

    if (!scannerVisible.value) {
        scannerVisible.value = true;
        await startScanner();
    } else {
        scannerVisible.value = false;
        await stopScanner();
    }
};

// Por si se cierra la página con la cámara encendida
onBeforeUnmount(async () => {
    await stopScanner();
});
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
                            código de barras, nombre, cantidad base, unidad y
                            ubicación habitual.
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
                        <!-- CÓDIGO DE BARRAS + BOTONES -->
                        <div class="space-y-1">
                            <label
                                for="barcode"
                                class="block text-sm font-medium text-slate-800"
                            >
                                Código de barras
                                <span class="text-xs text-slate-500">
                                    (puedes introducirlo o escanearlo)
                                </span>
                            </label>

                            <div class="flex flex-wrap gap-2">
                                <input
                                    id="barcode"
                                    v-model="form.barcode"
                                    type="text"
                                    class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm
                                           text-slate-900 placeholder-slate-400 shadow-sm
                                           focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/40 focus:outline-none
                                           sm:flex-1"
                                    placeholder="Ej: 8412345678901"
                                />

                                <button
                                    type="button"
                                    @click="lookupBarcode"
                                    :disabled="lookupLoading"
                                    class="inline-flex items-center rounded-xl bg-indigo-500 px-4 py-2 text-xs font-semibold text-white shadow-sm shadow-indigo-500/40 hover:bg-indigo-600 disabled:opacity-60 disabled:cursor-wait"
                                >
                                    {{ lookupLoading ? 'Buscando...' : 'Buscar datos online' }}
                                </button>

                                <button
                                    type="button"
                                    @click="toggleScanner"
                                    class="inline-flex items-center rounded-xl border border-slate-300 bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-800 shadow-sm hover:bg-slate-200"
                                >
                                    {{ scannerVisible ? 'Cerrar cámara' : 'Escanear con cámara' }}
                                </button>
                            </div>

                            <InputError
                                class="mt-1 text-xs text-rose-600"
                                :message="form.errors.barcode"
                            />

                            <p
                                v-if="lookupMessage"
                                class="mt-1 text-xs text-emerald-600"
                            >
                                {{ lookupMessage }}
                            </p>
                            <p
                                v-if="lookupError"
                                class="mt-1 text-xs text-rose-600"
                            >
                                {{ lookupError }}
                            </p>
                        </div>

                        <!-- PANEL DEL ESCÁNER -->
                        <div
                            v-if="scannerVisible"
                            class="mt-2 rounded-xl border border-slate-200 bg-slate-50 p-3 text-xs text-slate-700"
                        >
                            <p class="mb-2 font-semibold">
                                Escanear código de barras
                            </p>

                            <div
                                :id="SCANNER_ELEMENT_ID"
                                class="mx-auto aspect-[3/4] w-full max-w-xs overflow-hidden rounded-lg border border-slate-300 bg-black/80"
                            ></div>

                            <p
                                v-if="scannerError"
                                class="mt-2 text-xs text-rose-600"
                            >
                                {{ scannerError }}
                            </p>
                            <p
                                v-else
                                class="mt-2 text-xs text-slate-500"
                            >
                                Apunta con la cámara al código de barras. Al
                                leerlo se rellenará el código y se buscarán los
                                datos automáticamente.
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
