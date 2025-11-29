<script setup>
import { nextTick, onBeforeUnmount, onMounted, ref } from 'vue';
import { Html5Qrcode } from 'html5-qrcode';

const emit = defineEmits(['scanned', 'error', 'closed']);

const html5QrCode = ref(null);
const isStarting = ref(false);
const errorMessage = ref('');

const buildErrorMessage = (error) => {
    if (typeof error === 'string') return error;
    if (error?.name === 'NotAllowedError') {
        return 'No tenemos permisos para usar la cámara. Comprueba los ajustes del navegador.';
    }
    return error?.message ?? 'No se pudo iniciar la cámara.';
};

const stopScanner = async (notifyClosed = true) => {
    if (!html5QrCode.value) {
        if (notifyClosed) emit('closed');
        return;
    }

    try {
        await html5QrCode.value.stop();
        await html5QrCode.value.clear();
    } catch (e) {
        // ignoramos
    } finally {
        html5QrCode.value = null;
        if (notifyClosed) emit('closed');
    }
};

const startScanner = async () => {
    if (html5QrCode.value || isStarting.value) return;

    isStarting.value = true;
    errorMessage.value = '';

    try {
        const cameras = await Html5Qrcode.getCameras?.();

        if (Array.isArray(cameras) && cameras.length === 0) {
            throw new Error('No hemos encontrado ninguna cámara disponible.');
        }

        html5QrCode.value = new Html5Qrcode('barcode-scanner');

        await html5QrCode.value.start(
            cameras?.[0]?.id
                ? { deviceId: { exact: cameras[0].id } }
                : { facingMode: 'environment' },
            {
                fps: 10,
                qrbox: { width: 250, height: 250 },
            },
            async (decodedText) => {
                await stopScanner(false);
                emit('scanned', decodedText);
            },
            () => {
                // Errores de escaneo continuos, los ignoramos para no molestar al usuario.
            },
        );
    } catch (error) {
        const friendly = buildErrorMessage(error);
        errorMessage.value = friendly;
        emit('error', friendly);
        await stopScanner();
    } finally {
        isStarting.value = false;
    }
};

onMounted(async () => {
    await nextTick();
    startScanner();
});

onBeforeUnmount(() => {
    stopScanner(false);
});
</script>

<template>
    <div class="space-y-3">
        <div class="relative">
            <div
                id="barcode-scanner"
                class="relative w-full aspect-[3/4] overflow-hidden rounded-xl border border-slate-700 bg-black/80"
            ></div>

            <div
                v-if="isStarting"
                class="absolute inset-0 flex items-center justify-center rounded-xl bg-slate-900/60 text-xs text-slate-100 backdrop-blur-sm"
            >
                Iniciando la cámara…
            </div>
        </div>

        <p class="text-xs text-slate-400">
            Apunta la cámara al código de barras hasta que lo detecte.
        </p>

        <p
            v-if="errorMessage"
            class="rounded-lg border border-amber-300 bg-amber-50 px-3 py-2 text-xs font-medium text-amber-900"
        >
            {{ errorMessage }}
        </p>
    </div>
</template>
