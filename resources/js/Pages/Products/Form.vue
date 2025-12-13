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
    <!-- ... tal y como lo tenías ... -->
</template>
