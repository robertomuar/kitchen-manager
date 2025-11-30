<script setup>
import { onMounted, onBeforeUnmount, ref, nextTick } from 'vue';

const emit = defineEmits(['scanned', 'error', 'closed']);

const html5QrCode = ref(null);
const isStarting = ref(false);

const stopScanner = async (emitClose = false) => {
    if (!html5QrCode.value) return;

    try {
        await html5QrCode.value.stop();
        await html5QrCode.value.clear();
    } catch (e) {
        // ignoramos
    } finally {
        html5QrCode.value = null;
        if (emitClose) {
            emit('closed');
        }
    }
};

const startScanner = async () => {
    if (isStarting.value) return;

    isStarting.value = true;

    try {
        // Carga diferida para no penalizar el render inicial de la página
        const { Html5Qrcode } = await import('html5-qrcode');

        // El div con este id está en el template
        html5QrCode.value = new Html5Qrcode('barcode-scanner', {
            verbose: false,
        });

        await html5QrCode.value.start(
            { facingMode: 'environment' }, // cámara trasera en móvil
            {
                fps: 12,
                qrbox: { width: 260, height: 260 },
            },
            async (decodedText) => {
                await stopScanner();
                emit('scanned', decodedText);
            },
            () => {
                // Ignoramos errores de escaneo continuos para no saturar la consola
            },
        );
    } catch (e) {
        emit('error', e);
        await stopScanner(true);
    } finally {
        isStarting.value = false;
    }
};

onMounted(async () => {
    await nextTick();
    startScanner();
});

onBeforeUnmount(() => {
    stopScanner();
});
</script>

<template>
    <div class="space-y-3">
        <div
            id="barcode-scanner"
            class="w-full aspect-[3/4] overflow-hidden rounded-xl border border-slate-700 bg-black/80"
        ></div>
        <p class="text-xs text-slate-400">
            Apunta la cámara al código de barras hasta que lo detecte.
        </p>
    </div>
</template>
